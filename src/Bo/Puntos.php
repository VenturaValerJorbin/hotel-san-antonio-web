<?php

namespace App\Bo;

use App\Dao\Huesped as HuespedDAO;
use App\Dao\MovimientoPuntos as MovimientoPuntosDAO;
use App\Dao\Parametro as ParametroDAO;
use App\Dao\Recompensa as RecompensaDAO;

// BO del programa de puntos: reglas y beneficios que se muestran en la pagina publica. Todos los
// beneficios son PERMANENTES (ver Bo\Reserva::descuentoPorBeneficios): no se gastan puntos, y el
// huesped elige cuales usar en cada reserva (puede marcar varios a la vez si su saldo le alcanza).
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

    // Saldo de puntos de un huesped por su documento (para que lo consulte el mismo huesped, sin
    // iniciar sesion), mas la lista de beneficios que ya le alcanzan con ese saldo (para que los
    // pueda elegir en el formulario de reserva). Si no hay estadias previas, el saldo es 0.
    public function saldoPorDocumento(string $tipoDocumento, string $numeroDocumento): array
    {
        $huesped = (new HuespedDAO())->buscarPorDocumento($tipoDocumento, $numeroDocumento);
        if (!$huesped) {
            return ["encontrado" => false, "puntos" => 0, "beneficios" => []];
        }
        $puntos = (new MovimientoPuntosDAO())->saldo((int) $huesped["id"]);
        return ["encontrado" => true, "puntos" => $puntos, "beneficios" => $this->beneficiosPorPuntos($puntos)];
    }

    // Beneficios elegibles con ese saldo: el pago fraccionado no entra aqui (se elige aparte, con
    // su propio campo), y de los descuentos en soles solo se ofrece el MEJOR (no tiene sentido
    // ofrecer el de 300 puntos si ya alcanza el de 500, que es mas alto).
    private function beneficiosPorPuntos(int $puntos): array
    {
        $beneficios = [];
        $mejorDescuento = null;
        foreach ((new RecompensaDAO())->listarActivas() as $r) {
            if ($r["tipo"] === "pago_fraccionado" || $puntos < (int) $r["puntos_requeridos"]) {
                continue;
            }
            if ($r["tipo"] === "descuento") {
                if (!$mejorDescuento || $r["valor"] > $mejorDescuento["valor"]) {
                    $mejorDescuento = $r;
                }
                continue;
            }
            $beneficios[] = $r;
        }
        if ($mejorDescuento) {
            $beneficios[] = $mejorDescuento;
        }
        return array_map(fn($r) => [
            "id" => (int) $r["id"],
            "nombre" => $r["nombre"],
            "descripcion" => $r["descripcion"],
            "valor" => (float) $r["valor"],
            "tipo" => $r["tipo"],
        ], $beneficios);
    }
}
