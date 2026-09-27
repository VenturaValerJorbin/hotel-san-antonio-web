<?php

namespace dao;

// DAO de habitaciones: unico lugar donde se escribe SQL de esta tabla (CRUD completo).
// Recibe la conexion por parametro para poder compartirla dentro de una transaccion.
class Habitacion
{
    private \PDO $conn;

    public function __construct(?\PDO $conn = null)
    {
        $this->conn = $conn ?? (new Conexion())->conectar();
    }

    // READ: todas las habitaciones con su tipo y precio
    public function listar(): array
    {
        $stmt = $this->conn->prepare(
            "SELECT h.*, t.nombre AS tipo, t.precio_noche
             FROM habitacion h
             JOIN tipo_habitacion t ON t.id = h.tipo_id
             ORDER BY h.numero"
        );
        $stmt->execute();
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
        $stmt->bindValue(":id", $id, \PDO::PARAM_INT);
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
        $stmt->bindValue(":numero", $d["numero"], \PDO::PARAM_STR);
        $stmt->bindValue(":piso", $d["piso"], \PDO::PARAM_INT);
        $stmt->bindValue(":tipo_id", $d["tipo_id"], \PDO::PARAM_INT);
        $stmt->bindValue(":estado", $d["estado"], \PDO::PARAM_STR);
        $stmt->bindValue(":descripcion", $d["descripcion"], \PDO::PARAM_STR);
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
        $stmt->bindValue(":numero", $d["numero"], \PDO::PARAM_STR);
        $stmt->bindValue(":piso", $d["piso"], \PDO::PARAM_INT);
        $stmt->bindValue(":tipo_id", $d["tipo_id"], \PDO::PARAM_INT);
        $stmt->bindValue(":estado", $d["estado"], \PDO::PARAM_STR);
        $stmt->bindValue(":descripcion", $d["descripcion"], \PDO::PARAM_STR);
        $stmt->bindValue(":id", $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    // DELETE
    public function eliminar(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM habitacion WHERE id = :id");
        $stmt->bindValue(":id", $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Cambia solo el estado (ocupada, limpieza, etc.) en check-in / check-out
    public function cambiarEstado(int $id, string $estado): bool
    {
        $stmt = $this->conn->prepare("UPDATE habitacion SET estado = :estado WHERE id = :id");
        return $stmt->execute([":estado" => $estado, ":id" => $id]);
    }

    // Sirve para validar que el numero no se repita (al editar se excluye la propia habitacion)
    public function existeNumero(string $numero, int $exceptoId = 0): bool
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM habitacion WHERE numero = :numero AND id <> :id");
        $stmt->execute([":numero" => $numero, ":id" => $exceptoId]);
        return $stmt->fetchColumn() > 0;
    }
}
