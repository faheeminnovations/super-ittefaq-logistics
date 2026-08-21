<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Driver;
use App\Models\Job;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }

        $results = [];

        try {
            // Search Vehicles (simplified - avoid complex relationships)
            $vehicles = Vehicle::where('reg_no', 'like', "%{$query}%")
                ->orWhere('make_model', 'like', "%{$query}%")
                ->limit(3)
                ->get();

            foreach ($vehicles as $vehicle) {
                $results[] = [
                    'type' => 'vehicle',
                    'title' => $vehicle->reg_no,
                    'subtitle' => $vehicle->make_model ?? 'Unknown make/model',
                    'extra' => $vehicle->status ?? 'Unknown status',
                    'url' => url('/vehicles'),
                    'icon' => 'bi-truck-front'
                ];
            }

            // Search Drivers (simplified)
            $drivers = Driver::where('name', 'like', "%{$query}%")
                ->limit(3)
                ->get();

            foreach ($drivers as $driver) {
                $results[] = [
                    'type' => 'driver',
                    'title' => $driver->name,
                    'subtitle' => $driver->phone ?? 'No phone',
                    'extra' => $driver->licence_no ?? 'No license',
                    'url' => url('/drivers'),
                    'icon' => 'bi-person-badge'
                ];
            }

            // Search Customers (simplified)
            $customers = Customer::where('name', 'like', "%{$query}%")
                ->limit(3)
                ->get();

            foreach ($customers as $customer) {
                $results[] = [
                    'type' => 'customer',
                    'title' => $customer->name,
                    'subtitle' => $customer->phone ?? 'No phone',
                    'extra' => $customer->email ?? 'No email',
                    'url' => url('/customers'),
                    'icon' => 'bi-people'
                ];
            }

        } catch (\Exception $e) {
            \Log::error('Global search error: ' . $e->getMessage());
            return response()->json(['error' => 'Search failed'], 500);
        }

        return response()->json($results);
    }
}