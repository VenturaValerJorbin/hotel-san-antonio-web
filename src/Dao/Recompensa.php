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

    // Recompensas vigentes de menos a mas puntos (pagina publica del programa de puntos)
    public function listarActivas(): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM recompensa WHERE activo = 1 ORDER BY puntos_requeridos, id");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
