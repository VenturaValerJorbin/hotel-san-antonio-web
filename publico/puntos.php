<?php
require __DIR__ . "/../config/Autoload.php";

use App\Bo\Puntos as PuntosBO;

$bo = new PuntosBO();
$solesPorPunto = $bo->solesPorPunto();
$recompensas = $bo->recompensas();
$montoEjemplo = 500;
$fraccionado = current(array_filter($recompensas, fn($r) => $r["tipo"] === "pago_fraccionado")) ?: null;
$iconos = ["pago_fraccionado" => "bi-cash-coin", "producto" => "bi-cup-hot", "descuento" => "bi-percent", "noche_gratis" => "bi-moon-stars"];

$titulo = "Programa de puntos";

require __DIR__ . "/../views/partes/cabecera.php";

hero(
    "Programa de puntos",
    "Cada estadía te acerca a más beneficios. Hospédate, acumula puntos y aprovéchalos en tu próxima visita.",
    [["Inicio", "index.php"], ["Programa de puntos", null]]
);
?>
<main class="container py-4 py-lg-5">
    <!-- Consulta de puntos: el huesped ve su saldo con su documento, sin crear cuenta -->
    <section class="sa-card sa-card-cuerpo mb-5" id="consulta">
        <h2 class="h4"><i class="bi bi-search-heart text-danger"></i> Consulta tus puntos</h2>
        <p class="text-muted small mb-3">Ingresa tu documento para ver cuántos puntos tienes acumulados.</p>
        <form id="formConsultaPuntos" class="row g-2 align-items-end" data-consulta="<?= url("publico/consultar_puntos.php") ?>">
            <div class="col-6 col-sm-3">
                <label class="form-label small mb-1" for="consulta_tipo_documento">Tipo de documento</label>
                <select class="form-select form-select-sm" id="consulta_tipo_documento">
                    <option value="DNI">DNI</option>
                    <option value="PASAPORTE">Pasaporte</option>
                </select>
            </div>
            <div class="col-6 col-sm-4">
                <label class="form-label small mb-1" for="consulta_numero_documento">Número de documento</label>
                <input class="form-control form-control-sm" id="consulta_numero_documento" placeholder="Ej. 12345678" maxlength="12">
            </div>
            <div class="col-12 col-sm-3">
                <button class="btn-sa btn-sm w-100" type="submit">Consultar</button>
            </div>
        </form>
        <div class="mt-3" id="resultadoConsultaPuntos"></div>
    </section>

    <!-- Como funciona -->
    <div class="sa-subtitulo">Tan simple como hospedarte</div>
    <h2 class="h3">¿Cómo funciona?</h2>
    <div class="sa-linea-dorada"></div>
    <div class="row g-3 mb-3">
        <div class="col-12 col-md-4">
            <article class="sa-card sa-card-cuerpo h-100">
                <span class="sa-paso">1</span>
                <h3 class="h5 mt-2">Reserva y hospédate</h3>
                <p class="small text-muted mb-0">Reserva sin crear una cuenta. Te identificamos con tu DNI o pasaporte, así tus puntos te siguen en cada visita.</p>
            </article>
        </div>
        <div class="col-12 col-md-4">
            <article class="sa-card sa-card-cuerpo h-100">
                <span class="sa-paso">2</span>
                <h3 class="h5 mt-2">Acumula puntos</h3>
                <p class="small text-muted mb-0">Al registrar tu salida (check-out) sumas <strong>1 punto por cada <?= soles($solesPorPunto) ?></strong> de tu estadía.</p>
            </article>
        </div>
        <div class="col-12 col-md-4">
            <article class="sa-card sa-card-cuerpo h-100">
                <span class="sa-paso">3</span>
                <h3 class="h5 mt-2">Aprovecha tus beneficios</h3>
                <p class="small text-muted mb-0">Con tus puntos accedes a pago fraccionado, cortesías y descuentos en tus siguientes estadías.</p>
            </article>
        </div>
    </div>
    <div class="sa-caja-crema mb-5">
        <i class="bi bi-lightbulb text-danger"></i>
        <strong>Ejemplo:</strong> una estadía de <?= soles($montoEjemplo) ?> suma <strong><?= $bo->puntosPorMonto($montoEjemplo) ?> puntos</strong>.
    </div>

    <!-- Beneficios -->
    <div class="sa-subtitulo">Mientras más te quedas, más ganas</div>
    <h2 class="h3">Tus beneficios</h2>
    <div class="sa-linea-dorada"></div>
    <div class="row g-3 mb-5">
        <?php foreach ($recompensas as $r) : ?>
            <?php $permanente = !(int) $r["consume_puntos"] ?>
            <div class="col-12 col-sm-6 col-lg-3">
                <article class="sa-card sa-card-cuerpo h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start">
                        <i class="bi <?= $iconos[$r["tipo"]] ?? "bi-gift" ?> fs-2 text-danger"></i>
                        <span class="badge <?= $permanente ? "text-bg-warning" : "text-bg-light border" ?>"><?= $permanente ? "Permanente" : "Canje" ?></span>
                    </div>
                    <div class="sa-nivel-puntos mt-2"><?= (int) $r["puntos_requeridos"] ?> <small>puntos</small></div>
                    <h3 class="h6 mt-1"><?= e($r["nombre"]) ?></h3>
                    <p class="small text-muted"><?= e($r["descripcion"]) ?></p>
                    <p class="small mt-auto mb-0">
                        <i class="bi bi-info-circle text-danger"></i>
                        <?= $permanente ? "No gasta tus puntos." : "Descuenta " . (int) $r["puntos_requeridos"] . " puntos de tu saldo." ?>
                        <span class="text-muted">Se logra con unos S/ <?= number_format($r["puntos_requeridos"] * $solesPorPunto) ?> en estadías.</span>
                    </p>
                </article>
            </div>
        <?php endforeach ?>
    </div>

    <!-- Reglas -->
    <div class="row">
        <div class="col-12 col-lg-9">
            <h2 class="h4">Lo que debes saber</h2>
            <div class="sa-linea-dorada"></div>
            <ul class="sa-lista-check">
                <li>Los puntos se suman al registrar tu salida de una estadía completada.</li>
                <li>Tu saldo se valida con tu DNI o pasaporte al momento de reservar; no necesitas crear una cuenta.</li>
                <?php if ($fraccionado) : ?>
                    <li>Con <?= (int) $fraccionado["puntos_requeridos"] ?> puntos pagas solo el <?= (float) $fraccionado["valor"] ?> % al reservar y el resto al llegar al hotel. Este beneficio se mantiene mientras conserves esos puntos.</li>
                <?php endif ?>
                <li>Los demás beneficios descuentan sus puntos de tu saldo y se solicitan en recepción.</li>
                <li>El pago realizado no es reembolsable, según nuestros <a href="<?= url("publico/terminos.php") ?>">términos y condiciones</a>.</li>
            </ul>
        </div>
    </div>

    <section class="sa-caja-crema d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 p-4 mt-4">
        <div>
            <h2 class="h4 mb-1">Empieza a sumar desde tu próxima estadía</h2>
            <p class="mb-0">Reserva en línea sin crear una cuenta.</p>
        </div>
        <div class="d-flex flex-column flex-sm-row gap-2">
            <a class="btn-sa text-decoration-none text-center" href="<?= url("publico/reservar.php") ?>">Reservar ahora</a>
            <a class="btn-linea text-decoration-none text-center" href="<?= url("publico/habitaciones.php") ?>">Ver habitaciones</a>
        </div>
    </section>
</main>
<?php require __DIR__ . "/../views/partes/pie.php" ?>
