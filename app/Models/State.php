<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = ['stateName'];

    public function farms()
    {
        return $this->hasMany(Farm::class, 'state_id');
    }

    public function user()
    {
        return $this->hasMany(User::class, 'state_id');
    }
}
