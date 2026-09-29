<?php

namespace App\Bo;

use App\Dao\Conexion;
use App\Dao\Habitacion as HabitacionDAO;
use App\Dao\Huesped as HuespedDAO;
use App\Dao\MovimientoPuntos as MovimientoPuntosDAO;
use App\Dao\Pago as PagoDAO;
use App\Dao\Parametro as ParametroDAO;
use App\Dao\Recompensa as RecompensaDAO;
use App\Dao\Reserva as ReservaDAO;
use App\Dao\TipoHabitacion as TipoHabitacionDAO;
use App\Dto\Reserva as ReservaDTO;

// BO de reservas: reglas del hotel (disponibilidad, puntos, pago 100 % o 50 %)
// y transacciones. Todos los DAO comparten UNA conexion para que el commit/rollBack sea conjunto.
class Reserva
{
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
                $f["saldo_pendiente"], $f["modalidad_pago"], $f["estado"]
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

            $huespedDao = new HuespedDAO($conn);
            $huesped = $huespedDao->buscarPorDocumento($d["tipo_documento"], $d["numero_documento"]);
            if ($huesped) {
                $huespedId = (int) $huesped["id"];
                $huespedDao->actualizarTelefono($huespedId, $d["telefono"]);
            } else {
                $huespedId = $huespedDao->insertar($d);
            }

            // Montos: el total se calcula, el adelanto depende de la modalidad elegida
            $noches = (new \DateTime($d["fecha_ingreso"]))->diff(new \DateTime($d["fecha_salida"]))->days;
            $total = round($noches * $tipo["precio_noche"], 2);
            $fraccionado = $d["modalidad_pago"] === "fraccionado";
            $adelanto = $fraccionado ? $this->adelantoFraccionado($conn, $huespedId, $total) : $total;

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
                "codigo" => $codigo, "total" => $total, "pagado" => $adelanto, "saldo" => round($total - $adelanto, 2),
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
