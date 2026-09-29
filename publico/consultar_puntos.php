<?php
// Endpoint de solo lectura para consultar el saldo de puntos de un documento (usado por JS,
// desde el formulario de reserva y desde la pagina de puntos). No pasa por procesar.php porque
// no crea ni modifica nada: es una lectura (GET), no una accion (POST).
require __DIR__ . "/../config/Autoload.php";

use App\Bo\Puntos as PuntosBO;

header("Content-Type: application/json; charset=utf-8");

$tipoDocumento = strtoupper(trim($_GET["tipo_documento"] ?? ""));
$numeroDocumento = strtoupper(trim($_GET["numero_documento"] ?? ""));

$documentoValido = match ($tipoDocumento) {
    "DNI" => (bool) preg_match('/^\d{8}$/', $numeroDocumento),
    "PASAPORTE" => (bool) preg_match('/^(?=.*[A-Z])[A-Z0-9]{6,12}$/', $numeroDocumento),
    default => false,
};

if (!$documentoValido) {
    http_response_code(400);
    echo json_encode(["ok" => false, "mensaje" => "Ingresa un documento valido."]);
    exit;
}

$resultado = (new PuntosBO())->saldoPorDocumento($tipoDocumento, $numeroDocumento);
echo json_encode([
    "ok" => true,
    "encontrado" => $resultado["encontrado"],
    "puntos" => $resultado["puntos"],
]);
