@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">Fleet & People</div>
        <h1>Maintenance</h1>
        <div class="sub">Service, repairs, MOT & inspections</div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-navy" onclick="exportMaintenance()"><i class="bi bi-download me-1"></i> Export</button>
        <button class="btn btn-navy" data-bs-toggle="modal" data-bs-target="#maintenanceModal" onclick="resetForm()"><i class="bi bi-plus-lg me-1"></i> Schedule Service</button>
      </div>
    </div>
    
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <div class="row g-3 mb-3">
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#EAF0FB;color:var(--navy-800);"><i class="bi bi-tools"></i></div>
          <div class="label">Total Records</div>
          <div class="value">{{ $totalMaintenance ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> All maintenance</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FFF3E0;color:var(--warn);"><i class="bi bi-exclamation-triangle"></i></div>
          <div class="label">Scheduled</div>
          <div class="value">{{ $scheduledMaintenance ?? 0 }}</div>
          <div class="delta down"><i class="bi bi-arrow-down-short"></i> Upcoming services</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FBE9E7;color:var(--danger);"><i class="bi bi-truck-front"></i></div>
          <div class="label">In Progress</div>
          <div class="value">{{ $inProgressMaintenance ?? 0 }}</div>
          <div class="delta down"><i class="bi bi-arrow-down-short"></i> Currently in workshop</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#EAF7EF;color:var(--success);"><i class="bi bi-check-circle"></i></div>
          <div class="label">Completed</div>
          <div class="value">{{ $completedMaintenance ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> Finished services</div>
        </div>
      </div>
    </div>
    
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <span class="badge-status badge-pending" style="cursor:pointer;" onclick="filterByStatus('all')">All</span> 
      <span class="badge-status badge-pending" style="cursor:pointer;" onclick="filterByStatus('scheduled')">Scheduled</span> 
      <span class="badge-status badge-transit" style="cursor:pointer;" onclick="filterByStatus('in_progress')">In Progress</span> 
      <span class="badge-status badge-delivered" style="cursor:pointer;" onclick="filterByStatus('completed')">Completed</span> 
      <span class="badge-status badge-delayed" style="cursor:pointer;" onclick="filterByStatus('cancelled')">Cancelled</span> 
    </div>
    
    <div class="panel">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
          <div class="panel-title mb-0">Maintenance Schedule</div>
          <div class="panel-sub mb-0">Servicing, repairs & inspection history</div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-striped" id="maintenanceTable">
          <thead>
            <tr>
              <th>Vehicle</th>
              <th>Service Type</th>
              <th>Date</th>
              <th>Workshop</th>
              <th>Cost</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @isset($maintenances)
              @foreach($maintenances as $maintenance)
            <tr>
              <td>{{ $maintenance->vehicle ? $maintenance->vehicle->plate_number : 'N/A' }}</td>
              <td>{{ $maintenance->service_type }}</td>
              <td>{{ \Carbon\Carbon::parse($maintenance->service_date)->format('d M Y') }}</td>
              <td>{{ $maintenance->workshop }}</td>
              <td>{{ $maintenance->formatted_cost }}</td>
              <td>
                @switch($maintenance->status)
                  @case('scheduled')
                    <span class="badge-status badge-pending">Scheduled</span>
                    @break
                  @case('in_progress')
                    <span class="badge-status badge-transit">In Progress</span>
                    @break
                  @case('completed')
                    <span class="badge-status badge-delivered">Completed</span>
                    @break
                  @case('cancelled')
                    <span class="badge-status badge-delayed">Cancelled</span>
                    @break
                  @default
                    <span class="badge-status badge-pending">{{ ucfirst(str_replace('_', ' ', $maintenance->status)) }}</span>
                @endswitch
              </td>
              <td>
                <div class="btn-group btn-group-sm">
                  <button type="button" class="btn btn-outline-primary" onclick="editMaintenance({{ $maintenance->id }})" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button type="button" class="btn btn-outline-info" onclick="viewMaintenance({{ $maintenance->id }})" title="View">
                    <i class="bi bi-eye"></i>
                  </button>
                  <button type="button" class="btn btn-outline-danger" onclick="deleteMaintenance({{ $maintenance->id }})" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            @endforeach
            @else
            <tr><td colspan="7" class="text-center">No maintenance records found</td></tr>
            @endisset
          </tbody>
        </table>
      </div>
    </div>
    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
      &copy; 2026 Super Ittefaq Logistics &middot; Transport Management System
    </div>
  </div>

  <!-- Create/Edit Maintenance Modal -->
  <div class="modal fade" id="maintenanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="maintenanceModalLabel">Schedule Service</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="maintenanceForm" action="{{ route('maintenance.store') }}" method="POST">
          @csrf
          <div class="modal-body">
            <input type="hidden" name="maintenance_id" id="maintenance_id">
            <input type="hidden" name="_method" id="_method" value="POST">
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="vehicle_id" class="form-label">Vehicle</label>
                <select class="form-select" name="vehicle_id" id="vehicle_id" required>
                  <option value="">Select Vehicle</option>
                  @foreach($vehicles ?? [] as $vehicle)
                    <option value="{{ $vehicle->id }}">{{ $vehicle->plate_number }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="service_type" class="form-label">Service Type</label>
                <input type="text" class="form-control" name="service_type" id="service_type" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="service_date" class="form-label">Service Date</label>
                <input type="date" class="form-control" name="service_date" id="service_date" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="workshop" class="form-label">Workshop</label>
                <input type="text" class="form-control" name="workshop" id="workshop" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="cost" class="form-label">Cost</label>
                <input type="number" step="0.01" class="form-control" name="cost" id="cost">
              </div>
              <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" name="status" id="status" required>
                  <option value="scheduled">Scheduled</option>
                  <option value="in_progress">In Progress</option>
                  <option value="completed">Completed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="mileage" class="form-label">Mileage</label>
                <input type="number" class="form-control" name="mileage" id="mileage">
              </div>
            </div>

            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control" name="description" id="description" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Maintenance</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- View Maintenance Modal -->
  <div class="modal fade" id="viewMaintenanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Maintenance Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="viewMaintenanceContent">
          <!-- Content will be loaded dynamically -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#maintenanceTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[2, 'desc']],
        columnDefs: [
            { orderable: false, targets: 6 } // Actions column
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search maintenance..."
        }
    });

    // Form submission
    $('#maintenanceForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var maintenanceId = $('#maintenance_id').val();
        var url = maintenanceId ? '/maintenance/' + maintenanceId : '{{ route("maintenance.store") }}';
        var method = 'POST';
        
        // Set _method to PUT for updates
        if (maintenanceId) {
            $('#_method').val('PUT');
        } else {
            $('#_method').val('POST');
        }
        
        $.ajax({
            url: url,
            type: method,
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#maintenanceModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                var errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                var errorMessage = 'Error saving maintenance';
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
});

function resetForm() {
    $('#maintenanceForm')[0].reset();
    $('#maintenance_id').val('');
    $('#_method').val('POST');
    $('#maintenanceModalLabel').text('Schedule Service');
}

function editMaintenance(id) {
    $.ajax({
        url: '/maintenance/' + id + '/edit',
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(maintenance) {
            fillMaintenanceForm(maintenance);
            $('#maintenanceModal').modal('show');
        },
        error: function() {
            alert('Error loading maintenance data');
        }
    });
}

function fillMaintenanceForm(maintenance) {
    $('#maintenance_id').val(maintenance.id);
    $('#vehicle_id').val(maintenance.vehicle_id);
    $('#service_type').val(maintenance.service_type);
    $('#service_date').val(maintenance.service_date);
    $('#workshop').val(maintenance.workshop);
    $('#cost').val(maintenance.cost);
    $('#status').val(maintenance.status);
    $('#mileage').val(maintenance.mileage);
    $('#description').val(maintenance.description);
    
    $('#maintenanceModalLabel').text('Edit Maintenance');
}

function viewMaintenance(id) {
    $.ajax({
        url: '/maintenance/' + id,
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(data) {
            var maintenance = data.maintenance || data;
            var html = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Service Information</h6>
                        <p><strong>Vehicle:</strong> ${maintenance.vehicle ? maintenance.vehicle.plate_number : 'N/A'}</p>
                        <p><strong>Service Type:</strong> ${maintenance.service_type}</p>
                        <p><strong>Service Date:</strong> ${maintenance.service_date}</p>
                        <p><strong>Workshop:</strong> ${maintenance.workshop}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Cost & Status</h6>
                        <p><strong>Cost:</strong> PKR ${parseFloat(maintenance.cost || 0).toFixed(2)}</p>
                        <p><strong>Status:</strong> ${maintenance.status}</p>
                        <p><strong>Mileage:</strong> ${maintenance.mileage || 'N/A'}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Description</h6>
                        <p>${maintenance.description || 'No description provided'}</p>
                    </div>
                </div>
            `;
            $('#viewMaintenanceContent').html(html);
            $('#viewMaintenanceModal').modal('show');
        },
        error: function() {
            alert('Error loading maintenance details');
        }
    });
}

function deleteMaintenance(id) {
    if (confirm('Are you sure you want to delete this maintenance record?')) {
        $.ajax({
            url: '/maintenance/' + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            },
            error: function() {
                alert('Error deleting maintenance');
            }
        });
    }
}

function filterByStatus(status) {
    var table = $('#maintenanceTable').DataTable();
    if (status === 'all') {
        table.column(5).search('').draw();
    } else {
        table.column(5).search(status).draw();
    }
}

function exportMaintenance() {
    fetch('/maintenance/export', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'text/csv'
        },
    })
    .then(response => response.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'maintenance_export_' + new Date().toISOString().split('T')[0] + '.csv';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    })
    .catch(error => {
        console.error('Export error:', error);
        alert('Error exporting maintenance data');
    });
}
</script>
@endpush

