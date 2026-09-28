<?php
require __DIR__ . "/../config/Autoload.php";

$titulo = "Términos y condiciones";

require __DIR__ . "/../views/partes/cabecera.php";
/** @var array $hotel Datos del hotel (los crea views/partes/ayudas.php) */

hero(
    "Términos y condiciones",
    "Condiciones para reservar y hospedarte en el Hotel Turístico San Antonio.",
    [["Inicio", "index.php"], ["Términos y condiciones", null]]
);
?>
<main class="container py-4 py-lg-5">
    <div class="row">
        <div class="col-12 col-lg-9">
            <h2 class="h5">1. Reservas</h2>
            <p>Puedes reservar sin crear una cuenta, indicando tu nombre completo, tu número de DNI o pasaporte y tu celular. Al confirmar la reserva recibes un código con el que puedes identificarla.</p>
            <p>Al reservar se te asigna una habitación libre del tipo que elegiste y puedes indicar el piso en el que prefieres alojarte. Al confirmar verás el número y el piso de tu habitación. Por causas operativas, como mantenimiento, el hotel podría cambiarla por otra del mismo tipo.</p>

            <h2 class="h5 mt-4">2. Pagos</h2>
            <p>El pago se realiza en línea. Como regla general, se paga el 100 % del monto de la estadía al reservar.</p>
            <p>Los huéspedes frecuentes que reúnen los puntos requeridos por nuestro programa pueden pagar solo una parte al reservar (el porcentaje y los puntos necesarios se muestran en el formulario de reserva) y el saldo restante al llegar al hotel.</p>
            <p>Medios de pago aceptados: Yape, tarjeta Visa y transferencia bancaria.</p>

            <h2 class="h5 mt-4">3. Cancelaciones y reembolsos</h2>
            <p>El pago realizado no es reembolsable. La única excepción es que, al llegar al hotel, la habitación no corresponda a lo publicado en esta web; en ese caso el hotel registra el motivo y procede con el reembolso del pago correspondiente.</p>

            <h2 class="h5 mt-4">4. Programa de puntos</h2>
            <p>Acumulas puntos por tus estadías completadas, que se suman al registrar tu salida (check-out). Los puntos te permiten acceder a beneficios, como el pago fraccionado al reservar. Las condiciones del programa pueden actualizarse; las vigentes se muestran en el formulario de reserva o puedes consultarlas en recepción. Conoce el detalle en la página del <a href="<?= url("publico/puntos.php") ?>">programa de puntos</a>.</p>

            <h2 class="h5 mt-4">5. Llegada y salida</h2>
            <p>Nuestra recepción atiende las 24 horas. Al llegar, debes presentar el documento de identidad con el que realizaste la reserva.</p>

            <h2 class="h5 mt-4">6. Datos personales</h2>
            <p>El tratamiento de tus datos personales se rige por nuestra <a href="<?= url("publico/privacidad.php") ?>">política de privacidad</a>.</p>

            <h2 class="h5 mt-4">7. Consultas y reclamos</h2>
            <p>Puedes escribirnos por WhatsApp al <?= e($hotel["whatsapp_texto"]) ?> o al correo <?= e($hotel["correo"]) ?>. También puedes presentar un reclamo o una queja en nuestro <a href="<?= url("publico/libro_reclamaciones.php") ?>">Libro de Reclamaciones</a>.</p>
            <p>Estos términos se rigen por las leyes de la República del Perú.</p>
        </div>
    </div>
</main>
<?php require __DIR__ . "/../views/partes/pie.php" ?>
