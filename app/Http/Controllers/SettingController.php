<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all();
        $setting = $settings->first();
        
        // Create default settings if none exist (Pakistan business default)
        if (!$setting) {
            $setting = Setting::create([
                'company_name' => 'Super Ittefaq Logistics',
                'operator_licence_no' => '',
                'vat_number' => '',
                'ntn_number' => '',
                'strn_number' => '',
                'currency' => 'PKR',
                'distance_unit' => 'kilometres',
                'document_reminder_window' => '30/14/7',
                'address' => '',
                'phone' => '',
                'email' => '',
                'website' => '',
                'gst_rate' => 17.00,
                'invoice_prefix' => 'INV-',
                'quotation_prefix' => 'QUO-',
                'bank_name' => '',
                'bank_account_number' => '',
                'bank_iban' => '',
            ]);
            $settings = Setting::all();
        }
        
        return view('pages.settings', compact('settings', 'setting'));
    }

    public function create()
    {
        return view('pages.settings-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'operator_licence_no' => 'nullable|string|max:50',
            'vat_number' => 'nullable|string|max:50',
            'ntn_number' => 'nullable|string|max:50',
            'strn_number' => 'nullable|string|max:50',
            'currency' => 'required|string|max:10',
            'distance_unit' => 'required|string|max:20',
            'document_reminder_window' => 'required|string|max:50',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'gst_rate' => 'nullable|numeric|min:0|max:100',
            'invoice_prefix' => 'nullable|string|max:10',
            'quotation_prefix' => 'nullable|string|max:10',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_iban' => 'nullable|string|max:50',
        ]);

        Setting::create($validated);
        return redirect()->route('settings.index')->with('success', 'Setting created successfully.');
    }

    public function show(string $id)
    {
        $setting = Setting::findOrFail($id);
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['setting' => $setting]);
        }
        
        return view('pages.settings-show', compact('setting'));
    }

    public function edit(string $id)
    {
        $setting = Setting::findOrFail($id);
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($setting);
        }
        
        return view('pages.settings-edit', compact('setting'));
    }

    public function update(Request $request, string $id)
    {
        $setting = Setting::findOrFail($id);
        
        // For partial updates, only validate fields that are present
        $rules = [
            'company_name' => 'sometimes|required|string|max:255',
            'operator_licence_no' => 'nullable|string|max:50',
            'vat_number' => 'nullable|string|max:50',
            'ntn_number' => 'nullable|string|max:50',
            'strn_number' => 'nullable|string|max:50',
            'currency' => 'sometimes|required|string|max:10',
            'distance_unit' => 'sometimes|required|string|max:20',
            'document_reminder_window' => 'sometimes|required|string|max:50',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'gst_rate' => 'nullable|numeric|min:0|max:100',
            'invoice_prefix' => 'nullable|string|max:10',
            'quotation_prefix' => 'nullable|string|max:10',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_iban' => 'nullable|string|max:50',
        ];

        $validated = $request->validate($rules);

        // Only update fields that are present in the request
        $updateData = [];
        foreach ($validated as $key => $value) {
            if ($request->has($key)) {
                $updateData[$key] = $value;
            }
        }

        $setting->update($updateData);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => 'Settings updated successfully.']);
        }
        
        return redirect()->route('settings.index')->with('success', 'Setting updated successfully.');
    }

    public function destroy(string $id)
    {
        $setting = Setting::findOrFail($id);
        $setting->delete();
        return redirect()->route('settings.index')->with('success', 'Setting deleted successfully.');
    }
}
