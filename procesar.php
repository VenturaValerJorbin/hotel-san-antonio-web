<?php
// Punto de entrada de TODOS los formularios: solo acepta POST y deriva al controlador.
require "config/Autoload.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Metodo no permitido.");
}

// Lista blanca: solo estos modulos pueden ser invocados desde un formulario
$controladores = [
    "habitacion" => \App\Controller\Habitacion::class,
    "reserva" => \App\Controller\Reserva::class,
    "contacto" => \App\Controller\Contacto::class,
];

$clase = $controladores[$_POST["modulo"] ?? ""] ?? null;
if ($clase === null) {
    http_response_code(400);
    exit("Modulo no valido.");
}

$resultado = (new $clase())->manejar($_POST["accion"] ?? "", $_POST);

// El mensaje y, si hubo errores, los datos escritos se guardan para la siguiente pagina
$_SESSION["flash"] = ["ok" => $resultado["ok"], "mensaje" => $resultado["mensaje"]];
if (!$resultado["ok"]) {
    $_SESSION["errores"] = $resultado["errores"];
    $_SESSION["antiguo"] = $_POST;
}

// Post/Redirect/Get: al recargar no se reenvia el formulario
header("Location: " . $resultado["destino"]);
exit;
