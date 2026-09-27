<?php

namespace dao;

class Recompensa
{
    private \PDO $conn;

    public function __construct(?\PDO $conn = null)
    {
        $this->conn = $conn ?? (new Conexion())->conectar();
    }

    public function obtenerPorTipo(string $tipo): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM recompensa WHERE tipo = :tipo AND activo = 1 LIMIT 1");
        $stmt->execute([":tipo" => $tipo]);
        return $stmt->fetch() ?: null;
    }
}
