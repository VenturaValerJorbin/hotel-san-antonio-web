<?php
require __DIR__ . "/../config/Autoload.php";

use App\Bo\Habitacion as HabitacionBO;
use App\Bo\Reserva as ReservaBO;

$titulo = "Reservar";
$tipos = (new HabitacionBO())->tipos();
$beneficio = (new ReservaBO())->beneficioFraccionado();

require __DIR__ . "/../views/partes/cabecera.php";

// Valor de un campo: lo escrito antes de un error, o el que llega por la URL, o un valor por defecto
$valor = fn($campo, $porDefecto = "") => $antiguo[$campo] ?? $porDefecto;
$claseError = fn($campo) => isset($errores[$campo]) ? " is-invalid" : "";
$mensaje = fn($campo) => isset($errores[$campo]) ? '<div class="invalid-feedback d-block">' . e($errores[$campo]) . "</div>" : "";

$tipoElegido = (string) ($antiguo["tipo_id"] ?? $_GET["tipo"] ?? "");
$ingreso = $antiguo["fecha_ingreso"] ?? fechaGet("ingreso");
$salida = $antiguo["fecha_salida"] ?? fechaGet("salida");
$modalidad = $valor("modalidad_pago", "completo");
$porcentaje = $beneficio ? (float) $beneficio["valor"] : 50;

