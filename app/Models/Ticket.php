<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    /** @use HasFactory<\Database\Factories\TicketFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'ticket_number',
        'customer_name',
        'status',
        'completed_at',
    ];

    public function steps()
    {
        return $this->hasMany(TicketStep::class);
    }
}
