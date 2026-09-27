<?php
require "config/Autoload.php";
require_once "views/partes/ayudas.php";

use bo\Habitacion as HabitacionBO;

$titulo = "Habitaciones";

// Filtros opcionales que llegan del buscador de la portada (solo lectura, por GET)
$ingreso = fechaGet("ingreso");
$salida = fechaGet("salida");
$rangoValido = $ingreso && $salida && $salida > $ingreso;
$filtroTipo = (int) ($_GET["tipo"] ?? 0);

$tipos = (new HabitacionBO())->tipos($rangoValido ? $ingreso : null, $rangoValido ? $salida : null);
if ($filtroTipo > 0) {
    $tipos = array_filter($tipos, fn($t) => $t->id === $filtroTipo);
}
$consulta = $rangoValido ? http_build_query(["ingreso" => $ingreso, "salida" => $salida]) : "";

require "views/partes/cabecera.php";
hero("Nuestras habitaciones", "Comodidad, tranquilidad y una experiencia única en el corazón de la Amazonía.", [], "Descansa en Bagua");
?>
<main class="container py-5">
    <div class="sa-subtitulo"><?= count($tipos) === 1 ? "Una opción" : count($tipos) . " opciones" ?> para tu estadía</div>
    <h2 class="h3 mb-1">Habitaciones del Hotel San Antonio</h2>
    <p class="text-muted">Elige la habitación ideal para tu viaje y vive la calidez de nuestra hospitalidad en Bagua.</p>

    <?php if ($rangoValido) : ?>
        <div class="alert alert-light border small">
            Disponibilidad del <strong><?= fechaLarga($ingreso) ?></strong> al <strong><?= fechaLarga($salida) ?></strong>.
            <a href="habitaciones.php">Quitar fechas</a>
        </div>
    <?php elseif (isset($_GET["ingreso"]) || isset($_GET["salida"])) : ?>
        <div class="alert alert-warning small">Revisa las fechas: la salida debe ser posterior al ingreso.</div>
    <?php endif ?>

    <div class="row g-3">
        <?php foreach ($tipos as $t) {
            tarjetaTipo($t, $consulta);
        } ?>
    </div>
</main>
<?php require "views/partes/pie.php" ?>
