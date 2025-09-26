<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Batch extends Model
{
    protected $table = 'batches';
    protected $fillable = ['batchName','totalBatch'];

    public function details()
    {
        return $this->hasMany(BatchDetail::class, 'batch_id');
    }

    public function harvests()
    {
        return $this->hasMany(Harvest::class, 'batch_id');
    }

    // Totales calculados
    public function getTotalHarvestEggsAttribute(): int
    {
        return (int) $this->harvests()->sum('totalEggs');
    }

    public function getTotalClassifiedAttribute(): int
    {
        return (int) $this->details()->sum('totalClassification');
    }

    public function getBalanceAttribute(): int
    {
        return $this->total_harvest_eggs - $this->total_classified;
    }
}

