<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::with('operator')->latest()->get();

        return view('dashboard.service.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.service.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:services,code'],
            'opening_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'closing_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/', 'after:opening_time'],
            'max_queue_per_day' => ['required', 'integer', 'min:1'],
            'icon' => ['nullable', 'image', 'max:2048'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('service-icons', 'public');
            $validated['icon'] = $path;
        }

        $service = Service::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'opening_time' => $validated['opening_time'],
            'closing_time' => $validated['closing_time'],
            'max_queue_per_day' => $validated['max_queue_per_day'],
        ]);

        $service->operator()->create([
            'role_id' => Role::SERVICE,
            'name' => $validated['username'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
        ]);

        return to_route('services.index')->with('success', 'Layanan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        $service->load('operator');

        return view('dashboard.service.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        $service->load('operator');

        return view('dashboard.service.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:services,code,' . $service->id],
            'opening_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'closing_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/', 'after:opening_time'],
            'max_queue_per_day' => ['required', 'integer', 'min:1'],
            'icon' => ['nullable', 'image', 'max:2048'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $service->operator->id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $service->operator->id],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('service-icons', 'public');
            $validated['icon'] = $path;

            Storage::disk('public')->delete($service->icon);
        } else {
            $validated['icon'] = $service->icon;
        }

        $service->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
        ]);

        $service->operator->update([
            'name' => $validated['username'],
            'email' => $validated['email'],
            'username' => $validated['username'],
        ]);

        if (! empty($validated['password'])) {
            $service->operator->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        return to_route('services.index')->with('success', 'Layanan berhasil diperbarui.');
    }

    public function toggleStatus(Service $service)
    {
        $service->is_active = ! $service->is_active;
        $service->save();

        return to_route('services.index')->with('success', 'Status layanan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $service->operator->delete();
        Storage::disk('public')->delete($service->icon);
        $service->delete();

        return to_route('services.index')->with('success', 'Layanan berhasil dihapus.');
    }
}
