<?php
require __DIR__ . "/../config/Autoload.php";

$titulo = "Libro de Reclamaciones";

require __DIR__ . "/../views/partes/cabecera.php";

hero(
    "Libro de Reclamaciones",
    "Tu opinión nos ayuda a mejorar. Puedes presentar un reclamo o una queja.",
    [["Inicio", "index.php"], ["Libro de Reclamaciones", null]]
);
?>
<main class="container py-4 py-lg-5">
    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <h2 class="h5">¿Qué puedes presentar?</h2>
            <ul>
                <li><strong>Reclamo:</strong> tu disconformidad con los productos o servicios que contrataste.</li>
                <li><strong>Queja:</strong> tu malestar por la atención al público, sin relación con el producto o servicio contratado.</li>
            </ul>

            <h2 class="h5 mt-4">¿Qué datos debes indicar?</h2>
            <p>Tu nombre completo, tu número de documento, tu celular y correo, el servicio contratado (por ejemplo, tu reserva o tu consumo en el restaurante), el detalle de tu reclamo o queja, y lo que solicitas.</p>

            <h2 class="h5 mt-4">¿En cuánto tiempo respondemos?</h2>
            <p>De acuerdo con la normativa de protección al consumidor, el proveedor debe responder los reclamos en un plazo máximo de 15 días hábiles.</p>
            <p>Presentar un reclamo no impide acudir a otras vías de solución de controversias, ni es requisito previo para interponer una denuncia ante el Indecopi.</p>

            <p class="small text-muted border-top pt-3 mt-4">Conforme al Código de Protección y Defensa del Consumidor (Ley N.° 29571) y al Reglamento del Libro de Reclamaciones (D. S. N.° 011-2011-PCM).</p>
        </div>

        <div class="col-12 col-lg-4">
            <aside class="sa-card sa-card-cuerpo resumen-fijo">
                <h2 class="h5"><i class="bi bi-journal-bookmark text-danger"></i> Presenta tu reclamo</h2>
                <p class="small text-muted">Completa el formulario de contacto con el asunto "Reclamo" y cuéntanos qué ocurrió.</p>
                <a class="btn-sa d-block text-center text-decoration-none" href="<?= url("publico/contacto.php") ?>?asunto=reclamo">Ir al formulario</a>
                <hr>
                <p class="small mb-1"><strong>Otros canales</strong></p>
                <p class="small mb-0"><i class="bi bi-whatsapp text-danger"></i> WhatsApp <?= e($hotel["whatsapp_texto"]) ?><br><i class="bi bi-envelope text-danger"></i> <?= e($hotel["correo"]) ?></p>
            </aside>
        </div>
    </div>
</main>
<?php require __DIR__ . "/../views/partes/pie.php" ?>
