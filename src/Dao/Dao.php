<?php

namespace App\Dao;

// Base de todos los DAO: guarda la conexion y ofrece el paso "Bind" de Prepare - Bind - Execute.
// Las conexiones se pueden compartir entre DAO para agruparlos en una misma transaccion.
abstract class Dao
{
    protected \PDO $conn;

    public function __construct(?\PDO $conn = null)
    {
        $this->conn = $conn ?? (new Conexion())->conectar();
    }

    // Bind: asocia cada valor a su marcador con el tipo de dato de PDO que le corresponde.
    // Los valores nunca se concatenan al SQL: viajan aparte, por eso no hay inyeccion SQL.
    protected function enlazar(\PDOStatement $stmt, array $valores): void
    {
        foreach ($valores as $marcador => $valor) {
            $tipo = match (true) {
                is_int($valor) => \PDO::PARAM_INT,
                is_bool($valor) => \PDO::PARAM_BOOL,
                $valor === null => \PDO::PARAM_NULL,
                default => \PDO::PARAM_STR,
            };
            $stmt->bindValue($marcador, $valor, $tipo);
        }
    }
}
