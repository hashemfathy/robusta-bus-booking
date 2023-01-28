<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\Seat;
use App\Models\Station;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeatStationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (range(1,12) as $value) {
            $seat = Seat::create([
                'bus_id' => Bus::first()->id
            ]);
            $seat->stations()->attach(Station::pluck('id'));
        }
    }
}
