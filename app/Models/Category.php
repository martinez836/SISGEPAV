<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = ['categoryName'];

    public function batchDetails()
    {
        return $this->hasMany(BatchDetail::class, 'category_id');
    }
}

