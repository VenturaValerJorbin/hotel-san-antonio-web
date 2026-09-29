<?php
require __DIR__ . "/../config/Autoload.php";
require_once __DIR__ . "/../views/partes/ayudas.php";

use App\Bo\Puntos as PuntosBO;

$titulo = "Canje de recompensas";
$activo = "canjes";

$tipoDocumento = $_GET["tipo_documento"] ?? "DNI";
$numeroDocumento = trim($_GET["numero_documento"] ?? "");
$buscado = $numeroDocumento !== "";
$datos = $buscado ? (new PuntosBO())->buscarParaCanje($tipoDocumento, $numeroDocumento) : null;

require __DIR__ . "/../views/partes/panel_cabecera.php";
?>
<h1 class="h3 mb-3">Canje de recompensas</h1>

<form class="sa-card sa-card-cuerpo mb-3" method="get">
    <div class="row g-3 align-items-end">
        <div class="col-5 col-md-3">
            <label class="form-label" for="tipo_documento">Tipo de documento</label>
            <select class="form-select" id="tipo_documento" name="tipo_documento">
                <option value="DNI" <?= $tipoDocumento === "DNI" ? "selected" : "" ?>>DNI</option>
                <option value="PASAPORTE" <?= $tipoDocumento === "PASAPORTE" ? "selected" : "" ?>>Pasaporte</option>
            </select>
        </div>
        <div class="col-7 col-md-5">
            <label class="form-label" for="numero_documento">Número de documento</label>
            <input class="form-control" id="numero_documento" name="numero_documento" placeholder="Ej. 12345678" value="<?= e($numeroDocumento) ?>">
        </div>
        <div class="col-12 col-md-4"><button class="btn-sa w-100" type="submit">Buscar huésped</button></div>
    </div>
</form>

<?php if ($buscado && !$datos) : ?>
    <div class="alert alert-warning">No encontramos ningún huésped con ese documento.</div>
<?php elseif ($datos) : ?>
    <div class="row g-3">
        <div class="col-12 col-lg-4">
            <section class="sa-card sa-card-cuerpo h-100">
                <h2 class="h5"><i class="bi bi-person text-danger"></i> Huésped</h2>
                <p class="mb-1"><strong><?= e($datos["huesped"]["nombre_completo"]) ?></strong></p>
                <p class="text-muted small mb-3"><?= e($datos["huesped"]["tipo_documento"]) ?> <?= e($datos["huesped"]["numero_documento"]) ?></p>
                <div class="sa-caja-crema d-inline-block">
                    <span class="sa-nivel-puntos"><?= (int) $datos["puntos"] ?> <small>puntos</small></span>
                </div>
            </section>
        </div>

        <div class="col-12 col-lg-4">
            <section class="sa-card sa-card-cuerpo h-100">
                <h2 class="h5"><i class="bi bi-gift text-danger"></i> Beneficios disponibles</h2>
                <?php if (!$datos["canjeables"]) : ?>
                    <p class="text-muted small mb-0">Con <?= (int) $datos["puntos"] ?> puntos todavía no alcanza para ningún canje.</p>
                <?php else : ?>
                    <form action="<?= url("procesar.php") ?>" method="post">
                        <input type="hidden" name="modulo" value="puntos">
                        <input type="hidden" name="accion" value="canjear">
                        <input type="hidden" name="huesped_id" value="<?= (int) $datos["huesped"]["id"] ?>">
                        <input type="hidden" name="tipo_documento" value="<?= e($tipoDocumento) ?>">
                        <input type="hidden" name="numero_documento" value="<?= e($numeroDocumento) ?>">
                        <?php foreach ($datos["canjeables"] as $i => $r) : ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="recompensa_id" id="r<?= $r["id"] ?>" value="<?= $r["id"] ?>" <?= $i === 0 ? "checked" : "" ?> required>
                                <label class="form-check-label" for="r<?= $r["id"] ?>">
                                    <strong><?= e($r["nombre"]) ?></strong> <span class="text-muted small">(<?= (int) $r["puntos_requeridos"] ?> pts)</span><br>
                                    <span class="text-muted small"><?= e($r["descripcion"]) ?></span>
                                </label>
                            </div>
                        <?php endforeach ?>
                        <button class="btn-sa w-100 mt-2" type="submit" onclick="return confirm('¿Registrar este canje? Se descontarán los puntos.')">Registrar canje</button>
                    </form>
                <?php endif ?>
            </section>
        </div>

        <div class="col-12 col-lg-4">
            <section class="sa-card sa-card-cuerpo h-100">
                <h2 class="h5"><i class="bi bi-clock-history text-danger"></i> Canjes anteriores</h2>
                <?php if (!$datos["historial"]) : ?>
                    <p class="text-muted small mb-0">Este huésped todavía no ha canjeado nada.</p>
                <?php else : ?>
                    <ul class="list-unstyled small mb-0">
                        <?php foreach ($datos["historial"] as $h) : ?>
                            <li class="mb-2">
                                <strong><?= e($h["nombre"]) ?></strong> <span class="text-muted">(<?= (int) $h["puntos_requeridos"] ?> pts)</span><br>
                                <span class="text-muted"><?= fechaLarga(substr($h["created_at"], 0, 10)) ?></span>
                            </li>
                        <?php endforeach ?>
                    </ul>
                <?php endif ?>
            </section>
        </div>
    </div>
<?php endif ?>
<?php require __DIR__ . "/../views/partes/panel_pie.php" ?>
