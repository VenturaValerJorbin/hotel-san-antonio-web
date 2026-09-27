<?php

namespace bo;

use dao\LugarTuristico as LugarTuristicoDAO;

class Turismo
{
    public function lugares(): array
    {
        return (new LugarTuristicoDAO())->listar();
    }
}
