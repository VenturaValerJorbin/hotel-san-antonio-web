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
                <div class="col-12 col-md-4">
                    <?php $claro = "claro"; require __DIR__ . "/logo.php" ?>
                </div>
                <div class="col-12 col-md-4">
                    <h6>Contáctanos</h6>
                    <ul>
                        <li><i class="bi bi-geo-alt"></i> <?= e($hotel["direccion"]) ?></li>
                        <li><i class="bi bi-telephone"></i> <?= e($hotel["telefono"]) ?></li>
                        <li><i class="bi bi-whatsapp"></i> <a href="https://wa.me/<?= e($hotel["whatsapp"]) ?>"><?= e($hotel["whatsapp_texto"]) ?></a></li>
                        <li><i class="bi bi-wallet2"></i> Yape <?= e($hotel["yape"]) ?></li>
                        <li><i class="bi bi-envelope"></i> <?= e($hotel["correo"]) ?></li>
                    </ul>
                </div>
                <div class="col-12 col-md-4">
                    <h6>Enlaces rápidos</h6>
                    <ul>
                        <li><a href="index.php">Inicio</a></li>
                        <li><a href="habitaciones.php">Habitaciones</a></li>
                        <li><a href="restaurante.php">Restaurante</a></li>
                        <li><a href="ubicacion.php">Ubicación</a></li>
                        <li><a href="recomendaciones.php">Recomendaciones</a></li>
                        <li><a href="contacto.php">Contacto</a></li>
                    </ul>
                </div>
            </div>
            <div class="copy d-flex flex-column flex-md-row justify-content-between gap-1">
                <span>© <?= date("Y") ?> Hotel Turístico San Antonio. Todos los derechos reservados.</span>
                <span>Bagua, Amazonas, Perú · Tu hogar en la selva amazónica</span>
            </div>
        </div>
    </footer>

    <script src="assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>

</html>
