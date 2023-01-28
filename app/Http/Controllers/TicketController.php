<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetAvailableSeatRequest;
use App\Models\Seat;
use App\Models\SeatStation;
use App\Models\Station;
use App\Models\StationTrip;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function getAvailableSeats(GetAvailableSeatRequest $request)
    {      
        $seats = $this->getAvailableSeatsQuery($request)->get();
        return response()->json([
            'data' => $seats
        ],200) ;
    }

    public function store(GetAvailableSeatRequest $request,Seat $seat)
    {
        $seats = $this->getAvailableSeatsQuery($request);
        if(!$seats->get()->contains($seat->id)){
            abort(400,'seat is not available');
        }
        DB::beginTransaction();
        try {
            Ticket::create([
                'source_id' => $request->source_id,
                'destination_id' => $request->destination_id,
                'seat_id' => $seat->id,
            ]);
            $all_trip_stations = StationTrip::where('station_id','>=',$request->source_id)->where('station_id','<',$request->destination_id)->pluck('station_id');
            SeatStation::where('seat_id',$seat->id)->whereIn('station_id',$all_trip_stations)->delete();
            DB::commit();
            return response()->json([
                'message' => 'Seat has been successfully booked'
            ],200) ;
        } catch(\Exception $ex){
            DB::rollBack();
            \Log::error($ex->getMessage() . ' in Line ' . $ex->getLine());
            return response()->json([
                'data' => 'Something went wrong'
            ],500) ;
        }  
    }

    public function getAvailableSeatsQuery($request)
    {
        $source = Station::findOrFail($request->source_id) ;
        $destination = Station::findOrFail($request->destination_id) ;
        $trips = $source->trips->where(function ($trip) use ($source, $destination) {
            $source_order = $trip->stations->find($source->id)->pivot->order;
            $destination_order = $trip->stations->find($destination->id)->pivot->order;
            return $destination_order > $source_order;
        });
        $trip = $trips->first();
        $source_order = $trip->stations->find($source->id)->pivot->order;
        $destination_order = $trip->stations->find($destination->id)->pivot->order;
        $all_trip_stations = StationTrip::where('order','>=',$source_order)->where('order','<',$destination_order)->where('trip_id',$trip->id)->pluck('station_id');
        $seats = Seat::query();
        foreach ($all_trip_stations as $station) {
            $seats->whereHas('stations', function ($seats) use ($station) {
                $seats->where('station_id', $station);
            });
        }
        return $seats;
    }
}
