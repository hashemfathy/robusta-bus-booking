<?php

namespace App\Http\Requests;

use App\Models\Station;
use App\Models\Trip;
use Illuminate\Foundation\Http\FormRequest;

class GetAvailableSeatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'source_id' => 'required|exists:stations,id',
            'destination_id' => 'required|exists:stations,id',
            'seat_id' => 'nullable|exists:seats,id'
        ];
    }

    public function withValidator($validator)
    {
        $source = Station::with('trips.stations')->find($this->source_id);
        $destination = Station::find($this->destination_id);

        if(!$source || !$destination){
            return;
        }
        
        $validator->after(function ($validator) use ($source, $destination) {
            $has_at_least_one_trip = $source->trips->contains(function ($trip) use ($source, $destination) {
                $source_order = $trip->stations->find($source->id)->pivot->order;
                $destination_order = $trip->stations->find($destination->id)->pivot->order;
                if ($destination_order > $source_order)
                    return true;
            });

            if (!$has_at_least_one_trip) {
                $validator->errors()->add('destination_id', 'the destination station is invalid');
            }
        });
    }

}
