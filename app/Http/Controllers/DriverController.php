<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $drivers = Driver::paginate(15);
        $allDrivers = Driver::all();

        return view('pages.drivers', [
            'drivers' => $drivers,
            'totalDrivers' => $allDrivers->count(),
            'onDutyDrivers' => $allDrivers->where('status', 'on_duty')->count(),
            'onTripDrivers' => $allDrivers->where('status', 'on_trip')->count(),
            'onLeaveDrivers' => $allDrivers->where('status', 'on_leave')->count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.drivers-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'licence_no' => 'required|string|unique:drivers,licence_no|max:50',
            'category' => 'required|string|max:10',
            'cpc_expiry' => 'required|date',
            'phone' => 'required|string|max:20',
            'status' => 'required|in:on_trip,on_duty,on_leave,suspended,licence_expired',
            'address' => 'nullable|string',
            'licence_expiry' => 'nullable|date',
        ]);

        Driver::create($validated);

        return redirect()->route('drivers.index')->with('success', 'Driver created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $driver = Driver::findOrFail($id);
        
        // Return JSON for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['driver' => $driver]);
        }
        
        return view('pages.drivers-show', compact('driver'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $driver = Driver::findOrFail($id);
        
        // Return JSON for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($driver);
        }
        
        return view('pages.drivers-edit', compact('driver'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $driver = Driver::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'licence_no' => 'required|string|unique:drivers,licence_no,' . $id . '|max:50',
            'category' => 'required|string|max:10',
            'cpc_expiry' => 'required|date',
            'phone' => 'required|string|max:20',
            'status' => 'required|in:on_trip,on_duty,on_leave,suspended,licence_expired',
            'address' => 'nullable|string',
            'licence_expiry' => 'nullable|date',
        ]);

        $driver->update($validated);

        return redirect()->route('drivers.index')->with('success', 'Driver updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $driver = Driver::findOrFail($id);
        $driver->delete();

        return redirect()->route('drivers.index')->with('success', 'Driver deleted successfully.');
    }

    public function export(Request $request)
    {
        $drivers = Driver::all();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="drivers_export_' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($drivers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Phone', 'License Number', 'License Expiry', 'Status', 'Address', 'Hire Date']);
            
            foreach ($drivers as $driver) {
                fputcsv($file, [
                    $driver->id,
                    $driver->name,
                    $driver->phone,
                    $driver->license_number,
                    $driver->license_expiry,
                    $driver->status,
                    $driver->address,
                    $driver->hire_date,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
