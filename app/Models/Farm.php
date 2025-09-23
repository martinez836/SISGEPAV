<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    protected $fillable = ['farmName', 'state_id'];

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function harvests()
    {
        return $this->hasMany(Harvest::class, 'farm_id');
    }
}
