<?php

namespace App\Dao;

class MensajeContacto extends Dao
{
    public function insertar(array $d): int
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO mensaje_contacto (nombre, correo, telefono, asunto, mensaje)
             VALUES (:nombre, :correo, :telefono, :asunto, :mensaje)"
        );
        $this->enlazar($stmt, [
            ":nombre" => $d["nombre"],
            ":correo" => $d["correo"],
            ":telefono" => $d["telefono"],
            ":asunto" => $d["asunto"],
            ":mensaje" => $d["mensaje"],
        ]);
        $stmt->execute();
        return (int) $this->conn->lastInsertId();
    }
}
