<?php

namespace App\Dao;

class Reserva extends Dao
{
    // Busca una habitacion del tipo pedido (y del piso pedido, si $piso > 0) sin cruce de fechas con otras reservas activas.
    // Devuelve id, numero y piso, o null si no hay. Es la primera libre por numero.
    // FOR UPDATE bloquea la fila dentro de la transaccion: evita reservar la misma habitacion dos veces.
    public function habitacionLibre(int $tipoId, string $ingreso, string $salida, int $piso = 0): ?array
    {
        $filtroPiso = $piso > 0 ? "AND h.piso = :piso" : "";   // fragmento fijo: el valor del piso viaja como parametro
        $stmt = $this->conn->prepare(
            "SELECT h.id, h.numero, h.piso FROM habitacion h
             WHERE h.tipo_id = :tipo AND h.activo = 1 AND h.estado <> 'mantenimiento' $filtroPiso
               AND NOT EXISTS (
                   SELECT 1 FROM reserva r
                   WHERE r.habitacion_id = h.id
                     AND r.estado IN ('pendiente','confirmada','checkin')
                     AND r.fecha_ingreso < :salida AND r.fecha_salida > :ingreso)
             ORDER BY h.numero LIMIT 1 FOR UPDATE"
        );
        $valores = [":tipo" => $tipoId, ":ingreso" => $ingreso, ":salida" => $salida];
        if ($piso > 0) {
            $valores[":piso"] = $piso;
        }
        $this->enlazar($stmt, $valores);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    // Habitaciones del tipo libres en las fechas de una reserva, sin contar la propia reserva (para asignar en el check-in)
    public function habitacionesLibres(int $tipoId, string $ingreso, string $salida, int $excluirReservaId): array
    {
        $stmt = $this->conn->prepare(
            "SELECT h.id, h.numero, h.piso FROM habitacion h
             WHERE h.tipo_id = :tipo AND h.activo = 1 AND h.estado <> 'mantenimiento'
               AND NOT EXISTS (
                   SELECT 1 FROM reserva r
                   WHERE r.habitacion_id = h.id AND r.id <> :excluir
                     AND r.estado IN ('pendiente','confirmada','checkin')
                     AND r.fecha_ingreso < :salida AND r.fecha_salida > :ingreso)
             ORDER BY h.numero"
        );
        $this->enlazar($stmt, [":tipo" => $tipoId, ":excluir" => $excluirReservaId, ":ingreso" => $ingreso, ":salida" => $salida]);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function insertar(array $d): int
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO reserva (codigo, huesped_id, habitacion_id, fecha_ingreso, fecha_salida,
                                  num_huespedes, precio_noche, plan_pension_id, precio_plan_pension,
                                  monto_descuento, monto_adelanto, modalidad_pago, estado)
             VALUES (:codigo, :huesped_id, :habitacion_id, :fecha_ingreso, :fecha_salida,
                     :num_huespedes, :precio_noche, :plan_pension_id, :precio_plan_pension,
                     :monto_descuento, :monto_adelanto, :modalidad_pago, :estado)"
        );
        $this->enlazar($stmt, [
            ":codigo" => $d["codigo"],
            ":huesped_id" => $d["huesped_id"],
            ":habitacion_id" => $d["habitacion_id"],
            ":fecha_ingreso" => $d["fecha_ingreso"],
            ":fecha_salida" => $d["fecha_salida"],
            ":num_huespedes" => $d["num_huespedes"],
            ":precio_noche" => $d["precio_noche"],
            ":plan_pension_id" => $d["plan_pension_id"],
            ":precio_plan_pension" => $d["precio_plan_pension"],
            ":monto_descuento" => $d["monto_descuento"],
            ":monto_adelanto" => $d["monto_adelanto"],
            ":modalidad_pago" => $d["modalidad_pago"],
            ":estado" => $d["estado"],
        ]);
        $stmt->execute();
        return (int) $this->conn->lastInsertId();
    }

