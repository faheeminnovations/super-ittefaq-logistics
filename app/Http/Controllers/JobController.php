<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Driver;
use App\Models\Job;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobs = Job::with(['customer', 'vehicle', 'driver'])->paginate(15);
        $allJobs = Job::all();
        $customers = Customer::all();
        $vehicles = Vehicle::all();
        $drivers = Driver::all();

        return view('pages.jobs', [
            'jobs' => $jobs,
            'totalJobs' => $allJobs->count(),
            'pendingJobs' => $allJobs->where('status', 'pending')->count(),
            'assignedJobs' => $allJobs->where('status', 'assigned')->count(),
            'inTransitJobs' => $allJobs->where('status', 'in_transit')->count(),
            'deliveredJobs' => $allJobs->where('status', 'delivered')->count(),
            'customers' => $customers,
            'vehicles' => $vehicles,
            'drivers' => $drivers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::all();
        $vehicles = Vehicle::where('status', 'available')->get();
        $drivers = Driver::where('status', 'on_duty')->get();

        return view('pages.jobs-create', compact('customers', 'vehicles', 'drivers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_number' => 'nullable|string|unique:transport_jobs,job_number|max:50',
            'customer_id' => 'required|exists:customers,id',
            'pickup_location' => 'required|string',
            'delivery_location' => 'required|string',
            'job_date' => 'required|date',
            'status' => 'required|in:pending,assigned,in_transit,delivered,delayed,cancelled',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'notes' => 'nullable|string',
            'quoted_price' => 'nullable|numeric|min:0',
            'bags' => 'nullable|integer|min:0',
            'rent' => 'nullable|numeric|min:0',
            'advance' => 'nullable|numeric|min:0',
            'advance_date' => 'nullable|date',
            'dues' => 'nullable|numeric|min:0',
        ]);

        // Generate job number if not provided
        if (empty($validated['job_number'])) {
            $validated['job_number'] = 'JOB-' . str_pad(Job::count() + 1, 4, '0', STR_PAD_LEFT);
        }

        Job::create($validated);

        return redirect()->route('jobs.index')->with('success', 'Job created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $job = Job::with(['customer', 'vehicle', 'driver'])->findOrFail($id);
        
        // Return JSON for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['job' => $job]);
        }
        
        return view('pages.jobs-show', compact('job'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $job = Job::with(['customer', 'vehicle', 'driver'])->findOrFail($id);
        
        // Return JSON for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($job);
        }
        
        $customers = Customer::all();
        $vehicles = Vehicle::all();
        $drivers = Driver::all();

        return view('pages.jobs-edit', compact('job', 'customers', 'vehicles', 'drivers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $job = Job::findOrFail($id);

        $validated = $request->validate([
            'job_number' => 'nullable|string|unique:transport_jobs,job_number,' . $id . '|max:50',
            'customer_id' => 'required|exists:customers,id',
            'pickup_location' => 'required|string',
            'delivery_location' => 'required|string',
            'job_date' => 'required|date',
            'status' => 'required|in:pending,assigned,in_transit,delivered,delayed,cancelled',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'notes' => 'nullable|string',
            'quoted_price' => 'nullable|numeric|min:0',
            'bags' => 'nullable|integer|min:0',
            'rent' => 'nullable|numeric|min:0',
            'advance' => 'nullable|numeric|min:0',
            'advance_date' => 'nullable|date',
            'dues' => 'nullable|numeric|min:0',
        ]);

        $job->update($validated);

        return redirect()->route('jobs.index')->with('success', 'Job updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $job = Job::findOrFail($id);
        $job->delete();

        return redirect()->route('jobs.index')->with('success', 'Job deleted successfully.');
    }

    public function export(Request $request)
    {
        $jobs = Job::with(['customer', 'vehicle', 'driver'])->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="jobs_export_' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($jobs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Job Number', 'Customer', 'Vehicle', 'Driver', 'Pickup Location', 'Drop Location', 'Pickup Date', 'Status', 'Amount', 'Notes']);
            
            foreach ($jobs as $job) {
                fputcsv($file, [
                    $job->id,
                    $job->job_number,
                    $job->customer ? $job->customer->name : 'N/A',
                    $job->vehicle ? $job->vehicle->vehicle_number : 'N/A',
                    $job->driver ? $job->driver->name : 'N/A',
                    $job->pickup_location,
                    $job->drop_location,
                    $job->pickup_date,
                    $job->status,
                    $job->amount ?? 0,
                    $job->notes,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
