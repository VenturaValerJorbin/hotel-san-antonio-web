<?php

namespace App\Dao;

class Recompensa extends Dao
{
    public function obtenerPorTipo(string $tipo): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM recompensa WHERE tipo = :tipo AND activo = 1 LIMIT 1");
        $this->enlazar($stmt, [":tipo" => $tipo]);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }
}
