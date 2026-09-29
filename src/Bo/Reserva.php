<?php

namespace App\Bo;

use App\Dao\Conexion;
use App\Dao\Habitacion as HabitacionDAO;
use App\Dao\Huesped as HuespedDAO;
use App\Dao\MovimientoPuntos as MovimientoPuntosDAO;
use App\Dao\Pago as PagoDAO;
use App\Dao\Parametro as ParametroDAO;
use App\Dao\PlanPension as PlanPensionDAO;
use App\Dao\Recompensa as RecompensaDAO;
use App\Dao\Reserva as ReservaDAO;
use App\Dao\TipoHabitacion as TipoHabitacionDAO;
use App\Dto\PlanPension as PlanPensionDTO;
use App\Dto\Reserva as ReservaDTO;

// BO de reservas: reglas del hotel (disponibilidad, puntos, pago 100 % o 50 %)
// y transacciones. Todos los DAO comparten UNA conexion para que el commit/rollBack sea conjunto.
class Reserva
{
    // Cierra sola las reservas "confirmada" cuya fecha de salida ya paso sin que el huesped
    // llegara, y libera esa habitacion. No hay tarea programada en el proyecto: se llama antes
    // de mostrar el panel o de buscar disponibilidad, para que el estado este siempre al dia.
    public function liberarNoShow(): int
    {
        return (new ReservaDAO())->liberarNoShowVencidos();
    }

    // Comprobante publico de una reserva: el huesped la recupera con su codigo y su documento,
    // sin necesidad de haber guardado nada mas (ni de crear una cuenta).
    public function buscarComprobante(string $codigo, string $numeroDocumento): ?array
    {
        $reserva = (new ReservaDAO())->porCodigoYDocumento($codigo, $numeroDocumento);
        if (!$reserva) {
            return null;
        }
        $reserva["pagos"] = (new PagoDAO())->listarPorReserva((int) $reserva["id"]);
        return $reserva;
    }

    public function listar(array $filtros = []): array
    {
        return array_map(
            fn($f) => new ReservaDTO(
                $f["id"], $f["codigo"], $f["huesped"], $f["numero_documento"], $f["habitacion"], $f["tipo"],
                $f["fecha_ingreso"], $f["fecha_salida"], $f["noches"], $f["monto_total"], $f["monto_pagado"],
                $f["saldo_pendiente"], $f["modalidad_pago"], $f["estado"], $f["plan_pension"]
            ),
            (new ReservaDAO())->listar($filtros)
        );
    }

    public function resumen(): array
    {
        return (new ReservaDAO())->resumen();
    }

    // Condiciones del pago fraccionado (puntos necesarios y porcentaje), para mostrarlas en el formulario
    public function beneficioFraccionado(): ?array
    {
        return (new RecompensaDAO())->obtenerPorTipo("pago_fraccionado");
    }

    // Planes de pension (solo alojamiento, desayuno, media pension, pension completa), para el formulario
    public function planesPension(): array
    {
        return array_map(
            fn($p) => new PlanPensionDTO((int) $p["id"], $p["nombre"], $p["descripcion"] ?? "", (float) $p["precio_por_noche"]),
            (new PlanPensionDAO())->listar()
        );
    }

    // Datos de una reserva y habitaciones que se le pueden asignar (pantalla de check-in)
    public function detalleCheckin(int $id): ?array
    {
        $dao = new ReservaDAO();
        $reserva = $dao->detalle($id);
        if (!$reserva) {
            return null;
        }
        $reserva["libres"] = $dao->habitacionesLibres(
            (int) $reserva["tipo_id"], $reserva["fecha_ingreso"], $reserva["fecha_salida"], $id
        );
        return $reserva;
    }

