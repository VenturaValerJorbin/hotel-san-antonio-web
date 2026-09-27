<?php
require "config/Autoload.php";

use bo\Reserva as ReservaBO;

$titulo = "Registro de check-in";
$activo = "checkin";

$r = (new ReservaBO())->detalleCheckin((int) ($_GET["id"] ?? 0));
if (!$r) {
    header("Location: reservas.php");
    exit;
}

require "views/partes/panel_cabecera.php";

$puedeIngresar = $r["estado"] === "confirmada";
$asignada = (int) ($antiguo["habitacion_id"] ?? $r["habitacion_id"]);
?>
<h1 class="h3 mb-3">Registro de check-in</h1>

<section class="sa-card sa-card-cuerpo mb-3">
    <h2 class="h5"><i class="bi bi-file-earmark-text text-warning"></i> Reserva asociada</h2>
    <div class="row g-2 small">
        <div class="col-6 col-md-3"><span class="text-muted">Huésped</span><br><strong><?= e($r["nombre_completo"]) ?></strong></div>
        <div class="col-6 col-md-2"><span class="text-muted">Habitación pedida</span><br><strong><?= e($r["tipo"]) ?></strong></div>
        <div class="col-6 col-md-3"><span class="text-muted">Fechas</span><br><strong><?= fechaLarga($r["fecha_ingreso"]) ?> – <?= fechaLarga($r["fecha_salida"]) ?></strong></div>
        <div class="col-6 col-md-2"><span class="text-muted">Estado</span><br><span class="pill pill-<?= e($r["estado"]) ?>"><?= e($r["estado"]) ?></span></div>
        <div class="col-12 col-md-2"><span class="text-muted">Pago</span><br>
            <span class="pill <?= $r["saldo_pendiente"] > 0 ? "pill-saldo" : "pill-pagado" ?>"><?= $r["saldo_pendiente"] > 0 ? "Adelanto " . soles($r["monto_pagado"]) : "Pagado" ?></span>
        </div>
    </div>
</section>

<form action="procesar.php" method="post" novalidate>
    <input type="hidden" name="modulo" value="reserva">
    <input type="hidden" name="accion" value="checkin">
    <input type="hidden" name="id" value="<?= (int) $r["id"] ?>">

    <div class="row g-3">
        <div class="col-12 col-lg-4">
            <section class="sa-card sa-card-cuerpo h-100">
                <h2 class="h5"><i class="bi bi-person text-danger"></i> Datos del huésped</h2>
                <div class="mb-3"><label class="form-label">Nombre completo</label><input class="form-control" value="<?= e($r["nombre_completo"]) ?>" readonly></div>
                <div class="mb-3"><label class="form-label"><?= e($r["tipo_documento"]) ?></label><input class="form-control" value="<?= e($r["numero_documento"]) ?>" readonly></div>
                <div class="mb-3"><label class="form-label">Celular</label><input class="form-control" value="<?= e($r["telefono"]) ?>" readonly></div>
                <div><label class="form-label">Correo electrónico</label><input class="form-control" value="<?= e($r["correo"] ?? "—") ?>" readonly></div>
            </section>
        </div>

        <div class="col-12 col-lg-4">
            <section class="sa-card sa-card-cuerpo h-100">
                <h2 class="h5"><i class="bi bi-door-open text-danger"></i> Habitación y estadía</h2>
                <div class="row g-2 mb-3">
                    <div class="col-6"><label class="form-label">Ingreso</label><input class="form-control" value="<?= fechaLarga($r["fecha_ingreso"]) ?>" readonly></div>
                    <div class="col-6"><label class="form-label">Salida</label><input class="form-control" value="<?= fechaLarga($r["fecha_salida"]) ?>" readonly></div>
                </div>
                <div class="mb-3"><label class="form-label">Tipo de habitación</label><input class="form-control" value="<?= e($r["tipo"]) ?>" readonly></div>
                <div class="mb-3">
                    <label class="form-label requerido" for="habitacion_id">Habitación asignada</label>
                    <select class="form-select" id="habitacion_id" name="habitacion_id" <?= $puedeIngresar ? "" : "disabled" ?> required>
                        <?php foreach ($r["libres"] as $h) : ?>
                            <option value="<?= (int) $h["id"] ?>" <?= $asignada === (int) $h["id"] ? "selected" : "" ?>>Habitación <?= e($h["numero"]) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="observaciones">Observaciones</label>
                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3" maxlength="255" <?= $puedeIngresar ? "" : "disabled" ?>
                        placeholder="Ej. Llegada aproximada 2:00 p. m."><?= e($antiguo["observaciones"] ?? $r["observaciones"] ?? "") ?></textarea>
                </div>
            </section>
        </div>

        <div class="col-12 col-lg-4">
            <section class="sa-card sa-card-cuerpo h-100">
                <h2 class="h5"><i class="bi bi-receipt text-danger"></i> Resumen de la estadía</h2>
                <dl class="row mb-0">
                    <dt class="col-7 fw-normal text-muted">Precio por noche</dt><dd class="col-5 text-end"><?= soles($r["precio_noche"]) ?></dd>
                    <dt class="col-7 fw-normal text-muted">Noches</dt><dd class="col-5 text-end"><?= (int) $r["noches"] ?></dd>
                    <dt class="col-7">Total</dt><dd class="col-5 text-end fw-bold"><?= soles($r["monto_total"]) ?></dd>
                    <dt class="col-7 fw-normal text-muted">Pagado en línea</dt><dd class="col-5 text-end text-success"><?= soles($r["monto_pagado"]) ?></dd>
                </dl>
                <div class="sa-caja-pago mt-3 d-flex justify-content-between"><span>Saldo a cobrar al salir</span><strong><?= soles($r["saldo_pendiente"]) ?></strong></div>
                <div class="sa-caja-crema small mt-3"><i class="bi bi-exclamation-triangle"></i> Verifica la identidad del huésped antes de confirmar el registro.</div>
            </section>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3">
        <a class="btn-linea text-decoration-none" href="reservas.php">Cancelar</a>
        <button class="btn-sa" type="submit" <?= $puedeIngresar ? "" : "disabled" ?>><i class="bi bi-person-check"></i> Registrar check-in</button>
    </div>
</form>
<?php require "views/partes/panel_pie.php" ?>
