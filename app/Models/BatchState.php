<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchState extends Model
{
    // atributos que se pueden asignar masivamente
    protected $fillable = ['state'];

    // Relación uno a muchos con Batch
    public function batches()
    {
        return $this->hasMany(Batch::class, 'batch_state_id');
    }
}
