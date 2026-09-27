<?php

namespace dto;

class Reserva
{
    public function __construct(
        public int $id = 0,
        public string $codigo = "",
        public string $huesped = "",
        public string $documento = "",
        public string $habitacion = "",
        public string $tipo = "",
        public string $fechaIngreso = "",
        public string $fechaSalida = "",
        public int $noches = 0,
        public float $montoTotal = 0,
        public float $montoPagado = 0,
        public float $saldoPendiente = 0,
        public string $modalidadPago = "completo",
        public string $estado = "pendiente"
    ) {
    }
}
