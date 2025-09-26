<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Harvest extends Model
{
    use HasFactory;

    protected $table = 'harvests';

    protected $fillable = [
        'trayQuantity',
        'eggUnits',
        'totalEggs',
        'farm_id',
        'user_id',
        'batch_id',
    ];

    protected $casts = [
        'trayQuantity' => 'integer',
        'eggUnits'     => 'integer',
        'totalEggs'    => 'integer',
        'farm_id'      => 'integer',
        'user_id'      => 'integer',
        'batch_id'     => 'integer',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class, 'farm_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function scopeOfBatch($query, int $batchId)
    {
        return $query->where('batch_id', $batchId);
    }

    public function scopeForFarm($query, int $farmId)
    {
        return $query->where('farm_id', $farmId);
    }

    public function scopeBetween($query, string $from, string $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    public static function totalEggsForBatch(int $batchId): int
    {
        return (int) static::ofBatch($batchId)->sum('totalEggs');
    }
}
