<?php
require __DIR__ . "/../config/Autoload.php";

use App\Bo\Reserva as ReservaBO;

$titulo = "Gestión de reservas";
$activo = "reservas";

// Filtros del panel (solo lectura: se envian por GET)
$filtros = [
    "buscar" => trim($_GET["buscar"] ?? ""),
    "ingreso" => $_GET["ingreso"] ?? "",
    "estado" => $_GET["estado"] ?? "",
];
$bo = new ReservaBO();
$bo->liberarNoShow();   // cierra sola cualquier reserva cuya fecha de salida ya paso sin check-in
$reservas = $bo->listar($filtros);
$resumen = $bo->resumen();

// "pendiente" y "cancelada" no se usan hoy: toda reserva nace "confirmada" (pago instantaneo,
// aunque simulado) y el pago no es reembolsable, asi que no hay una accion de "cancelar".
$estados = ["confirmada" => "Confirmada", "checkin" => "En el hotel", "checkout" => "Finalizada", "no_show" => "No llegó"];

require __DIR__ . "/../views/partes/panel_cabecera.php";

// Estado del pago de una reserva, para la columna "Pago"
$pago = fn($r) => $r->saldoPendiente > 0
    ? '<span class="pill pill-saldo">Saldo ' . soles($r->saldoPendiente) . "</span>"
    : '<span class="pill pill-pagado">Pagado</span>';
?>
<h1 class="h3 mb-3">Gestión de reservas</h1>

<div class="row g-3 mb-3">
    <div class="col-12 col-md-4"><div class="sa-card sa-stat"><i class="bi bi-journal-check"></i><div><div class="num"><?= (int) $resumen["por_llegar"] ?></div><div class="text-muted small">Por llegar</div></div></div></div>
    <div class="col-12 col-md-4"><div class="sa-card sa-stat"><i class="bi bi-people"></i><div><div class="num"><?= (int) $resumen["llegadas_hoy"] ?></div><div class="text-muted small">Llegadas de hoy</div></div></div></div>
    <div class="col-12 col-md-4"><div class="sa-card sa-stat"><i class="bi bi-wallet2"></i><div><div class="num"><?= (int) $resumen["con_saldo"] ?></div><div class="text-muted small">Con saldo por cobrar</div></div></div></div>
</div>

<form class="sa-card sa-card-cuerpo mb-3" method="get">
    <div class="row g-3 align-items-end">
        <div class="col-12 col-md-4">
            <label class="form-label" for="buscar">Buscar por huésped o DNI</label>
            <input class="form-control" id="buscar" name="buscar" placeholder="Nombre, apellido o DNI..." value="<?= e($filtros["buscar"]) ?>">
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label" for="ingreso">Fecha de ingreso</label>
            <input class="form-control" type="date" id="ingreso" name="ingreso" value="<?= e($filtros["ingreso"]) ?>">
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label" for="estado">Estado</label>
            <select class="form-select" id="estado" name="estado">
                <option value="">Todos</option>
                <?php foreach ($estados as $clave => $texto) : ?>
                    <option value="<?= $clave ?>" <?= $filtros["estado"] === $clave ? "selected" : "" ?>><?= $texto ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex gap-2">
            <button class="btn-sa flex-grow-1" type="submit">Filtrar</button>
            <a class="btn-linea text-decoration-none" href="<?= url("admin/reservas.php") ?>" title="Quitar filtros"><i class="bi bi-x-lg"></i></a>
        </div>
    </div>
</form>

<?php
// Botones de la reserva segun su estado (los usan la tabla y las tarjetas del celular)
$acciones = function ($r) {
    if ($r->estado === "confirmada") {
        echo '<a class="btn btn-sm btn-danger" href="' . url('admin/checkin.php') . '?id=' . $r->id . '">Check-in</a>';
    } elseif ($r->estado === "checkin") {
        echo '<form action="' . url('procesar.php') . '" method="post" class="d-inline" onsubmit="return confirm(\'¿Registrar el check-out y cobrar el saldo?\')">'
            . '<input type="hidden" name="modulo" value="reserva"><input type="hidden" name="accion" value="checkout">'
            . '<input type="hidden" name="id" value="' . $r->id . '"><button class="btn btn-sm btn-outline-danger">Check-out</button></form>';
    } else {
        echo '<span class="text-muted">—</span>';
    }
};
?>

<!-- Celular: una tarjeta por reserva -->
<div class="d-md-none">
    <?php foreach ($reservas as $r) : ?>
        <article class="sa-card sa-card-cuerpo mb-2">
            <div class="d-flex justify-content-between"><strong><?= e($r->huesped) ?></strong><span class="pill pill-<?= e($r->estado) ?>"><?= e($estados[$r->estado] ?? $r->estado) ?></span></div>
            <div class="small text-muted">
                <?= e($r->tipo) ?> · Hab. <?= e($r->habitacion) ?> · <?= e($r->codigo) ?>
                <?php if ($r->planPension !== "Solo alojamiento") : ?> · <?= e($r->planPension) ?><?php endif ?>
            </div>
            <div class="small my-2"><i class="bi bi-calendar3"></i> <?= fechaLarga($r->fechaIngreso) ?> → <?= fechaLarga($r->fechaSalida) ?></div>
            <div class="d-flex justify-content-between align-items-center"><?= $pago($r) ?><span><?php $acciones($r) ?></span></div>
        </article>
    <?php endforeach ?>
</div>

<!-- Tablet y escritorio: tabla -->
<div class="sa-card d-none d-md-block">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr><th>Huésped</th><th>Habitación</th><th>Ingreso</th><th>Salida</th><th>Estado</th><th>Pago</th><th class="text-end">Total</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                <?php foreach ($reservas as $r) : ?>
                    <tr>
                        <td><?= e($r->huesped) ?><br><small class="text-muted"><?= e($r->documento) ?> · <?= e($r->codigo) ?></small></td>
                        <td>
                            <?= e($r->tipo) ?><br><small class="text-muted">Hab. <?= e($r->habitacion) ?></small>
                            <?php if ($r->planPension !== "Solo alojamiento") : ?><br><small class="text-muted"><?= e($r->planPension) ?></small><?php endif ?>
                        </td>
                        <td><?= fechaLarga($r->fechaIngreso) ?></td>
                        <td><?= fechaLarga($r->fechaSalida) ?></td>
                        <td><span class="pill pill-<?= e($r->estado) ?>"><?= e($estados[$r->estado] ?? $r->estado) ?></span></td>
                        <td><?= $pago($r) ?></td>
                        <td class="text-end"><?= soles($r->montoTotal) ?></td>
                        <td><?php $acciones($r) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (!$reservas) : ?>
    <p class="text-center text-muted my-4">No hay reservas con esos filtros.</p>
<?php else : ?>
    <p class="text-muted small mt-2">Mostrando <?= count($reservas) ?> reserva(s).</p>
<?php endif ?>
<?php require __DIR__ . "/../views/partes/panel_pie.php" ?>
