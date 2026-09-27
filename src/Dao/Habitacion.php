<?php

namespace App\Dao;

// DAO de habitaciones: unico lugar donde se escribe SQL de esta tabla (CRUD completo).
// Cada metodo sigue el mismo orden: Prepare (SQL con marcadores) - Bind (valores) - Execute.
class Habitacion extends Dao
{
    // READ: todas las habitaciones con su tipo y precio
    public function listar(): array
    {
        $stmt = $this->conn->prepare(                                   // Prepare
            "SELECT h.*, t.nombre AS tipo, t.precio_noche
             FROM habitacion h
             JOIN tipo_habitacion t ON t.id = h.tipo_id
             ORDER BY h.numero"
        );
        $stmt->execute();                                               // Execute
        return $stmt->fetchAll();
    }

    // READ: una habitacion por id (null si no existe)
    public function obtener(int $id): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT h.*, t.nombre AS tipo, t.precio_noche
             FROM habitacion h
             JOIN tipo_habitacion t ON t.id = h.tipo_id
             WHERE h.id = :id"
        );
        $this->enlazar($stmt, [":id" => $id]);                          // Bind
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    // CREATE: devuelve el id generado
    public function insertar(array $d): int
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO habitacion (numero, piso, tipo_id, estado, descripcion)
             VALUES (:numero, :piso, :tipo_id, :estado, :descripcion)"
        );
        $this->enlazar($stmt, [
            ":numero" => $d["numero"],
            ":piso" => $d["piso"],
            ":tipo_id" => $d["tipo_id"],
            ":estado" => $d["estado"],
            ":descripcion" => $d["descripcion"],
        ]);
        $stmt->execute();
        return (int) $this->conn->lastInsertId();
    }

    // UPDATE
    public function actualizar(int $id, array $d): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE habitacion
             SET numero = :numero, piso = :piso, tipo_id = :tipo_id,
                 estado = :estado, descripcion = :descripcion
             WHERE id = :id"
        );
        $this->enlazar($stmt, [
            ":numero" => $d["numero"],
            ":piso" => $d["piso"],
            ":tipo_id" => $d["tipo_id"],
            ":estado" => $d["estado"],
            ":descripcion" => $d["descripcion"],
            ":id" => $id,
        ]);
        return $stmt->execute();
    }

    // DELETE
    public function eliminar(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM habitacion WHERE id = :id");
        $this->enlazar($stmt, [":id" => $id]);
        return $stmt->execute();
    }

    // Cambia solo el estado (ocupada, limpieza, etc.) en check-in / check-out
    public function cambiarEstado(int $id, string $estado): bool
    {
        $stmt = $this->conn->prepare("UPDATE habitacion SET estado = :estado WHERE id = :id");
        $this->enlazar($stmt, [":estado" => $estado, ":id" => $id]);
        return $stmt->execute();
    }

    // Sirve para validar que el numero no se repita (al editar se excluye la propia habitacion)
    public function existeNumero(string $numero, int $exceptoId = 0): bool
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM habitacion WHERE numero = :numero AND id <> :id");
        $this->enlazar($stmt, [":numero" => $numero, ":id" => $exceptoId]);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}
