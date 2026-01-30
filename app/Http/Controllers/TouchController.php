<?php

namespace App\Http\Controllers;

use App\Events\GotQueue;
use App\Models\Queue;
use App\Models\QueueLog;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class TouchController extends Controller
{
    public function index()
    {
        return view('frontend.touch.index');
    }

    public function getQueueNumber(Service $service, Request $request)
    {
        try {
            $now = now();

            if (! $service->is_active) {
                Alert::error('Layanan Tidak Aktif', 'Layanan ' . $service->name . ' sedang tidak aktif.');

                return to_route('touch.index')
                    ->with('error', 'Layanan ' . $service->name . ' sedang tidak aktif.');
            }

            if ($now->format('H:i:s') < $service->opening_time || $now->format('H:i:s') > $service->closing_time) {
                Alert::error('Layanan Tutup', 'Layanan ' . $service->name . ' sedang tutup.');

                return to_route('touch.index')
                    ->with('error', 'Layanan ' . $service->name . ' sedang tutup.');
            }

            $todayQueueCount = Queue::where('service_id', $service->id)
                ->whereDate('created_at', $now->toDateString())
                ->count();

            if ($todayQueueCount >= $service->max_queue_per_day) {
                Alert::error(
                    'Kuota Penuh',
                    'Kuota antrian untuk layanan ' . $service->name . ' hari ini sudah penuh.'
                );

                return to_route('touch.index')
                    ->with('error', 'Kuota antrian untuk layanan ' . $service->name . ' hari ini sudah penuh.');
            }

            DB::transaction(function () use ($service, $now, &$ticketNumber) {

                $lastSequence = Queue::where('service_id', $service->id)
                    ->whereDate('created_at', $now->toDateString())
                    ->max('sequence');

                $nextSequence = ($lastSequence ?? 0) + 1;

                $ticketNumber = strtoupper($service->code) . '-' . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);

                $queue = Queue::create([
                    'service_id' => $service->id,
                    'counter_id' => null,
                    'ticket_number' => $ticketNumber,
                    'sequence' => $nextSequence,
                    'status' => Queue::STATUS_WAITING,
                ]);

                QueueLog::create([
                    'queue_id' => $queue->id,
                    'event' => QueueLog::EVENT_CREATED,
                ]);

                broadcast(new GotQueue($service));
            });

            Alert::success('Berhasil', 'Nomor antrian Anda: ' . $ticketNumber);

            return to_route('touch.index')
                ->with('success', 'Nomor antrian berhasil diambil: ' . $ticketNumber);
        } catch (\Throwable $e) {
            Log::error('Error getting queue number', [
                'service_id' => $service->id,
                'message' => $e->getMessage(),
            ]);

            Alert::error('Gagal', 'Gagal mengambil nomor antrian. Silakan coba lagi.');

            return to_route('touch.index')
                ->with('error', 'Gagal mengambil nomor antrian. Silakan coba lagi.');
        }
    }
}
