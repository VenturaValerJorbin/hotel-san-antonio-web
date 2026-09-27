<?php

namespace dao;

class Pago
{
    private \PDO $conn;

    public function __construct(?\PDO $conn = null)
    {
        $this->conn = $conn ?? (new Conexion())->conectar();
    }

    public function insertar(array $d): int
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO pago (reserva_id, tipo, monto, metodo, estado, pasarela, codigo_transaccion, fecha_pago)
             VALUES (:reserva_id, :tipo, :monto, :metodo, :estado, :pasarela, :codigo_transaccion, :fecha_pago)"
        );
        $stmt->execute([
            ":reserva_id" => $d["reserva_id"],
            ":tipo" => $d["tipo"],
            ":monto" => $d["monto"],
            ":metodo" => $d["metodo"],
            ":estado" => $d["estado"],
            ":pasarela" => $d["pasarela"] ?? null,
            ":codigo_transaccion" => $d["codigo_transaccion"] ?? null,
            ":fecha_pago" => $d["fecha_pago"] ?? null,
        ]);
        return (int) $this->conn->lastInsertId();
    }
}
