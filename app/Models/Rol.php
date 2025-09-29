<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'rol';
    protected $fillable = ['rolName'];

    public function users()
    {
        return $this->hasMany(User::class, 'rol_id');
    }

}
