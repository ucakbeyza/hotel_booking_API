<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Hotel;

class Room extends Model
{
    protected $fillable = [
        'hotel_id',
        'room_number',
        'type',
        'price',
        'status'
    ];
    protected $casts = [
        'price' => 'decimal:2',
    ];  
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
