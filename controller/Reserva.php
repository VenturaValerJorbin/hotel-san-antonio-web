<?php

namespace controller;

use bo\Reserva as ReservaBO;

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
            default => $this->resultado(false, "Accion no valida.", "publico/reservar.php"),
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
            // Un solo campo "DNI / Pasaporte": 8 digitos es DNI, cualquier otro formato valido es pasaporte
            "tipo_documento" => preg_match('/^\d{8}$/', $documento) ? "DNI" : "PASAPORTE",
            "numero_documento" => $documento,
            "nombre_completo" => trim($p["nombre_completo"] ?? ""),
            "telefono" => trim($p["telefono"] ?? ""),
            "modalidad_pago" => $p["modalidad_pago"] ?? "",
            "metodo_pago" => $p["metodo_pago"] ?? "",
        ];

        $errores = $this->validar($datos, isset($p["acepta"]));
        if ($errores) {
            return $this->resultado(false, "Revisa los datos marcados en el formulario.", $this->volverAReservar($datos), $errores);
        }

        return $this->ejecutar(
            function () use ($datos) {
                $r = $this->bo->reservar($datos);
                return sprintf(
                    "Reserva %s confirmada. Total S/ %.2f, pagado S/ %.2f, saldo a pagar al llegar S/ %.2f.",
                    $r["codigo"], $r["total"], $r["pagado"], $r["saldo"]
                );
            },
            "",
            "publico/reservar.php",
            $this->volverAReservar($datos)
        );
    }

    private function checkin(array $p): array
    {
        $id = (int) ($p["id"] ?? 0);
        $habitacionId = (int) ($p["habitacion_id"] ?? 0);
        $observaciones = trim($p["observaciones"] ?? "");
        $formulario = "admin/checkin.php?id=$id";

        if ($id <= 0 || $habitacionId <= 0) {
            return $this->resultado(false, "Selecciona la habitacion a asignar.", $formulario);
        }
        if (mb_strlen($observaciones) > 255) {
            return $this->resultado(false, "Las observaciones admiten hasta 255 caracteres.", $formulario);
        }
        return $this->ejecutar(
            fn() => $this->bo->registrarCheckin($id, $habitacionId, $observaciones),
            "Check-in registrado.",
            "admin/reservas.php",
            $formulario
        );
    }

    private function checkout(array $p): array
    {
        $id = (int) ($p["id"] ?? 0);
        if ($id <= 0) {
            return $this->resultado(false, "Datos no validos.", "admin/reservas.php");
        }
        return $this->ejecutar(
            fn() => $this->bo->registrarCheckout($id),
            "Check-out registrado, saldo cobrado y puntos sumados.",
            "admin/reservas.php",
            "admin/reservas.php"
        );
    }

    // Al fallar se vuelve al formulario conservando la habitacion y las fechas elegidas
    private function volverAReservar(array $d): string
    {
        return "publico/reservar.php?" . http_build_query(
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
        if (!$this->fechaValida($d["fecha_ingreso"]) || $d["fecha_ingreso"] < $hoy) {
            $e["fecha_ingreso"] = "Ingresa una fecha de llegada valida (hoy o posterior).";
        }
        if (!$this->fechaValida($d["fecha_salida"]) || $d["fecha_salida"] <= $d["fecha_ingreso"]) {
            $e["fecha_salida"] = "La salida debe ser posterior a la llegada.";
        } elseif ((new \DateTime($d["fecha_ingreso"]))->diff(new \DateTime($d["fecha_salida"]))->days > 30) {
            $e["fecha_salida"] = "La estadia maxima es de 30 noches.";
        }
        if (!preg_match('/^(\d{8}|[A-Z0-9]{6,12})$/', $d["numero_documento"])) {
            $e["numero_documento"] = "Ingresa un DNI (8 digitos) o un pasaporte (6 a 12 letras o numeros).";
        }
        if (!preg_match("/^[\\p{L}][\\p{L} '.\\-]{3,158}$/u", $d["nombre_completo"]) || !str_contains($d["nombre_completo"], " ")) {
            $e["nombre_completo"] = "Escribe tu nombre y apellido (solo letras).";
        }
        if (!preg_match('/^\+?\d{7,15}$/', $d["telefono"])) {
            $e["telefono"] = "Celular no valido (solo numeros, 7 a 15 digitos).";
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
