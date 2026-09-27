<?php
require __DIR__ . "/../config/Autoload.php";
require_once __DIR__ . "/../views/partes/ayudas.php";

use App\Bo\Reserva as ReservaBO;

$titulo = "Tablero de disponibilidad";
$activo = "disponibilidad";

// Rango a mostrar: desde una fecha, 7 o 14 dias
$desde = fechaGet("desde") ?: date("Y-m-d");
$dias = (int) ($_GET["dias"] ?? 7);
$dias = in_array($dias, [7, 14], true) ? $dias : 7;
$tablero = (new ReservaBO())->tablero($desde, $dias);
$totales = $tablero["totales"];

$textos = ["disponible" => "Disp.", "reservada" => "Res.", "ocupada" => "Ocup.", "mantenimiento" => "Mant."];
$diasSemana = ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"];

require __DIR__ . "/../views/partes/panel_cabecera.php";
?>
<h1 class="h3 mb-3">Tablero de disponibilidad</h1>

<div class="row g-3 mb-3">
    <div class="col-12 col-md-4"><div class="sa-card sa-stat"><i class="bi bi-door-open" style="color:#1E6B3A"></i><div><div class="num"><?= $totales["disponible"] ?? 0 ?></div><div class="text-muted small">Disponibles <?= $desde === date("Y-m-d") ? "hoy" : "el " . fechaLarga($desde) ?></div></div></div></div>
    <div class="col-12 col-md-4"><div class="sa-card sa-stat"><i class="bi bi-calendar-check"></i><div><div class="num"><?= $totales["reservada"] ?? 0 ?></div><div class="text-muted small">Reservadas</div></div></div></div>
    <div class="col-12 col-md-4"><div class="sa-card sa-stat"><i class="bi bi-people" style="color:var(--rojo)"></i><div><div class="num"><?= ($totales["ocupada"] ?? 0) + ($totales["mantenimiento"] ?? 0) ?></div><div class="text-muted small">Ocupadas o en mantenimiento</div></div></div></div>
</div>

<form class="sa-card sa-card-cuerpo mb-3" method="get">
    <div class="row g-3 align-items-end">
        <div class="col-7 col-md-4">
            <label class="form-label" for="desde">Desde</label>
            <input class="form-control" type="date" id="desde" name="desde" value="<?= e($desde) ?>">
        </div>
        <div class="col-5 col-md-3">
            <label class="form-label" for="dias">Días</label>
            <select class="form-select" id="dias" name="dias">
                <option value="7" <?= $dias === 7 ? "selected" : "" ?>>7 días</option>
                <option value="14" <?= $dias === 14 ? "selected" : "" ?>>14 días</option>
            </select>
        </div>
        <div class="col-12 col-md-2"><button class="btn-sa w-100" type="submit">Filtrar</button></div>
    </div>
</form>

<div class="sa-card sa-card-cuerpo">
    <div class="table-responsive">
        <table class="tablero w-100">
            <thead>
                <tr>
                    <th class="text-start">Habitación</th>
                    <?php foreach ($tablero["fechas"] as $f) : ?>
                        <th><?= $diasSemana[(int) date("w", strtotime($f))] ?><br><?= date("d/m", strtotime($f)) ?></th>
                    <?php endforeach ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tablero["filas"] as $fila) : ?>
                    <tr>
                        <td class="hab"><?= e($fila["numero"]) ?> <small class="text-muted fw-normal"><?= e($fila["tipo"]) ?></small></td>
                        <?php foreach ($fila["celdas"] as $estado) : ?>
                            <td class="celda celda-<?= $estado ?>"><?= $textos[$estado] ?></td>
                        <?php endforeach ?>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <div class="d-flex flex-wrap gap-3 mt-3 small">
        <span><span class="punto" style="background:#1E6B3A"></span>Disponible</span>
        <span><span class="punto" style="background:#D9A521"></span>Reservada</span>
        <span><span class="punto" style="background:var(--rojo)"></span>Ocupada</span>
        <span><span class="punto" style="background:#8a807a"></span>Mantenimiento</span>
    </div>
</div>
<?php require __DIR__ . "/../views/partes/panel_pie.php" ?>
