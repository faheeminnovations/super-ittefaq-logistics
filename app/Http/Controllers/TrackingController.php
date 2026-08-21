<?php

namespace App\Http\Controllers;

use App\Models\Tracking;
use App\Services\TraccarService;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    protected $traccarService;

    public function __construct(TraccarService $traccarService)
    {
        $this->traccarService = $traccarService;
    }

    public function index()
    {
        // Try to get live data from Traccar
        $traccarData = $this->traccarService->getLiveTrackingData();
        
        if (!empty($traccarData)) {
            // Use Traccar data
            $vehicles = collect($traccarData)->map(function($device) {
                $status = $this->determineDeviceStatus($device);
                return [
                    'id' => $device['device_id'],
                    'reg' => $device['name'] ?? 'Unknown',
                    'driver' => 'Traccar Device',
                    'status' => $status,
                    'lat' => $device['latitude'] ?? 0,
                    'lng' => $device['longitude'] ?? 0,
                    'loc' => $device['address'] ?? 'Unknown location',
                    'speed' => $this->convertSpeed($device['speed'] ?? 0),
                    'color' => $this->getStatusColor($status),
                    'job_number' => null,
                    'tracked_at' => $device['fix_time']
                ];
            })->toArray();

            // Calculate statistics from Traccar data
            $totalVehicles = count($vehicles);
            $movingVehicles = collect($vehicles)->where('status', 'moving')->count();
            $idleVehicles = collect($vehicles)->where('status', 'idle')->count();
            $offlineVehicles = collect($vehicles)->where('status', 'offline')->count();
            $delayedVehicles = collect($vehicles)->where('status', 'delayed')->count();
        } else {
            // Fallback to local database tracking
            $tracking = Tracking::with(['vehicle', 'driver'])
                ->orderBy('tracked_at', 'desc')
                ->get()
                ->unique('vehicle_id')
                ->values();

            // Calculate statistics
            $totalVehicles = $tracking->count();
            $movingVehicles = $tracking->where('status', 'moving')->count();
            $idleVehicles = $tracking->where('status', 'idle')->count();
            $offlineVehicles = $tracking->where('status', 'offline')->count();
            $delayedVehicles = $tracking->where('status', 'delayed')->count();

            // Format tracking data for JavaScript
            $vehicles = $tracking->map(function($track) {
                return [
                    'id' => $track->id,
                    'reg' => $track->vehicle->reg_no ?? 'N/A',
                    'driver' => $track->driver->name ?? 'Unknown',
                    'status' => $track->status ?? 'offline',
                    'lat' => $track->latitude ?? 0,
                    'lng' => $track->longitude ?? 0,
                    'loc' => $track->location_description ?? 'Unknown location',
                    'speed' => $track->speed ? $track->speed . ' mph' : '0 mph',
                    'color' => $this->getStatusColor($track->status),
                    'job_number' => $track->job_number,
                    'tracked_at' => $track->tracked_at
                ];
            })->toArray();
        }

        return view('pages.tracking', compact(
            'vehicles',
            'totalVehicles',
            'movingVehicles',
            'idleVehicles',
            'offlineVehicles',
            'delayedVehicles'
        ));
    }

    private function determineDeviceStatus($device)
    {
        $speed = $device['speed'] ?? 0;
        $lastUpdate = $device['last_update'] ?? null;
        
        // If speed > 0, consider moving
        if ($speed > 0) {
            return 'moving';
        }
        
        // If last update is recent (within 5 minutes), consider idle
        if ($lastUpdate) {
            $lastUpdate = strtotime($lastUpdate);
            $now = time();
            if (($now - $lastUpdate) < 300) { // 5 minutes
                return 'idle';
            }
        }
        
        // Otherwise consider offline
        return 'offline';
    }

    private function convertSpeed($speedInKnots)
    {
        // Convert from knots to mph (1 knot = 1.15078 mph)
        $speedInMph = $speedInKnots * 1.15078;
        return round($speedInMph) . ' mph';
    }

    private function getStatusColor($status)
    {
        $colors = [
            'moving' => '#2E7D5B',
            'idle' => '#6B7688',
            'delayed' => '#C9822A',
            'offline' => '#C0392B'
        ];
        return $colors[$status] ?? '#6B7688';
    }

    public function create()
    {
        return view('pages.tracking-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location_description' => 'nullable|string',
            'speed' => 'nullable|integer',
            'status' => 'required|in:moving,idle,delayed,offline',
            'job_number' => 'nullable|string|max:50',
            'tracked_at' => 'required|date',
            'job_id' => 'nullable|exists:transport_jobs,id',
        ]);

        Tracking::create($validated);
        return redirect()->route('tracking.index')->with('success', 'Tracking record created successfully.');
    }

    public function show(string $id)
    {
        $tracking = Tracking::with(['vehicle', 'driver'])->findOrFail($id);
        return view('pages.tracking-show', compact('tracking'));
    }

    public function edit(string $id)
    {
        $tracking = Tracking::findOrFail($id);
        return view('pages.tracking-edit', compact('tracking'));
    }

    public function update(Request $request, string $id)
    {
        $tracking = Tracking::findOrFail($id);
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location_description' => 'nullable|string',
            'speed' => 'nullable|integer',
            'status' => 'required|in:moving,idle,delayed,offline',
            'job_number' => 'nullable|string|max:50',
            'tracked_at' => 'required|date',
            'job_id' => 'nullable|exists:transport_jobs,id',
        ]);

        $tracking->update($validated);
        return redirect()->route('tracking.index')->with('success', 'Tracking record updated successfully.');
    }

    public function destroy(string $id)
    {
        $tracking = Tracking::findOrFail($id);
        $tracking->delete();
        return redirect()->route('tracking.index')->with('success', 'Tracking record deleted successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $tracking = Tracking::with(['vehicle', 'driver'])
            ->whereHas('vehicle', function($q) use ($query) {
                $q->where('reg_no', 'like', "%{$query}%");
            })
            ->orWhereHas('driver', function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->orWhere('location_description', 'like', "%{$query}%")
            ->orWhere('job_number', 'like', "%{$query}%")
            ->latest('tracked_at')
            ->limit(20)
            ->get();
            
        return response()->json($tracking);
    }
}