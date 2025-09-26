<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchDetail extends Model
{
    protected $table = 'batch_details';
    protected $fillable = ['category_id','batch_id','totalClassification'];

    public function batch()    { return $this->belongsTo(Batch::class, 'batch_id'); }
    public function category() { return $this->belongsTo(Category::class, 'category_id'); }
}

