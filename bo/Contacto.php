<?php

namespace bo;

use dao\MensajeContacto as MensajeContactoDAO;

class Contacto
{
    public function enviar(array $datos): void
    {
        (new MensajeContactoDAO())->insertar($datos);
    }
}
