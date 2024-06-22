<?php

namespace App\Entities\Computers;

use App\Models\Computer;

class RepairComputer extends Computer
{
    public function color()
    {
        return 'black';
    }
}