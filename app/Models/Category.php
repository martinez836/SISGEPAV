<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['categoryName'];

    public function BatchDetail()
    {
        return $this->hasMany(BatchDetail::class, 'category_id');
    }
}
