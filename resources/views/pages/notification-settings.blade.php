@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Notification Settings</h5>
                    <button class="btn btn-primary" onclick="saveSettings()">
                        <i class="bi bi-check-lg me-1"></i>Save Settings
                    </button>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Configure your notification preferences for different modules and actions. Choose whether you want to receive notifications in the app and/or via email.
                    </div>
                    
                    <div id="settingsContainer">
                        @php
                            $modules = ['billing', 'customer', 'vehicle', 'driver', 'job', 'trip', 'dispatch', 'expense', 'invoice'];
                            $types = ['save', 'update', 'delete'];
                        @endphp
                        
                        @foreach($modules as $module)
                        <div class="settings-section">
                            <h6>{{ ucfirst($module) }}</h6>
                            <table class="settings-table">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>App Notification</th>
                                        <th>Email Notification</th>
                                        <th>WhatsApp Notification</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($types as $type)
                                    <tr>
                                        <td>
                                            <span class="action-badge {{ $type }}">{{ ucfirst($type) }}</span>
                                        </td>
                                        <td>
                                            <label class="toggle-switch">
                                                <input type="checkbox" 
                                                       data-module="{{ $module }}" 
                                                       data-type="{{ $type }}" 
                                                       data-setting="app_enabled"
                                                       checked>
                                                <span class="slider"></span>
                                            </label>
                                            <span class="toggle-label">Enabled</span>
                                        </td>
                                        <td>
                                            <label class="toggle-switch">
                                                <input type="checkbox" 
                                                       data-module="{{ $module }}" 
                                                       data-type="{{ $type }}" 
                                                       data-setting="email_enabled">
                                                <span class="slider"></span>
                                            </label>
                                            <span class="toggle-label">Disabled</span>
                                        </td>
                                        <td>
                                            <label class="toggle-switch">
                                                <input type="checkbox" 
                                                       data-module="{{ $module }}" 
                                                       data-type="{{ $type }}" 
                                                       data-setting="whatsapp_enabled">
                                                <span class="slider"></span>
                                            </label>
                                            <span class="toggle-label">Disabled</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.settings-section {
    margin-bottom: 24px;
}

.settings-section h6 {
    color: var(--navy-900);
    font-weight: 600;
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 2px solid var(--navy-200);
}

.settings-table {
    width: 100%;
    border-collapse: collapse;
}

.settings-table th {
    background-color: var(--navy-50);
    padding: 12px 16px;
    text-align: left;
    font-weight: 600;
    color: var(--navy-900);
    border-bottom: 2px solid var(--navy-200);
}

.settings-table td {
    padding: 12px 16px;
    border-bottom: 1px solid #eee;
}

.settings-table tr:hover {
    background-color: #f8f9fa;
}

.settings-table .module-name {
    font-weight: 500;
    color: var(--navy-900);
}

.settings-table .action-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.settings-table .action-badge.save {
    background-color: #d4edda;
    color: #155724;
}

.settings-table .action-badge.update {
    background-color: #d1ecf1;
    color: #0c5460;
}

.settings-table .action-badge.delete {
    background-color: #f8d7da;
    color: #721c24;
}

.settings-table .toggle-switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
}

.settings-table .toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.settings-table .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 24px;
}

.settings-table .slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

.settings-table input:checked + .slider {
    background-color: var(--navy-600);
}

.settings-table input:checked + .slider:before {
    transform: translateX(20px);
}

.settings-table .toggle-label {
    margin-left: 8px;
    font-size: 13px;
    color: #666;
}

.loading-state {
    text-align: center;
    padding: 60px 20px;
    color: #999;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #999;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
}
</style>

<script>
function saveSettings() {
    const settings = [];
    
    // Collect all settings from the form
    document.querySelectorAll('input[data-module]').forEach(input => {
        const module = input.dataset.module;
        const type = input.dataset.type;
        const setting = input.dataset.setting;
        const value = input.checked;
        
        // Find or create setting object
        let settingObj = settings.find(s => s.module === module && s.notification_type === type);
        if (!settingObj) {
            settingObj = {
                module: module,
                notification_type: type,
                email_enabled: false,
                app_enabled: true,
                whatsapp_enabled: false
            };
            settings.push(settingObj);
        }
        
        settingObj[setting] = value;
    });
    
    // Send to server
    fetch('{{ route("notifications.update-settings") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ settings: settings })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Settings saved successfully!');
        } else {
            alert('Error saving settings');
        }
    })
    .catch(error => {
        console.error('Error saving settings:', error);
        alert('Error saving settings');
    });
}
</script>
@endsection