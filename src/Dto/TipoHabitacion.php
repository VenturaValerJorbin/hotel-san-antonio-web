<?php

namespace App\Dto;

class TipoHabitacion
{
    public function __construct(
        public int $id = 0,
        public string $nombre = "",
        public string $descripcion = "",
        public string $detalle = "",
        public int $capacidad = 1,
        public float $precioNoche = 0,
        public array $servicios = [],
        public array $fotos = [],
        public ?int $libres = null   // habitaciones libres en las fechas buscadas (null si no se buscaron)
    ) {
    }
}
