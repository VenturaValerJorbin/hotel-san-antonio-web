<?php

namespace App\Dao;

class Parametro extends Dao
{
    public function valor(string $clave): ?string
    {
        $stmt = $this->conn->prepare("SELECT valor FROM parametro WHERE clave = :clave");
        $this->enlazar($stmt, [":clave" => $clave]);
        $stmt->execute();
        $valor = $stmt->fetchColumn();
        return $valor === false ? null : $valor;
    }
}
