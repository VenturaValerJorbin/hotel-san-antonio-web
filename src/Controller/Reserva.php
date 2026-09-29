<?php

namespace App\Controller;

use App\Bo\Reserva as ReservaBO;

// Controlador de reservas: valida el formulario y delega la reserva, el check-in y el check-out al BO.
class Reserva extends Controlador
{
    private ReservaBO $bo;

    public function __construct()
    {
        $this->bo = new ReservaBO();
    }

    public function manejar(string $accion, array $post): array
    {
        return match ($accion) {
            "crear" => $this->crear($post),
            "checkin" => $this->checkin($post),
            "checkout" => $this->checkout($post),
            default => $this->resultado(false, "Accion no valida.", "views/publico/reservar.php"),
        };
    }

    private function crear(array $p): array
    {
        $documento = strtoupper(trim($p["numero_documento"] ?? ""));
        $datos = [
            "tipo_id" => (int) ($p["tipo_id"] ?? 0),
            "fecha_ingreso" => $p["fecha_ingreso"] ?? "",
            "fecha_salida" => $p["fecha_salida"] ?? "",
            "num_huespedes" => 1,
            "piso" => (int) ($p["piso"] ?? 0),   // 0 = sin preferencia
            "plan_pension_id" => (int) ($p["plan_pension_id"] ?? 0),
            // El huesped elige el tipo; cada uno tiene su propio formato valido (ver validar())
            "tipo_documento" => $p["tipo_documento"] ?? "",
            "numero_documento" => $documento,
            "nombre_completo" => trim($p["nombre_completo"] ?? ""),
            "telefono" => trim($p["telefono"] ?? ""),
            "correo" => trim($p["correo"] ?? "") ?: null,   // opcional
            "modalidad_pago" => $p["modalidad_pago"] ?? "",
            "metodo_pago" => $p["metodo_pago"] ?? "",
            // Beneficios de huesped frecuente que eligio usar en esta reserva (el BO vuelve a
            // comprobar contra el saldo real; esto es solo lo que el formulario mando marcado).
            "beneficios" => array_map("intval", $p["beneficios"] ?? []),
        ];

        $errores = $this->validar($datos, isset($p["acepta"]));
        if ($errores) {
            return $this->resultado(false, "Revisa los datos marcados en el formulario.", $this->volverAReservar($datos), $errores);
        }

        // Al confirmar, se redirige al comprobante (no a un mensaje que desaparece al recargar):
        // con el codigo y su documento, el huesped puede volver a verlo o imprimirlo cuando quiera.
        return $this->ejecutar(
            fn() => $this->bo->reservar($datos),
            "Reserva confirmada. Aquí tienes tu comprobante.",
            fn($r) => "views/publico/comprobante.php?" . http_build_query([
                "codigo" => $r["codigo"],
                "numero_documento" => $datos["numero_documento"],
            ]),
            $this->volverAReservar($datos)
        );
    }

    private function checkin(array $p): array
    {
        $id = (int) ($p["id"] ?? 0);
        $habitacionId = (int) ($p["habitacion_id"] ?? 0);
        $observaciones = trim($p["observaciones"] ?? "");
        $formulario = "views/admin/checkin.php?id=$id";

        if ($id <= 0 || $habitacionId <= 0) {
            return $this->resultado(false, "Selecciona la habitacion a asignar.", $formulario);
        }
        if (mb_strlen($observaciones) > 255) {
            return $this->resultado(false, "Las observaciones admiten hasta 255 caracteres.", $formulario);
        }
        return $this->ejecutar(
            fn() => $this->bo->registrarCheckin($id, $habitacionId, $observaciones),
            "Check-in registrado.",
            "views/admin/reservas.php",
            $formulario
        );
    }

    private function checkout(array $p): array
    {
        $id = (int) ($p["id"] ?? 0);
        if ($id <= 0) {
            return $this->resultado(false, "Datos no validos.", "views/admin/reservas.php");
        }
        return $this->ejecutar(
            fn() => $this->bo->registrarCheckout($id),
            "Check-out registrado, saldo cobrado y puntos sumados.",
            "views/admin/reservas.php",
            "views/admin/reservas.php"
        );
    }

