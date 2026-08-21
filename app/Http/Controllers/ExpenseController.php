<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Expense;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with(['vehicle', 'driver'])->paginate(15);
        $vehicles = Vehicle::all();
        $drivers = Driver::all();
        $allExpenses = Expense::all();

        return view('pages.expenses', [
            'expenses' => $expenses,
            'vehicles' => $vehicles,
            'drivers' => $drivers,
            'totalExpenses' => $allExpenses->count(),
            'totalAmount' => $allExpenses->sum('amount'),
            'approvedExpenses' => $allExpenses->where('status', 'approved')->count(),
            'pendingExpenses' => $allExpenses->where('status', 'pending_review')->count(),
            'rejectedExpenses' => $allExpenses->where('status', 'rejected')->count(),
        ]);
    }

    public function create()
    {
        $vehicles = Vehicle::all();
        $drivers = Driver::all();
        return view('pages.expenses-create', compact('vehicles', 'drivers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_date' => 'required|date',
            'category' => 'required|string|max:50',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'amount' => 'required|numeric|min:0',
            'submitted_by' => 'required|string|max:255',
            'status' => 'required|in:approved,pending_review,rejected',
            'description' => 'nullable|string',
            'receipt_url' => 'nullable|string|max:255',
            'driver_id' => 'nullable|exists:drivers,id',
        ]);

        Expense::create($validated);
        return redirect()->route('expenses.index')->with('success', 'Expense created successfully.');
    }

    public function show(string $id)
    {
        $expense = Expense::with(['vehicle', 'driver'])->findOrFail($id);
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['expense' => $expense]);
        }
        
        return view('pages.expenses-show', compact('expense'));
    }

    public function edit(string $id)
    {
        $expense = Expense::with(['vehicle', 'driver'])->findOrFail($id);
        $vehicles = Vehicle::all();
        $drivers = Driver::all();
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($expense);
        }
        
        return view('pages.expenses-edit', compact('expense', 'vehicles', 'drivers'));
    }

    public function update(Request $request, string $id)
    {
        $expense = Expense::findOrFail($id);
        $validated = $request->validate([
            'expense_date' => 'required|date',
            'category' => 'required|string|max:50',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'amount' => 'required|numeric|min:0',
            'submitted_by' => 'required|string|max:255',
            'status' => 'required|in:approved,pending_review,rejected',
            'description' => 'nullable|string',
            'receipt_url' => 'nullable|string|max:255',
            'driver_id' => 'nullable|exists:drivers,id',
        ]);

        $expense->update($validated);
        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(string $id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }

    public function export(Request $request)
    {
        $expenses = Expense::with(['vehicle', 'driver'])->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="expenses_export_' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($expenses) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Date', 'Category', 'Amount', 'Vehicle', 'Driver', 'Description', 'Status']);
            
            foreach ($expenses as $expense) {
                fputcsv($file, [
                    $expense->id,
                    $expense->expense_date,
                    $expense->category,
                    $expense->amount,
                    $expense->vehicle ? $expense->vehicle->vehicle_number : 'N/A',
                    $expense->driver ? $expense->driver->name : 'N/A',
                    $expense->description,
                    $expense->status,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
