<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class DispatchController extends Controller
{
    public function index()
    {
        $dispatches = Dispatch::with(['vehicle', 'driver'])->paginate(15);
        $allDispatches = Dispatch::all();
        $vehicles = Vehicle::all();
        $drivers = Driver::all();

        return view('pages.dispatch', [
            'dispatches' => $dispatches,
            'unassignedDispatches' => $allDispatches->where('status', 'unassigned')->count(),
            'inTransitDispatches' => $allDispatches->where('status', 'in_transit')->count(),
            'availableVehicles' => $vehicles->where('status', 'available')->count(),
            'availableDrivers' => $drivers->where('status', 'on_duty')->count(),
            'vehicles' => $vehicles,
            'drivers' => $drivers,
        ]);
    }

    public function create()
    {
        $vehicles = Vehicle::where('status', 'available')->get();
        $drivers = Driver::where('status', 'on_duty')->get();
        return view('pages.dispatch-create', compact('vehicles', 'drivers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_number' => 'required|string|unique:dispatches,job_number|max:50',
            'job_description' => 'required|string',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'pickup_location' => 'nullable|string',
            'delivery_location' => 'nullable|string',
            'status' => 'required|in:unassigned,assigned,in_transit,delivered,cancelled',
            'dispatch_time' => 'nullable|date',
            'completion_time' => 'nullable|date',
            'notes' => 'nullable|string',
            'job_id' => 'nullable|exists:transport_jobs,id',
        ]);

        Dispatch::create($validated);
        return redirect()->route('dispatch.index')->with('success', 'Dispatch created successfully.');
    }

    public function show(string $id)
    {
        $dispatch = Dispatch::with(['vehicle', 'driver'])->findOrFail($id);
        
        // Return JSON for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['dispatch' => $dispatch]);
        }
        
        return view('pages.dispatch-show', compact('dispatch'));
    }

    public function edit(string $id)
    {
        $dispatch = Dispatch::with(['vehicle', 'driver'])->findOrFail($id);
        
        // Return JSON for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($dispatch);
        }
        
        $vehicles = Vehicle::all();
        $drivers = Driver::all();
        return view('pages.dispatch-edit', compact('dispatch', 'vehicles', 'drivers'));
    }

    public function update(Request $request, string $id)
    {
        $dispatch = Dispatch::findOrFail($id);
        $validated = $request->validate([
            'job_number' => 'required|string|unique:dispatches,job_number,' . $id . '|max:50',
            'job_description' => 'required|string',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'pickup_location' => 'nullable|string',
            'delivery_location' => 'nullable|string',
            'status' => 'required|in:unassigned,assigned,in_transit,delivered,cancelled',
            'dispatch_time' => 'nullable|date',
            'completion_time' => 'nullable|date',
            'notes' => 'nullable|string',
            'job_id' => 'nullable|exists:transport_jobs,id',
        ]);

        $dispatch->update($validated);
        return redirect()->route('dispatch.index')->with('success', 'Dispatch updated successfully.');
    }

    public function destroy(string $id)
    {
        $dispatch = Dispatch::findOrFail($id);
        $dispatch->delete();
        return redirect()->route('dispatch.index')->with('success', 'Dispatch deleted successfully.');
    }

    public function export(Request $request)
    {
        $dispatches = Dispatch::with(['vehicle', 'driver'])->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="dispatch_export_' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($dispatches) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Vehicle', 'Driver', 'Date', 'Status', 'Notes']);
            
            foreach ($dispatches as $dispatch) {
                fputcsv($file, [
                    $dispatch->id,
                    $dispatch->vehicle ? $dispatch->vehicle->vehicle_number : 'N/A',
                    $dispatch->driver ? $dispatch->driver->name : 'N/A',
                    $dispatch->date,
                    $dispatch->status,
                    $dispatch->notes,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
