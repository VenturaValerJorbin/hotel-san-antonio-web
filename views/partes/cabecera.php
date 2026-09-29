<?php
// Diseno de las paginas publicas: cabecera con menu. Cada pagina define $titulo antes de incluirla.
require_once __DIR__ . "/ayudas.php";
/** @var array|null $flash Mensaje de la pagina anterior (lo crea views/partes/ayudas.php) */

$pagina = basename($_SERVER["SCRIPT_NAME"]);
$menu = [
    "index.php" => "Inicio",
    "views/publico/habitaciones.php" => "Habitaciones",
    "views/publico/restaurante.php" => "Restaurante",
    "views/publico/ubicacion.php" => "Ubicación",
    "views/publico/recomendaciones.php" => "Recomendaciones",
    "views/publico/puntos.php" => "Puntos",
    "views/publico/nosotros.php" => "Nosotros",
    "views/publico/contacto.php" => "Contacto",
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
    <link href="<?= recurso("assets/css/estilo.css") ?>" rel="stylesheet">
</head>

<body>
    <header class="sa-header">
        <?php // El menu se despliega en escritorio ancho (xl): con tantos enlaces no cabe en pantallas medianas ?>
        <nav class="navbar navbar-expand-xl">
            <div class="container">
                <?php require __DIR__ . "/logo.php" ?>
                <div class="d-flex align-items-center order-xl-last">
                    <a class="btn-linea sa-boton-buscar text-decoration-none" href="<?= url("views/publico/comprobante.php") ?>">
                        <i class="bi bi-search"></i> <span class="d-none d-sm-inline">Mi reserva</span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPublico"
                        aria-controls="menuPublico" aria-expanded="false" aria-label="Abrir menú">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="menuPublico">
                    <ul class="navbar-nav ms-auto align-items-xl-center">
                        <?php foreach ($menu as $archivo => $texto) : ?>
                            <li class="nav-item">
                                <a class="nav-link <?= $activa === basename($archivo) ? "active" : "" ?>" href="<?= url($archivo) ?>"><?= $texto ?></a>
                            </li>
                        <?php endforeach ?>
                        <li class="nav-item ms-xl-3 my-2 my-xl-0">
                            <a class="btn-sa d-inline-block text-decoration-none" href="<?= url("views/publico/reservar.php") ?>"><i class="bi bi-calendar-check"></i> Reservar</a>
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
