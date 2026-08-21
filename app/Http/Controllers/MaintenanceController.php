<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenances = Maintenance::with('vehicle')->paginate(15);
        $vehicles = Vehicle::all();
        $allMaintenance = Maintenance::all();

        return view('pages.maintenance', [
            'maintenances' => $maintenances,
            'vehicles' => $vehicles,
            'totalMaintenance' => $allMaintenance->count(),
            'scheduledMaintenance' => $allMaintenance->where('status', 'scheduled')->count(),
            'inProgressMaintenance' => $allMaintenance->where('status', 'in_progress')->count(),
            'completedMaintenance' => $allMaintenance->where('status', 'completed')->count(),
        ]);
    }

    public function create()
    {
        $vehicles = Vehicle::all();
        return view('pages.maintenance-create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'service_type' => 'required|string|max:50',
            'service_date' => 'required|date',
            'workshop' => 'required|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'description' => 'nullable|string',
            'mileage' => 'nullable|integer',
        ]);

        Maintenance::create($validated);
        return redirect()->route('maintenance.index')->with('success', 'Maintenance record created successfully.');
    }

    public function show(string $id)
    {
        $maintenance = Maintenance::with('vehicle')->findOrFail($id);
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['maintenance' => $maintenance]);
        }
        
        return view('pages.maintenance-show', compact('maintenance'));
    }

    public function edit(string $id)
    {
        $maintenance = Maintenance::with('vehicle')->findOrFail($id);
        $vehicles = Vehicle::all();
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($maintenance);
        }
        
        return view('pages.maintenance-edit', compact('maintenance', 'vehicles'));
    }

    public function update(Request $request, string $id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'service_type' => 'required|string|max:50',
            'service_date' => 'required|date',
            'workshop' => 'required|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'description' => 'nullable|string',
            'mileage' => 'nullable|integer',
        ]);

        $maintenance->update($validated);
        return redirect()->route('maintenance.index')->with('success', 'Maintenance record updated successfully.');
    }

    public function destroy(string $id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $maintenance->delete();
        return redirect()->route('maintenance.index')->with('success', 'Maintenance record deleted successfully.');
    }

    public function export(Request $request)
    {
        $maintenances = Maintenance::with('vehicle')->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="maintenance_export_' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($maintenances) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Vehicle', 'Type', 'Date', 'Cost', 'Description', 'Status', 'Next Service Date']);
            
            foreach ($maintenances as $maintenance) {
                fputcsv($file, [
                    $maintenance->id,
                    $maintenance->vehicle ? $maintenance->vehicle->vehicle_number : 'N/A',
                    $maintenance->type,
                    $maintenance->date,
                    $maintenance->cost,
                    $maintenance->description,
                    $maintenance->status,
                    $maintenance->next_service_date,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
