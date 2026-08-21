<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Trip;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trips = Trip::with(['vehicle', 'driver', 'job'])->paginate(15);
        $allTrips = Trip::all();
        $vehicles = Vehicle::all();
        $drivers = Driver::all();

        return view('pages.trips', [
            'trips' => $trips,
            'totalTrips' => $allTrips->count(),
            'inTransitTrips' => $allTrips->where('status', 'in_transit')->count(),
            'deliveredTrips' => $allTrips->where('status', 'delivered')->count(),
            'delayedTrips' => $allTrips->where('status', 'delayed')->count(),
            'vehicles' => $vehicles,
            'drivers' => $drivers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vehicles = Vehicle::where('status', 'available')->get();
        $drivers = Driver::where('status', 'on_duty')->get();

        return view('pages.trips-create', compact('vehicles', 'drivers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trip_number' => 'required|string|unique:trips,trip_number|max:50',
            'job_number' => 'nullable|string|max:50',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:drivers,id',
            'pickup_time' => 'required|date',
            'delivery_time' => 'nullable|date',
            'status' => 'required|in:pickup,in_transit,delivered,delayed,cancelled',
            'pickup_location' => 'nullable|string',
            'delivery_location' => 'nullable|string',
            'distance' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'job_id' => 'nullable|exists:transport_jobs,id',
        ]);

        Trip::create($validated);

        return redirect()->route('trips.index')->with('success', 'Trip created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $trip = Trip::with(['vehicle', 'driver', 'job'])->findOrFail($id);
        
        // Return JSON for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['trip' => $trip]);
        }
        
        return view('pages.trips-show', compact('trip'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $trip = Trip::with(['vehicle', 'driver', 'job'])->findOrFail($id);
        
        // Return JSON for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($trip);
        }
        
        $vehicles = Vehicle::all();
        $drivers = Driver::all();

        return view('pages.trips-edit', compact('trip', 'vehicles', 'drivers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $trip = Trip::findOrFail($id);

        $validated = $request->validate([
            'trip_number' => 'required|string|unique:trips,trip_number,' . $id . '|max:50',
            'job_number' => 'nullable|string|max:50',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:drivers,id',
            'pickup_time' => 'required|date',
            'delivery_time' => 'nullable|date',
            'status' => 'required|in:pickup,in_transit,delivered,delayed,cancelled',
            'pickup_location' => 'nullable|string',
            'delivery_location' => 'nullable|string',
            'distance' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'job_id' => 'nullable|exists:transport_jobs,id',
        ]);

        $trip->update($validated);

        return redirect()->route('trips.index')->with('success', 'Trip updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $trip = Trip::findOrFail($id);
        $trip->delete();

        return redirect()->route('trips.index')->with('success', 'Trip deleted successfully.');
    }
}
