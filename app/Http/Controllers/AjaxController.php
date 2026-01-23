<?php

namespace App\Http\Controllers;

use App\Events\CallQueue;
use App\Events\GotQueue;
use App\Models\Counter;
use App\Models\Queue;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AjaxController extends Controller
{
    public function getCurrentQueue()
    {
        $user = Auth::user();

        if (! $user->counter) {
            abort(403, 'User tidak terhubung dengan counter.');
        }

        $counter = $user->counter;
        $service = $counter->service;
        $today = now()->toDateString();

        $currentQueue = Queue::with('service')
            ->where('counter_id', $counter->id)
            ->whereIn('status', [
                Queue::STATUS_SERVING,
                Queue::STATUS_CALLED,
            ])
            ->whereDate('created_at', $today)
            ->orderBy('updated_at')
            ->first();

        $nextQueue = Queue::with('service')
            ->where('service_id', $service->id)
            ->whereNull('counter_id')
            ->where('status', Queue::STATUS_WAITING)
            ->whereDate('created_at', $today)
            ->orderBy('sequence')
            ->first();

        $waitingList = Queue::where('service_id', $service->id)
            ->where('status', Queue::STATUS_WAITING)
            ->whereDate('created_at', $today)
            ->orderBy('sequence')
            ->limit(10)
            ->get();

        $historyList = Queue::where('counter_id', $counter->id)
            ->whereIn('status', [
                Queue::STATUS_COMPLETED,
                Queue::STATUS_SKIPPED,
            ])
            ->whereDate('created_at', $today)
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        $stats = (object) [
            'waiting' => Queue::where('service_id', $service->id)
                ->where('status', Queue::STATUS_WAITING)
                ->whereDate('created_at', $today)
                ->count(),

            'completed' => Queue::where('counter_id', $counter->id)
                ->where('status', Queue::STATUS_COMPLETED)
                ->whereDate('created_at', $today)
                ->count(),

            'skipped' => Queue::where('counter_id', $counter->id)
                ->where('status', Queue::STATUS_SKIPPED)
                ->whereDate('created_at', $today)
                ->count(),
        ];

        $serverTimeMs = $currentQueue && $currentQueue->start_time
            ? strtotime($currentQueue->start_time) * 1000
            : 0;

        return response()->json([
            'user' => $user,
            'counter' => $counter,
            'service' => $service,
            'currentQueue' => $currentQueue,
            'nextQueue' => $nextQueue,
            'waitingList' => $waitingList,
            'historyList' => $historyList,
            'stats' => $stats,
            'serverTimeMs' => $serverTimeMs,
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
                'start_time' => now(),
            ]);
        });

        broadcast(new CallQueue($queue));
        broadcast(new GotQueue($queue->service));

        return response()->json([
            'status' => 'success',
            'message' => 'Nomor ' . $queue->ticket_number . ' berhasil dipanggil.',
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

    public function getCurrentQueuesForDisplay()
    {
        $today = now()->toDateString();

        $currentQueues = Queue::with('service')
            ->whereIn('status', [
                Queue::STATUS_CALLED,
                Queue::STATUS_SERVING,
            ])
            ->whereDate('created_at', $today)
            ->orderBy('updated_at')
            ->get();

        return response()->json([
            'currentQueues' => $currentQueues,
        ], 200);
    }
}
