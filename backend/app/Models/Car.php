<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = ['model', 'comfort_category', 'driver_id'];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function isAvailable($start, $end)
    {
        return !$this->trips()
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->exists();
    }
}
