<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketStepLog extends Model
{
    /** @use HasFactory<\Database\Factories\TicketStepLogFactory> */
    use HasFactory, HasUlids;

    public const EVENT = [
        'created',
        'called',
        'started',
        'ended',
        'skipped',
        'cancelled',
    ];

    protected $fillable = [
        'ticket_step_id',
        'event',
    ];

    public function ticketStep()
    {
        return $this->belongsTo(TicketStep::class);
    }
}