    // Tablero de disponibilidad: para cada habitacion y dia, disponible / reservada / ocupada / mantenimiento
    public function tablero(string $desde, int $dias): array
    {
        $this->liberarNoShow();
        $fechas = array_map(fn($i) => date("Y-m-d", strtotime("$desde +$i day")), range(0, $dias - 1));
        $reservas = (new ReservaDAO())->enRango($fechas[0], end($fechas));

        $filas = [];
        foreach ((new HabitacionDAO())->listar() as $hab) {
            $celdas = array_map(function ($dia) use ($hab, $reservas) {
                if ($hab["estado"] === "mantenimiento") {
                    return "mantenimiento";
                }
                foreach ($reservas as $r) {
                    if ($r["habitacion_id"] == $hab["id"] && $r["fecha_ingreso"] <= $dia && $dia < $r["fecha_salida"]) {
                        return $r["estado"] === "checkin" ? "ocupada" : "reservada";
                    }
                }
                return "disponible";
            }, $fechas);
            $filas[] = ["numero" => $hab["numero"], "tipo" => $hab["tipo"], "celdas" => $celdas];
        }

        // Totales del primer dia del rango: cuantas habitaciones hay en cada estado
        $primerDia = array_count_values(array_map(fn($f) => $f["celdas"][0], $filas));
        return ["fechas" => $fechas, "filas" => $filas, "totales" => $primerDia];
    }

    // Reserva completa: huesped + reserva + pago. Si algo falla, no se guarda nada (ACID).
    public function reservar(array $d): array
    {
        $this->liberarNoShow();
        $conn = (new Conexion())->conectar();
        try {
            $conn->beginTransaction();

            $tipo = (new TipoHabitacionDAO($conn))->obtener($d["tipo_id"])
                ?? throw new \DomainException("El tipo de habitacion no existe.");
            if ($d["num_huespedes"] > $tipo["capacidad"]) {
                throw new \DomainException("La habitacion {$tipo['nombre']} admite hasta {$tipo['capacidad']} persona(s).");
            }

            // Piso de preferencia (0 = sin preferencia). Si se pide un piso y no hay libre, se avisa en vez de asignar otro.
            $piso = (int) ($d["piso"] ?? 0);
            $habitacion = (new ReservaDAO($conn))->habitacionLibre($tipo["id"], $d["fecha_ingreso"], $d["fecha_salida"], $piso)
                ?? throw new \DomainException(
                    $piso > 0
                        ? "No hay habitaciones {$tipo['nombre']} libres en el piso $piso en esas fechas. Elige otro piso o \"Sin preferencia\"."
                        : "No hay habitaciones {$tipo['nombre']} disponibles en esas fechas."
                );

            $plan = (new PlanPensionDAO($conn))->obtener((int) $d["plan_pension_id"])
                ?? throw new \DomainException("El plan de alimentacion elegido no existe.");

            $huespedDao = new HuespedDAO($conn);
            $huesped = $huespedDao->buscarPorDocumento($d["tipo_documento"], $d["numero_documento"]);
            if ($huesped) {
                $huespedId = (int) $huesped["id"];
                $huespedDao->actualizarContacto($huespedId, $d["telefono"], $d["correo"] ?? null);
            } else {
                $huespedId = $huespedDao->insertar($d);
            }

            // Montos: el total se calcula, el adelanto depende de la modalidad elegida.
            // El plan de pension se suma por noche (no por huesped: la reserva hoy no pide cuantos son).
            $noches = (new \DateTime($d["fecha_ingreso"]))->diff(new \DateTime($d["fecha_salida"]))->days;
            $precioPorNoche = $tipo["precio_noche"] + $plan["precio_por_noche"];
            $total = round($noches * $precioPorNoche, 2);

            // Beneficios de huesped frecuente: PERMANENTES (no se gastan puntos, igual que el pago
            // fraccionado), pero el huesped elige cuales usar en ESTA reserva (puede marcar varios
            // a la vez). Nunca se confia en lo que llego marcado desde el formulario: se vuelve a
            // comprobar aqui, con el saldo real, cuales de esos beneficios le corresponden de verdad.
            $idsElegidos = array_map("intval", $d["beneficios"] ?? []);
            $descuento = $this->descuentoPorBeneficios($conn, $huespedId, $total, $idsElegidos, $noches);

            $fraccionado = $d["modalidad_pago"] === "fraccionado";
            $totalConDescuento = round($total - $descuento, 2);
            $adelanto = $fraccionado ? $this->adelantoFraccionado($conn, $huespedId, $totalConDescuento) : $totalConDescuento;

            $reservaDao = new ReservaDAO($conn);
            $codigo = "SA" . strtoupper(bin2hex(random_bytes(4)));
            $reservaId = $reservaDao->insertar([
                "codigo" => $codigo,
                "huesped_id" => $huespedId,
                "habitacion_id" => $habitacion["id"],
                "fecha_ingreso" => $d["fecha_ingreso"],
                "fecha_salida" => $d["fecha_salida"],
                "num_huespedes" => $d["num_huespedes"],
                "precio_noche" => $tipo["precio_noche"],
                "plan_pension_id" => $plan["id"],
                "precio_plan_pension" => $plan["precio_por_noche"],
                "monto_descuento" => $descuento,
                "monto_adelanto" => $adelanto,
                "modalidad_pago" => $d["modalidad_pago"],
                "estado" => "confirmada",
            ]);

            // Pago online (simulado hasta integrar la pasarela real en la Unidad III)
            (new PagoDAO($conn))->insertar([
                "reserva_id" => $reservaId,
                "tipo" => $fraccionado ? "adelanto" : "total",
                "monto" => $adelanto,
                "metodo" => $d["metodo_pago"],
                "estado" => "aprobado",
                "pasarela" => "simulada",
                "codigo_transaccion" => "SIM-" . strtoupper(bin2hex(random_bytes(4))),
                "fecha_pago" => date("Y-m-d H:i:s"),
            ]);

            $conn->commit();
            return [
                "codigo" => $codigo, "total" => $totalConDescuento, "pagado" => $adelanto, "saldo" => round($totalConDescuento - $adelanto, 2),
                "habitacion" => $habitacion["numero"], "piso" => (int) $habitacion["piso"],
            ];
        } catch (\Throwable $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            throw $e;
        }
    }

