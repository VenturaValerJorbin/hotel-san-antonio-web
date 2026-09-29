<?php
require __DIR__ . "/../config/Autoload.php";

use App\Bo\Reserva as ReservaBO;

$titulo = "Comprobante de reserva";
$codigo = strtoupper(trim($_GET["codigo"] ?? ""));
$documento = strtoupper(trim($_GET["numero_documento"] ?? ""));
$buscado = $codigo !== "" || $documento !== "";
$reserva = $buscado ? (new ReservaBO())->buscarComprobante($codigo, $documento) : null;

$estados = ["pendiente" => "Pendiente", "confirmada" => "Confirmada", "checkin" => "En el hotel",
    "checkout" => "Finalizada", "cancelada" => "Cancelada", "no_show" => "No llegó"];
$metodos = ["yape" => "Yape", "tarjeta" => "Tarjeta Visa", "transferencia" => "Transferencia", "efectivo" => "Efectivo"];

require __DIR__ . "/../views/partes/cabecera.php";
/** @var array $hotel Datos del hotel (los crea views/partes/ayudas.php) */

hero(
    "Comprobante de reserva",
    "Guarda o imprime este comprobante: es tu prueba de que reservaste con nosotros.",
    [["Inicio", "index.php"], ["Comprobante", null]]
);
?>
<main class="container py-4 py-lg-5">
    <?php if (!$reserva) : ?>
        <?php if ($buscado) : ?>
            <div class="alert alert-warning">No encontramos ninguna reserva con ese código y ese documento. Verifica ambos datos.</div>
        <?php endif ?>
        <section class="sa-card sa-card-cuerpo mx-auto" style="max-width: 32rem">
            <h2 class="h4"><i class="bi bi-search text-danger"></i> Buscar mi reserva</h2>
            <p class="text-muted small">Ingresa el código que te dimos al confirmar tu reserva y el documento con el que la hiciste.</p>
            <form method="get" class="row g-3">
                <div class="col-12">
                    <label class="form-label requerido" for="codigo">Código de reserva</label>
                    <input class="form-control" id="codigo" name="codigo" placeholder="Ej. SAAB12CD34" value="<?= e($codigo) ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label requerido" for="numero_documento">DNI o pasaporte</label>
                    <input class="form-control" id="numero_documento" name="numero_documento" placeholder="Ej. 12345678" value="<?= e($documento) ?>" required>
                </div>
                <div class="col-12"><button class="btn-sa w-100" type="submit">Buscar</button></div>
            </form>
        </section>
    <?php else : ?>
        <section class="sa-card sa-card-cuerpo mx-auto sa-comprobante" style="max-width: 42rem">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
                <div>
                    <div class="sa-subtitulo">Código de reserva</div>
                    <div class="h3 mb-0 font-monospace"><?= e($reserva["codigo"]) ?></div>
                </div>
                <span class="badge text-bg-<?= $reserva["estado"] === "cancelada" ? "secondary" : "warning" ?> fs-6">
                    <?= e($estados[$reserva["estado"]] ?? $reserva["estado"]) ?>
                </span>
            </div>
            <div class="sa-linea-dorada"></div>

            <h3 class="h6 text-muted mt-3">Huésped</h3>
            <p class="mb-3">
                <?= e($reserva["nombre_completo"]) ?><br>
                <?= e($reserva["tipo_documento"]) ?> <?= e($reserva["numero_documento"]) ?>
                <?php if ($reserva["telefono"]) : ?> · <?= e($reserva["telefono"]) ?><?php endif ?>
            </p>

            <h3 class="h6 text-muted">Habitación y estadía</h3>
            <dl class="row mb-3">
                <dt class="col-6 fw-normal text-muted">Habitación</dt><dd class="col-6 text-end"><?= e($reserva["tipo"]) ?> N.° <?= e($reserva["habitacion"]) ?> (piso <?= (int) $reserva["piso"] ?>)</dd>
                <dt class="col-6 fw-normal text-muted">Ingreso</dt><dd class="col-6 text-end"><?= fechaLarga($reserva["fecha_ingreso"]) ?></dd>
                <dt class="col-6 fw-normal text-muted">Salida</dt><dd class="col-6 text-end"><?= fechaLarga($reserva["fecha_salida"]) ?></dd>
                <dt class="col-6 fw-normal text-muted">Estadía</dt><dd class="col-6 text-end"><?= (int) $reserva["noches"] ?> <?= (int) $reserva["noches"] === 1 ? "noche" : "noches" ?></dd>
                <dt class="col-6 fw-normal text-muted">Tarifa por noche</dt><dd class="col-6 text-end"><?= soles($reserva["precio_noche"]) ?></dd>
            </dl>

            <h3 class="h6 text-muted">Pago</h3>
            <dl class="row mb-1">
                <dt class="col-6 fw-normal text-muted">Modalidad</dt><dd class="col-6 text-end"><?= $reserva["modalidad_pago"] === "fraccionado" ? "Pago fraccionado" : "Pago completo" ?></dd>
                <dt class="col-6 fw-normal text-muted">Total de la reserva</dt><dd class="col-6 text-end fw-bold"><?= soles($reserva["monto_total"]) ?></dd>
                <dt class="col-6 fw-normal text-muted">Pagado</dt><dd class="col-6 text-end"><?= soles($reserva["monto_pagado"]) ?></dd>
                <dt class="col-6 fw-normal text-muted">Saldo pendiente (al llegar)</dt><dd class="col-6 text-end"><?= soles($reserva["saldo_pendiente"]) ?></dd>
            </dl>
            <?php foreach ($reserva["pagos"] as $p) : ?>
                <p class="small text-muted mb-1">
                    <i class="bi bi-receipt"></i> <?= soles($p["monto"]) ?> por <?= $metodos[$p["metodo"]] ?? e($p["metodo"]) ?>
                    <?php if ($p["fecha_pago"]) : ?> · <?= fechaLarga($p["fecha_pago"]) ?><?php endif ?>
                </p>
            <?php endforeach ?>

            <div class="sa-caja-crema small mt-4">
                <i class="bi bi-info-circle"></i> El pago no es reembolsable, salvo que la habitación no corresponda a lo publicado en esta web.
                Consulta nuestros <a href="<?= url("publico/terminos.php") ?>">términos y condiciones</a>.
            </div>

            <div class="d-flex flex-column flex-sm-row gap-2 mt-4 no-imprimir">
                <button type="button" class="btn-sa" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir / Guardar como PDF</button>
                <a class="btn-linea text-decoration-none text-center" href="<?= url("publico/comprobante.php") ?>">Buscar otra reserva</a>
            </div>

            <p class="small text-muted mt-4 mb-0 no-imprimir">
                ¿Necesitas ayuda? Escríbenos por WhatsApp al <?= e($hotel["whatsapp_texto"]) ?> con tu código de reserva.
            </p>
        </section>
    <?php endif ?>
</main>
<?php require __DIR__ . "/../views/partes/pie.php" ?>