hero(
    "Reserva tu habitación",
    "Confirma tu estadía en Bagua y vive una experiencia inolvidable.",
    [["Inicio", "index.php"], ["Habitaciones", "publico/habitaciones.php"], ["Reserva", null]]
);
?>
<main class="container py-4 py-lg-5">
    <form id="formReserva" action="<?= url("procesar.php") ?>" method="post" novalidate data-porcentaje="<?= $porcentaje ?>">
        <input type="hidden" name="modulo" value="reserva">
        <input type="hidden" name="accion" value="crear">

        <div class="row g-4">
            <div class="col-12 col-lg-7">
                <section class="sa-card sa-card-cuerpo">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h2 class="h4 mb-0"><i class="bi bi-house-heart text-danger"></i> Datos de la reserva</h2>
                        <span class="sa-caja-crema small py-1"><i class="bi bi-person"></i> Reserva sin crear cuenta</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label requerido" for="tipo_id">Habitación seleccionada</label>
                            <select class="form-select<?= $claseError("tipo_id") ?>" id="tipo_id" name="tipo_id" required>
                                <option value="" data-precio="0">Selecciona una habitación...</option>
                                <?php foreach ($tipos as $t) : ?>
                                    <option value="<?= $t->id ?>" data-precio="<?= $t->precioNoche ?>" data-nombre="<?= e($t->nombre) ?>"
                                        <?= $tipoElegido === (string) $t->id ? "selected" : "" ?>>
                                        <?= e($t->nombre) ?> · <?= $t->capacidad ?> <?= $t->capacidad === 1 ? "persona" : "personas" ?> · <?= soles($t->precioNoche) ?> por noche
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <?= $mensaje("tipo_id") ?>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label requerido" for="fecha_ingreso">Fecha de ingreso</label>
                            <input class="form-control<?= $claseError("fecha_ingreso") ?>" type="date" id="fecha_ingreso" name="fecha_ingreso"
                                min="<?= date("Y-m-d") ?>" value="<?= e($ingreso) ?>" required>
                            <?= $mensaje("fecha_ingreso") ?>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label requerido" for="fecha_salida">Fecha de salida</label>
                            <input class="form-control<?= $claseError("fecha_salida") ?>" type="date" id="fecha_salida" name="fecha_salida"
                                min="<?= date("Y-m-d", strtotime("+1 day")) ?>" value="<?= e($salida) ?>" required>
                            <?= $mensaje("fecha_salida") ?>
                        </div>
                        <div class="col-12">
                            <label class="form-label requerido" for="nombre_completo">Nombre completo</label>
                            <input class="form-control<?= $claseError("nombre_completo") ?>" id="nombre_completo" name="nombre_completo" maxlength="160"
                                placeholder="Ej. Juan Pérez García" value="<?= e($valor("nombre_completo")) ?>" autocomplete="name" required>
                            <?= $mensaje("nombre_completo") ?>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label requerido" for="numero_documento">DNI / Pasaporte</label>
                            <input class="form-control<?= $claseError("numero_documento") ?>" id="numero_documento" name="numero_documento" maxlength="12"
                                placeholder="Ej. 12345678" value="<?= e($valor("numero_documento")) ?>" inputmode="text" required>
                            <?= $mensaje("numero_documento") ?>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label requerido" for="telefono">Celular / WhatsApp</label>
                            <input class="form-control<?= $claseError("telefono") ?>" type="tel" id="telefono" name="telefono" maxlength="16"
                                placeholder="Ej. 914 137 531" value="<?= e($valor("telefono")) ?>" autocomplete="tel" required>
                            <?= $mensaje("telefono") ?>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h2 class="h4"><i class="bi bi-credit-card text-danger"></i> Pago</h2>
                    <p class="text-muted small">Selecciona cuánto pagar ahora y cómo deseas hacerlo.</p>

                    <div class="mb-3">
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="modalidad_pago" id="m_completo" value="completo" <?= $modalidad === "completo" ? "checked" : "" ?>>
                            <label class="form-check-label" for="m_completo">Pagar el 100 % ahora</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="modalidad_pago" id="m_fraccionado" value="fraccionado" <?= $modalidad === "fraccionado" ? "checked" : "" ?>>
                            <label class="form-check-label" for="m_fraccionado">
                                Pagar el <?= $porcentaje ?> % ahora y el resto al llegar
                                <?php if ($beneficio) : ?>
                                    <span class="badge text-bg-warning">Huéspedes frecuentes · <?= $beneficio["puntos_requeridos"] ?> puntos</span>
                                <?php endif ?>
                            </label>
                        </div>
                        <?= $mensaje("modalidad_pago") ?>
                        <div class="form-text">Acumulas puntos en cada estadía. Se valida con tu DNI o pasaporte al confirmar. <a href="<?= url("publico/puntos.php") ?>">Conoce el programa de puntos</a>.</div>
                    </div>

                    <div class="row g-2 mb-1">
                        <?php foreach (["yape" => ["Yape", "bi-phone"], "tarjeta" => ["Tarjeta Visa", "bi-credit-card-2-front"], "transferencia" => ["Transferencia", "bi-bank"]] as $clave => [$texto, $icono]) : ?>
                            <div class="col-12 col-md-4">
                                <label class="sa-metodo">
                                    <input type="radio" name="metodo_pago" value="<?= $clave ?>" <?= $valor("metodo_pago") === $clave ? "checked" : "" ?>>
                                    <span><i class="bi <?= $icono ?> fs-4"></i> <strong><?= $texto ?></strong></span>
                                </label>
                            </div>
                        <?php endforeach ?>
                    </div>
                    <?= $mensaje("metodo_pago") ?>
                    <div class="alert alert-info small mt-3 mb-0">
                        <i class="bi bi-info-circle"></i> Modo de prueba: el cobro se simula. La pasarela de pago real se conectará en la siguiente etapa del proyecto.
                    </div>

                    <div class="form-check mt-4">
                        <input class="form-check-input<?= $claseError("acepta") ?>" type="checkbox" id="acepta" name="acepta" required>
                        <label class="form-check-label" for="acepta">
                            Acepto los <a href="<?= url("publico/terminos.php") ?>" target="_blank" rel="noopener">términos y condiciones</a> y la
                            <a href="<?= url("publico/privacidad.php") ?>" target="_blank" rel="noopener">política de privacidad</a>: el pago no es reembolsable, salvo que la habitación no corresponda a lo publicado en esta web.
                        </label>
                        <?= $mensaje("acepta") ?>
                    </div>
                </section>
            </div>

            <div class="col-12 col-lg-5">
                <aside class="sa-card sa-card-cuerpo resumen-fijo">
                    <h2 class="h4"><i class="bi bi-receipt text-danger"></i> Resumen de tu reserva</h2>
                    <p class="text-muted small">Revisa los detalles de tu estadía.</p>
                    <dl class="row mb-0">
                        <dt class="col-6 fw-normal text-muted">Habitación</dt><dd class="col-6 text-end" id="r_habitacion">—</dd>
                        <dt class="col-6 fw-normal text-muted">Ingreso</dt><dd class="col-6 text-end" id="r_ingreso">—</dd>
                        <dt class="col-6 fw-normal text-muted">Salida</dt><dd class="col-6 text-end" id="r_salida">—</dd>
                        <dt class="col-6 fw-normal text-muted">Estadía</dt><dd class="col-6 text-end" id="r_noches">—</dd>
                        <dt class="col-6 fw-normal text-muted">Tarifa por noche</dt><dd class="col-6 text-end" id="r_tarifa">S/ 0.00</dd>
                    </dl>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold mb-2"><span>Total de la reserva</span><span id="r_total">S/ 0.00</span></div>
                    <div class="sa-caja-pago mb-2">
                        <div class="d-flex justify-content-between"><span id="r_ahora_txt">Pagas ahora (100 %)</span><strong id="r_ahora">S/ 0.00</strong></div>
                    </div>
                    <div class="sa-caja-crema d-flex justify-content-between mb-3">
                        <span>Saldo pendiente (al llegar)</span><strong id="r_saldo">S/ 0.00</strong>
                    </div>
                    <button class="btn-sa w-100" type="submit">Confirmar reserva <i class="bi bi-arrow-right"></i></button>
                    <p class="text-center small text-muted mt-2 mb-0"><i class="bi bi-lock"></i> Tus datos están protegidos y son confidenciales.</p>
                    <div class="sa-caja-crema small mt-3"><i class="bi bi-chat-dots"></i> ¿Tienes alguna consulta? Escríbenos por WhatsApp al <?= e($hotel["whatsapp_texto"]) ?>.</div>
                </aside>
            </div>
        </div>
    </form>
</main>
<?php require __DIR__ . "/../views/partes/pie.php" ?>
