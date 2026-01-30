<?php

namespace App\Http\Controllers;

use App\Events\CallQueue;
use App\Events\GotQueue;
use App\Models\Counter;
use App\Models\Queue;
use App\Models\QueueLog;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AjaxController extends Controller
{
    public function touch()
    {
        $services = Service::active()->latest()->get();

        return response()->json($services, 200);
    }

    public function getCurrentQueue()
    {
        $user = Auth::user();

        if (! $user->counter) {
            abort(403, 'User tidak terhubung dengan counter.');
        }

        $counter = $user->counter;
        $service = $counter->service;

        $currentQueue = Queue::with('service')
            ->where('counter_id', $counter->id)
            ->whereIn('status', [
                Queue::STATUS_CALLED,
                Queue::STATUS_SERVING,
            ])
            ->orderBy('updated_at')
            ->first();

        $nextQueue = Queue::with('service')
            ->where('service_id', $service->id)
            ->whereNull('counter_id')
            ->where('status', Queue::STATUS_WAITING)
            ->orderBy('sequence')
            ->first();

        $waitingList = Queue::where('service_id', $service->id)
            ->where('status', Queue::STATUS_WAITING)
            ->orderBy('sequence')
            ->limit(10)
            ->get();

        $historyList = Queue::where('counter_id', $counter->id)
            ->whereIn('status', [
                Queue::STATUS_COMPLETED,
                Queue::STATUS_SKIPPED,
            ])
            ->orderByDesc('end_time')
            ->limit(10)
            ->get();

        $stats = (object) [
            'waiting' => Queue::where('service_id', $service->id)
                ->where('status', Queue::STATUS_WAITING)
                ->count(),

            'completed' => Queue::where('counter_id', $counter->id)
                ->where('status', Queue::STATUS_COMPLETED)
                ->count(),

            'skipped' => Queue::where('counter_id', $counter->id)
                ->where('status', Queue::STATUS_SKIPPED)
                ->count(),
        ];

        return response()->json([
            'user' => $user,
            'counter' => $counter,
            'service' => $service,
            'currentQueue' => $currentQueue,
            'nextQueue' => $nextQueue,
            'waitingList' => $waitingList,
            'historyList' => $historyList,
            'stats' => $stats,
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

    public function displayData()
    {
        $settings = (object) [
            'logo' => 'https://cdn-icons-png.flaticon.com/512/3063/3063822.png',
            'media_type' => 'image',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
            'slideshow_images' => [
                'https://images.unsplash.com/photo-1589829085413-56de8ae18c73?q=80&w=2000&auto=format&fit=crop',
                'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2000&auto=format&fit=crop',
            ],
            'company_name' => 'RSUD KOTA',
            'running_text' => 'PENGUMUMAN: Harap menjaga kebersihan ruang tunggu. Dilarang merokok di area rumah sakit.',
        ];

        $today = now()->toDateString();

        $currentQueue = Queue::with(['service', 'counter'])
            ->where('status', Queue::STATUS_SERVING)
            ->whereDate('start_time', $today)
            ->orderBy('start_time')
            ->get();

        $history = Queue::with(['service', 'counter'])
            ->whereIn('status', [
                Queue::STATUS_COMPLETED,
                Queue::STATUS_SKIPPED,
            ])
            ->whereDate('end_time', $today)
            ->orderByDesc('end_time')
            ->limit(4)
            ->get();

        return response()->json([
            'settings' => $settings,
            'currentQueue' => $currentQueue,
            'history' => $history,
        ], 200);
    }
}
