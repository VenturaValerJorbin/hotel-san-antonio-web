<?php

namespace App\Dao;

class Conexion
{
    private $host = "localhost";
    private $dbname = "daw2_hotel_san_antonio";
    private $user = "root";
    private $password = "";

    // Crea y devuelve la conexion PDO
    public function conectar(): \PDO
    {
        try {
            $dsn = "mysql:host=$this->host;dbname=$this->dbname;charset=utf8mb4";
            $conexion = new \PDO($dsn, $this->user, $this->password);
            // Los errores de SQL se lanzan como excepciones (PDOException), en vez de fallar en silencio
            $conexion->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            // Los resultados de las consultas vienen como arreglo asociativo: $fila["columna"]
            $conexion->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            // Sentencias preparadas reales en el servidor (no simuladas por PHP)
            $conexion->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            return $conexion;
        } catch (\PDOException $e) {
            throw $e;
        }
    }
}
