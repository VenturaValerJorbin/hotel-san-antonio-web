<?php

namespace App\Dto;

class Habitacion
{
    public function __construct(
        public int $id = 0,
        public string $numero = "",
        public int $piso = 1,
        public int $tipoId = 0,
        public string $tipo = "",
        public float $precioNoche = 0,
        public string $estado = "disponible",
        public string $descripcion = ""
    ) {
    }
}
