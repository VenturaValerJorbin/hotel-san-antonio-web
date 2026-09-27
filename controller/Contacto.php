<?php

namespace controller;

use bo\Contacto as ContactoBO;

// Controlador del formulario de contacto.
class Contacto extends Controlador
{
    private const ASUNTOS = ["reserva", "consulta", "sugerencia", "reclamo"];

    public function manejar(string $accion, array $post): array
    {
        return $accion === "enviar"
            ? $this->enviar($post)
            : $this->resultado(false, "Accion no valida.", "contacto.php");
    }

    private function enviar(array $p): array
    {
        $datos = [
            "nombre" => trim($p["nombre"] ?? ""),
            "correo" => trim($p["correo"] ?? ""),
            "telefono" => trim($p["telefono"] ?? ""),
            "asunto" => $p["asunto"] ?? "",
            "mensaje" => trim($p["mensaje"] ?? ""),
        ];

        $errores = [];
        if (!preg_match("/^[\\p{L}][\\p{L} '.\\-]{2,99}$/u", $datos["nombre"])) {
            $errores["nombre"] = "Escribe tu nombre (solo letras).";
        }
        if (!filter_var($datos["correo"], FILTER_VALIDATE_EMAIL)) {
            $errores["correo"] = "Correo electronico no valido.";
        }
        if (!preg_match('/^\+?\d{7,15}$/', $datos["telefono"])) {
            $errores["telefono"] = "Celular no valido (solo numeros, 7 a 15 digitos).";
        }
        if (!in_array($datos["asunto"], self::ASUNTOS, true)) {
            $errores["asunto"] = "Selecciona un asunto.";
        }
        if (mb_strlen($datos["mensaje"]) < 10 || mb_strlen($datos["mensaje"]) > 500) {
            $errores["mensaje"] = "El mensaje debe tener entre 10 y 500 caracteres.";
        }
        if ($errores) {
            return $this->resultado(false, "Revisa los datos marcados en el formulario.", "contacto.php", $errores);
        }

        return $this->ejecutar(
            fn() => (new ContactoBO())->enviar($datos),
            "Gracias por escribirnos. Te responderemos a la brevedad.",
            "contacto.php",
            "contacto.php"
        );
    }
}
