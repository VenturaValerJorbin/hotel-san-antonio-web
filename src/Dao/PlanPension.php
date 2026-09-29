<?php

namespace App\Dao;

class PlanPension extends Dao
{
    // Solo los planes activos y en el orden en que deben mostrarse (de menos a mas comidas incluidas)
    public function listar(): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM plan_pension WHERE activo = 1 ORDER BY orden");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtener(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM plan_pension WHERE id = :id AND activo = 1");
        $this->enlazar($stmt, [":id" => $id]);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }
}
