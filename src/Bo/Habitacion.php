<?php

namespace App\Bo;

use App\Dao\Habitacion as HabitacionDAO;
use App\Dao\TipoHabitacion as TipoHabitacionDAO;
use App\Dto\Habitacion as HabitacionDTO;
use App\Dto\TipoHabitacion as TipoHabitacionDTO;

// BO: reglas de negocio y conversion de los arreglos del DAO a objetos DTO.
class Habitacion
{
    private HabitacionDAO $dao;

    public function __construct()
    {
        $this->dao = new HabitacionDAO();
    }

    public function listar(): array
    {
        return array_map(fn($f) => $this->aDto($f), $this->dao->listar());
    }

    public function obtener(int $id): ?HabitacionDTO
    {
        $fila = $this->dao->obtener($id);
        return $fila ? $this->aDto($fila) : null;
    }

    // Tipos de habitacion con sus servicios. Si se dan fechas, indica cuantas habitaciones hay libres.
    public function tipos(?string $ingreso = null, ?string $salida = null): array
    {
        $tipoDao = new TipoHabitacionDAO();
        $libres = ($ingreso && $salida) ? $tipoDao->disponibles($ingreso, $salida) : null;

        return array_map(
            fn($t) => $this->aTipoDto($tipoDao, $t, $libres === null ? null : (int) ($libres[$t["id"]] ?? 0)),
            $tipoDao->listar()
        );
    }

    public function tipo(int $id): ?TipoHabitacionDTO
    {
        $tipoDao = new TipoHabitacionDAO();
        $fila = $tipoDao->obtener($id);
        return $fila ? $this->aTipoDto($tipoDao, $fila) : null;
    }

    // Crea (id = 0) o actualiza (id > 0). Regla de negocio: el numero de habitacion es unico.
    public function guardar(int $id, array $datos): void
    {
        if ($this->dao->existeNumero($datos["numero"], $id)) {
            throw new \DomainException("Ya existe una habitacion con el numero {$datos['numero']}.");
        }
        $id > 0 ? $this->dao->actualizar($id, $datos) : $this->dao->insertar($datos);
    }

    public function eliminar(int $id): void
    {
        try {
            $this->dao->eliminar($id);
        } catch (\PDOException $e) {
            // 23000 = violacion de llave foranea: la habitacion ya tiene reservas
            if ($e->getCode() === "23000") {
                throw new \DomainException("No se puede eliminar: la habitacion tiene reservas registradas.");
            }
            throw $e;
        }
    }

    private function aDto(array $f): HabitacionDTO
    {
        return new HabitacionDTO(
            $f["id"], $f["numero"], $f["piso"], $f["tipo_id"],
            $f["tipo"], $f["precio_noche"], $f["estado"], $f["descripcion"] ?? ""
        );
    }

    private function aTipoDto(TipoHabitacionDAO $dao, array $t, ?int $libres = null): TipoHabitacionDTO
    {
        return new TipoHabitacionDTO(
            $t["id"], $t["nombre"], $t["descripcion"], $t["detalle"] ?? "", $t["capacidad"], $t["precio_noche"],
            $dao->servicios($t["id"]), $dao->fotos($t["id"]), $libres, $dao->pisos($t["id"])
        );
    }
}
