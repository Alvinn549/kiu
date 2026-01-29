<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    /** @use HasFactory<\Database\Factories\CounterFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'service_id',
        'name',
        'status',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
