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
        'icon',
        'opening_time',
        'closing_time',
        'max_queue_per_day',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function operator()
    {
        return $this->hasOne(User::class);
    }

    public function routesFrom()
    {
        return $this->hasMany(ServiceRoute::class, 'from_service_id');
    }

    public function routesTo()
    {
        return $this->hasMany(ServiceRoute::class, 'to_service_id');
    }

    public function reorderRoutesFrom()
    {
        $routes = $this->routesFrom()->orderBy('step_order')->get();
        foreach ($routes as $index => $route) {
            $route->step_order = $index + 1;
            $route->save();
        }
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
