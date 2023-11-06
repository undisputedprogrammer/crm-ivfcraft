<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id', 'id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'id');
    }

    // public function appointmentDate(): Attribute
    // {
    //     return Attribute::make(
    //         get: function($val) {
    //             return Carbon::createFromFormat('Y-m-d', $val)->format('d-m-Y');
    //         }
    //     );
    // }
}
