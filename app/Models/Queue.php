<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    /** @use HasFactory<\Database\Factories\QueueFactory> */
    use HasFactory, HasUlids;

    public const STATUS = ['waiting', 'called', 'serving', 'completed', 'skipped', 'cancelled'];

    public const STATUS_WAITING = 'waiting';

    public const STATUS_CALLED = 'called';

    public const STATUS_SERVING = 'serving';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_SKIPPED = 'skipped';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'service_id',
        'counter_id',
        'ticket_number',
        'sequence',
        'status',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function counter()
    {
        return $this->belongsTo(Counter::class);
    }
}
