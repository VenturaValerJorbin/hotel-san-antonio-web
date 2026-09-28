<?php
require __DIR__ . "/../config/Autoload.php";

$titulo = "Contacto";

require __DIR__ . "/../views/partes/cabecera.php";

$valor = fn($campo) => $antiguo[$campo] ?? "";
$claseError = fn($campo) => isset($errores[$campo]) ? " is-invalid" : "";
$mensaje = fn($campo) => isset($errores[$campo]) ? '<div class="invalid-feedback d-block">' . e($errores[$campo]) . "</div>" : "";

// El asunto puede llegar preseleccionado por la URL (ej. el enlace del Libro de Reclamaciones usa ?asunto=reclamo)
$asuntos = ["reserva" => "Reserva", "consulta" => "Consulta", "sugerencia" => "Sugerencia", "reclamo" => "Reclamo"];
$asuntoElegido = $valor("asunto") ?: (array_key_exists($_GET["asunto"] ?? "", $asuntos) ? $_GET["asunto"] : "");

hero(
    "Contacto",
    "Estamos listos para ayudarte y darte la bienvenida en Bagua, Amazonas, Perú.",
    [["Inicio", "index.php"], ["Contacto", null]]
);
?>
<main class="container py-4 py-lg-5">
    <div class="row g-4">
        <div class="col-12 col-lg-6">
            <section class="sa-card sa-card-cuerpo">
                <h2 class="h4">Formulario de contacto</h2>
                <div class="sa-linea-dorada"></div>
                <p class="text-muted small">Escríbenos y te responderemos a la brevedad. Estaremos encantados de ayudarte con tu reserva o cualquier consulta.</p>
                <form action="<?= url("procesar.php") ?>" method="post" novalidate>
                    <input type="hidden" name="modulo" value="contacto">
                    <input type="hidden" name="accion" value="enviar">
                    <div class="mb-3">
                        <label class="form-label requerido" for="nombre">Nombre completo</label>
                        <input class="form-control<?= $claseError("nombre") ?>" id="nombre" name="nombre" maxlength="100" placeholder="Tu nombre completo" value="<?= e($valor("nombre")) ?>" required>
                        <?= $mensaje("nombre") ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label requerido" for="correo">Correo electrónico</label>
                        <input class="form-control<?= $claseError("correo") ?>" type="email" id="correo" name="correo" maxlength="100" placeholder="tu@email.com" value="<?= e($valor("correo")) ?>" required>
                        <?= $mensaje("correo") ?>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label requerido" for="telefono">Celular / WhatsApp</label>
                            <input class="form-control<?= $claseError("telefono") ?>" type="tel" id="telefono" name="telefono" maxlength="16" placeholder="Ej. 914 137 531" value="<?= e($valor("telefono")) ?>" required>
                            <?= $mensaje("telefono") ?>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label requerido" for="asunto">Asunto</label>
                            <select class="form-select<?= $claseError("asunto") ?>" id="asunto" name="asunto" required>
                                <option value="">Selecciona un asunto</option>
                                <?php foreach ($asuntos as $clave => $texto) : ?>
                                    <option value="<?= $clave ?>" <?= $asuntoElegido === $clave ? "selected" : "" ?>><?= $texto ?></option>
                                <?php endforeach ?>
                            </select>
                            <?= $mensaje("asunto") ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label requerido" for="mensaje">Mensaje</label>
                        <textarea class="form-control<?= $claseError("mensaje") ?>" id="mensaje" name="mensaje" rows="4" maxlength="500" placeholder="Cuéntanos en qué podemos ayudarte..." required><?= e($valor("mensaje")) ?></textarea>
                        <?= $mensaje("mensaje") ?>
                    </div>
                    <button class="btn-sa w-100" type="submit"><i class="bi bi-send"></i> Enviar mensaje</button>
                    <p class="form-text mt-2 mb-0">Al enviar este formulario aceptas nuestra <a href="<?= url("publico/privacidad.php") ?>" target="_blank" rel="noopener">política de privacidad</a>.</p>
                </form>
            </section>
        </div>

        <div class="col-12 col-lg-6">
            <section class="sa-card sa-card-cuerpo mb-4">
                <h2 class="h4">Datos de contacto</h2>
                <div class="sa-linea-dorada"></div>
                <ul class="list-unstyled mb-0">
                    <li class="mb-3"><i class="bi bi-geo-alt-fill text-danger"></i> <strong>Dirección</strong><br><?= e($hotel["direccion"]) ?></li>
                    <li class="mb-3"><i class="bi bi-telephone-fill text-danger"></i> <strong>Teléfono</strong><br><a href="tel:<?= e(preg_replace('/\D/', '', $hotel["telefono"])) ?>"><?= e($hotel["telefono"]) ?></a></li>
                    <li class="mb-3"><i class="bi bi-whatsapp text-danger"></i> <strong>WhatsApp</strong><br><a href="https://wa.me/<?= e($hotel["whatsapp"]) ?>"><?= e($hotel["whatsapp_texto"]) ?></a></li>
                    <li class="mb-3"><i class="bi bi-envelope-fill text-danger"></i> <strong>Correo electrónico</strong><br><?= e($hotel["correo"]) ?></li>
                    <li class="mb-3"><i class="bi bi-wallet2 text-danger"></i> <strong>Yape</strong><br><?= e($hotel["yape"]) ?></li>
                    <li><i class="bi bi-clock text-danger"></i> <strong>Recepción</strong><br>Atención las 24 horas</li>
                </ul>
            </section>
            <section class="sa-card sa-card-cuerpo d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h2 class="h5 mb-1">¿Prefieres atención directa?</h2>
                    <p class="small text-muted mb-0">Escríbenos por WhatsApp y te ayudamos de inmediato.</p>
                </div>
                <a class="btn-sa text-decoration-none text-center" href="https://wa.me/<?= e($hotel["whatsapp"]) ?>" target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i> <?= e($hotel["whatsapp_texto"]) ?></a>
            </section>
        </div>
    </div>
</main>
<?php require __DIR__ . "/../views/partes/pie.php" ?>
