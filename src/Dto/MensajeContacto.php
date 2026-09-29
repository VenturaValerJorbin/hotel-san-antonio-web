<?php

namespace App\Dto;

class MensajeContacto
{
    public function __construct(
        public int $id = 0,
        public string $nombre = "",
        public string $correo = "",
        public string $telefono = "",
        public string $asunto = "",
        public string $mensaje = "",
        public bool $leido = false,
        public string $creadoEn = ""
    ) {
    }
}
