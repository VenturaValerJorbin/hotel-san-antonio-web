<?php

namespace App\Dao;

class LimiteIntento extends Dao
{
    public function contarRecientes(string $ip, string $accion, string $desde): int
    {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) FROM limite_intento WHERE ip = :ip AND accion = :accion AND creado_en > :desde"
        );
        $this->enlazar($stmt, [":ip" => $ip, ":accion" => $accion, ":desde" => $desde]);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function registrar(string $ip, string $accion): void
    {
        $stmt = $this->conn->prepare("INSERT INTO limite_intento (ip, accion) VALUES (:ip, :accion)");
        $this->enlazar($stmt, [":ip" => $ip, ":accion" => $accion]);
        $stmt->execute();
    }

    // Limpieza oportunista (no hay tarea programada en el proyecto): sin esto la tabla creceria
    // para siempre. Se llama de paso cada vez que se revisa un limite.
    public function limpiarAntiguos(): void
    {
        $this->conn->exec("DELETE FROM limite_intento WHERE creado_en < (NOW() - INTERVAL 1 DAY)");
    }
}