    // Lee de la vista, que ya calcula noches, total y saldo pendiente.
    // Filtros opcionales: buscar (nombre o documento), ingreso (fecha) y estado.
    public function listar(array $filtros = []): array
    {
        $donde = [];
        $valores = [];
        if (($filtros["buscar"] ?? "") !== "") {
            $donde[] = "(hu.nombre_completo LIKE :nombre OR hu.numero_documento LIKE :documento)";
            $valores[":nombre"] = $valores[":documento"] = "%" . $filtros["buscar"] . "%";
        }
        if (($filtros["ingreso"] ?? "") !== "") {
            $donde[] = "v.fecha_ingreso = :ingreso";
            $valores[":ingreso"] = $filtros["ingreso"];
        }
        if (($filtros["estado"] ?? "") !== "") {
            $donde[] = "v.estado = :estado";
            $valores[":estado"] = $filtros["estado"];
        }

        $stmt = $this->conn->prepare(
            "SELECT v.id, v.codigo, hu.nombre_completo AS huesped, hu.numero_documento,
                    hab.numero AS habitacion, t.nombre AS tipo, pp.nombre AS plan_pension,
                    v.fecha_ingreso, v.fecha_salida, v.noches,
                    v.monto_total, v.monto_pagado, v.saldo_pendiente, v.modalidad_pago, v.estado
             FROM vista_reserva v
             JOIN huesped hu ON hu.id = v.huesped_id
             JOIN habitacion hab ON hab.id = v.habitacion_id
             JOIN tipo_habitacion t ON t.id = hab.tipo_id
             JOIN plan_pension pp ON pp.id = v.plan_pension_id"
            . ($donde ? " WHERE " . implode(" AND ", $donde) : "") .
            " ORDER BY v.fecha_ingreso DESC, v.id DESC"
        );
        $this->enlazar($stmt, $valores);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Cifras del encabezado del panel de recepcion
    public function resumen(): array
    {
        $stmt = $this->conn->prepare(
            "SELECT COALESCE(SUM(estado = 'confirmada'), 0) AS por_llegar,
                    COALESCE(SUM(estado = 'confirmada' AND fecha_ingreso = CURDATE()), 0) AS llegadas_hoy,
                    COALESCE(SUM(estado IN ('confirmada','checkin') AND saldo_pendiente > 0), 0) AS con_saldo
             FROM vista_reserva"
        );
        $stmt->execute();
        return $stmt->fetch();
    }

    // Reservas activas que tocan un rango de fechas (para el tablero de disponibilidad)
    public function enRango(string $desde, string $hasta): array
    {
        $stmt = $this->conn->prepare(
            "SELECT habitacion_id, fecha_ingreso, fecha_salida, estado FROM reserva
             WHERE estado IN ('pendiente','confirmada','checkin')
               AND fecha_ingreso <= :hasta AND fecha_salida > :desde"
        );
        $this->enlazar($stmt, [":desde" => $desde, ":hasta" => $hasta]);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtener(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM vista_reserva WHERE id = :id");
        $this->enlazar($stmt, [":id" => $id]);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    // Reserva con los datos del huesped y de la habitacion (pantalla de check-in)
    public function detalle(int $id): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT v.*, hu.nombre_completo, hu.tipo_documento, hu.numero_documento, hu.telefono, hu.correo,
                    hab.numero AS habitacion, hab.tipo_id, t.nombre AS tipo, pp.nombre AS plan_pension
             FROM vista_reserva v
             JOIN huesped hu ON hu.id = v.huesped_id
             JOIN habitacion hab ON hab.id = v.habitacion_id
             JOIN tipo_habitacion t ON t.id = hab.tipo_id
             JOIN plan_pension pp ON pp.id = v.plan_pension_id
             WHERE v.id = :id"
        );
        $this->enlazar($stmt, [":id" => $id]);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    // Comprobante publico: el huesped se identifica con el codigo Y su documento (dos datos que
    // solo el deberia tener), asi nadie puede ver una reserva ajena solo adivinando el codigo.
    public function porCodigoYDocumento(string $codigo, string $numeroDocumento): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT v.*, hu.nombre_completo, hu.tipo_documento, hu.numero_documento, hu.telefono, hu.correo,
                    hab.numero AS habitacion, hab.piso, t.nombre AS tipo, pp.nombre AS plan_pension
             FROM vista_reserva v
             JOIN huesped hu ON hu.id = v.huesped_id
             JOIN habitacion hab ON hab.id = v.habitacion_id
             JOIN tipo_habitacion t ON t.id = hab.tipo_id
             JOIN plan_pension pp ON pp.id = v.plan_pension_id
             WHERE v.codigo = :codigo AND hu.numero_documento = :documento"
        );
        $this->enlazar($stmt, [":codigo" => $codigo, ":documento" => $numeroDocumento]);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    public function actualizarAsignacion(int $id, int $habitacionId, string $observaciones): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE reserva SET habitacion_id = :habitacion, observaciones = :obs WHERE id = :id"
        );
        $this->enlazar($stmt, [":habitacion" => $habitacionId, ":obs" => $observaciones ?: null, ":id" => $id]);
        return $stmt->execute();
    }

    public function cambiarEstado(int $id, string $estado): bool
    {
        $stmt = $this->conn->prepare("UPDATE reserva SET estado = :estado WHERE id = :id");
        $this->enlazar($stmt, [":estado" => $estado, ":id" => $id]);
        return $stmt->execute();
    }

    // "No llegó": el huesped tenia hasta su propia fecha de salida para presentarse (pudo llegar
    // tarde dentro de su estadia). Si ese plazo ya paso y nunca hizo check-in, se cierra sola la
    // reserva y la habitacion queda libre para otras fechas (no vuelve a contar como ocupada,
    // porque habitacionLibre() solo bloquea por 'pendiente', 'confirmada' o 'checkin').
    // No hay tarea programada en el proyecto, asi que esto se ejecuta "de paso" justo antes de
    // consultar disponibilidad o listar reservas, en vez de esperar un proceso aparte.
    public function liberarNoShowVencidos(): int
    {
        $stmt = $this->conn->prepare(
            "UPDATE reserva SET estado = 'no_show' WHERE estado = 'confirmada' AND fecha_salida < CURDATE()"
        );
        $stmt->execute();
        return $stmt->rowCount();
    }
}
