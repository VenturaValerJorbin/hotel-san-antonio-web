<?php

namespace App\Controller;

use App\Bo\Habitacion as HabitacionBO;

// Controlador de habitaciones: recibe el POST, valida en el servidor y llama al BO.
class Habitacion extends Controlador
{
    private const ESTADOS = ["disponible", "ocupada", "limpieza", "mantenimiento"];
    private HabitacionBO $bo;

    public function __construct()
    {
        $this->bo = new HabitacionBO();
    }

    public function manejar(string $accion, array $post): array
    {
        return match ($accion) {
            "guardar" => $this->guardar($post),
            "eliminar" => $this->eliminar($post),
            default => $this->resultado(false, "Accion no valida.", "admin/gestion_habitaciones.php"),
        };
    }

    private function guardar(array $p): array
    {
        $id = (int) ($p["id"] ?? 0);
        $formulario = "admin/gestion_habitaciones.php" . ($id > 0 ? "?editar=$id" : "");

        $datos = [
            "numero" => trim($p["numero"] ?? ""),
            "piso" => (int) ($p["piso"] ?? 0),
            "tipo_id" => (int) ($p["tipo_id"] ?? 0),
            "estado" => $p["estado"] ?? "",
            "descripcion" => trim($p["descripcion"] ?? ""),
        ];

        $errores = $this->validar($datos);
        if ($errores) {
            return $this->resultado(false, "Revisa los datos ingresados.", $formulario, $errores);
        }

        return $this->ejecutar(
            fn() => $this->bo->guardar($id, $datos),
            $id > 0 ? "Habitacion actualizada." : "Habitacion registrada.",
            "admin/gestion_habitaciones.php",
            $formulario
        );
    }

    private function eliminar(array $p): array
    {
        $id = (int) ($p["id"] ?? 0);
        if ($id <= 0) {
            return $this->resultado(false, "Habitacion no valida.", "admin/gestion_habitaciones.php");
        }
        return $this->ejecutar(fn() => $this->bo->eliminar($id), "Habitacion eliminada.", "admin/gestion_habitaciones.php", "admin/gestion_habitaciones.php");
    }

    // Validacion de forma: formato, rango y valores permitidos
    private function validar(array $d): array
    {
        $errores = [];
        if (!preg_match('/^[0-9A-Za-z]{1,5}$/', $d["numero"])) {
            $errores["numero"] = "Usa de 1 a 5 letras o numeros.";
        }
        if ($d["piso"] < 1 || $d["piso"] > 10) {
            $errores["piso"] = "El piso debe estar entre 1 y 10.";
        }
        if ($d["tipo_id"] <= 0) {
            $errores["tipo_id"] = "Selecciona un tipo de habitacion.";
        }
        if (!in_array($d["estado"], self::ESTADOS, true)) {
            $errores["estado"] = "Estado no valido.";
        }
        if (mb_strlen($d["descripcion"]) > 255) {
            $errores["descripcion"] = "Maximo 255 caracteres.";
        }
        return $errores;
    }
}
