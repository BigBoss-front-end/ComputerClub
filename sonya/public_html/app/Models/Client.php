<?php

namespace App\Models;

use App\Scopes\Client\UserId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new UserId);
    }

    protected $fillable = ['name', 'phone', 'email', 'user_id'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
