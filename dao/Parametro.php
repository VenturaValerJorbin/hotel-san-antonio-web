<?php

namespace dao;

class Parametro
{
    private \PDO $conn;

    public function __construct(?\PDO $conn = null)
    {
        $this->conn = $conn ?? (new Conexion())->conectar();
    }

    public function valor(string $clave): ?string
    {
        $stmt = $this->conn->prepare("SELECT valor FROM parametro WHERE clave = :clave");
        $stmt->execute([":clave" => $clave]);
        $valor = $stmt->fetchColumn();
        return $valor === false ? null : $valor;
    }
}
