<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchDetail extends Model
{
    protected $fillable = ['category_id', 'batch_id', 'totalClassification'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }
}
