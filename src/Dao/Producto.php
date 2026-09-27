<?php

namespace App\Dao;

class Producto extends Dao
{
    // Carta publica: productos activos con el nombre de su categoria
    public function listarCarta(): array
    {
        $stmt = $this->conn->prepare(
            "SELECT p.id, p.nombre, p.descripcion, p.precio, p.foto, c.nombre AS categoria
             FROM producto p JOIN categoria_producto c ON c.id = p.categoria_id
             WHERE p.activo = 1 ORDER BY c.id, p.id"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
