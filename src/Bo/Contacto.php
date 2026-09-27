<?php

namespace App\Bo;

use App\Dao\MensajeContacto as MensajeContactoDAO;

class Contacto
{
    public function enviar(array $datos): void
    {
        (new MensajeContactoDAO())->insertar($datos);
    }
}
