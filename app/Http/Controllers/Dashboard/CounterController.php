<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use App\Models\Service;
use Illuminate\Http\Request;

class CounterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $counters = Counter::latest()->get();

        return view('dashboard.counter.index', compact('counters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $services = Service::active()->latest()->get();
        $counter_status = Counter::STATUS;

        return view('dashboard.counter.create', compact('services', 'counter_status'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'service_id' => ['required', 'exists:services,id'],
                'name' => ['required', 'string', 'max:255'],
                'status' => ['required', 'in:' . implode(',', array_keys(Counter::STATUS))],
            ]
        );

        Counter::create($validated);

        return to_route('counters.index')->with('success', 'Loket pemanggil berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Counter $counter)
    {
        return view('dashboard.counter.show', compact('counter'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Counter $counter)
    {
        $services = Service::active()->latest()->get();
        $counter_status = Counter::STATUS;

        return view('dashboard.counter.edit', compact('counter', 'services', 'counter_status'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Counter $counter)
    {
        $validated = $request->validate(
            [
                'service_id' => ['required', 'exists:services,id'],
                'name' => ['required', 'string', 'max:255'],
                'status' => ['required', 'in:' . implode(',', array_keys(Counter::STATUS))],
            ]
        );

        $counter->update($validated);

        return to_route('counters.index')->with('success', 'Loket pemanggil berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Counter $counter)
    {
        $counter->delete();

        return to_route('counters.index')->with('success', 'Loket pemanggil berhasil dihapus.');
    }
}
