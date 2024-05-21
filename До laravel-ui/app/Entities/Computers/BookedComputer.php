<?php

namespace App\Entities\Computers;

use App\Models\Computer;

class BookedComputer extends Computer
{
    public function color()
    {
        return 'gray';
    }
}