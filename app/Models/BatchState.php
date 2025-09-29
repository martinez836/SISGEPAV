<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchState extends Model
{
    protected $table = 'batch_states';
    protected $fillable = ['state'];

    public function batches()
    {
        return $this->hasMany(Batch::class, 'batch_state_id');
    }
}
