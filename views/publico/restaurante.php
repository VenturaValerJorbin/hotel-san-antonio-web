
<?php
require __DIR__ . "/../../config/Autoload.php";

use App\Bo\Restaurante as RestauranteBO;

$titulo = "Restaurante";
$carta = (new RestauranteBO())->carta();

require __DIR__ . "/../partes/cabecera.php";

//imagen restaurante cabesera
hero(
    "Carta del restaurante",
    "Sabores amazónicos del corazón de Bagua. Disfruta de una experiencia única con lo mejor de nuestra tierra.",
    [["Inicio", "index.php"], ["Restaurante", null]],
    "",                          // <- NUEVO (sin etiqueta)
    "sa-hero-foto-restaurante"   // <- NUEVO (clase de la foto)
);
?>
<main class="container py-4 py-lg-5">
    <div class="d-flex flex-wrap gap-2 mb-4">
        <span class="sa-caja-crema"><i class="bi bi-clock"></i> <strong>Horario del menú:</strong> 12:00 – 15:00</span>
        <span class="sa-caja-crema"><i class="bi bi-cup-hot"></i> <strong>Menú:</strong> S/ 12 – S/ 16</span>
    </div>

    <?php if (!empty($carta["Menú del día"])) : ?>
        <h2 class="h3"><i class="bi bi-egg-fried text-danger"></i> Menú del día</h2>
        <p class="text-muted">Opciones caseras y nutritivas, con ingredientes de la región.</p>
        <div class="row g-3 mb-5">
            <?php foreach ($carta["Menú del día"] as $p) : ?>
                <div class="col-12 col-md-6">
                    <article class="sa-card sa-card-cuerpo h-100">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <h3 class="h5"><?= e($p["nombre"]) ?></h3>
                            <span class="sa-precio-menu"><?= soles($p["precio"]) ?></span>
                        </div>
                        <p class="mb-0 text-muted"><?= e($p["descripcion"]) ?></p>
                    </article>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <?php if (!empty($carta["Platos a la carta"])) : ?>
        <h2 class="h3"><i class="bi bi-cup-straw text-danger"></i> Platos a la carta</h2>
        <p class="text-muted">Especialidades amazónicas que destacan lo mejor de nuestra región.</p>
        <div class="row g-3 mb-4">
            <?php foreach ($carta["Platos a la carta"] as $p) : ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="sa-card p-2 h-100 d-flex gap-3 align-items-center">
                        <div style="width: 6rem; flex: none"><?php foto($p["foto"], $p["nombre"], "bi-egg-fried") ?></div>
                        <div>
                            <h3 class="h6 mb-1"><?= e($p["nombre"]) ?></h3>
                            <p class="small text-muted mb-1"><?= e($p["descripcion"]) ?></p>
                            <span class="small fw-bold text-danger"><?= $p["precio"] !== null ? soles($p["precio"]) : "A la carta" ?></span>
                        </div>
                    </article>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <div class="sa-caja-crema">
        <i class="bi bi-leaf"></i> Nuestro restaurante es un servicio complementario del hotel, abierto a huéspedes y público en general.
        Te invitamos a disfrutar de la gastronomía amazónica en un ambiente acogedor y familiar.
    </div>
</main>
<?php require __DIR__ . "/../partes/pie.php" ?>
