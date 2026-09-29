<?php

namespace App\Bo;

use App\Dao\CanjeRecompensa as CanjeRecompensaDAO;
use App\Dao\Conexion;
use App\Dao\Huesped as HuespedDAO;
use App\Dao\MovimientoPuntos as MovimientoPuntosDAO;
use App\Dao\Parametro as ParametroDAO;
use App\Dao\Recompensa as RecompensaDAO;

// BO del programa de puntos: reglas y beneficios que se muestran en la pagina publica.
// Los valores salen de la BD (tablas parametro y recompensa), no van fijos en el codigo.
class Puntos
{
    // Soles de estadia que valen 1 punto (misma regla que usa el check-out en Bo\Reserva)
    public function solesPorPunto(): int
    {
        return max(1, (int) (new ParametroDAO())->valor("soles_por_punto"));
    }

    public function puntosPorMonto(float $monto): int
    {
        return intdiv((int) $monto, $this->solesPorPunto());
    }

    public function recompensas(): array
    {
        return (new RecompensaDAO())->listarActivas();
    }

    // Saldo de puntos de un huesped por su documento (para que lo consulte el mismo huesped,
    // sin iniciar sesion). Si no hay estadias previas con ese documento, el saldo es 0.
    public function saldoPorDocumento(string $tipoDocumento, string $numeroDocumento): array
    {
        $huesped = (new HuespedDAO())->buscarPorDocumento($tipoDocumento, $numeroDocumento);
        if (!$huesped) {
            return ["encontrado" => false, "puntos" => 0];
        }
        $puntos = (new MovimientoPuntosDAO())->saldo((int) $huesped["id"]);
        return ["encontrado" => true, "puntos" => $puntos];
    }

    // Datos para la pantalla de canjes de recepcion: el huesped, su saldo, que puede canjear
    // ahora mismo con ese saldo, y su historial de canjes anteriores.
    public function buscarParaCanje(string $tipoDocumento, string $numeroDocumento): ?array
    {
        $huesped = (new HuespedDAO())->buscarPorDocumento($tipoDocumento, $numeroDocumento);
        if (!$huesped) {
            return null;
        }
        $huespedId = (int) $huesped["id"];
        $puntos = (new MovimientoPuntosDAO())->saldo($huespedId);
        $canjeables = array_values(array_filter(
            (new RecompensaDAO())->listarActivas(),
            fn($r) => (int) $r["consume_puntos"] === 1 && $puntos >= (int) $r["puntos_requeridos"]
        ));
        return [
            "huesped" => $huesped,
            "puntos" => $puntos,
            "canjeables" => $canjeables,
            "historial" => (new CanjeRecompensaDAO())->listarPorHuesped($huespedId),
        ];
    }

    // Registra el canje: resta los puntos y deja constancia. Vuelve a comprobar el saldo real
    // dentro de la transaccion (nunca el que se veia en pantalla), por si cambio mientras tanto.
    public function canjear(int $huespedId, int $recompensaId): void
    {
        $conn = (new Conexion())->conectar();
        try {
            $conn->beginTransaction();
            $recompensa = (new RecompensaDAO($conn))->obtener($recompensaId)
                ?? throw new \DomainException("La recompensa no existe.");
            if (!$recompensa["consume_puntos"]) {
                throw new \DomainException("Ese beneficio es permanente, no se canjea.");
            }
            $puntos = (new MovimientoPuntosDAO($conn))->saldo($huespedId);
            if ($puntos < $recompensa["puntos_requeridos"]) {
                throw new \DomainException("El huesped ya no tiene puntos suficientes para este canje.");
            }
            $canjeId = (new CanjeRecompensaDAO($conn))->insertar($huespedId, $recompensaId);
            (new MovimientoPuntosDAO($conn))->insertar(
                $huespedId, null, "canjeado", -(int) $recompensa["puntos_requeridos"],
                "Canje: " . $recompensa["nombre"], $canjeId
            );
            $conn->commit();
        } catch (\Throwable $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            throw $e;
        }
    }
}
