<?php
// Autoload: convierte el namespace de la clase en la ruta de su archivo (estilo PSR-4).
// Ej: dao\Habitacion -> <raiz del proyecto>/dao/Habitacion.php
spl_autoload_register(function ($class_name) {
    $ruta = dirname(__DIR__) . "/" . str_replace("\\", "/", $class_name) . ".php";
    if (file_exists($ruta)) {
        require $ruta;
    }
});
