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
    public function hasConflict($startDate, $endDate)
    {
        return self::where('room_id', $this->room_id)
        ->where('id', '!=', $this->id)
        ->where('start_date', '<', $endDate)
        ->where('end_date', '>', $startDate)
        ->exists();
    }
}
