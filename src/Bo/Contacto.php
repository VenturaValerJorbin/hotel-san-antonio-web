<?php

namespace App\Bo;

use App\Dao\MensajeContacto as MensajeContactoDAO;
use App\Dto\MensajeContacto as MensajeContactoDTO;

class Contacto
{
    public function enviar(array $datos): void
    {
        (new MensajeContactoDAO())->insertar($datos);
    }

    public function listar(array $filtros = []): array
    {
        return array_map(
            fn($m) => new MensajeContactoDTO(
                (int) $m["id"], $m["nombre"], $m["correo"], $m["telefono"],
                $m["asunto"], $m["mensaje"], (bool) $m["leido"], $m["created_at"]
            ),
            (new MensajeContactoDAO())->listar($filtros)
        );
    }

    public function noLeidos(): int
    {
        return (new MensajeContactoDAO())->contarNoLeidos();
    }

    public function marcarLeido(int $id): void
    {
        (new MensajeContactoDAO())->marcarLeido($id);
    }
}
