<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueueLog extends Model
{
    /** @use HasFactory<\Database\Factories\QueueLogFactory> */
    use HasFactory, HasUlids;

    public const EVENT = ['created', 'called', 'started', 'ended'];

    public const EVENT_CREATED = 'created';

    public const EVENT_CALLED = 'called';

    public const EVENT_STARTED = 'started';

    public const EVENT_ENDED = 'ended';

    protected $fillable = [
        'queue_id',
        'event',
    ];

    public function queue()
    {
        return $this->belongsTo(Queue::class);
    }
}
