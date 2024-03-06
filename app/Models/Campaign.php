<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = ["name"];
    // protected $casts = [
    //     'enabled_hospitals' => 'array'
    // ];
    use HasFactory;

    public function isEnabled(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return in_array(auth()->user()->hospital_id, $this->enabled_hospitals);
            }
        );
    }

    public function scopeEnabledHospital($query, $hospitalId)
    {
        $query = $query->whereJsonContains('enabled_hospitals', $hospitalId);
        return $query;
    }

    public function enabledHospitals(): Attribute
    {
        return Attribute::make(
            get: function ($val) {
                return $val != null ? json_decode($val) : [];
            }
        );
    }

    public function enableHospital($hospitalId)
    {
        $eArr = $this->enabled_hospitals;
        info($eArr);
        if (!in_array($hospitalId, $eArr)) {
            info('inside in_array');
            array_push($eArr, $hospitalId);
            $this->enabled_hospitals = '['.implode(",",$eArr).']';
            $this->save();
        }
    }

    public function disableHospital($hospitalId)
    {
        $eArr = $this->enabled_hospitals;
        info($eArr);
        if (in_array($hospitalId, $eArr)) {
            info('inside in_array');
            $eArr = array_filter($eArr, function ($h) use ($hospitalId) {
                return $h != $hospitalId;
            });
            $this->enabled_hospitals = '['.implode(",",$eArr).']';
            $this->save();
        }
    }
}
