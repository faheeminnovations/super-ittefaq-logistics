<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::paginate(15);
        return view('pages.reports', compact('reports'));
    }

    public function create()
    {
        return view('pages.reports-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_name' => 'required|string|max:255',
            'report_type' => 'required|string|max:50',
            'report_date' => 'required|date',
            'revenue' => 'nullable|numeric|min:0',
            'expenses' => 'nullable|numeric|min:0',
            'profit' => 'nullable|numeric',
            'total_mileage' => 'nullable|integer',
            'total_jobs' => 'nullable|integer',
            'completed_jobs' => 'nullable|integer',
            'generated_data' => 'nullable|string',
        ]);

        Report::create($validated);
        return redirect()->route('reports.index')->with('success', 'Report created successfully.');
    }

    public function show(string $id)
    {
        $report = Report::findOrFail($id);
        return view('pages.reports-show', compact('report'));
    }

    public function edit(string $id)
    {
        $report = Report::findOrFail($id);
        return view('pages.reports-edit', compact('report'));
    }

    public function update(Request $request, string $id)
    {
        $report = Report::findOrFail($id);
        $validated = $request->validate([
            'report_name' => 'required|string|max:255',
            'report_type' => 'required|string|max:50',
            'report_date' => 'required|date',
            'revenue' => 'nullable|numeric|min:0',
            'expenses' => 'nullable|numeric|min:0',
            'profit' => 'nullable|numeric',
            'total_mileage' => 'nullable|integer',
            'total_jobs' => 'nullable|integer',
            'completed_jobs' => 'nullable|integer',
            'generated_data' => 'nullable|string',
        ]);

        $report->update($validated);
        return redirect()->route('reports.index')->with('success', 'Report updated successfully.');
    }

    public function destroy(string $id)
    {
        $report = Report::findOrFail($id);
        $report->delete();
        return redirect()->route('reports.index')->with('success', 'Report deleted successfully.');
    }
}
