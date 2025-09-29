<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Batch extends Model
{
    protected $table = 'batches';
    protected $fillable = ['batchName','totalBatch','batch_state_id'];

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

    public function state()
    {
        return $this->belongsTo(\App\Models\BatchState::class, 'batch_state_id');
    }

    public function novelties()
    {
        return $this->hasMany(\App\Models\Novelty::class, 'batch_code', 'batchName');
    }

}

