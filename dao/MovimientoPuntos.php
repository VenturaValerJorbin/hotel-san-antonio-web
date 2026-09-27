<?php

namespace dao;

class MovimientoPuntos
{
    private \PDO $conn;

    public function __construct(?\PDO $conn = null)
    {
        $this->conn = $conn ?? (new Conexion())->conectar();
    }

    public function insertar(int $huespedId, ?int $reservaId, string $tipo, int $puntos, string $descripcion): int
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO movimiento_puntos (huesped_id, reserva_id, tipo, puntos, descripcion)
             VALUES (:huesped_id, :reserva_id, :tipo, :puntos, :descripcion)"
        );
        $stmt->execute([
            ":huesped_id" => $huespedId,
            ":reserva_id" => $reservaId,
            ":tipo" => $tipo,
            ":puntos" => $puntos,
            ":descripcion" => $descripcion,
        ]);
        return (int) $this->conn->lastInsertId();
    }

    // El saldo no se guarda: se calcula desde el historial (vista_puntos_huesped)
    public function saldo(int $huespedId): int
    {
        $stmt = $this->conn->prepare("SELECT puntos FROM vista_puntos_huesped WHERE huesped_id = :id");
        $stmt->execute([":id" => $huespedId]);
        return (int) $stmt->fetchColumn();
    }
}
