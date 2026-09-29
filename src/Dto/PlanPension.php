<?php

namespace App\Dto;

class PlanPension
{
    public function __construct(
        public int $id = 0,
        public string $nombre = "",
        public string $descripcion = "",
        public float $precioPorNoche = 0
    ) {
    }
}
