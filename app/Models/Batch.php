<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = ['batchName', 'totalBatch'];
    public function BatchDetail()
    {
        return $this->hasMany(BatchDetail::class, 'batch_id');
    }

    public function harvests()
    {
        return $this->hasMany(Harvest::class, 'batch_id');
    }
}
