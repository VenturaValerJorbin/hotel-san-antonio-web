<?php
require __DIR__ . "/../../config/Autoload.php";

use App\Bo\Turismo as TurismoBO;

$titulo = "Recomendaciones";
$lugares = (new TurismoBO())->lugares();

// Color de la etiqueta segun la categoria del lugar
$colores = ["Naturaleza" => "success", "Arqueología" => "secondary", "Cascada" => "primary", "Cultura" => "warning"];

require __DIR__ . "/../partes/cabecera.php";
/** @var array $hotel Datos del hotel (los crea views/partes/ayudas.php) */
hero(
    "Recomendaciones turísticas",
    "Descubre lugares imperdibles cerca de Bagua para complementar tu estadía.",
    [["Inicio", "index.php"], ["Recomendaciones", null]]
);
?>
<main class="container py-4 py-lg-5">
    <p class="text-muted">En Hotel Turístico San Antonio compartimos contigo algunas recomendaciones de atractivos turísticos cercanos, para que vivas una experiencia inolvidable en esta hermosa región.</p>

    <div class="row g-3">
        <?php foreach ($lugares as $l) : ?>
            <div class="col-12 col-md-6 col-lg-4">
                <article class="sa-card h-100 d-flex flex-column">
                    <div class="p-2 pb-0"><?php foto($l["foto"], $l["nombre"], "bi-tree") ?></div>
                    <div class="sa-card-cuerpo d-flex flex-column flex-grow-1">
                        <span class="badge text-bg-<?= $colores[$l["categoria"]] ?? "light" ?> align-self-start mb-2"><?= e($l["categoria"]) ?></span>
                        <h3 class="h5"><?= e($l["nombre"]) ?></h3>
                        <p class="small text-muted"><?= e($l["descripcion"]) ?></p>
                        <a class="btn-linea mt-auto text-center text-decoration-none" target="_blank" rel="noopener"
                            href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($l["nombre"] . ", Amazonas, Perú") ?>">
                            <i class="bi bi-geo-alt-fill text-danger"></i> Ver ubicación
                        </a>
                    </div>
                </article>
            </div>
        <?php endforeach ?>
    </div>

    <div class="sa-caja-crema d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-4">
        <span><strong>Consulta en recepción o por WhatsApp</strong> si deseas orientación para llegar a estos destinos.</span>
        <a class="btn-sa text-decoration-none text-center" href="https://wa.me/<?= e($hotel["whatsapp"]) ?>"><i class="bi bi-whatsapp"></i> <?= e($hotel["whatsapp_texto"]) ?></a>
    </div>
</main>
<?php require __DIR__ . "/../partes/pie.php" ?>
