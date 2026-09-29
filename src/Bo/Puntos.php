<?php

namespace App\Bo;

use App\Dao\Huesped as HuespedDAO;
use App\Dao\MovimientoPuntos as MovimientoPuntosDAO;
use App\Dao\Parametro as ParametroDAO;
use App\Dao\Recompensa as RecompensaDAO;

// BO del programa de puntos: reglas y beneficios que se muestran en la pagina publica. Todos los
// beneficios son PERMANENTES (ver Bo\Reserva::descuentoPorPuntos): se aplican solos al reservar
// segun el saldo del huesped, nadie tiene que canjear nada.
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
}
