<?php

namespace App\Controller;

use App\Bo\Puntos as PuntosBO;

// Controlador del canje de recompensas (lo usa recepcion desde admin/canjes.php).
class Puntos extends Controlador
{
    public function manejar(string $accion, array $post): array
    {
        return $accion === "canjear"
            ? $this->canjear($post)
            : $this->resultado(false, "Accion no valida.", "admin/canjes.php");
    }

    private function canjear(array $p): array
    {
        $huespedId = (int) ($p["huesped_id"] ?? 0);
        $recompensaId = (int) ($p["recompensa_id"] ?? 0);
        $formulario = "admin/canjes.php?" . http_build_query([
            "tipo_documento" => $p["tipo_documento"] ?? "",
            "numero_documento" => $p["numero_documento"] ?? "",
        ]);

        if ($huespedId <= 0 || $recompensaId <= 0) {
            return $this->resultado(false, "Selecciona un beneficio para canjear.", $formulario);
        }

        return $this->ejecutar(
            fn() => (new PuntosBO())->canjear($huespedId, $recompensaId),
            "Canje registrado.",
            $formulario,
            $formulario
        );
    }
}
