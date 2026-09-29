<?php

namespace App\Dao;

class CanjeRecompensa extends Dao
{
    public function insertar(int $huespedId, int $recompensaId): int
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO canje_recompensa (huesped_id, recompensa_id, estado) VALUES (:huesped_id, :recompensa_id, 'entregado')"
        );
        $this->enlazar($stmt, [":huesped_id" => $huespedId, ":recompensa_id" => $recompensaId]);
        $stmt->execute();
        return (int) $this->conn->lastInsertId();
    }

    // Historial de canjes de un huesped: para que recepcion vea que ya se le entrego antes
    public function listarPorHuesped(int $huespedId): array
    {
        $stmt = $this->conn->prepare(
            "SELECT c.id, c.created_at, r.nombre, r.puntos_requeridos
             FROM canje_recompensa c
             JOIN recompensa r ON r.id = c.recompensa_id
             WHERE c.huesped_id = :id
             ORDER BY c.created_at DESC"
        );
        $this->enlazar($stmt, [":id" => $huespedId]);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
