<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('customer')->paginate(15);
        $customers = Customer::all();
        $allInvoices = Invoice::all();

        return view('pages.invoices', [
            'invoices' => $invoices,
            'customers' => $customers,
            'totalInvoices' => $allInvoices->count(),
            'totalAmount' => $allInvoices->sum('amount'),
            'paidInvoices' => $allInvoices->where('status', 'paid')->count(),
            'unpaidInvoices' => $allInvoices->where('status', 'unpaid')->count(),
            'overdueInvoices' => $allInvoices->where('status', 'overdue')->count(),
        ]);
    }

    public function create()
    {
        $customers = Customer::all();
        return view('pages.invoices-create', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number|max:50',
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0',
            'vat' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'required|in:paid,unpaid,overdue,cancelled',
            'invoice_date' => 'required|date',
            'paid_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        Invoice::create($validated);
        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function show(string $id)
    {
        $invoice = Invoice::with('customer')->findOrFail($id);
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['invoice' => $invoice]);
        }
        
        return view('pages.invoices-show', compact('invoice'));
    }

    public function edit(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $customers = Customer::all();
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($invoice);
        }
        
        return view('pages.invoices-edit', compact('invoice', 'customers'));
    }

    public function update(Request $request, string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number,' . $id . '|max:50',
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0',
            'vat' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'required|in:paid,unpaid,overdue,cancelled',
            'invoice_date' => 'required|date',
            'paid_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $invoice->update($validated);
        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully.');
    }

    public function destroy(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }

    public function export(Request $request)
    {
        $invoices = Invoice::with('customer')->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="invoices_export_' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($invoices) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Invoice Number', 'Customer', 'Date', 'Due Date', 'Amount', 'Status', 'Notes']);
            
            foreach ($invoices as $invoice) {
                fputcsv($file, [
                    $invoice->id,
                    $invoice->invoice_number,
                    $invoice->customer ? $invoice->customer->name : 'N/A',
                    $invoice->invoice_date,
                    $invoice->due_date,
                    $invoice->amount,
                    $invoice->status,
                    $invoice->notes,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
