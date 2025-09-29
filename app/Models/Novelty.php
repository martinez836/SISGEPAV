<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Novelty extends Model
{
    protected $table = 'novelty';

    protected $fillable = [
        'batch_code',
        'quantity',
        'novelty',
        'user_name',
    ];

    // Relación por código → nombre del lote
    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_code', 'batchName');
    }
}
