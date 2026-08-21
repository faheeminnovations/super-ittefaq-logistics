<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the operations dashboard with today's data
     */
    public function index()
    {
        $today = Carbon::today();
        $now = Carbon::now();

        // Get today's trips with relationships
        $todayTrips = \App\Models\Trip::with(['vehicle', 'driver', 'job.customer'])
            ->whereDate('created_at', $today)
            ->latest()
            ->take(5)
            ->get();

        // Get all vehicles and drivers for status counts
        $vehicles = \App\Models\Vehicle::all();
        $drivers = \App\Models\Driver::all();

        // Get today's jobs
        $todayJobs = \App\Models\Job::whereDate('job_date', $today)->get();
        $allJobs = \App\Models\Job::all();

        // Calculate stat cards (today's data)
        $activeTrips = $todayTrips->where('status', 'in_transit')->count();
        $totalTrips = $todayTrips->count();
        $availableVehicles = $vehicles->where('status', 'available')->count();
        $totalVehicles = $vehicles->count();
        $onDutyDrivers = $drivers->where('status', 'on_duty')->count();
        $totalDrivers = $drivers->count();

        // Today's pipeline - job status counts for today
        $totalJobs = $todayJobs->count();
        $pendingJobs = $todayJobs->where('status', 'pending')->count();
        $assignedJobs = $todayJobs->where('status', 'assigned')->count();
        $inTransitJobs = $todayJobs->where('status', 'in_transit')->count();
        $deliveredJobs = $todayJobs->where('status', 'delivered')->count();

        // Calculate expiring documents
        $expiringDocuments = [];
        
        // Check vehicle MOT expirations
        foreach ($vehicles as $vehicle) {
            if ($vehicle->mot_expiry) {
                $days = $now->diffInDays($vehicle->mot_expiry, false);
                if ($days <= 30 && $days >= 0) {
                    $color = $days <= 7 ? '#FBE9E7' : ($days <= 14 ? '#FFF3E0' : '#EDEFF5');
                    $textColor = $days <= 7 ? 'var(--danger)' : ($days <= 14 ? 'var(--warn)' : 'var(--muted)');
                    
                    $expiringDocuments[] = [
                        'title' => "MOT — {$vehicle->reg_no}",
                        'type' => 'Vehicle document',
                        'days' => $days,
                        'icon' => 'bi-file-earmark-x',
                        'color' => $color,
                        'textColor' => $textColor
                    ];
                }
            }
            
            // Check vehicle insurance expirations
            if ($vehicle->insurance_expiry) {
                $days = $now->diffInDays($vehicle->insurance_expiry, false);
                if ($days <= 30 && $days >= 0) {
                    $color = $days <= 7 ? '#FBE9E7' : ($days <= 14 ? '#FFF3E0' : '#EDEFF5');
                    $textColor = $days <= 7 ? 'var(--danger)' : ($days <= 14 ? 'var(--warn)' : 'var(--muted)');
                    
                    $expiringDocuments[] = [
                        'title' => "Insurance — {$vehicle->reg_no}",
                        'type' => 'Vehicle document',
                        'days' => $days,
                        'icon' => 'bi-shield-check',
                        'color' => $color,
                        'textColor' => $textColor
                    ];
                }
            }
        }
        
        // Sort by days remaining
        usort($expiringDocuments, function($a, $b) {
            return $a['days'] - $b['days'];
        });
        
        // Take only top 5
        $expiringDocuments = array_slice($expiringDocuments, 0, 5);

        // Get revenue and expenses data for the last 6 months
        $revenueData = [];
        $expenseData = [];
        $months = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = $now->copy()->subMonths($i);
            $monthName = $monthDate->format('M');
            $months[] = $monthName;
            
            // Revenue from invoices for this month
            $monthlyRevenue = \App\Models\Invoice::whereYear('invoice_date', $monthDate->year)
                ->whereMonth('invoice_date', $monthDate->month)
                ->sum('amount');
            
            // Revenue from billing records for this month (fallback for revenue data)
            $monthlyBillingRevenue = \App\Models\Billing::whereYear('date', $monthDate->year)
                ->whereMonth('date', $monthDate->month)
                ->sum('rent');
            
            // Use the higher of the two or sum them
            $totalRevenue = max($monthlyRevenue, $monthlyBillingRevenue);
            
            // Expenses for this month
            $monthlyExpense = \App\Models\Expense::whereYear('expense_date', $monthDate->year)
                ->whereMonth('expense_date', $monthDate->month)
                ->sum('amount');
            
            $revenueData[] = $totalRevenue;
            $expenseData[] = $monthlyExpense;
        }

        return view('pages.index', [
            // Stat cards
            'activeTrips' => $activeTrips,
            'totalTrips' => $totalTrips,
            'availableVehicles' => $availableVehicles,
            'totalVehicles' => $totalVehicles,
            'onDutyDrivers' => $onDutyDrivers,
            'totalDrivers' => $totalDrivers,
            
            // Today's pipeline
            'totalJobs' => $totalJobs,
            'pendingJobs' => $pendingJobs,
            'assignedJobs' => $assignedJobs,
            'inTransitJobs' => $inTransitJobs,
            'deliveredJobs' => $deliveredJobs,
            
            // Recent trips
            'recentTrips' => $todayTrips,
            
            // Expiring documents
            'expiringDocuments' => $expiringDocuments,
            
            // Revenue vs Expenses chart data
            'revenueData' => $revenueData,
            'expenseData' => $expenseData,
            'chartMonths' => $months,
        ]);
    }
}