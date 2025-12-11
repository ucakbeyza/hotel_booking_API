<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Reservation;
use App\Models\Room;
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = Room::all();

        foreach ($rooms->take(3) as $room) {
            Reservation::create([
                'room_id' => $room->id,
                'guest_name' => 'John Doe',
                'guest_email' => 'john@example.com',
                'start_date' => Carbon::now()->addDays(rand(1, 10)),
                'end_date' => Carbon::now()->addDays(rand(11, 20)),
                'status' => ['pending', 'confirmed'][rand(0, 1)],
            ]);
            $room->update(['status' => 'booked']);
        }
    }
}
