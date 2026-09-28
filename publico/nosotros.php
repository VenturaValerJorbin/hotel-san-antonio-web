<?php
require __DIR__ . "/../config/Autoload.php";

$titulo = "Nosotros";

require __DIR__ . "/../views/partes/cabecera.php";

// Los valores salen de los mock-ups del Avance 01 (naturaleza, atencion personalizada, comodidad, hospitalidad)
$valores = [
    ["bi-heart", "Hospitalidad", "Recibimos a cada huésped con la calidez de nuestra gente."],
    ["bi-headset", "Atención personalizada", "Estamos atentos a lo que necesitas, a cualquier hora."],
    ["bi-star", "Comodidad", "Habitaciones cuidadas y servicios pensados para tu descanso."],
    ["bi-tree", "Respeto por nuestra tierra", "Valoramos la naturaleza y la cultura de Bagua y la Amazonía."],
];

hero(
    "Nosotros",
    "Conoce al Hotel Turístico San Antonio: nuestra historia, lo que nos mueve y lo que ofrecemos.",
    [["Inicio", "index.php"], ["Nosotros", null]]
);
?>
<main class="container py-4 py-lg-5">
    <?php // Historia y foto. La foto se configura en config/hotel.php (foto_nosotros); mientras no haya, se ve un marcador ?>
    <div class="row g-4 align-items-center mb-5">
        <div class="col-12 col-lg-6 order-lg-2">
            <?php foto($hotel["foto_nosotros"], "Hotel Turístico San Antonio", "bi-building") ?>
        </div>
        <div class="col-12 col-lg-6 order-lg-1">
            <div class="sa-subtitulo">Quiénes somos</div>
            <h2 class="h3">Nuestra historia</h2>
            <div class="sa-linea-dorada"></div>
            <p>Hotel Turístico San Antonio es un hotel de tres estrellas ubicado en el Jr. Amazonas N.° 456, en la ciudad de Bagua, región Amazonas. Bagua se encuentra en la selva alta, tiene clima cálido y es punto de paso y descanso para quienes viajan hacia el nororiente del país; por eso recibimos a viajeros que llegan por turismo, trabajo o comercio.</p>
            <p>Nuestro servicio principal es el alojamiento, y lo complementamos con un restaurante de comida regional, abierto tanto para nuestros huéspedes como para el público en general, y un restobar en horario nocturno. Nuestra recepción atiende las 24 horas del día.</p>
            <p class="mb-0">Nuestra propuesta combina hospitalidad, comodidad y una ubicación estratégica para disfrutar Bagua y sus alrededores.</p>
        </div>
    </div>

    <!-- Mision y vision -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <section class="sa-card sa-card-cuerpo h-100">
                <h2 class="h4"><i class="bi bi-bullseye text-danger"></i> Misión</h2>
                <div class="sa-linea-dorada"></div>
                <p class="mb-0">Brindar un alojamiento cómodo y una atención cercana a quienes visitan Bagua, acompañados de la gastronomía amazónica de nuestro restaurante, para que cada huésped se sienta como en casa.</p>
            </section>
        </div>
        <div class="col-12 col-md-6">
            <section class="sa-card sa-card-cuerpo h-100">
                <h2 class="h4"><i class="bi bi-binoculars text-danger"></i> Visión</h2>
                <div class="sa-linea-dorada"></div>
                <p class="mb-0">Ser el hotel preferido de los viajeros que llegan a Bagua, reconocido por su hospitalidad, su comodidad y su compromiso con la cultura y la naturaleza de nuestra región.</p>
            </section>
        </div>
    </div>

    <!-- Valores -->
    <h2 class="h4 mt-5">Nuestros valores</h2>
    <div class="sa-linea-dorada"></div>
    <div class="row g-3 mb-5">
        <?php foreach ($valores as [$icono, $nombre, $texto]) : ?>
            <div class="col-12 col-sm-6 col-lg-3">
                <article class="sa-card sa-card-cuerpo h-100 text-center">
                    <i class="bi <?= $icono ?> fs-2 text-danger"></i>
                    <h3 class="h6 mt-2"><?= e($nombre) ?></h3>
                    <p class="small text-muted mb-0"><?= e($texto) ?></p>
                </article>
            </div>
        <?php endforeach ?>
    </div>

    <!-- Lo que ofrecemos -->
    <h2 class="h4">Lo que ofrecemos</h2>
    <div class="sa-linea-dorada"></div>
    <div class="row g-3 mb-5">
        <div class="col-12 col-md-4">
            <article class="sa-card sa-card-cuerpo h-100">
                <h3 class="h5"><i class="bi bi-house-heart text-danger"></i> Alojamiento</h3>
                <p class="small text-muted mb-0">Seis tipos de habitación, todas con baño privado, televisor y aire acondicionado. Wi-Fi y cochera sin costo para nuestros huéspedes.</p>
            </article>
        </div>
        <div class="col-12 col-md-4">
            <article class="sa-card sa-card-cuerpo h-100">
                <h3 class="h5"><i class="bi bi-cup-hot text-danger"></i> Restaurante</h3>
                <p class="small text-muted mb-0">Platos típicos de la región, como cecina con patacones, chaufa amazónica, tilapia, trucha, pato y gallina, y menú del día de 12:00 a 15:00. Atendemos almuerzos grupales y eventos bajo pedido.</p>
            </article>
        </div>
        <div class="col-12 col-md-4">
            <article class="sa-card sa-card-cuerpo h-100">
                <h3 class="h5"><i class="bi bi-headset text-danger"></i> Atención</h3>
                <p class="small text-muted mb-0">Recepción las 24 horas, servicio a la habitación y reservas en línea sin necesidad de crear una cuenta.</p>
            </article>
        </div>
    </div>

    <section class="sa-caja-crema d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 p-4">
        <div>
            <h2 class="h4 mb-1">¿Listo para conocernos?</h2>
            <p class="mb-0">Reserva tu habitación en línea o escríbenos, con gusto te atendemos.</p>
        </div>
        <div class="d-flex flex-column flex-sm-row gap-2">
            <a class="btn-sa text-decoration-none text-center" href="<?= url("publico/reservar.php") ?>">Reservar ahora</a>
            <a class="btn-linea text-decoration-none text-center" href="<?= url("publico/contacto.php") ?>">Contáctanos</a>
        </div>
    </section>
</main>
<?php require __DIR__ . "/../views/partes/pie.php" ?>
