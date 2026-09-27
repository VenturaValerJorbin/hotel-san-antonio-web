<?php

namespace dao;

class MensajeContacto
{
    private \PDO $conn;

    public function __construct(?\PDO $conn = null)
    {
        $this->conn = $conn ?? (new Conexion())->conectar();
    }

    public function insertar(array $d): int
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO mensaje_contacto (nombre, correo, telefono, asunto, mensaje)
             VALUES (:nombre, :correo, :telefono, :asunto, :mensaje)"
        );
        $stmt->execute([
            ":nombre" => $d["nombre"],
            ":correo" => $d["correo"],
            ":telefono" => $d["telefono"],
            ":asunto" => $d["asunto"],
            ":mensaje" => $d["mensaje"],
        ]);
        return (int) $this->conn->lastInsertId();
    }
}
