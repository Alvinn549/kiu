<?php

namespace App\Http\Controllers;

use App\Events\NewTicketEvent;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\TicketStep;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class TouchController extends Controller
{
    public function index()
    {
        return view('frontend.touch.index');
    }

    public function getServices()
    {
        $services = Service::active()->latest()->get();

        return response()->json($services, 200);
    }

    public function getTicket(Service $service)
    {
        try {
            if (! $service->is_active) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Layanan {$service->name} sedang tidak aktif.",
                ], 400);
            }

            if ($service->opening_time && $service->closing_time) {
                if (
                    now()->format('H:i:s') < $service->opening_time ||
                    now()->format('H:i:s') > $service->closing_time
                ) {

                    return response()->json([
                        'status' => 'error',
                        'message' => "Layanan {$service->name} sedang tutup.",
                    ], 400);
                }
            }

            if ($service->max_queue_per_day > 0) {
                $todayCount = TicketStep::where('service_id', $service->id)
                    ->whereDate('created_at', today())
                    ->count();

                if ($todayCount >= $service->max_queue_per_day) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Kuota hari ini penuh.',
                    ], 400);
                }
            }

            $ticket = DB::transaction(function () use ($service) {

                $lastNumber = Ticket::where('ticket_number', 'like', $service->code . '-%')
                    ->whereDate('created_at', today())
                    ->lockForUpdate()
                    ->latest()
                    ->value('ticket_number');

                $next = $lastNumber
                    ? ((int) explode('-', $lastNumber)[1]) + 1
                    : 1;

                $ticketNumber = $service->code . '-' . str_pad($next, 3, '0', STR_PAD_LEFT);

                $ticket = Ticket::create([
                    'ticket_number' => $ticketNumber,
                ]);

                $routes = $service->routesFrom()->orderBy('step_order')->get();

                if ($routes->isEmpty()) {

                    TicketStep::create([
                        'ticket_id' => $ticket->id,
                        'service_id' => $service->id,
                        'step_order' => 1,
                    ]);
                } else {

                    foreach ($routes as $route) {
                        TicketStep::create([
                            'ticket_id' => $ticket->id,
                            'service_id' => $route->to_service_id,
                            'step_order' => $route->step_order,
                        ]);
                    }
                }

                return $ticket;
            });

            broadcast(new NewTicketEvent);

            return response()->json([
                'status' => 'success',
                'ticket' => $ticket->load('steps.service'),
            ]);
        } catch (Throwable $e) {

            Log::error('Queue Error', [
                'service_id' => $service->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil nomor.',
            ], 500);
        }
    }
}
