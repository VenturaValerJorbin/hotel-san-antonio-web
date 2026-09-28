<?php
// Diseno de las paginas publicas: cabecera con menu. Cada pagina define $titulo antes de incluirla.
require_once __DIR__ . "/ayudas.php";

$pagina = basename($_SERVER["SCRIPT_NAME"]);
$menu = [
    "index.php" => "Inicio",
    "publico/habitaciones.php" => "Habitaciones",
    "publico/restaurante.php" => "Restaurante",
    "publico/ubicacion.php" => "Ubicación",
    "publico/recomendaciones.php" => "Recomendaciones",
    "publico/puntos.php" => "Puntos",
    "publico/nosotros.php" => "Nosotros",
    "publico/contacto.php" => "Contacto",
];
// El detalle de una habitacion pertenece a la seccion "Habitaciones"
$activa = $pagina === "habitacion.php" ? "habitaciones.php" : $pagina;   // se compara solo el nombre del archivo
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo ?? "Inicio") ?> | Hotel Turístico San Antonio</title>
    <link href="<?= url("assets/vendor/bootstrap/bootstrap.min.css") ?>" rel="stylesheet">
    <link href="<?= url("assets/vendor/bootstrap-icons/bootstrap-icons.min.css") ?>" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?= url("assets/img/logo.png") ?>">
    <link href="<?= url("assets/css/estilo.css") ?>" rel="stylesheet">
</head>

<body>
    <header class="sa-header">
        <!-- El menu se despliega en escritorio ancho (xl): con 7 enlaces no cabe en pantallas medianas -->
        <nav class="navbar navbar-expand-xl">
            <div class="container">
                <?php require __DIR__ . "/logo.php" ?>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPublico"
                    aria-controls="menuPublico" aria-expanded="false" aria-label="Abrir menú">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="menuPublico">
                    <ul class="navbar-nav ms-auto align-items-xl-center">
                        <?php foreach ($menu as $archivo => $texto) : ?>
                            <li class="nav-item">
                                <a class="nav-link <?= $activa === basename($archivo) ? "active" : "" ?>" href="<?= url($archivo) ?>"><?= $texto ?></a>
                            </li>
                        <?php endforeach ?>
                        <li class="nav-item ms-xl-3 my-2 my-xl-0">
                            <a class="btn-sa d-inline-block text-decoration-none" href="<?= url("publico/reservar.php") ?>"><i class="bi bi-calendar-check"></i> Reservar</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <?php if ($flash) : ?>
        <div class="container mt-3">
            <div class="alert alert-<?= $flash["ok"] ? "success" : "danger" ?> mb-0" role="alert"><?= e($flash["mensaje"]) ?></div>
        </div>
    <?php endif ?>
