<?php
require "config/Autoload.php";

use App\Bo\Habitacion as HabitacionBO;

$titulo = "Inicio";
$tipos = (new HabitacionBO())->tipos();
// En la portada se destacan tres tipos
$destacadas = array_filter($tipos, fn($t) => in_array($t->nombre, ["Simple", "Matrimonial", "King"], true));
$servicios = [
    ["bi-car-front", "Cochera gratuita", "Estacionamiento sin costo para nuestros huéspedes."],
    ["bi-wifi", "Wi-Fi gratis", "Internet sin costo durante tu estadía."],
    ["bi-clock", "Recepción 24 horas", "Atención a cualquier hora del día."],
    ["bi-cup-hot", "Restaurante", "Comida regional, menú del día y servicio a la habitación."],
];

require "views/partes/cabecera.php";
?>
<section class="sa-hero sa-hero-grande sa-hero-foto ">
    <div class="container ">
        <div class="etiqueta mb-3">Tu hogar en la selva amazónica</div>
        <h1>Hotel Turístico<br>San Antonio</h1>
        <p class="serif fs-5 mt-2 mb-1">Bagua, Amazonas, Perú</p>
        <p class="mb-4">Hospitalidad, comodidad y la calidez de nuestra gente en un destino lleno de naturaleza y cultura.</p>
        <div class="d-grid d-sm-flex gap-2 mb-4">
            <a class="btn-sa text-decoration-none text-center" href="<?= url("views/publico/reservar.php") ?>">Reservar ahora <i class="bi bi-arrow-right"></i></a>
            <a class="btn-hero-linea text-decoration-none text-center" href="<?= url("views/publico/habitaciones.php") ?>">Ver habitaciones</a>
        </div>
        <div class="row g-3">
            <div class="col-12 col-sm-4 sa-rasgo"><i class="bi bi-tree"></i> Naturaleza cerca de ti</div>
            <div class="col-12 col-sm-4 sa-rasgo"><i class="bi bi-people"></i> Atención personalizada</div>
            <div class="col-12 col-sm-4 sa-rasgo"><i class="bi bi-star"></i> Comodidad garantizada</div>
        </div>
    </div>
</section>

<div class="container sa-buscador">
    <form class="sa-card sa-card-cuerpo" action="<?= url("views/publico/habitaciones.php") ?>" method="get">
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
    <!-- Aviso del programa de puntos: entre el buscador y las habitaciones, a la vista al abrir la web -->
    <section class="sa-caja-crema d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 p-4 mb-5">
        <div>
            <h2 class="h4 mb-1"><i class="bi bi-gift text-danger"></i> Programa de puntos</h2>
            <p class="mb-0">Acumula puntos en cada estadía y accede a pago fraccionado, cortesías y descuentos.</p>
        </div>
        <a class="btn-linea text-decoration-none text-center" href="<?= url("views/publico/puntos.php") ?>">Conocer beneficios</a>
    </section>

    <div class="d-flex justify-content-between align-items-end mb-3">
        <div>
            <div class="sa-subtitulo">Descansa en un lugar especial</div>
            <h2 class="h3 mb-0">Habitaciones destacadas</h2>
        </div>
        <a class="small fw-bold" href="<?= url("views/publico/habitaciones.php") ?>">Ver todas <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="row g-3">
        <?php foreach ($destacadas as $t) {
            tarjetaTipo($t);
        } ?>
    </div>

    <!-- Servicios del hotel -->
    <div class="mt-5">
        <div class="sa-subtitulo">Pensado para tu comodidad</div>
        <h2 class="h3">Servicios del hotel</h2>
        <div class="sa-linea-dorada"></div>
        <div class="row g-3">
            <?php foreach ($servicios as [$icono, $nombre, $texto]) : ?>
                <div class="col-6 col-lg-3">
                    <article class="sa-card sa-card-cuerpo h-100 text-center">
                        <i class="bi <?= $icono ?> fs-2 text-danger"></i>
                        <h3 class="h6 mt-2"><?= e($nombre) ?></h3>
                        <p class="small text-muted mb-0"><?= e($texto) ?></p>
                    </article>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</main>
<?php require "views/partes/pie.php" ?>
