@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">System</div>
        <h1>Settings</h1>
        <div class="sub">Pakistan Business Setup & System Preferences</div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-navy" onclick="exportSettings()"><i class="bi bi-download me-1"></i> Export</button>
        <button class="btn btn-navy" onclick="saveAllSettings()"><i class="bi bi-save me-1"></i> Save All</button>
      </div>
    </div>
    
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <div class="row g-3">
      <div class="col-lg-6">
        <div class="panel">
          <div class="panel-title">Company Profile</div>
          <div class="panel-sub">Basic details used across invoices & documents</div>
          <form id="companyProfileForm" action="{{ route('settings.update', $setting->id) }}" method="POST">
            @csrf
            <input type="hidden" name="setting_id" value="{{ $setting->id }}">
            <div class="mb-3">
              <label for="company_name" class="form-label">Company Name</label>
              <input type="text" class="form-control" name="company_name" id="company_name" value="{{ $setting->company_name ?? 'Super Ittefaq Logistics' }}" required>
            </div>
            <div class="mb-3">
              <label for="operator_licence_no" class="form-label">Operator's Licence No.</label>
              <input type="text" class="form-control" name="operator_licence_no" id="operator_licence_no" value="{{ $setting->operator_licence_no ?? '' }}">
            </div>
            <div class="mb-3">
              <label for="address" class="form-label">Address</label>
              <textarea class="form-control" name="address" id="address" rows="3">{{ $setting->address ?? '' }}</textarea>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" name="phone" id="phone" value="{{ $setting->phone ?? '' }}">
              </div>
              <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="email" value="{{ $setting->email ?? '' }}">
              </div>
            </div>
            <div class="mb-3">
              <label for="website" class="form-label">Website</label>
              <input type="url" class="form-control" name="website" id="website" value="{{ $setting->website ?? '' }}" placeholder="https://www.example.com">
            </div>
            <button type="submit" class="btn btn-navy">Save Company Profile</button>
          </form>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="panel">
          <div class="panel-title">Tax Information (Pakistan)</div>
          <div class="panel-sub">Pakistan tax registration numbers & GST rate</div>
          <form id="taxForm" action="{{ route('settings.update', $setting->id) }}" method="POST">
            @csrf
            <input type="hidden" name="setting_id" value="{{ $setting->id }}">
            <div class="mb-3">
              <label for="ntn_number" class="form-label">NTN Number (National Tax Number)</label>
              <input type="text" class="form-control" name="ntn_number" id="ntn_number" value="{{ $setting->ntn_number ?? '' }}" placeholder="e.g., 1234567-8">
            </div>
            <div class="mb-3">
              <label for="strn_number" class="form-label">STRN Number (Sales Tax Registration)</label>
              <input type="text" class="form-control" name="strn_number" id="strn_number" value="{{ $setting->strn_number ?? '' }}" placeholder="e.g., 12-34-5678-9">
            </div>
            <div class="mb-3">
              <label for="vat_number" class="form-label">VAT Number (International)</label>
              <input type="text" class="form-control" name="vat_number" id="vat_number" value="{{ $setting->vat_number ?? '' }}">
            </div>
            <div class="mb-3">
              <label for="gst_rate" class="form-label">GST Rate (%)</label>
              <input type="number" class="form-control" name="gst_rate" id="gst_rate" value="{{ $setting->gst_rate ?? 17.00 }}" step="0.01" min="0" max="100">
              <small class="text-muted">Standard Pakistan GST rate is 17%</small>
            </div>
            <button type="submit" class="btn btn-navy">Save Tax Information</button>
          </form>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="panel">
          <div class="panel-title">System Preferences</div>
          <div class="panel-sub">Defaults applied across the platform</div>
          <form id="systemPreferencesForm" action="{{ route('settings.update', $setting->id) }}" method="POST">
            @csrf
            <input type="hidden" name="setting_id" value="{{ $setting->id }}">
            <div class="mb-3">
              <label for="currency" class="form-label">Currency</label>
              <select class="form-select" name="currency" id="currency" required>
                <option value="PKR" {{ ($setting->currency ?? 'PKR') == 'PKR' ? 'selected' : '' }}>PKR (Rs) - Pakistan Rupee</option>
                <option value="GBP" {{ ($setting->currency ?? 'PKR') == 'GBP' ? 'selected' : '' }}>GBP (£) - British Pound</option>
                <option value="USD" {{ ($setting->currency ?? 'PKR') == 'USD' ? 'selected' : '' }}>USD ($) - US Dollar</option>
                <option value="EUR" {{ ($setting->currency ?? 'PKR') == 'EUR' ? 'selected' : '' }}>EUR (€) - Euro</option>
                <option value="AED" {{ ($setting->currency ?? 'PKR') == 'AED' ? 'selected' : '' }}>AED (د.إ) - UAE Dirham</option>
                <option value="SAR" {{ ($setting->currency ?? 'PKR') == 'SAR' ? 'selected' : '' }}>SAR (﷼) - Saudi Riyal</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="distance_unit" class="form-label">Distance Unit</label>
              <select class="form-select" name="distance_unit" id="distance_unit" required>
                <option value="kilometres" {{ ($setting->distance_unit ?? 'kilometres') == 'kilometres' ? 'selected' : '' }}>Kilometres (km)</option>
                <option value="miles" {{ ($setting->distance_unit ?? 'kilometres') == 'miles' ? 'selected' : '' }}>Miles</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="document_reminder_window" class="form-label">Document Reminder Window</label>
              <select class="form-select" name="document_reminder_window" id="document_reminder_window" required>
                <option value="30/14/7" {{ ($setting->document_reminder_window ?? '30/14/7') == '30/14/7' ? 'selected' : '' }}>30 / 14 / 7 days</option>
                <option value="60/30/7" {{ ($setting->document_reminder_window ?? '30/14/7') == '60/30/7' ? 'selected' : '' }}>60 / 30 / 7 days</option>
                <option value="90/60/30" {{ ($setting->document_reminder_window ?? '30/14/7') == '90/60/30' ? 'selected' : '' }}>90 / 60 / 30 days</option>
              </select>
            </div>
            <button type="submit" class="btn btn-navy">Save Preferences</button>
          </form>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="panel">
          <div class="panel-title">Business Preferences</div>
          <div class="panel-sub">Invoice & Quotation settings</div>
          <form id="businessPreferencesForm" action="{{ route('settings.update', $setting->id) }}" method="POST">
            @csrf
            <input type="hidden" name="setting_id" value="{{ $setting->id }}">
            <div class="mb-3">
              <label for="invoice_prefix" class="form-label">Invoice Prefix</label>
              <input type="text" class="form-control" name="invoice_prefix" id="invoice_prefix" value="{{ $setting->invoice_prefix ?? 'INV-' }}" placeholder="INV-">
            </div>
            <div class="mb-3">
              <label for="quotation_prefix" class="form-label">Quotation Prefix</label>
              <input type="text" class="form-control" name="quotation_prefix" id="quotation_prefix" value="{{ $setting->quotation_prefix ?? 'QUO-' }}" placeholder="QUO-">
            </div>
            <button type="submit" class="btn btn-navy">Save Business Preferences</button>
          </form>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="panel">
          <div class="panel-title">Banking Information</div>
          <div class="panel-sub">Bank details for invoices & payments</div>
          <form id="bankingForm" action="{{ route('settings.update', $setting->id) }}" method="POST">
            @csrf
            <input type="hidden" name="setting_id" value="{{ $setting->id }}">
            <div class="mb-3">
              <label for="bank_name" class="form-label">Bank Name</label>
              <input type="text" class="form-control" name="bank_name" id="bank_name" value="{{ $setting->bank_name ?? '' }}" placeholder="e.g., HBL, MCB, UBL">
            </div>
            <div class="mb-3">
              <label for="bank_account_number" class="form-label">Account Number</label>
              <input type="text" class="form-control" name="bank_account_number" id="bank_account_number" value="{{ $setting->bank_account_number ?? '' }}">
            </div>
            <div class="mb-3">
              <label for="bank_iban" class="form-label">IBAN</label>
              <input type="text" class="form-control" name="bank_iban" id="bank_iban" value="{{ $setting->bank_iban ?? '' }}" placeholder="PKXXHABB0000000000000000">
            </div>
            <button type="submit" class="btn btn-navy">Save Banking Information</button>
          </form>
        </div>
      </div>
    </div>
    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
      &copy; 2026 Super Ittefaq Logistics &middot; Transport Management System
    </div>
  </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Generic form handler for all settings forms
    function handleFormSubmit(formId, successMessage) {
        $(formId).on('submit', function(e) {
            e.preventDefault();
            
            var formData = $(this).serialize() + '&_method=PUT';
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    alert(successMessage);
                    location.reload();
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                    var errorMessage = 'Error saving settings';
                    if (errors) {
                        errorMessage = '';
                        $.each(errors, function(key, value) {
                            errorMessage += value + '\n';
                        });
                    }
                    alert(errorMessage);
                }
            });
        });
    }

    // Initialize all form handlers
    handleFormSubmit('#companyProfileForm', 'Company profile saved successfully!');
    handleFormSubmit('#taxForm', 'Tax information saved successfully!');
    handleFormSubmit('#systemPreferencesForm', 'System preferences saved successfully!');
    handleFormSubmit('#businessPreferencesForm', 'Business preferences saved successfully!');
    handleFormSubmit('#bankingForm', 'Banking information saved successfully!');
});

function saveAllSettings() {
    // Trigger all form submissions sequentially
    $('#companyProfileForm').submit();
    setTimeout(function() {
        $('#taxForm').submit();
    }, 300);
    setTimeout(function() {
        $('#systemPreferencesForm').submit();
    }, 600);
    setTimeout(function() {
        $('#businessPreferencesForm').submit();
    }, 900);
    setTimeout(function() {
        $('#bankingForm').submit();
    }, 1200);
}

function exportSettings() {
    alert('Export functionality will be implemented to export settings as JSON/CSV');
}
</script>
@endpush

