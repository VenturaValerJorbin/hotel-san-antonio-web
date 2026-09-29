<?php
// Zona horaria del hotel. Sin esto, PHP usa la del servidor (o UTC), y "hoy" puede
// adelantarse un dia entero respecto a la hora real de Bagua (Peru no usa horario de verano).
date_default_timezone_set("America/Lima");

// Autoload estilo PSR-4: el prefijo del namespace se reemplaza por una carpeta base.
//   App\Dao\Habitacion  ->  <raiz del proyecto>/src/Dao/Habitacion.php
//   App\Bo\Reserva      ->  <raiz del proyecto>/src/Bo/Reserva.php
spl_autoload_register(function (string $clase): void {
    $prefijo = "App\\";
    $carpetaBase = dirname(__DIR__) . "/src/";

    // Si la clase no es del proyecto (no empieza con App\) se ignora
    if (strncmp($clase, $prefijo, strlen($prefijo)) !== 0) {
        return;
    }

    // Lo que sigue al prefijo es la ruta dentro de src/: se cambia "\" por "/"
    $ruta = $carpetaBase . str_replace("\\", "/", substr($clase, strlen($prefijo))) . ".php";

    // Validacion: solo se carga si el archivo existe y se puede leer
    if (is_file($ruta) && is_readable($ruta)) {
        require $ruta;
    }
});
