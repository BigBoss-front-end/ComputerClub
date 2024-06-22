<?php

namespace App\Models;

use App\Entities\Computers\BookedComputer;
use App\Entities\Computers\BusyComputer;
use App\Entities\Computers\FreeComputer;
use App\Entities\Computers\RepairComputer;
use App\Http\Services\ComputerService;
use App\Scopes\Computer\UserId;
use Illuminate\Database\Eloquent\Model;

class Computer extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new UserId);
    }

    protected $fillable = ['name', 'sort', 'status_id', 'user_id'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getType() 
    {
        $instance = $this;
        switch ($this->status->alias) {
            case 'occupied':
                $instance = new BusyComputer();
                break;
            case 'booked':
                $instance = new BookedComputer();
                break;
            case 'under_repair':
                $instance = new RepairComputer();
                break;
            
            default:
                $instance = new FreeComputer();
                break;
        }

        $instance->fill($this->toArray());
        $instance->id = $this->id;

        $instance->status = $this->status;
        $instance->client = $this->client;
        $instance->nearest = $this->nearest;
        $instance->booking = $this->booking;
        $instance->color = $instance->color();

        return $instance;
    }
}
