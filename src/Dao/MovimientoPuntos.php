<?php

namespace App\Dao;

class MovimientoPuntos extends Dao
{
    public function insertar(int $huespedId, ?int $reservaId, string $tipo, int $puntos, string $descripcion): int
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO movimiento_puntos (huesped_id, reserva_id, tipo, puntos, descripcion)
             VALUES (:huesped_id, :reserva_id, :tipo, :puntos, :descripcion)"
        );
        $this->enlazar($stmt, [
            ":huesped_id" => $huespedId,
            ":reserva_id" => $reservaId,
            ":tipo" => $tipo,
            ":puntos" => $puntos,
            ":descripcion" => $descripcion,
        ]);
        $stmt->execute();
        return (int) $this->conn->lastInsertId();
    }

    // El saldo no se guarda: se calcula desde el historial (vista_puntos_huesped)
    public function saldo(int $huespedId): int
    {
        $stmt = $this->conn->prepare("SELECT puntos FROM vista_puntos_huesped WHERE huesped_id = :id");
        $this->enlazar($stmt, [":id" => $huespedId]);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }
}
