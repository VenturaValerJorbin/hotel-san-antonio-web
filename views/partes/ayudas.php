<?php
// Utilidades comunes de las vistas: sesion, mensajes flash y funciones para imprimir HTML.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Mensaje y datos que dejo procesar.php en la peticion anterior (se muestran una sola vez)
$flash = $_SESSION["flash"] ?? null;
$errores = $_SESSION["errores"] ?? [];
$antiguo = $_SESSION["antiguo"] ?? [];
unset($_SESSION["flash"], $_SESSION["errores"], $_SESSION["antiguo"]);

$hotel = require dirname(__DIR__, 2) . "/config/hotel.php";

// Direccion base del proyecto (ej. /hotel_san_antonio_web/). Las paginas viven en la raiz, en publico/ o en admin/,
// asi que se quita esa subcarpeta para que todos los enlaces partan siempre de la raiz.
$base = rtrim(preg_replace("#/(publico|admin)$#", "", str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"]))), "/") . "/";

// Arma un enlace desde la raiz del proyecto: url("publico/reservar.php"), url("assets/css/estilo.css")
function url(string $ruta): string
{
    global $base;
    return $base . $ruta;
}

// Escapa texto antes de imprimirlo en HTML (evita XSS)
function e($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8");
}

function soles($valor): string
{
    return "S/ " . number_format((float) $valor, 2);
}

// Lee una fecha valida (Y-m-d) de la URL; si no es valida devuelve ""
function fechaGet(string $clave): string
{
    $valor = $_GET[$clave] ?? "";
    $fecha = DateTime::createFromFormat("!Y-m-d", $valor);
    return ($fecha && $fecha->format("Y-m-d") === $valor) ? $valor : "";
}

function fechaLarga(string $fecha): string
{
    return date("d/m/Y", strtotime($fecha));
}

// Foto real si existe la ruta; si no, un marcador con el mismo tamano
function foto(?string $ruta, string $alt, string $icono = "bi-image"): void
{
    echo $ruta
        ? '<img class="sa-foto" src="' . e($ruta) . '" alt="' . e($alt) . '" loading="lazy">'
        : '<div class="sa-foto" role="img" aria-label="' . e($alt) . '"><i class="bi ' . e($icono) . '"></i></div>';
}

// Banda superior de cada pagina publica: migas de pan, titulo y texto
function hero(string $titulo, string $texto = "", array $migas = [], string $etiqueta = ""): void
{
    echo '<section class="sa-hero"><div class="container">';
    if ($migas) {
        echo '<div class="migas mb-2">';
        foreach ($migas as $indice => [$nombre, $url]) {
            echo $indice ? " / " : "";
            echo $url ? '<a href="' . e(url($url)) . '">' . e($nombre) . "</a>" : '<span class="actual">' . e($nombre) . "</span>";
        }
        echo "</div>";
    }
    if ($etiqueta) {
        echo '<div class="etiqueta mb-2">' . e($etiqueta) . "</div>";
    }
    echo '<h1>' . e($titulo) . "</h1>";
    if ($texto) {
        echo '<p class="mt-2 mb-0">' . e($texto) . "</p>";
    }
    echo "</div></section>";
}

// Tarjeta de un tipo de habitacion (listado y destacadas)
function tarjetaTipo(\App\Dto\TipoHabitacion $t, string $consulta = ""): void
{
    $sufijo = $consulta ? "&" . $consulta : "";
    echo '<div class="col-12 col-md-6 col-lg-4"><article class="sa-card h-100 d-flex flex-column">';
    echo '<div class="p-2 pb-0">';
    foto($t->fotos[0] ?? null, "Habitación " . $t->nombre, "bi-house-heart");
    echo '</div><div class="sa-card-cuerpo d-flex flex-column flex-grow-1">';
    echo '<div class="d-flex justify-content-between align-items-start gap-2"><div>';
    echo '<h3 class="h5 mb-1">' . e($t->nombre) . '</h3>';
    echo '<div class="text-muted small"><i class="bi bi-people"></i> ' . $t->capacidad . ($t->capacidad === 1 ? " persona" : " personas") . '</div>';
    echo '</div><div class="sa-precio">' . soles($t->precioNoche) . '<small>por noche</small></div></div>';
    echo '<div class="my-3">';
    foreach (array_slice($t->servicios, 0, 3) as $s) {
        echo '<span class="sa-servicio"><i class="bi ' . e($s["icono"]) . '"></i>' . e($s["nombre"]) . '</span>';
    }
    echo '</div><div class="mt-auto">';
    if ($t->libres !== null) {
        echo '<div class="small mb-2 ' . ($t->libres > 0 ? "text-success" : "text-danger") . ' fw-bold">'
            . ($t->libres > 0 ? $t->libres . " disponible(s) en tus fechas" : "Sin disponibilidad en tus fechas") . '</div>';
    }
    echo '<a class="btn-linea d-block text-center text-decoration-none" href="' . e(url("publico/habitacion.php")) . '?id=' . $t->id . $sufijo . '">Ver detalle</a>';
    echo '</div></div></article></div>';
}
