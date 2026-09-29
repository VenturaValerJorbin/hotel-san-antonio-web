<?php
require __DIR__ . "/../../config/Autoload.php";

use App\Bo\Habitacion as HabitacionBO;

$titulo = "Gestión de habitaciones";
$activo = "habitaciones";
$bo = new HabitacionBO();
$habitaciones = $bo->listar();
$tipos = $bo->tipos();

// Si viene ?editar=ID se carga la habitacion en el formulario (UPDATE); si no, el formulario crea (CREATE)
$editar = isset($_GET["editar"]) ? $bo->obtener((int) $_GET["editar"]) : null;

require __DIR__ . "/../partes/panel_cabecera.php";
/** @var array $errores Errores del formulario (los crea views/partes/ayudas.php) */
/** @var array $antiguo Datos escritos antes de un error (los crea views/partes/ayudas.php) */

$valor = fn($campo, $porDefecto = "") => $antiguo[$campo] ?? $porDefecto;
$claseError = fn($campo) => isset($errores[$campo]) ? " is-invalid" : "";
$mensaje = fn($campo) => isset($errores[$campo]) ? '<div class="invalid-feedback">' . e($errores[$campo]) . "</div>" : "";
$estados = ["disponible" => "Disponible", "ocupada" => "Ocupada", "limpieza" => "En limpieza", "mantenimiento" => "Mantenimiento"];
?>
<h1 class="h3 mb-3">Gestión de habitaciones</h1>

<section class="sa-card sa-card-cuerpo mb-4">
    <h2 class="h5"><?= $editar ? "Editar habitación " . e($editar->numero) : "Nueva habitación" ?></h2>
    <form action="<?= url("procesar.php") ?>" method="post" class="row g-3" novalidate>
        <input type="hidden" name="modulo" value="habitacion">
        <input type="hidden" name="accion" value="guardar">
        <input type="hidden" name="id" value="<?= $editar->id ?? 0 ?>">

        <div class="col-6 col-md-2">
            <label class="form-label requerido" for="numero">Número</label>
            <input class="form-control<?= $claseError("numero") ?>" id="numero" name="numero" maxlength="5" value="<?= e($valor("numero", $editar->numero ?? "")) ?>" required>
            <?= $mensaje("numero") ?>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label requerido" for="piso">Piso</label>
            <input class="form-control<?= $claseError("piso") ?>" type="number" id="piso" name="piso" min="1" max="10" value="<?= e($valor("piso", $editar->piso ?? 1)) ?>" required>
            <?= $mensaje("piso") ?>
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label requerido" for="tipo_id">Tipo</label>
            <select class="form-select<?= $claseError("tipo_id") ?>" id="tipo_id" name="tipo_id" required>
                <option value="">Selecciona...</option>
                <?php foreach ($tipos as $t) : ?>
                    <option value="<?= $t->id ?>" <?= (string) $valor("tipo_id", $editar->tipoId ?? "") === (string) $t->id ? "selected" : "" ?>>
                        <?= e($t->nombre) ?> (<?= soles($t->precioNoche) ?>)
                    </option>
                <?php endforeach ?>
            </select>
            <?= $mensaje("tipo_id") ?>
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label" for="estado">Estado</label>
            <select class="form-select<?= $claseError("estado") ?>" id="estado" name="estado">
                <?php foreach ($estados as $clave => $texto) : ?>
                    <option value="<?= $clave ?>" <?= $valor("estado", $editar->estado ?? "disponible") === $clave ? "selected" : "" ?>><?= $texto ?></option>
                <?php endforeach ?>
            </select>
            <?= $mensaje("estado") ?>
        </div>
        <div class="col-12">
            <label class="form-label" for="descripcion">Descripción (opcional)</label>
            <input class="form-control<?= $claseError("descripcion") ?>" id="descripcion" name="descripcion" maxlength="255" value="<?= e($valor("descripcion", $editar->descripcion ?? "")) ?>">
            <?= $mensaje("descripcion") ?>
        </div>
        <div class="col-12 d-flex gap-2">
            <button class="btn-sa" type="submit"><?= $editar ? "Guardar cambios" : "Registrar habitación" ?></button>
            <?php if ($editar) : ?><a class="btn-linea text-decoration-none" href="<?= url("views/admin/gestion_habitaciones.php") ?>">Cancelar</a><?php endif ?>
        </div>
    </form>
</section>

<div class="sa-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Número</th><th>Piso</th><th>Tipo</th><th class="text-end">Precio / noche</th><th>Estado</th><th>Operaciones</th></tr></thead>
            <tbody>
                <?php foreach ($habitaciones as $h) : ?>
                    <tr>
                        <td class="fw-bold"><?= e($h->numero) ?></td>
                        <td><?= $h->piso ?></td>
                        <td><?= e($h->tipo) ?></td>
                        <td class="text-end"><?= soles($h->precioNoche) ?></td>
                        <td><?= e($estados[$h->estado] ?? $h->estado) ?></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a class="btn btn-sm btn-outline-secondary" href="<?= url("views/admin/gestion_habitaciones.php") ?>?editar=<?= $h->id ?>">Editar</a>
                                <form action="<?= url("procesar.php") ?>" method="post" data-confirmar="¿Eliminar la habitación <?= e($h->numero) ?>?">
                                    <input type="hidden" name="modulo" value="habitacion">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="id" value="<?= $h->id ?>">
                                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . "/../partes/panel_pie.php" ?>
