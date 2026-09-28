<?php
require __DIR__ . "/../config/Autoload.php";

$titulo = "Política de privacidad";

require __DIR__ . "/../views/partes/cabecera.php";

hero(
    "Política de privacidad",
    "Cómo tratamos tus datos personales, conforme a la Ley N.° 29733, Ley de Protección de Datos Personales.",
    [["Inicio", "index.php"], ["Política de privacidad", null]]
);
?>
<main class="container py-4 py-lg-5">
    <div class="row">
        <div class="col-12 col-lg-9">
            <h2 class="h5">1. Responsable del tratamiento</h2>
            <p><?= e($hotel["nombre"]) ?>, con domicilio en <?= e($hotel["direccion"]) ?>. Correo de contacto: <?= e($hotel["correo"]) ?>.</p>

            <h2 class="h5 mt-4">2. Datos que recopilamos</h2>
            <ul>
                <li>Al reservar: nombre completo, tipo y número de documento de identidad, celular, y los datos de la reserva y de sus pagos.</li>
                <li>Al escribirnos por el formulario de contacto: nombre, correo electrónico, celular y el mensaje que envíes.</li>
            </ul>

            <h2 class="h5 mt-4">3. Para qué usamos tus datos</h2>
            <p>Usamos tus datos para gestionar tu reserva y tu estadía, atender tus consultas y reclamos, administrar el programa de puntos y cumplir obligaciones legales.</p>

            <h2 class="h5 mt-4">4. Tu consentimiento</h2>
            <p>Tratamos tus datos con tu consentimiento, que nos das al aceptar los términos y condiciones y esta política al reservar, o al enviar el formulario de contacto.</p>

            <h2 class="h5 mt-4">5. Conservación y terceros</h2>
            <p>Conservamos tus datos mientras sean necesarios para las finalidades indicadas y para cumplir obligaciones legales. No vendemos tus datos personales. Solo podrán compartirse con los proveedores que intervienen en el procesamiento de los pagos, o cuando una norma lo exija.</p>

            <h2 class="h5 mt-4">6. Tus derechos</h2>
            <p>Puedes ejercer tus derechos de acceso, rectificación, cancelación y oposición sobre tus datos personales escribiendo a <?= e($hotel["correo"]) ?> e indicando tu nombre completo y tu número de documento.</p>

            <p class="small text-muted border-top pt-3 mt-4">Texto de referencia elaborado para el proyecto académico. El hotel debe revisarlo y ajustarlo antes de su uso oficial.</p>
        </div>
    </div>
</main>
<?php require __DIR__ . "/../views/partes/pie.php" ?>
