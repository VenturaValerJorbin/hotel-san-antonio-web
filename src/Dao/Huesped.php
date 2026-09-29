<?php

namespace App\Dao;

class Huesped extends Dao
{
    public function buscarPorDocumento(string $tipo, string $numero): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM huesped WHERE tipo_documento = :tipo AND numero_documento = :numero"
        );
        $this->enlazar($stmt, [":tipo" => $tipo, ":numero" => $numero]);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    public function insertar(array $d): int
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO huesped (tipo_documento, numero_documento, nombre_completo, correo, telefono)
             VALUES (:tipo_documento, :numero_documento, :nombre_completo, :correo, :telefono)"
        );
        $this->enlazar($stmt, [
            ":tipo_documento" => $d["tipo_documento"],
            ":numero_documento" => $d["numero_documento"],
            ":nombre_completo" => $d["nombre_completo"],
            ":correo" => $d["correo"] ?? null,
            ":telefono" => $d["telefono"],
        ]);
        $stmt->execute();
        return (int) $this->conn->lastInsertId();
    }

    // Un huesped que vuelve puede haber cambiado su telefono o su correo. El correo es opcional:
    // si esta vez no lo escribe, se conserva el que ya tenia guardado (COALESCE no lo borra).
    public function actualizarContacto(int $id, string $telefono, ?string $correo): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE huesped SET telefono = :telefono, correo = COALESCE(:correo, correo) WHERE id = :id"
        );
        $this->enlazar($stmt, [":telefono" => $telefono, ":correo" => $correo, ":id" => $id]);
        return $stmt->execute();
    }
}
