<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\Hotel;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hotels = Hotel::all();
        foreach ($hotels as $hotel) {
            for($i = 1; $i <= 5; $i++) {
                Room::create([
                    'hotel_id' => $hotel->id,
                    'room_number' => $hotel->id . '0' . $i,
                    'type' => ['Single', 'Double', 'Suite'][rand(0, 2)],
                    'price' => rand(100, 500),
                    'status' => 'available',
                ]);
            }
        }
    }
}
