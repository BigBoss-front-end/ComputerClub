<?php

namespace App\Entities\Computers;

use App\Models\Computer;

class BusyComputer extends Computer
{
    public function color()
    {
        return 'red';
    }
}