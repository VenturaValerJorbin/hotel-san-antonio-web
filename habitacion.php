<?php
require "config/Autoload.php";
require_once "views/partes/ayudas.php";

use bo\Habitacion as HabitacionBO;
use bo\Reserva as ReservaBO;

$tipo = (new HabitacionBO())->tipo((int) ($_GET["id"] ?? 0));
if (!$tipo) {
    http_response_code(404);
    header("Location: habitaciones.php");
    exit;
}

$titulo = "Habitación " . $tipo->nombre;
$beneficio = (new ReservaBO())->beneficioFraccionado();
$query = http_build_query(array_filter([
    "tipo" => $tipo->id, "ingreso" => fechaGet("ingreso"), "salida" => fechaGet("salida"),
]));

require "views/partes/cabecera.php";
hero(
    "Habitación " . $tipo->nombre,
    $tipo->descripcion,
    [["Inicio", "index.php"], ["Habitaciones", "habitaciones.php"], [$tipo->nombre, null]]
);
?>
<main class="container py-4 py-lg-5">
    <div class="row g-4">
        <div class="col-12 col-lg-7">
            <div class="sa-card p-2">
                <?php foto($tipo->fotos[0] ?? null, "Habitación " . $tipo->nombre, "bi-house-heart") ?>
                <?php if (count($tipo->fotos) > 1) : ?>
                    <div class="row g-2 mt-1">
                        <?php foreach (array_slice($tipo->fotos, 0, 4) as $f) : ?>
                            <div class="col-3"><?php foto($f, "Foto de la habitación " . $tipo->nombre) ?></div>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </div>

            <h2 class="h4 mt-4">Descripción</h2>
            <p><?= e($tipo->detalle ?: $tipo->descripcion) ?></p>

            <h2 class="h4 mt-4">Comodidades destacadas</h2>
            <div class="d-flex flex-wrap gap-3">
                <span class="sa-servicio"><i class="bi bi-people"></i> <?= $tipo->capacidad ?> <?= $tipo->capacidad === 1 ? "persona" : "personas" ?></span>
                <?php foreach ($tipo->servicios as $s) : ?>
                    <span class="sa-servicio"><i class="bi <?= e($s["icono"]) ?>"></i> <?= e($s["nombre"]) ?></span>
                <?php endforeach ?>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <aside class="sa-card sa-card-cuerpo resumen-fijo">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h2 class="h3 mb-1"><?= e($tipo->nombre) ?></h2>
                        <span class="text-muted"><i class="bi bi-people"></i> <?= $tipo->capacidad ?> <?= $tipo->capacidad === 1 ? "persona" : "personas" ?></span>
                    </div>
                    <div class="sa-precio fs-2"><?= soles($tipo->precioNoche) ?><small>por noche</small></div>
                </div>
                <hr>
                <h3 class="h6">Servicios de la habitación</h3>
                <div class="row g-2 mb-3">
                    <?php foreach ($tipo->servicios as $s) : ?>
                        <div class="col-6 small"><i class="bi <?= e($s["icono"]) ?> me-1"></i> <?= e($s["nombre"]) ?></div>
                    <?php endforeach ?>
                </div>

                <div class="sa-caja-pago mb-2 small">
                    <strong>Pago de la reserva en línea.</strong>
                    Pagas el 100 % al reservar<?= $beneficio ? ", o solo el " . (float) $beneficio["valor"] . " % si eres huésped frecuente (" . $beneficio["puntos_requeridos"] . " puntos)" : "" ?>.
                </div>
                <div class="sa-caja-crema mb-3 small"><strong>Reserva sin crear cuenta.</strong> Haz tu reserva de forma rápida y sencilla.</div>

                <a class="btn-sa d-block text-center text-decoration-none" href="reservar.php?<?= e($query) ?>">Reservar</a>
                <p class="text-center small text-muted mt-2 mb-0"><i class="bi bi-calendar-heart"></i> Tu descanso te espera en Bagua.</p>
            </aside>
        </div>
    </div>
</main>
<?php require "views/partes/pie.php" ?>
