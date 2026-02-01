<?php

namespace App\Http\Controllers;

use App\Events\CallQueue;
use App\Events\GotQueue;
use App\Models\Counter;
use App\Models\Queue;
use App\Models\QueueLog;
use App\Models\Service;
use App\Models\TicketStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FetchController extends Controller
{
    public function getCsrfToken()
    {
        return response()->json(['token' => csrf_token()], 200);
    }

    public function counterDashboard()
    {
        try {
            $user = Auth::user();

            if (! $user->counter) {
                abort(403, 'User tidak terhubung dengan counter.');
            }

            $counter = $user->counter;

            $currentTicket = TicketStep::with(['ticket', 'service'])
                ->where('counter_id', $counter->id)
                ->whereIn('status', ['called', 'serving'])
                ->whereDate('created_at', today())
                ->orderBy('updated_at', 'desc')
                ->first();

            $nextTicket = TicketStep::with(['ticket', 'service'])
                ->where('status', 'waiting')
                ->whereNull('counter_id')
                ->whereDate('created_at', today())
                ->orderBy('step_order', 'asc')
                ->orderBy('created_at', 'asc')
                ->first();

            $waitingTicket = TicketStep::with(['ticket', 'service'])
                ->where('status', 'waiting')
                ->whereNull('counter_id')
                ->whereDate('created_at', today())
                ->orderBy('created_at', 'asc')
                ->limit(10)
                ->get();

            $historyTicket = TicketStep::with(['ticket', 'service'])
                ->where('counter_id', $counter->id)
                ->whereIn('status', ['completed', 'skipped', 'cancelled'])
                ->whereDate('updated_at', today())
                ->orderByDesc('updated_at')
                ->limit(10)
                ->get();

            $waiting_count = TicketStep::where('status', 'waiting')
                ->whereNull('counter_id')
                ->whereDate('created_at', today())
                ->count();

            $completed_count = TicketStep::where('counter_id', $counter->id)
                ->where('status', 'completed')
                ->whereDate('updated_at', today())
                ->count();

            $skipped_count = TicketStep::where('counter_id', $counter->id)
                ->whereIn('status', ['skipped', 'cancelled'])
                ->whereDate('updated_at', today())
                ->count();

            return response()->json([
                'user' => $user,
                'counter' => $counter,
                'currentTicket' => $currentTicket,
                'nextTicket' => $nextTicket,
                'waitingTicket' => $waitingTicket,
                'historyTicket' => $historyTicket,
                'waiting_count' => $waiting_count,
                'completed_count' => $completed_count,
                'skipped_count' => $skipped_count,
                'avg_service_time' => '5 menit',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function callQueue(Queue $queue)
    {
        $user = Auth::user();

        if (! $user->counter) {
            return response()->json([
                'status' => 'error',
                'message' => 'User tidak terhubung dengan counter.',
            ], 403);
        }

        $counter = $user->counter;

        if ($counter->status !== Counter::STATUS_OPEN) {
            return response()->json([
                'status' => 'error',
                'message' => 'Counter sedang tidak tersedia untuk melayani.',
            ], 400);
        }

        if ($queue->service_id !== $counter->service_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Queue tidak sesuai dengan layanan counter.',
            ], 400);
        }

        if ($queue->status !== Queue::STATUS_WAITING) {
            return response()->json([
                'status' => 'error',
                'message' => 'Queue tidak dapat dipanggil.',
            ], 400);
        }

        $hasActiveQueue = Queue::where('counter_id', $counter->id)
            ->whereIn('status', [
                Queue::STATUS_CALLED,
                Queue::STATUS_SERVING,
            ])
            ->exists();

        if ($hasActiveQueue) {
            return response()->json([
                'status' => 'error',
                'message' => 'Masih ada antrian aktif di counter ini.',
            ], 400);
        }

        DB::transaction(function () use ($queue, $counter) {
            $queue->update([
                'counter_id' => $counter->id,
                'status' => Queue::STATUS_CALLED,
                'called_at' => now(),
            ]);

            $queue->logs()->create([
                'event' => QueueLog::EVENT_CALLED,
            ]);
        });

        broadcast(new CallQueue($queue));
        broadcast(new GotQueue($queue->service));

        return response()->json([
            'status' => 'success',
            'message' => 'Nomor ' . $queue->ticket_number . ' berhasil dipanggil.',
        ], 200);
    }

    public function startService(Queue $queue)
    {
        $user = Auth::user();

        if (! $user->counter || $queue->counter_id !== $user->counter->id) {
            abort(403, 'Queue tidak terkait dengan counter ini.');
        }

        if ($queue->status !== Queue::STATUS_CALLED) {
            abort(400, 'Queue belum dipanggil.');
        }

        $queue->update([
            'status' => Queue::STATUS_SERVING,
            'start_time' => now(),
        ]);

        $queue->logs()->create([
            'event' => QueueLog::EVENT_STARTED,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Layanan untuk nomor ' . $queue->ticket_number . ' telah dimulai.',
        ], 200);
    }

    public function setStatusCounter(Request $request, Counter $counter)
    {
        $validated = $request->validate(
            [
                'status' => ['required', 'in:' . implode(',', array_keys(Counter::STATUS))],
            ]
        );

        $counter->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Status loket pemanggil berhasil diubah.',
        ], 200);
    }

    public function completeQueue(Queue $queue)
    {
        if ($queue->status !== Queue::STATUS_SERVING) {
            return response()->json([
                'status' => 'error',
                'message' => 'Queue tidak dapat diselesaikan.',
            ], 400);
        }

        $queue->update([
            'status' => Queue::STATUS_COMPLETED,
            'end_time' => now(),
        ]);

        $queue->logs()->create([
            'event' => QueueLog::EVENT_ENDED,
        ]);

        broadcast(new CallQueue($queue));

        return response()->json([
            'status' => 'success',
            'message' => 'Nomor ' . $queue->ticket_number . ' berhasil diselesaikan.',
        ], 200);
    }

    public function directCallQueue(Queue $queue)
    {
        $user = Auth::user();

        if (! $user->counter) {
            return response()->json([
                'status' => 'error',
                'message' => 'User tidak terhubung dengan counter.',
            ], 403);
        }

        $counter = $user->counter;

        if ($counter->status !== Counter::STATUS_OPEN) {
            return response()->json([
                'status' => 'error',
                'message' => 'Counter sedang tidak tersedia untuk melayani.',
            ], 400);
        }

        if ($queue->service_id !== $counter->service_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Queue tidak sesuai dengan layanan counter.',
            ], 400);
        }

        if ($queue->status !== Queue::STATUS_WAITING) {
            return response()->json([
                'status' => 'error',
                'message' => 'Queue tidak dapat dipanggil.',
            ], 400);
        }

        DB::transaction(function () use ($queue, $counter) {
            $queue->update([
                'counter_id' => $counter->id,
                'status' => Queue::STATUS_CALLED,
                'start_time' => now(),
            ]);

            $queue->logs()->create([
                'event' => QueueLog::EVENT_CALLED,
            ]);
        });

        broadcast(new CallQueue($queue));

        return response()->json([
            'status' => 'success',
            'message' => 'Nomor ' . $queue->ticket_number . ' berhasil dipanggil.',
        ], 200);
    }

    public function toggleStatus(Service $service)
    {
        $service->is_active = ! $service->is_active;
        $service->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status layanan berhasil diubah.',
        ], 200);
    }
}
