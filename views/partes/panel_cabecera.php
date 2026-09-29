<?php
// Diseno del panel del personal: barra superior en el celular y menu lateral fijo en escritorio.
// Cada pagina define $titulo y $activo ("reservas", "disponibilidad" o "habitaciones") antes de incluirla.
require_once __DIR__ . "/ayudas.php";

use App\Bo\Contacto as ContactoBO;

/** @var array|null $flash Mensaje de la pagina anterior (lo crea views/partes/ayudas.php) */

$opciones = [
    "reservas" => ["views/admin/reservas.php", "bi-journal-text", "Reservas"],
    "disponibilidad" => ["views/admin/disponibilidad.php", "bi-calendar3", "Disponibilidad"],
    "habitaciones" => ["views/admin/gestion_habitaciones.php", "bi-door-open", "Habitaciones"],
    "mensajes" => ["views/admin/mensajes.php", "bi-envelope", "Mensajes"],
];
// Aviso de mensajes sin leer, visible en el menu sin tener que entrar a revisar
$mensajesNoLeidos = (new ContactoBO())->noLeidos();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo ?? "Panel") ?> | Recepción San Antonio</title>
    <link href="<?= url("assets/vendor/bootstrap/bootstrap.min.css") ?>" rel="stylesheet">
    <link href="<?= url("assets/vendor/bootstrap-icons/bootstrap-icons.min.css") ?>" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?= url("assets/img/logo.png") ?>">
    <link href="<?= recurso("assets/css/estilo.css") ?>" rel="stylesheet">
</head>

<body>
    <div class="panel-barra d-flex align-items-center justify-content-between px-3 py-2 d-lg-none">
        <?php $claro = "claro"; require __DIR__ . "/logo.php" ?>
        <button class="btn text-white fs-3 p-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuPanel" aria-label="Abrir menú">
            <i class="bi bi-list"></i>
        </button>
    </div>

    <div class="panel">
        <aside class="panel-lado offcanvas-lg offcanvas-start" tabindex="-1" id="menuPanel">
            <div class="offcanvas-header d-lg-none">
                <span class="panel-lema">Recepción</span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#menuPanel" aria-label="Cerrar"></button>
            </div>
            <div class="offcanvas-body flex-column p-3">
                <div class="mb-4 d-none d-lg-block"><?php $claro = "claro"; require __DIR__ . "/logo.php" ?></div>
                <nav class="nav flex-column gap-1 mb-auto">
                    <?php foreach ($opciones as $clave => [$url, $icono, $texto]) : ?>
                        <a class="nav-link <?= ($activo ?? "") === $clave ? "active" : "" ?>" href="<?= url($url) ?>">
                            <i class="bi <?= $icono ?>"></i> <?= $texto ?>
                            <?php if ($clave === "mensajes" && $mensajesNoLeidos > 0) : ?>
                                <span class="badge rounded-pill text-bg-danger ms-1"><?= $mensajesNoLeidos ?></span>
                            <?php endif ?>
                        </a>
                    <?php endforeach ?>
                    <a class="nav-link" href="<?= url("index.php") ?>"><i class="bi bi-box-arrow-up-right"></i> Ver sitio público</a>
                </nav>
                <div class="panel-lema mt-4">Hospitalidad que deja huellas</div>
            </div>
        </aside>

        <main class="panel-contenido">
            <?php if ($flash) : ?>
                <div class="alert alert-<?= $flash["ok"] ? "success" : "danger" ?>" role="alert"><?= e($flash["mensaje"]) ?></div>
            <?php endif ?>
