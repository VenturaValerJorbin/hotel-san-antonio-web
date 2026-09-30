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

// Direccion base del proyecto (ej. /hotel_san_antonio_web/). Las paginas viven en la raiz o en
// views/publico/ o views/admin/, asi que se quita esa subcarpeta para que todos los enlaces partan siempre de la raiz.
$base = rtrim(preg_replace("#/views/(publico|admin)$#", "", str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"]))), "/") . "/";

// Arma un enlace desde la raiz del proyecto: url("views/publico/reservar.php"), url("assets/css/estilo.css")
function url(string $ruta): string
{
    global $base;
    return $base . $ruta;
}

// Enlace a un CSS/JS propio con la fecha de su ultima modificacion (?v=...): cuando el archivo cambia,
// el navegador descarga la version nueva en vez de usar la que tiene guardada.
function recurso(string $ruta): string
{
    $archivo = dirname(__DIR__, 2) . "/" . $ruta;
    return url($ruta) . (is_file($archivo) ? "?v=" . filemtime($archivo) : "");
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

// Escribe una lista de pisos: [1] -> "Piso 1", [1, 3] -> "Pisos 1 y 3", [1, 2, 3] -> "Pisos 1, 2 y 3"
function textoPisos(array $pisos): string
{
    if (count($pisos) <= 1) {
        return $pisos ? "Piso " . $pisos[0] : "";
    }
    $ultimo = array_pop($pisos);
    return "Pisos " . implode(", ", $pisos) . " y " . $ultimo;
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

// Foto real si existe la ruta (relativa a la raiz del proyecto, como todos los enlaces del sitio);
// si no hay foto, un marcador con el mismo tamano
function foto(?string $ruta, string $alt, string $icono = "bi-image"): void
{
    echo $ruta
        ? '<img class="sa-foto" src="' . e(url($ruta)) . '" alt="' . e($alt) . '" loading="lazy">'
        : '<div class="sa-foto" role="img" aria-label="' . e($alt) . '"><i class="bi ' . e($icono) . '"></i></div>';
}

// Carrusel de fotos (usa el componente Carousel de Bootstrap, que ya se carga en pie.php).
// Si no hay fotos o hay solo una, cae en foto() en vez de armar el carrusel.
function carrusel(array $fotos, string $alt, string $icono = "bi-image", string $idBase = "carrusel"): void
{
    if (!$fotos) {
        foto(null, $alt, $icono);
        return;
    }
    if (count($fotos) === 1) {
        foto($fotos[0], $alt, $icono);
        return;
    }
    $id = $idBase . "-" . substr(md5($alt . implode("", $fotos)), 0, 8);
    echo '<div id="' . $id . '" class="carousel slide sa-carrusel" data-bs-ride="carousel">';
    echo '<div class="carousel-indicators">';
    foreach ($fotos as $i => $f) {
        echo '<button type="button" data-bs-target="#' . $id . '" data-bs-slide-to="' . $i . '"'
            . ($i === 0 ? ' class="active" aria-current="true"' : '')
            . ' aria-label="Foto ' . ($i + 1) . '"></button>';
    }
    echo '</div><div class="carousel-inner">';
    foreach ($fotos as $i => $f) {
        echo '<div class="carousel-item' . ($i === 0 ? ' active' : '') . '">';
        echo '<img class="sa-foto" src="' . e(url($f)) . '" alt="' . e($alt) . ' - foto ' . ($i + 1) . '" loading="lazy">';
        echo '</div>';
    }
    echo '</div>';
    echo '<button class="carousel-control-prev" type="button" data-bs-target="#' . $id . '" data-bs-slide="prev">'
        . '<span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Anterior</span></button>';
    echo '<button class="carousel-control-next" type="button" data-bs-target="#' . $id . '" data-bs-slide="next">'
        . '<span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Siguiente</span></button>';
    echo '</div>';
}

function hero(string $titulo, string $texto = "", array $migas = [], string $etiqueta = "", string $claseExtra = ""): void
{
    echo '<section class="sa-hero' . ($claseExtra ? ' ' . e($claseExtra) : '') . '"><div class="container">';

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
    carrusel($t->fotos, "Habitación " . $t->nombre, "bi-house-heart", "tarjeta-" . $t->id);
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
    echo '<a class="btn-linea d-block text-center text-decoration-none" href="' . e(url("views/publico/habitacion.php")) . '?id=' . $t->id . $sufijo . '">Ver detalle</a>';
    echo '</div></div></article></div>';
}