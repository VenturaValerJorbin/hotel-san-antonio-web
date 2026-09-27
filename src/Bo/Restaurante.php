<?php

namespace App\Bo;

use App\Dao\Producto as ProductoDAO;

// BO de la carta: agrupa los productos por categoria para mostrarlos en la pagina publica.
class Restaurante
{
    // Devuelve ["Menu del dia" => [productos...], "Platos a la carta" => [...], ...]
    public function carta(): array
    {
        $carta = [];
        foreach ((new ProductoDAO())->listarCarta() as $producto) {
            $carta[$producto["categoria"]][] = $producto;
        }
        return $carta;
    }
}
