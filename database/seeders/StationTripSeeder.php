<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\Station;
use App\Models\Trip;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StationTripSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $station_names = ['Cairo','AlFayyum','AlMinya','Asyut'];
        $bus = Bus::create([
            'plate_number' => 'robusta123'
        ]);
        $trip = Trip::create([
            'bus_id' => $bus->id
        ]);
        foreach ($station_names as $key => $station_name) {
           $station =  Station::create([
                'name'=> $station_name
            ]);
            $station->trips()->attach($trip->id,['order'=>$key]);
        }
    }
}
