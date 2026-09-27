<?php

namespace App\Dao;

class Pago extends Dao
{
    public function insertar(array $d): int
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO pago (reserva_id, tipo, monto, metodo, estado, pasarela, codigo_transaccion, fecha_pago)
             VALUES (:reserva_id, :tipo, :monto, :metodo, :estado, :pasarela, :codigo_transaccion, :fecha_pago)"
        );
        $this->enlazar($stmt, [
            ":reserva_id" => $d["reserva_id"],
            ":tipo" => $d["tipo"],
            ":monto" => $d["monto"],
            ":metodo" => $d["metodo"],
            ":estado" => $d["estado"],
            ":pasarela" => $d["pasarela"] ?? null,
            ":codigo_transaccion" => $d["codigo_transaccion"] ?? null,
            ":fecha_pago" => $d["fecha_pago"] ?? null,
        ]);
        $stmt->execute();
        return (int) $this->conn->lastInsertId();
    }
}
