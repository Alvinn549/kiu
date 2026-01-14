<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'name',
        'code',
        'avg_wait_time',
        'is_active',
    ];

    public function counters()
    {
        return $this->hasMany(Counter::class);
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
