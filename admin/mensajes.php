<?php
require __DIR__ . "/../config/Autoload.php";

use App\Bo\Contacto as ContactoBO;

$titulo = "Mensajes de contacto";
$activo = "mensajes";

$bo = new ContactoBO();
$filtros = [
    "asunto" => $_GET["asunto"] ?? "",
    "leido" => $_GET["leido"] ?? "",
];
$mensajes = $bo->listar($filtros);

require __DIR__ . "/../views/partes/panel_cabecera.php";

$asuntos = ["reserva" => "Reserva", "consulta" => "Consulta", "sugerencia" => "Sugerencia", "reclamo" => "Reclamo"];
?>
<h1 class="h3 mb-3">Mensajes de contacto</h1>

<form class="sa-card sa-card-cuerpo mb-3" method="get">
    <div class="row g-3 align-items-end">
        <div class="col-6 col-md-4">
            <label class="form-label" for="asunto">Asunto</label>
            <select class="form-select" id="asunto" name="asunto">
                <option value="">Todos</option>
                <?php foreach ($asuntos as $clave => $texto) : ?>
                    <option value="<?= $clave ?>" <?= $filtros["asunto"] === $clave ? "selected" : "" ?>><?= $texto ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-6 col-md-4">
            <label class="form-label" for="leido">Estado</label>
            <select class="form-select" id="leido" name="leido">
                <option value="">Todos</option>
                <option value="0" <?= $filtros["leido"] === "0" ? "selected" : "" ?>>No leídos</option>
                <option value="1" <?= $filtros["leido"] === "1" ? "selected" : "" ?>>Leídos</option>
            </select>
        </div>
        <div class="col-12 col-md-4 d-flex gap-2">
            <button class="btn-sa flex-grow-1" type="submit">Filtrar</button>
            <a class="btn-linea text-decoration-none" href="<?= url("admin/mensajes.php") ?>" title="Quitar filtros"><i class="bi bi-x-lg"></i></a>
        </div>
    </div>
</form>

<?php foreach ($mensajes as $m) : ?>
    <article class="sa-card sa-card-cuerpo mb-2<?= $m->leido ? "" : " sa-mensaje-nuevo" ?>">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <strong><?= e($m->nombre) ?></strong>
                <span class="pill pill-<?= $m->leido ? "pagado" : "saldo" ?>"><?= $m->leido ? "Leído" : "Nuevo" ?></span>
                <span class="text-muted small"><?= e($asuntos[$m->asunto] ?? $m->asunto) ?></span>
            </div>
            <span class="text-muted small"><?= fechaLarga(substr($m->creadoEn, 0, 10)) ?></span>
        </div>
        <p class="mb-2 mt-2"><?= e($m->mensaje) ?></p>
        <div class="small text-muted mb-2">
            <i class="bi bi-envelope"></i> <?= e($m->correo) ?> · <i class="bi bi-telephone"></i> <?= e($m->telefono) ?>
        </div>
        <?php if (!$m->leido) : ?>
            <form action="<?= url("procesar.php") ?>" method="post" class="d-inline">
                <input type="hidden" name="modulo" value="contacto">
                <input type="hidden" name="accion" value="marcarLeido">
                <input type="hidden" name="id" value="<?= $m->id ?>">
                <button class="btn btn-sm btn-outline-secondary">Marcar como leído</button>
            </form>
        <?php endif ?>
    </article>
<?php endforeach ?>

<?php if (!$mensajes) : ?>
    <p class="text-center text-muted my-4">No hay mensajes con esos filtros.</p>
<?php endif ?>
<?php require __DIR__ . "/../views/partes/panel_pie.php" ?>
