<?php
require "config/Autoload.php";

use bo\Habitacion as HabitacionBO;

$titulo = "Inicio";
$tipos = (new HabitacionBO())->tipos();
// En la portada se destacan tres tipos
$destacadas = array_filter($tipos, fn($t) => in_array($t->nombre, ["Simple", "Matrimonial", "King"], true));

require "views/partes/cabecera.php";
?>
<section class="sa-hero sa-hero-grande">
    <div class="container">
        <div class="etiqueta mb-3">Tu hogar en la selva amazónica</div>
        <h1>Hotel Turístico<br>San Antonio</h1>
        <p class="serif fs-5 mt-2 mb-1">Bagua, Amazonas, Perú</p>
        <p class="mb-4">Hospitalidad, comodidad y la calidez de nuestra gente en un destino lleno de naturaleza y cultura.</p>
        <div class="d-grid d-sm-flex gap-2 mb-4">
            <a class="btn-sa text-decoration-none text-center" href="<?= url("publico/reservar.php") ?>">Reservar ahora <i class="bi bi-arrow-right"></i></a>
            <a class="btn-hero-linea text-decoration-none text-center" href="<?= url("publico/habitaciones.php") ?>">Ver habitaciones</a>
        </div>
        <div class="row g-3">
            <div class="col-12 col-sm-4 sa-rasgo"><i class="bi bi-tree"></i> Naturaleza cerca de ti</div>
            <div class="col-12 col-sm-4 sa-rasgo"><i class="bi bi-people"></i> Atención personalizada</div>
            <div class="col-12 col-sm-4 sa-rasgo"><i class="bi bi-star"></i> Comodidad garantizada</div>
        </div>
    </div>
</section>

<div class="container sa-buscador">
    <form class="sa-card sa-card-cuerpo" action="<?= url("publico/habitaciones.php") ?>" method="get">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-md-4 col-lg-3">
                <label class="form-label" for="ingreso">Fecha de ingreso</label>
                <input class="form-control" type="date" id="ingreso" name="ingreso" min="<?= date("Y-m-d") ?>" required>
            </div>
            <div class="col-12 col-md-4 col-lg-3">
                <label class="form-label" for="salida">Fecha de salida</label>
                <input class="form-control" type="date" id="salida" name="salida" min="<?= date("Y-m-d", strtotime("+1 day")) ?>" required>
            </div>
            <div class="col-12 col-md-4 col-lg-3">
                <label class="form-label" for="tipo">Tipo de habitación</label>
                <select class="form-select" id="tipo" name="tipo">
                    <option value="">Todas las habitaciones</option>
                    <?php foreach ($tipos as $t) : ?>
                        <option value="<?= $t->id ?>"><?= e($t->nombre) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-12 col-lg-3">
                <button class="btn-sa w-100" type="submit"><i class="bi bi-search"></i> Buscar disponibilidad</button>
            </div>
        </div>
        <div class="row g-2 mt-2">
            <div class="col-12 col-md-auto sa-aviso"><i class="bi bi-check-circle-fill"></i> Reserva sin crear cuenta</div>
            <div class="col-12 col-md-auto sa-aviso"><i class="bi bi-credit-card"></i> Pago online y seguro</div>
            <div class="col-12 col-md-auto sa-aviso"><i class="bi bi-gift"></i> Acumula puntos y paga solo el 50 % al reservar</div>
        </div>
    </form>
</div>

<main class="container py-5">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <div>
            <div class="sa-subtitulo">Descansa en un lugar especial</div>
            <h2 class="h3 mb-0">Habitaciones destacadas</h2>
        </div>
        <a class="small fw-bold" href="<?= url("publico/habitaciones.php") ?>">Ver todas <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="row g-3">
        <?php foreach ($destacadas as $t) {
            tarjetaTipo($t);
        } ?>
    </div>
</main>
<?php require "views/partes/pie.php" ?>
