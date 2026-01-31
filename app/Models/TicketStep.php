<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketStep extends Model
{
    /** @use HasFactory<\Database\Factories\TicketStepFactory> */
    use HasFactory, HasUlids;

    public const STATUS = [
        'waiting',
        'called',
        'serving',
        'completed',
        'skipped',
        'cancelled',
    ];

    protected $fillable = [
        'ticket_id',
        'service_id',
        'counter_id',
        'step_order',
        'status',
        'called_at',
        'start_time',
        'end_time',
        'is_manual',
        'note',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function counter()
    {
        return $this->belongsTo(Counter::class);
    }

    public function logs()
    {
        return $this->hasMany(TicketStepLog::class);
    }
}
