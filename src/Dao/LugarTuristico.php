<?php

namespace App\Dao;

class LugarTuristico extends Dao
{
    public function listar(): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM lugar_turistico WHERE activo = 1 ORDER BY id");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
