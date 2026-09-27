<?php

namespace dao;

class LugarTuristico
{
    private \PDO $conn;

    public function __construct(?\PDO $conn = null)
    {
        $this->conn = $conn ?? (new Conexion())->conectar();
    }

    public function listar(): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM lugar_turistico WHERE activo = 1 ORDER BY id");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
