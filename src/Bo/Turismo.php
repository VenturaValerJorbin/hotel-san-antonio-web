<?php

namespace App\Bo;

use App\Dao\LugarTuristico as LugarTuristicoDAO;

class Turismo
{
    public function lugares(): array
    {
        return (new LugarTuristicoDAO())->listar();
    }
}
