<?php

namespace App\Bo;

use App\Dao\LimiteIntento as LimiteIntentoDAO;

// Limite de intentos por IP para formularios/consultas publicas sensibles: evita que alguien
// con un script pruebe miles de documentos (fuerza bruta) o mande spam de mensajes/consultas.
class LimiteIntento
{
    // Lanza DomainException si esta IP ya se paso del limite para esta accion; si no, registra
    // este intento y deja pasar. $ventanaSegundos: ventana de tiempo hacia atras a contar.
    public function verificar(string $accion, int $maximo, int $ventanaSegundos): void
    {
        $ip = $_SERVER["REMOTE_ADDR"] ?? "0.0.0.0";
        $dao = new LimiteIntentoDAO();
        $dao->limpiarAntiguos();
        $desde = date("Y-m-d H:i:s", time() - $ventanaSegundos);
        if ($dao->contarRecientes($ip, $accion, $desde) >= $maximo) {
            throw new \DomainException("Demasiados intentos seguidos. Espera un momento y vuelve a intentar.");
        }
        $dao->registrar($ip, $accion);
    }
}