    // Check-in: asigna la habitacion definitiva, guarda observaciones y ocupa la habitacion
    public function registrarCheckin(int $id, int $habitacionId, string $observaciones): void
    {
        $conn = (new Conexion())->conectar();
        try {
            $conn->beginTransaction();
            $reservaDao = new ReservaDAO($conn);
            $reserva = $reservaDao->detalle($id) ?? throw new \DomainException("La reserva no existe.");
            if ($reserva["estado"] !== "confirmada") {
                throw new \DomainException("Solo se puede hacer check-in de una reserva confirmada.");
            }
            if ($reserva["fecha_ingreso"] > date("Y-m-d")) {
                throw new \DomainException(
                    "Todavia no es la fecha de ingreso de esta reserva ("
                    . date("d/m/Y", strtotime($reserva["fecha_ingreso"])) . ")."
                );
            }

            $libres = $reservaDao->habitacionesLibres(
                (int) $reserva["tipo_id"], $reserva["fecha_ingreso"], $reserva["fecha_salida"], $id
            );
            if (!in_array($habitacionId, array_map("intval", array_column($libres, "id")), true)) {
                throw new \DomainException("La habitacion elegida no esta disponible para esas fechas.");
            }

            $reservaDao->actualizarAsignacion($id, $habitacionId, $observaciones);
            (new HabitacionDAO($conn))->cambiarEstado($habitacionId, "ocupada");
            $reservaDao->cambiarEstado($id, "checkin");
            $conn->commit();
        } catch (\Throwable $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            throw $e;
        }
    }

    // Check-out: cobra el saldo, suma los puntos de la estadia y deja la habitacion en limpieza
    public function registrarCheckout(int $id): void
    {
        $conn = (new Conexion())->conectar();
        try {
            $conn->beginTransaction();
            $reservaDao = new ReservaDAO($conn);
            $reserva = $reservaDao->obtener($id) ?? throw new \DomainException("La reserva no existe.");
            if ($reserva["estado"] !== "checkin") {
                throw new \DomainException("Solo se puede hacer check-out de una reserva con check-in.");
            }

            $this->cerrarEstadia($conn, $reserva);
            (new HabitacionDAO($conn))->cambiarEstado((int) $reserva["habitacion_id"], "limpieza");
            $reservaDao->cambiarEstado($id, "checkout");
            $conn->commit();
        } catch (\Throwable $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            throw $e;
        }
    }

