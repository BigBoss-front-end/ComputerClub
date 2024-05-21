<?php

namespace App\Entities\Computers;

use App\Models\Computer;

class FreeComputer extends Computer
{
    public function color()
    {
        return 'green';
    }
}