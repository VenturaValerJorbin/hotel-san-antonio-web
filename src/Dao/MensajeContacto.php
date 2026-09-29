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

    // Filtros opcionales: asunto y leido (0/1)
    public function listar(array $filtros = []): array
    {
        $donde = [];
        $valores = [];
        if (($filtros["asunto"] ?? "") !== "") {
            $donde[] = "asunto = :asunto";
            $valores[":asunto"] = $filtros["asunto"];
        }
        if (($filtros["leido"] ?? "") !== "") {
            $donde[] = "leido = :leido";
            $valores[":leido"] = (int) $filtros["leido"];
        }
        $stmt = $this->conn->prepare(
            "SELECT * FROM mensaje_contacto"
            . ($donde ? " WHERE " . implode(" AND ", $donde) : "")
            . " ORDER BY leido ASC, created_at DESC"
        );
        $this->enlazar($stmt, $valores);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function contarNoLeidos(): int
    {
        return (int) $this->conn->query("SELECT COUNT(*) FROM mensaje_contacto WHERE leido = 0")->fetchColumn();
    }

    public function marcarLeido(int $id): bool
    {
        $stmt = $this->conn->prepare("UPDATE mensaje_contacto SET leido = 1 WHERE id = :id");
        $this->enlazar($stmt, [":id" => $id]);
        return $stmt->execute();
    }
}
