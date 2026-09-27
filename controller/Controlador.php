<?php

namespace controller;

// Base comun de los controladores: todos responden con el mismo formato.
abstract class Controlador
{
    // Cada accion devuelve: ok, mensaje, errores por campo y la pagina a la que se redirige
    protected function resultado(bool $ok, string $mensaje, string $destino, array $errores = []): array
    {
        return ["ok" => $ok, "mensaje" => $mensaje, "destino" => $destino, "errores" => $errores];
    }

    // Ejecuta la accion del BO y traduce las excepciones a mensajes para el usuario
    protected function ejecutar(callable $accion, string $mensajeOk, string $destinoOk, string $destinoError): array
    {
        try {
            $resultado = $accion();
            return $this->resultado(true, is_string($resultado) ? $resultado : $mensajeOk, $destinoOk);
        } catch (\DomainException $e) {
            // Regla de negocio incumplida: el mensaje es seguro de mostrar
            return $this->resultado(false, $e->getMessage(), $destinoError);
        } catch (\PDOException $e) {
            // Error tecnico: no se muestra el detalle SQL al usuario
            error_log($e->getMessage());
            return $this->resultado(false, "Ocurrio un error con la base de datos. Intenta nuevamente.", $destinoError);
        }
    }

    protected function fechaValida(string $valor): bool
    {
        $fecha = \DateTime::createFromFormat("!Y-m-d", $valor);
        return $fecha && $fecha->format("Y-m-d") === $valor;
    }
}
