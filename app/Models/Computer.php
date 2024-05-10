<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Computer extends Model
{
    protected $fillable = ['name', 'sort', 'status_id', 'user_id'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
