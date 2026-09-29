<?php
require __DIR__ . "/../config/Autoload.php";

use App\Bo\Habitacion as HabitacionBO;
use App\Bo\Reserva as ReservaBO;

$titulo = "Reservar";
$reservaBo = new ReservaBO();
$tipos = (new HabitacionBO())->tipos();
$beneficio = $reservaBo->beneficioFraccionado();
$planes = $reservaBo->planesPension();

require __DIR__ . "/../views/partes/cabecera.php";
/** @var array $hotel Datos del hotel (los crea views/partes/ayudas.php) */
/** @var array $errores Errores del formulario (los crea views/partes/ayudas.php) */
/** @var array $antiguo Datos escritos antes de un error (los crea views/partes/ayudas.php) */

// Valor de un campo: lo escrito antes de un error, o el que llega por la URL, o un valor por defecto
$valor = fn($campo, $porDefecto = "") => $antiguo[$campo] ?? $porDefecto;
$claseError = fn($campo) => isset($errores[$campo]) ? " is-invalid" : "";
// Siempre imprime el contenedor del mensaje (vacio si no hay error), con data-error para que
// assets/js/app.js pueda mostrar y quitar los errores de validacion sin recargar la pagina.
$mensaje = fn($campo) => '<div class="invalid-feedback' . (isset($errores[$campo]) ? " d-block" : "") . '" data-error="' . $campo . '">' . e($errores[$campo] ?? "") . '</div>';

$tipoElegido = (string) ($antiguo["tipo_id"] ?? $_GET["tipo"] ?? "");
$ingreso = $antiguo["fecha_ingreso"] ?? fechaGet("ingreso");
$salida = $antiguo["fecha_salida"] ?? fechaGet("salida");
$modalidad = $valor("modalidad_pago", "completo");
// Piso de preferencia: solo se ofrecen los pisos donde existe la habitacion elegida (0 = sin preferencia)
$pisoElegido = (int) $valor("piso", 0);
$pisosDelTipo = [];
foreach ($tipos as $t) {
    if ($tipoElegido === (string) $t->id) {
        $pisosDelTipo = $t->pisos;
    }
}
$porcentaje = $beneficio ? (float) $beneficio["valor"] : 50;
// Plan de pension: sin eleccion previa, se ofrece el primero (Solo alojamiento)
$planElegido = (int) $valor("plan_pension_id", $planes[0]->id ?? 0);

