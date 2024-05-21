<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = ['computer_id', 'client_id', 'status_id', 'start_time', 'end_time'];

    public function status()
    {
        return $this->hasOne(Status::class, 'id', 'status_id');
    }

    public function computer()
    {
        return $this->belongsTo(Computer::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
