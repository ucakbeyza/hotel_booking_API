<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Room;

class Hotel extends Model
{
    protected $fillable = [
        'name',
        'location',
        'rating'
    ];
    protected $casts = [
        'rating' => 'decimal:1',
    ];
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
