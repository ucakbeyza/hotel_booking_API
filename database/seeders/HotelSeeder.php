<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hotel;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hotels = [
            [
                'name' => 'Grand Istanbul Hotel',
                'location' => 'Istanbul, Turkey',
                'rating' => 4.5,
            ],
            [
                'name' => 'Luxury Beach Resort',
                'location' => 'Antalya, Turkey',
                'rating' => 4.8,
            ],
            [
                'name' => 'Mountain View Hotel',
                'location' => 'Cappadocia, Turkey',
                'rating' => 4.2,
            ],
        ];

        foreach ($hotels as $hotel) {
            Hotel::create($hotel);
        }
    }
}
