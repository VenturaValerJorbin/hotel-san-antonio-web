<?php /** @var array $hotel Datos del hotel (los crea views/partes/ayudas.php) */ ?>
    <section class="sa-banner">
        <div class="container">
            <h2 class="h3 mb-1">Más que un hotel,</h2>
            <p class="serif fst-italic h4 mb-2" style="color: var(--dorado)">una experiencia en Bagua</p>
            <p class="mb-0 small">Descubre la naturaleza, la cultura y la calidez de nuestra tierra. Te esperamos en Hotel Turístico San Antonio.</p>
        </div>
    </section>

    <footer class="sa-pie">
        <div class="container">
            <div class="row g-4">
                <div class="col-12 col-lg-4">
                    <?php $claro = "claro"; require __DIR__ . "/logo.php" ?>
                    <p class="mt-3 mb-3">Hotel de tres estrellas con restaurante en Bagua, Amazonas. Tu hogar en la selva amazónica.</p>
                    <div class="d-flex flex-wrap gap-2" aria-label="Redes sociales">
                        <a class="sa-red" href="https://wa.me/<?= e($hotel["whatsapp"]) ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                        <?php foreach (["facebook" => "Facebook", "instagram" => "Instagram", "tiktok" => "TikTok"] as $red => $nombreRed) : ?>
                            <?php if (!empty($hotel["redes"][$red])) : ?>
                                <a class="sa-red" href="<?= e($hotel["redes"][$red]) ?>" target="_blank" rel="noopener" aria-label="<?= $nombreRed ?>"><i class="bi bi-<?= $red ?>"></i></a>
                            <?php endif ?>
                        <?php endforeach ?>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <h6>Información</h6>
                    <ul>
                        <li><a href="<?= url("publico/terminos.php") ?>">Términos y condiciones</a></li>
                        <li><a href="<?= url("publico/privacidad.php") ?>">Política de privacidad</a></li>
                    </ul>
                    <a class="sa-libro" href="<?= url("publico/libro_reclamaciones.php") ?>">
                        <img src="<?= url("assets/img/libro-reclamaciones.svg") ?>" alt="Libro de Reclamaciones" width="180" height="51" loading="lazy">
                    </a>
                </div>

                <div class="col-12 col-sm-6 col-lg-5">
                    <h6>Contáctanos</h6>
                    <ul>
                        <li><i class="bi bi-geo-alt"></i> <?= e($hotel["direccion"]) ?></li>
                        <li><i class="bi bi-telephone"></i> <?= e($hotel["telefono"]) ?></li>
                        <li><i class="bi bi-whatsapp"></i> <a href="https://wa.me/<?= e($hotel["whatsapp"]) ?>"><?= e($hotel["whatsapp_texto"]) ?></a></li>
                        <li><i class="bi bi-envelope"></i> <?= e($hotel["correo"]) ?></li>
                        <li><i class="bi bi-clock"></i> <?= e($hotel["recepcion"]) ?></li>
                    </ul>
                </div>
            </div>

            <div class="sa-pie-pagos d-flex flex-wrap align-items-center gap-2">
                <span class="small me-1">Medios de pago:</span>
                <span class="sa-pago-chip"><i class="bi bi-phone"></i> Yape</span>
                <span class="sa-pago-chip"><i class="bi bi-credit-card-2-front"></i> Tarjeta Visa</span>
                <span class="sa-pago-chip"><i class="bi bi-bank"></i> Transferencia bancaria</span>
            </div>

            <div class="copy d-flex flex-column flex-md-row justify-content-between gap-1">
                <span>© <?= date("Y") ?> Hotel Turístico San Antonio. Todos los derechos reservados.</span>
                <span>Bagua, Amazonas, Perú · Tu hogar en la selva amazónica</span>
            </div>
        </div>
    </footer>

    <script src="<?= url("assets/vendor/bootstrap/bootstrap.bundle.min.js") ?>"></script>
    <script src="<?= recurso("assets/js/app.js") ?>"></script>
</body>

</html>
