<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = ['computer_id', 'client_id', 'status_id', 'start_time', 'end_time'];

    public function computer()
    {
        return $this->belongsTo(Computer::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
