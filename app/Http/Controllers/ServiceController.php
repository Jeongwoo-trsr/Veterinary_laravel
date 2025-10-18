<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::paginate(15);
        return view('services.index', compact('services'));
    }

    public function create()
    {
        return view('services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'duration_minutes' => 'required|integer|min:1|max:480',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        Service::create($request->all());

        return redirect()->route('services.index')->with('success', 'Service created successfully.');
    }

    public function show(Service $service)
    {
        $service->load('appointments.pet.owner.user');
        return view('services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'duration_minutes' => 'required|integer|min:1|max:480',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $service->update($request->all());

        return redirect()->route('services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Service deleted successfully.');
    }
}
