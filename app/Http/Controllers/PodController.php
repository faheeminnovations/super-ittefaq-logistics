<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Driver;
use App\Models\Job;
use App\Models\Pod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PodController extends Controller
{
    public function index()
    {
        $pods = Pod::with(['job', 'driver', 'customer'])->paginate(15);
        $customers = Customer::all();
        $drivers = Driver::all();
        $jobs = Job::all();
        $allPods = Pod::all();

        return view('pages.pod', [
            'pods' => $pods,
            'customers' => $customers,
            'drivers' => $drivers,
            'jobs' => $jobs,
            'totalPods' => $allPods->count(),
            'completePods' => $allPods->where('status', 'complete')->count(),
            'missingSignaturePods' => $allPods->where('status', 'missing_signature')->count(),
            'missingPhotoPods' => $allPods->where('status', 'missing_photo')->count(),
        ]);
    }

    public function create()
    {
        return view('pages.pod-create');
    }

    public function store(Request $request)
    {
        \Log::info('POD Store Request:', $request->all());
        
        $validated = $request->validate([
            'job_number' => 'required|string|max:50',
            'customer_id' => 'required|exists:customers,id',
            'driver_id' => 'required|exists:drivers,id',
            'delivery_datetime' => 'required|date',
            'has_signature' => 'nullable|boolean',
            'has_photo' => 'nullable|boolean',
            'delivery_confirmation' => 'nullable|boolean',
            'status' => 'required|in:complete,missing_signature,missing_photo,pending',
            'signature_upload' => 'nullable|image|max:5120', // Max 5MB
            'photo_upload' => 'nullable|image|max:5120', // Max 5MB
            'signature_path' => 'nullable|string|max:255',
            'photo_path' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'job_id' => 'nullable|integer',
        ]);

        // Handle checkbox defaults
        $validated['has_signature'] = $request->has('has_signature') ? true : false;
        $validated['has_photo'] = $request->has('has_photo') ? true : false;
        $validated['delivery_confirmation'] = $request->has('delivery_confirmation') ? true : false;

        // Handle signature upload
        if ($request->hasFile('signature_upload')) {
            $file = $request->file('signature_upload');
            $fileName = 'signature_' . time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('pod/signatures', $fileName, 'public');
            $validated['signature_path'] = $filePath;
            $validated['has_signature'] = true;
            \Log::info('Signature uploaded:', ['path' => $filePath]);
        }

        // Handle photo upload
        if ($request->hasFile('photo_upload')) {
            $file = $request->file('photo_upload');
            $fileName = 'photo_' . time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('pod/photos', $fileName, 'public');
            $validated['photo_path'] = $filePath;
            $validated['has_photo'] = true;
            \Log::info('Photo uploaded:', ['path' => $filePath]);
        }

        // If job_id is provided, validate it exists
        if (!empty($validated['job_id'])) {
            $jobExists = \App\Models\Job::where('id', $validated['job_id'])->exists();
            if (!$jobExists) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['job_id' => 'The selected job ID is invalid.']);
            }
        }

        \Log::info('Creating POD with data:', $validated);
        
        Pod::create($validated);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'POD created successfully.']);
        }
        
        return redirect()->route('pod.index')->with('success', 'POD created successfully.');
    }

    public function show(string $id)
    {
        $pod = Pod::with(['job', 'driver'])->findOrFail($id);
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['pod' => $pod]);
        }
        
        return view('pages.pod-show', compact('pod'));
    }

    public function edit(string $id)
    {
        $pod = Pod::findOrFail($id);
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($pod);
        }
        
        return view('pages.pod-edit', compact('pod'));
    }

    public function update(Request $request, string $id)
    {
        $pod = Pod::findOrFail($id);
        $validated = $request->validate([
            'job_number' => 'required|string|max:50',
            'customer_id' => 'required|exists:customers,id',
            'driver_id' => 'required|exists:drivers,id',
            'delivery_datetime' => 'required|date',
            'has_signature' => 'nullable|boolean',
            'has_photo' => 'nullable|boolean',
            'delivery_confirmation' => 'nullable|boolean',
            'status' => 'required|in:complete,missing_signature,missing_photo,pending',
            'signature_upload' => 'nullable|image|max:5120', // Max 5MB
            'photo_upload' => 'nullable|image|max:5120', // Max 5MB
            'signature_path' => 'nullable|string|max:255',
            'photo_path' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'job_id' => 'nullable|integer',
        ]);

        // Handle checkbox defaults
        $validated['has_signature'] = $request->has('has_signature') ? true : false;
        $validated['has_photo'] = $request->has('has_photo') ? true : false;
        $validated['delivery_confirmation'] = $request->has('delivery_confirmation') ? true : false;

        // Handle signature upload
        if ($request->hasFile('signature_upload')) {
            // Delete old signature if exists
            if ($pod->signature_path) {
                $oldPath = storage_path('app/public/' . $pod->signature_path);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            
            $file = $request->file('signature_upload');
            $fileName = 'signature_' . time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('pod/signatures', $fileName, 'public');
            $validated['signature_path'] = $filePath;
            $validated['has_signature'] = true;
        }

        // Handle photo upload
        if ($request->hasFile('photo_upload')) {
            // Delete old photo if exists
            if ($pod->photo_path) {
                $oldPath = storage_path('app/public/' . $pod->photo_path);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            
            $file = $request->file('photo_upload');
            $fileName = 'photo_' . time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('pod/photos', $fileName, 'public');
            $validated['photo_path'] = $filePath;
            $validated['has_photo'] = true;
        }

        // If job_id is provided, validate it exists
        if (!empty($validated['job_id'])) {
            $jobExists = \App\Models\Job::where('id', $validated['job_id'])->exists();
            if (!$jobExists) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['job_id' => 'The selected job ID is invalid.']);
            }
        }

        $pod->update($validated);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'POD updated successfully.']);
        }
        
        return redirect()->route('pod.index')->with('success', 'POD updated successfully.');
    }

    public function destroy(string $id)
    {
        $pod = Pod::findOrFail($id);
        $pod->delete();
        return redirect()->route('pod.index')->with('success', 'POD deleted successfully.');
    }

    public function export(Request $request)
    {
        $pods = Pod::with(['customer', 'driver', 'job'])->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="pod_export_' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($pods) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Job Number', 'Customer', 'Driver', 'Delivery Date/Time', 'Has Signature', 'Has Photo', 'Delivery Confirmed', 'Status', 'Notes']);
            
            foreach ($pods as $pod) {
                fputcsv($file, [
                    $pod->id,
                    $pod->job_number,
                    $pod->customer ? $pod->customer->name : 'N/A',
                    $pod->driver ? $pod->driver->name : 'N/A',
                    $pod->delivery_datetime,
                    $pod->has_signature ? 'Yes' : 'No',
                    $pod->has_photo ? 'Yes' : 'No',
                    $pod->delivery_confirmation ? 'Yes' : 'No',
                    $pod->status,
                    $pod->notes,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
