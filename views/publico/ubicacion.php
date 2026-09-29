<?php
require __DIR__ . "/../../config/Autoload.php";

$titulo = "Ubicación";

require __DIR__ . "/../partes/cabecera.php";
/** @var array $hotel Datos del hotel (los crea views/partes/ayudas.php) */

$busqueda = urlencode("Hotel Turístico San Antonio, Jr. Amazonas 456, Bagua, Amazonas, Perú");
hero(
    "Ubicación y cómo llegar",
    "Encuéntranos fácilmente en Bagua, Amazonas, Perú.",
    [["Inicio", "index.php"], ["Ubicación", null]]
);
?>
<main class="container py-4 py-lg-5">
    <div class="row g-4">
        <div class="col-12 col-lg-7">
            <div class="sa-card p-2">
                <div class="ratio ratio-4x3">
                    <iframe title="Mapa del Hotel Turístico San Antonio" loading="lazy" style="border: 0; border-radius: .5rem"
                        src="https://www.google.com/maps?q=<?= $busqueda ?>&output=embed"></iframe>
                </div>
                <a class="btn-linea d-block text-center text-decoration-none mt-2" target="_blank" rel="noopener"
                    href="https://www.google.com/maps/search/?api=1&query=<?= $busqueda ?>">
                    <i class="bi bi-geo-alt-fill text-danger"></i> Ver en Google Maps
                </a>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <aside class="sa-card sa-card-cuerpo">
                <h2 class="h4">Datos de ubicación</h2>
                <div class="sa-linea-dorada"></div>
                <ul class="list-unstyled mb-4">
                    <li class="mb-2"><i class="bi bi-geo-alt-fill text-danger"></i> <?= e($hotel["direccion"]) ?></li>
                    <li class="mb-2"><i class="bi bi-telephone-fill text-danger"></i> <?= e($hotel["telefono"]) ?></li>
                    <li class="mb-2"><i class="bi bi-whatsapp text-danger"></i> <?= e($hotel["whatsapp_texto"]) ?></li>
                    <li><i class="bi bi-envelope-fill text-danger"></i> <?= e($hotel["correo"]) ?></li>
                </ul>

                <h2 class="h4">Cómo llegar</h2>
                <div class="sa-linea-dorada"></div>
                <ol class="mb-4">
                    <li>Busca el hotel en Google Maps.</li>
                    <li>Dirígete a Jr. Amazonas N.° 456, Bagua.</li>
                    <li>Si necesitas ayuda, comunícate por teléfono o WhatsApp.</li>
                </ol>

                <div class="d-grid gap-2">
                    <a class="btn-sa text-center text-decoration-none" target="_blank" rel="noopener"
                        href="https://www.google.com/maps/dir/?api=1&destination=<?= $busqueda ?>"><i class="bi bi-cursor"></i> Abrir ruta</a>
                    <button class="btn-linea" type="button" data-copiar="<?= e($hotel["direccion"]) ?>"><i class="bi bi-clipboard"></i> Copiar dirección</button>
                    <a class="btn-linea text-center text-decoration-none" target="_blank" rel="noopener"
                        href="https://wa.me/<?= e($hotel["whatsapp"]) ?>"><i class="bi bi-whatsapp"></i> Contactar por WhatsApp</a>
                </div>
            </aside>
        </div>
    </div>

    <h2 class="h4 mt-5">Información útil</h2>
    <div class="sa-linea-dorada"></div>
    <div class="row g-3">
        <div class="col-12 col-md-4"><div class="sa-card sa-card-cuerpo h-100"><h3 class="h6"><i class="bi bi-headset text-danger"></i> Atención al huésped</h3><p class="small mb-0 text-muted">Recepción disponible las 24 horas para brindarte una atención cálida y personalizada durante tu estancia.</p></div></div>
        <div class="col-12 col-md-4"><div class="sa-card sa-card-cuerpo h-100"><h3 class="h6"><i class="bi bi-calendar-check text-danger"></i> Check-in y reservas</h3><p class="small mb-0 text-muted">Realiza tu reserva con anticipación y disfruta de una estadía tranquila y segura.</p></div></div>
        <div class="col-12 col-md-4"><div class="sa-card sa-card-cuerpo h-100"><h3 class="h6"><i class="bi bi-pin-map text-danger"></i> Zona céntrica de Bagua</h3><p class="small mb-0 text-muted">Nos encontramos en una ubicación estratégica, cerca de los principales servicios de la ciudad.</p></div></div>
    </div>
</main>
<?php require __DIR__ . "/../partes/pie.php" ?>
