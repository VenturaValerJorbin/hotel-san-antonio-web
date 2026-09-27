<?php

namespace App\Dao;

class TipoHabitacion extends Dao
{
    public function listar(): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM tipo_habitacion WHERE activo = 1 ORDER BY precio_noche, id");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtener(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM tipo_habitacion WHERE id = :id AND activo = 1");
        $this->enlazar($stmt, [":id" => $id]);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    // Servicios que incluye el tipo de habitacion (bano privado, TV, Wi-Fi...)
    public function servicios(int $tipoId): array
    {
        $stmt = $this->conn->prepare(
            "SELECT s.nombre, s.icono FROM servicio s
             JOIN tipo_servicio ts ON ts.servicio_id = s.id
             WHERE ts.tipo_id = :id ORDER BY s.id"
        );
        $this->enlazar($stmt, [":id" => $tipoId]);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function fotos(int $tipoId): array
    {
        $stmt = $this->conn->prepare("SELECT ruta FROM foto_tipo_habitacion WHERE tipo_id = :id ORDER BY orden");
        $this->enlazar($stmt, [":id" => $tipoId]);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    // Habitaciones libres por tipo en un rango de fechas: [tipo_id => cantidad]
    public function disponibles(string $ingreso, string $salida): array
    {
        $stmt = $this->conn->prepare(
            "SELECT h.tipo_id, COUNT(*) AS libres FROM habitacion h
             WHERE h.activo = 1 AND h.estado <> 'mantenimiento'
               AND NOT EXISTS (
                   SELECT 1 FROM reserva r
                   WHERE r.habitacion_id = h.id
                     AND r.estado IN ('pendiente','confirmada','checkin')
                     AND r.fecha_ingreso < :salida AND r.fecha_salida > :ingreso)
             GROUP BY h.tipo_id"
        );
        $this->enlazar($stmt, [":ingreso" => $ingreso, ":salida" => $salida]);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
    }
}