    // Beneficio del programa de puntos: pagar solo un porcentaje como adelanto
    private function adelantoFraccionado(\PDO $conn, int $huespedId, float $total): float
    {
        $beneficio = (new RecompensaDAO($conn))->obtenerPorTipo("pago_fraccionado")
            ?? throw new \DomainException("El pago fraccionado no esta disponible.");
        $puntos = (new MovimientoPuntosDAO($conn))->saldo($huespedId);
        if ($puntos < $beneficio["puntos_requeridos"]) {
            throw new \DomainException(
                "Para pagar el {$beneficio['valor']} % necesitas {$beneficio['puntos_requeridos']} puntos y tienes $puntos."
            );
        }
        return round($total * $beneficio["valor"] / 100, 2);
    }

    // Descuento por huesped frecuente: PERMANENTE, no gasta puntos (a diferencia del canje de un
    // solo uso). Se suman los beneficios de tipo "producto"/"plan_pension" que ya tenga ganados
    // (monto fijo, ej. el desayuno de cortesia), mas el MEJOR porcentaje de descuento que alcance
    // (no se suman varios descuentos, solo el mas alto: el de 500 puntos reemplaza al de 300).
    private function descuentoPorBeneficios(\PDO $conn, int $huespedId, float $total, array $idsElegidos, int $noches): float
    {
        $puntos = (new MovimientoPuntosDAO($conn))->saldo($huespedId);
        $mejorComida = 0.0;    // "producto" y "plan_pension": el mayor ya incluye al menor (la
        $mejorPorcentaje = 0.0; // pension completa ya trae el desayuno), nunca se suman los dos.
        foreach ((new RecompensaDAO($conn))->listarActivas() as $r) {
            $id = (int) $r["id"];
            // El pago fraccionado se elige por su propio campo (modalidad_pago), no aqui.
            if (!in_array($id, $idsElegidos, true) || $r["tipo"] === "pago_fraccionado" || (int) $r["consume_puntos"] === 1) {
                continue;
            }
            if ($puntos < (int) $r["puntos_requeridos"]) {
                continue;   // no se confia en lo marcado desde el formulario: se revisa el saldo real
            }
            // Beneficios con noches minimas (ej. "pension completa" pide 2+): una estadia mas
            // corta no alcanza a justificar un dia entero de comida gratis.
            if ($r["noches_minimas"] !== null && $noches < (int) $r["noches_minimas"]) {
                continue;
            }
            if ($r["tipo"] === "descuento") {
                $mejorPorcentaje = max($mejorPorcentaje, (float) $r["valor"]);
            } elseif (in_array($r["tipo"], ["producto", "plan_pension"], true) && $r["valor"] !== null) {
                $mejorComida = max($mejorComida, (float) $r["valor"]);
            }
        }
        $descuento = round($mejorComida + $total * $mejorPorcentaje / 100, 2);
        return min($descuento, $total);
    }

    // Al salir: se cobra lo que falta y se suman los puntos de la estadia
    private function cerrarEstadia(\PDO $conn, array $reserva): void
    {
        if ($reserva["saldo_pendiente"] > 0) {
            (new PagoDAO($conn))->insertar([
                "reserva_id" => $reserva["id"],
                "tipo" => "saldo",
                "monto" => $reserva["saldo_pendiente"],
                "metodo" => "efectivo",
                "estado" => "aprobado",
                "fecha_pago" => date("Y-m-d H:i:s"),
            ]);
        }
        $solesPorPunto = max(1, (int) (new ParametroDAO($conn))->valor("soles_por_punto"));
        $puntos = intdiv((int) $reserva["monto_total"], $solesPorPunto);
        if ($puntos > 0) {
            (new MovimientoPuntosDAO($conn))->insertar(
                (int) $reserva["huesped_id"], (int) $reserva["id"], "ganado", $puntos, "Estadia {$reserva['codigo']}"
            );
        }
    }
}
