<?php

namespace App\Bo;

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
}
