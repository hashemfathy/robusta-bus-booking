<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    protected $fillable=['source_id','destination_id','seat_id'];

    public function source()
    {
        return $this->belongsTo(Station::class,'source_id');
    }
    public function destination()
    {
        return $this->belongsTo(Station::class,'destination_id');
    }
}