    // Al fallar se vuelve al formulario conservando la habitacion y las fechas elegidas
    private function volverAReservar(array $d): string
    {
        return "views/publico/reservar.php?" . http_build_query(
            array_filter(["tipo" => $d["tipo_id"], "ingreso" => $d["fecha_ingreso"], "salida" => $d["fecha_salida"]])
        );
    }

    private function validar(array $d, bool $acepta): array
    {
        $e = [];
        $hoy = date("Y-m-d");

        if ($d["tipo_id"] <= 0) {
            $e["tipo_id"] = "Selecciona una habitacion.";
        }
        if ($d["piso"] < 0 || $d["piso"] > 9) {
            $e["piso"] = "Elige un piso valido.";
        }
        if ($d["plan_pension_id"] <= 0) {
            $e["plan_pension_id"] = "Elige un plan de alimentacion.";
        }
        if (!$this->fechaValida($d["fecha_ingreso"]) || $d["fecha_ingreso"] < $hoy) {
            $e["fecha_ingreso"] = "Ingresa una fecha de llegada valida (hoy o posterior).";
        }
        if (!$this->fechaValida($d["fecha_salida"]) || $d["fecha_salida"] <= $d["fecha_ingreso"]) {
            $e["fecha_salida"] = "La salida debe ser posterior a la llegada.";
        } elseif ((new \DateTime($d["fecha_ingreso"]))->diff(new \DateTime($d["fecha_salida"]))->days > 30) {
            $e["fecha_salida"] = "La estadia maxima es de 30 noches.";
        }
        // El formato valido del numero depende del tipo elegido; un DNI de puros digitos
        // no debe poder colarse como si fuera un pasaporte (y viceversa).
        if (!in_array($d["tipo_documento"], ["DNI", "PASAPORTE"], true)) {
            $e["tipo_documento"] = "Elige un tipo de documento.";
        } elseif ($d["tipo_documento"] === "DNI") {
            // Ademas del formato, se rechazan los 8 digitos repetidos (00000000, 11111111...):
            // ningun DNI real es asi, es el truco mas comun para colar un documento inventado.
            if (!preg_match('/^(?!(\d)\1{7}$)\d{8}$/', $d["numero_documento"])) {
                $e["numero_documento"] = "Ingresa un DNI real de 8 digitos.";
            }
        } elseif (!preg_match('/^(?=.*[A-Z])[A-Z0-9]{6,12}$/', $d["numero_documento"])) {
            $e["numero_documento"] = "El pasaporte debe tener de 6 a 12 letras o numeros, con al menos una letra.";
        }
        if (!preg_match("/^[\\p{L}][\\p{L} '.\\-]{3,158}$/u", $d["nombre_completo"]) || !str_contains($d["nombre_completo"], " ")) {
            $e["nombre_completo"] = "Escribe tu nombre y apellido (solo letras).";
        }
        if (!preg_match('/^\+?\d{7,15}$/', $d["telefono"])) {
            $e["telefono"] = "Celular no valido (solo numeros, 7 a 15 digitos).";
        }
        if ($d["correo"] !== null && !filter_var($d["correo"], FILTER_VALIDATE_EMAIL)) {
            $e["correo"] = "Ingresa un correo valido, o deja el campo vacio.";
        }
        if (!in_array($d["modalidad_pago"], ["completo", "fraccionado"], true)) {
            $e["modalidad_pago"] = "Elige como pagar.";
        }
        if (!in_array($d["metodo_pago"], ["tarjeta", "yape", "transferencia"], true)) {
            $e["metodo_pago"] = "Elige un metodo de pago.";
        }
        if (!$acepta) {
            $e["acepta"] = "Debes aceptar los terminos y condiciones.";
        }
        return $e;
    }
}