hero(
    "Reserva tu habitación",
    "Confirma tu estadía en Bagua y vive una experiencia inolvidable.",
    [["Inicio", "index.php"], ["Habitaciones", "publico/habitaciones.php"], ["Reserva", null]]
);
?>
<main class="container py-4 py-lg-5">
    <form id="formReserva" action="<?= url("procesar.php") ?>" method="post" novalidate data-porcentaje="<?= $porcentaje ?>"
        data-consulta-puntos="<?= url("publico/consultar_puntos.php") ?>">
        <input type="hidden" name="modulo" value="reserva">
        <input type="hidden" name="accion" value="crear">

        <div class="row g-4">
            <div class="col-12 col-lg-7">
                <section class="sa-card sa-card-cuerpo">
                    <h2 class="h4 mb-3"><i class="bi bi-house-heart text-danger"></i> Datos de la reserva</h2>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label requerido" for="tipo_id">Habitación seleccionada</label>
                            <select class="form-select<?= $claseError("tipo_id") ?>" id="tipo_id" name="tipo_id" required>
                                <option value="" data-precio="0">Selecciona una habitación...</option>
                                <?php foreach ($tipos as $t) : ?>
                                    <option value="<?= $t->id ?>" data-precio="<?= $t->precioNoche ?>" data-nombre="<?= e($t->nombre) ?>"
                                        data-pisos="<?= e(implode(",", $t->pisos)) ?>" <?= $tipoElegido === (string) $t->id ? "selected" : "" ?>>
                                        <?= e($t->nombre) ?> · <?= $t->capacidad ?> <?= $t->capacidad === 1 ? "persona" : "personas" ?> · <?= soles($t->precioNoche) ?> por noche
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <?= $mensaje("tipo_id") ?>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="piso">Piso de preferencia</label>
                            <select class="form-select<?= $claseError("piso") ?>" id="piso" name="piso">
                                <option value="0">Sin preferencia</option>
                                <?php foreach ($pisosDelTipo as $p) : ?>
                                    <option value="<?= $p ?>" <?= $pisoElegido === $p ? "selected" : "" ?>>Piso <?= $p ?></option>
                                <?php endforeach ?>
                            </select>
                            <?= $mensaje("piso") ?>
                        </div>
                        <div class="col-12">
                            <label class="form-label requerido" for="plan_pension_id">Plan de alimentación</label>
                            <select class="form-select<?= $claseError("plan_pension_id") ?>" id="plan_pension_id" name="plan_pension_id" required>
                                <?php foreach ($planes as $p) : ?>
                                    <option value="<?= $p->id ?>" data-precio="<?= $p->precioPorNoche ?>" data-descripcion="<?= e($p->descripcion) ?>"
                                        <?= $planElegido === $p->id ? "selected" : "" ?>>
                                        <?= e($p->nombre) ?><?= $p->precioPorNoche > 0 ? " (+" . soles($p->precioPorNoche) . " por noche)" : "" ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <div class="form-text" id="planDescripcion"></div>
                            <?= $mensaje("plan_pension_id") ?>
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
                        <div class="col-12 col-sm-4">
                            <label class="form-label requerido" for="tipo_documento">Tipo de documento</label>
                            <select class="form-select<?= $claseError("tipo_documento") ?>" id="tipo_documento" name="tipo_documento" required>
                                <option value="DNI" <?= $valor("tipo_documento", "DNI") === "DNI" ? "selected" : "" ?>>DNI</option>
                                <option value="PASAPORTE" <?= $valor("tipo_documento", "DNI") === "PASAPORTE" ? "selected" : "" ?>>Pasaporte</option>
                            </select>
                            <?= $mensaje("tipo_documento") ?>
                        </div>
                        <div class="col-12 col-sm-8">
                            <label class="form-label requerido" for="numero_documento">Número de documento</label>
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
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="correo">Correo electrónico (opcional)</label>
                            <input class="form-control<?= $claseError("correo") ?>" type="email" id="correo" name="correo" maxlength="100"
                                placeholder="Ej. correo@ejemplo.com" value="<?= e($valor("correo")) ?>" autocomplete="email">
                            <?= $mensaje("correo") ?>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h2 class="h4"><i class="bi bi-credit-card text-danger"></i> Pago</h2>
                    <p class="text-muted small">Selecciona cuánto pagar ahora y cómo deseas hacerlo.</p>

                    <div class="mb-3">
                        <label class="form-label requerido" for="modalidad_pago">Modalidad de pago</label>
                        <select class="form-select<?= $claseError("modalidad_pago") ?>" id="modalidad_pago" name="modalidad_pago" required>
                            <option value="completo" <?= $modalidad === "completo" ? "selected" : "" ?>>Pagar el 100 % ahora</option>
                            <?php if ($beneficio) : ?>
                                <option value="fraccionado" data-puntos-requeridos="<?= (int) $beneficio["puntos_requeridos"] ?>"
                                    <?= $modalidad === "fraccionado" ? "selected" : "" ?> disabled>
                                    Pagar el <?= $porcentaje ?> % ahora y el resto al llegar (huéspedes frecuentes)
                                </option>
                            <?php endif ?>
                        </select>
                        <?= $mensaje("modalidad_pago") ?>

                        <?php if ($beneficio) : ?>
                            <div class="sa-caja-crema mt-2 p-3" id="verificarPuntos">
                                <div class="small fw-bold mb-2"><i class="bi bi-award text-danger"></i> ¿Eres huésped frecuente? Verifica tus puntos para desbloquear el pago del <?= $porcentaje ?> %</div>
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <button type="button" class="btn-linea btn-sm" id="btnVerificarPuntos">Verificar mis puntos</button>
                                    <span class="small" id="resultadoVerificacion"></span>
                                </div>
                            </div>
                        <?php endif ?>
                        <div class="sa-caja-crema mt-2 p-3 d-none" id="listaBeneficios">
                            <div class="small fw-bold mb-2"><i class="bi bi-stars text-danger"></i> Tus beneficios de huésped frecuente: elige los que quieras usar en esta reserva.</div>
                            <div id="checksBeneficios"></div>
                        </div>
                        <div class="form-text">
                            Acumulas puntos en cada estadía.
                            <a href="<?= url("publico/puntos.php") ?>#consulta" target="_blank" rel="noopener" id="linkVerPuntos" data-base="<?= url("publico/puntos.php") ?>">Consulta tus puntos</a>
                            o <a href="<?= url("publico/puntos.php") ?>">conoce el programa completo</a>.
                        </div>
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
                        <dt class="col-6 fw-normal text-muted">Piso</dt><dd class="col-6 text-end" id="r_piso">Sin preferencia</dd>
                        <dt class="col-6 fw-normal text-muted">Ingreso</dt><dd class="col-6 text-end" id="r_ingreso">—</dd>
                        <dt class="col-6 fw-normal text-muted">Salida</dt><dd class="col-6 text-end" id="r_salida">—</dd>
                        <dt class="col-6 fw-normal text-muted">Estadía</dt><dd class="col-6 text-end" id="r_noches">—</dd>
                        <dt class="col-6 fw-normal text-muted">Plan de alimentación</dt><dd class="col-6 text-end" id="r_plan">—</dd>
                        <dt class="col-6 fw-normal text-muted">Tarifa por noche</dt><dd class="col-6 text-end" id="r_tarifa">S/ 0.00</dd>
                    </dl>
                    <hr>
                    <div class="d-flex justify-content-between mb-2 text-success d-none" id="r_descuento_fila">
                        <span>Descuento huésped frecuente</span><strong id="r_descuento">-S/ 0.00</strong>
                    </div>
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
