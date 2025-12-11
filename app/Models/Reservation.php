<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Room;

class Reservation extends Model
{
    protected $fillable = [
        'room_id',
        'guest_name',
        'guest_email',
        'start_date',
        'end_date',
        'status'
    ];
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    public function room() 
    {
        return $this->belongsTo(Room::class);
    }
    public static function hasConflict($roomId, $startDate, $endDate, $excludeId = null)
    {
        $query = self::where('room_id', $roomId)
            ->where('status', '!=', 'canceled')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->where(function ($subQ) use ($startDate, $endDate) {
                    $subQ->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($dateQ) use ($startDate, $endDate) {
                            $dateQ->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
                });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
