<?php

namespace App\Http\Services;

use App\Entities\Computers\BookedComputer;
use App\Entities\Computers\BusyComputer;
use App\Entities\Computers\FreeComputer;
use App\Entities\Computers\RepairComputer;
use App\Models\Booking;
use App\Models\Computer;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ComputerService
{
    public static function setStatusesAndClients(Collection &$computers)
    {
        $bookings = Booking::query()
            ->with('status')
            ->with('client')
            ->whereIn('computer_id', $computers->pluck('id')->toArray())
            ->where('start_time', '<=', date('Y-m-d H:i:s'))
            ->where('end_time', '>=', date('Y-m-d H:i:s'))
            ->orderBy('start_time', 'asc')
            ->get();

        $statuses = Status::query()->get();
        $freeStatus = $statuses->first(function($value, $key) {
            return $value->alias == 'free';
        });

        $computers = $computers->map(function($computer) use($bookings, $freeStatus) {
            $bookings->map(function($booking) use($computer) {
                if($booking->computer_id == $computer->id) {
                    $computer->booking = $booking;
                    $computer->status = $booking->status;
                    $computer->client = $booking->client;
                }
            });
            if(empty($computer->status)) {
                $computer->status = $freeStatus;
            }

            return $computer->getType();
        });

        return self::class;
    }

    public static function setNearest(Collection $computers)
    {
        $bookings = Booking::query()->with('status')->with('client')->where('computer_id', $computers->pluck('id')->toArray())
            ->where('start_time', '>', date('Y-m-d H:i:s'))
            ->orderBy('start_time', 'asc')
            ->get();
        
        $computers->each(function($computer) use($bookings) {
            $nearests = [];
            $bookings->each(function($booking) use($computer, &$nearests) {
                if($booking->computer_id == $computer->id) {     
                    $nearests[] = $booking;
                }
            });
            $computer->nearest = $nearests;
        });

        return self::class;
    }

    public static function getType(Computer $computer) 
    {
        $instance = $computer;
        switch ($computer->status()) {
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

        $instance->fill($computer->toArray());
        return $instance;
    }

    public static function setFreeTimes(Collection $computers)
    {
        $model = Booking::query();

        $model
            ->where('start_time', '>', date('Y-m-d H:i:s'))
            ->where('computer_id', $computers->pluck('id')
            ->toArray());

        $times = $model->orderBy('start_time')->get();

        $computers->each(function($computer) use($times) {
            $data = [];
            $count = $times->count();
            $times->each(function($time, $k) use($count, $times, &$data) {

                $next = $times->get($k + 1);

                if($count != $k + 1) {
                    $startTime = Carbon::parse($time->end_time);
                    $endTime = Carbon::parse($next->start_time);

                    if($endTime->diffInMinutes($startTime, true) < 10) {
                        return true;
                    }
                }

                $date = \Carbon\Carbon::parse($time->end_time)->format('Y-m-d');
                if(empty($data[$date])) {
                    $data[$date] = [];
                }

                if($count == $k + 1) {
                    $data[$date][] = [
                        'start' => $time->end_time,
                        'end' => NULL
                    ];
                } else {
                    $data[$date][] = [
                        'start' => \Carbon\Carbon::parse($time->end_time)->format('Y-m-d H:i:s'),
                        'end' => \Carbon\Carbon::parse($next->start_time)->format('Y-m-d H:i:s')
                    ];
                }
            });

            $arData = [];
            foreach ($data as $key => $value) {
                $arData[] = [
                    'date' => $key,
                    'times' => $value,
                ];
            }
            $computer->freeTimes = $arData;
        });

        return self::class;
    }
}


