<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehicles = Vehicle::paginate(15);
        $allVehicles = Vehicle::all();

        return view('pages.vehicles', [
            'vehicles' => $vehicles,
            'totalVehicles' => $allVehicles->count(),
            'availableVehicles' => $allVehicles->where('status', 'available')->count(),
            'onTripVehicles' => $allVehicles->where('status', 'on_trip')->count(),
            'maintenanceVehicles' => $allVehicles->where('status', 'maintenance')->count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.vehicles-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reg_no' => 'required|string|unique:vehicles,reg_no|max:20',
            'type' => 'required|string|max:50',
            'make_model' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'mot_expiry' => 'required|date',
            'insurance_expiry' => 'required|date',
            'status' => 'required|in:available,on_trip,maintenance,out_of_service',
            'fuel_capacity' => 'nullable|numeric|min:0',
            'vin' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        Vehicle::create($validated);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        
        // Return JSON for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['vehicle' => $vehicle]);
        }
        
        return view('pages.vehicles-show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        
        // Return JSON for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($vehicle);
        }
        
        return view('pages.vehicles-edit', compact('vehicle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $validated = $request->validate([
            'reg_no' => 'required|string|unique:vehicles,reg_no,' . $id . '|max:20',
            'type' => 'required|string|max:50',
            'make_model' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'mot_expiry' => 'required|date',
            'insurance_expiry' => 'required|date',
            'status' => 'required|in:available,on_trip,maintenance,out_of_service',
            'fuel_capacity' => 'nullable|numeric|min:0',
            'vin' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $vehicle->update($validated);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->delete();

        return redirect()->route('vehicles.index')->with('success', 'Vehicle deleted successfully.');
    }

    public function export(Request $request)
    {
        $vehicles = Vehicle::all();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="vehicles_export_' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($vehicles) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Vehicle Number', 'Make/Model', 'Year', 'Type', 'Status', 'Fuel Type', 'Capacity', 'Current Mileage']);
            
            foreach ($vehicles as $vehicle) {
                fputcsv($file, [
                    $vehicle->id,
                    $vehicle->vehicle_number,
                    $vehicle->make_model,
                    $vehicle->year,
                    $vehicle->type,
                    $vehicle->status,
                    $vehicle->fuel_type,
                    $vehicle->capacity,
                    $vehicle->current_mileage,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
