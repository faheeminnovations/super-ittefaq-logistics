@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">Fleet & People</div>
        <h1>Drivers</h1>
        <div class="sub">Driver profiles, licences, CPC & documents</div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-navy" onclick="exportDrivers()"><i class="bi bi-download me-1"></i> Export</button>
        <button class="btn btn-navy" data-bs-toggle="modal" data-bs-target="#driverModal" onclick="resetForm()"><i class="bi bi-plus-lg me-1"></i> Add Driver</button>
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
          <div class="icon-badge" style="background:#EAF0FB;color:var(--navy-800);"><i class="bi bi-person-badge"></i></div>
          <div class="label">Total Drivers</div>
          <div class="value">{{ $totalDrivers ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> Full-time & agency</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#EAF7EF;color:var(--success);"><i class="bi bi-check-circle"></i></div>
          <div class="label">On Duty</div>
          <div class="value">{{ $onDutyDrivers ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> Available for work</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#E8F4FD;color:var(--navy-600);"><i class="bi bi-truck"></i></div>
          <div class="label">On Trip</div>
          <div class="value">{{ $onTripDrivers ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> Currently on delivery</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FBE9E7;color:var(--danger);"><i class="bi bi-calendar-x"></i></div>
          <div class="label">On Leave</div>
          <div class="value">{{ $onLeaveDrivers ?? 0 }}</div>
          <div class="delta down"><i class="bi bi-arrow-down-short"></i> Not available</div>
        </div>
      </div>
    </div>
    
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <span class="badge-status badge-pending" style="cursor:pointer;" onclick="filterByStatus('all')">All</span> 
      <span class="badge-status badge-delivered" style="cursor:pointer;" onclick="filterByStatus('on_duty')">On Duty</span> 
      <span class="badge-status badge-transit" style="cursor:pointer;" onclick="filterByStatus('on_trip')">On Trip</span> 
      <span class="badge-status badge-pending" style="cursor:pointer;" onclick="filterByStatus('on_leave')">On Leave</span> 
      <span class="badge-status badge-delayed" style="cursor:pointer;" onclick="filterByStatus('suspended')">Suspended</span> 
      <span class="badge-status badge-cancelled" style="cursor:pointer;" onclick="filterByStatus('licence_expired')">Licence Expired</span>
    </div>
    
    <div class="panel">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
          <div class="panel-title mb-0">Driver Roster</div>
          <div class="panel-sub mb-0">All employed and agency drivers</div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-striped" id="driversTable">
          <thead>
            <tr>
              <th>Driver</th>
              <th>Licence No</th>
              <th>Category</th>
              <th>CPC Expiry</th>
              <th>Licence Expiry</th>
              <th>Phone</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @isset($drivers)
              @foreach($drivers as $driver)
            <tr>
              <td>
                <div class='driver-mini'>
                  <div class='av'>{{ substr($driver->name, 0, 2) }}</div>
                  {{ $driver->name }}
                </div>
              </td>
              <td><span class='mono'>{{ $driver->licence_no }}</span></td>
              <td>{{ $driver->category }}</td>
              <td>{{ \Carbon\Carbon::parse($driver->cpc_expiry)->format('d M Y') }}</td>
              <td>{{ $driver->licence_expiry ? \Carbon\Carbon::parse($driver->licence_expiry)->format('d M Y') : 'N/A' }}</td>
              <td>{{ $driver->phone }}</td>
              <td>
                @switch($driver->status)
                  @case('on_duty')
                    <span class="badge-status badge-delivered">On Duty</span>
                    @break
                  @case('on_trip')
                    <span class="badge-status badge-transit">On Trip</span>
                    @break
                  @case('on_leave')
                    <span class="badge-status badge-pending">On Leave</span>
                    @break
                  @case('suspended')
                    <span class="badge-status badge-delayed">Suspended</span>
                    @break
                  @case('licence_expired')
                    <span class="badge-status badge-cancelled">Licence Expired</span>
                    @break
                  @default
                    <span class="badge-status badge-pending">{{ ucfirst(str_replace('_', ' ', $driver->status)) }}</span>
                @endswitch
              </td>
              <td>
                <div class="btn-group btn-group-sm">
                  <button type="button" class="btn btn-outline-primary" onclick="editDriver({{ $driver->id }})" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button type="button" class="btn btn-outline-info" onclick="viewDriver({{ $driver->id }})" title="View">
                    <i class="bi bi-eye"></i>
                  </button>
                  <button type="button" class="btn btn-outline-danger" onclick="deleteDriver({{ $driver->id }})" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            @endforeach
            @else
            <tr><td colspan="8" class="text-center">No drivers found</td></tr>
            @endisset
          </tbody>
        </table>
      </div>
    </div>
    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
      &copy; 2026 Super Ittefaq Logistics &middot; Transport Management System
    </div>
  </div>

  <!-- Create/Edit Driver Modal -->
  <div class="modal fade" id="driverModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="driverModalLabel">New Driver</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="driverForm" action="{{ route('drivers.store') }}" method="POST">
          @csrf
          <div class="modal-body">
            <input type="hidden" name="driver_id" id="driver_id">
            <input type="hidden" name="_method" id="_method" value="POST">
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Driver Name</label>
                <input type="text" class="form-control" name="name" id="name" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="licence_no" class="form-label">Licence Number</label>
                <input type="text" class="form-control" name="licence_no" id="licence_no" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" name="category" id="category" required>
                  <option value="">Select Category</option>
                  <option value="Cat B">Cat B</option>
                  <option value="Cat C">Cat C</option>
                  <option value="Cat C+E">Cat C+E</option>
                  <option value="Cat D">Cat D</option>
                  <option value="Cat D+E">Cat D+E</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" name="phone" id="phone" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="cpc_expiry" class="form-label">CPC Expiry Date</label>
                <input type="date" class="form-control" name="cpc_expiry" id="cpc_expiry" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="licence_expiry" class="form-label">Licence Expiry Date</label>
                <input type="date" class="form-control" name="licence_expiry" id="licence_expiry">
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" name="status" id="status" required>
                  <option value="on_duty">On Duty</option>
                  <option value="on_trip">On Trip</option>
                  <option value="on_leave">On Leave</option>
                  <option value="suspended">Suspended</option>
                  <option value="licence_expired">Licence Expired</option>
                </select>
              </div>
            </div>

            <div class="mb-3">
              <label for="address" class="form-label">Address</label>
              <textarea class="form-control" name="address" id="address" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Driver</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- View Driver Modal -->
  <div class="modal fade" id="viewDriverModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Driver Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="viewDriverContent">
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
    $('#driversTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: 7 } // Actions column
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search drivers..."
        }
    });

    // Form submission
    $('#driverForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var driverId = $('#driver_id').val();
        var url = driverId ? '/drivers/' + driverId : '{{ route("drivers.store") }}';
        var method = 'POST';
        
        // Set _method to PUT for updates
        if (driverId) {
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
                $('#driverModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                var errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                var errorMessage = 'Error saving driver';
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
    $('#driverForm')[0].reset();
    $('#driver_id').val('');
    $('#_method').val('POST');
    $('#driverModalLabel').text('New Driver');
}

function editDriver(id) {
    $.ajax({
        url: '/drivers/' + id + '/edit',
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(driver) {
            fillDriverForm(driver);
            $('#driverModal').modal('show');
        },
        error: function() {
            alert('Error loading driver data');
        }
    });
}

function fillDriverForm(driver) {
    $('#driver_id').val(driver.id);
    $('#name').val(driver.name);
    $('#licence_no').val(driver.licence_no);
    $('#category').val(driver.category);
    $('#phone').val(driver.phone);
    $('#status').val(driver.status);
    $('#address').val(driver.address);
    
    // Format dates for input type="date"
    if (driver.cpc_expiry) {
        var cpcDate = new Date(driver.cpc_expiry);
        var formattedCpc = cpcDate.toISOString().split('T')[0];
        $('#cpc_expiry').val(formattedCpc);
    }
    
    if (driver.licence_expiry) {
        var licenceDate = new Date(driver.licence_expiry);
        var formattedLicence = licenceDate.toISOString().split('T')[0];
        $('#licence_expiry').val(formattedLicence);
    }
    
    $('#driverModalLabel').text('Edit Driver');
}

function viewDriver(id) {
    $.ajax({
        url: '/drivers/' + id,
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(data) {
            var driver = data.driver || data;
            var html = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Driver Information</h6>
                        <p><strong>Name:</strong> ${driver.name}</p>
                        <p><strong>Licence No:</strong> ${driver.licence_no}</p>
                        <p><strong>Category:</strong> ${driver.category}</p>
                        <p><strong>Phone:</strong> ${driver.phone}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Licence Details</h6>
                        <p><strong>CPC Expiry:</strong> ${new Date(driver.cpc_expiry).toLocaleDateString()}</p>
                        <p><strong>Licence Expiry:</strong> ${driver.licence_expiry ? new Date(driver.licence_expiry).toLocaleDateString() : 'N/A'}</p>
                        <p><strong>Status:</strong> ${driver.status}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Address</h6>
                        <p>${driver.address || 'No address provided'}</p>
                    </div>
                </div>
            `;
            $('#viewDriverContent').html(html);
            $('#viewDriverModal').modal('show');
        },
        error: function() {
            alert('Error loading driver details');
        }
    });
}

function deleteDriver(id) {
    if (confirm('Are you sure you want to delete this driver?')) {
        $.ajax({
            url: '/drivers/' + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            },
            error: function() {
                alert('Error deleting driver');
            }
        });
    }
}

function filterByStatus(status) {
    var table = $('#driversTable').DataTable();
    if (status === 'all') {
        table.column(6).search('').draw();
    } else {
        table.column(6).search(status).draw();
    }
}

function exportDrivers() {
    fetch('/drivers/export', {
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
        a.download = 'drivers_export_' + new Date().toISOString().split('T')[0] + '.csv';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    })
    .catch(error => {
        console.error('Export error:', error);
        alert('Error exporting drivers data');
    });
}
</script>
@endpush

