@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">Operations</div>
        <h1>Planning / Dispatch</h1>
        <div class="sub">Assign jobs to a vehicle and driver</div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-navy" onclick="exportDispatch()"><i class="bi bi-download me-1"></i> Export</button>
        <button class="btn btn-navy" data-bs-toggle="modal" data-bs-target="#dispatchModal" onclick="resetForm()"><i class="bi bi-plus-lg me-1"></i> Assign Job</button>
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
          <div class="icon-badge" style="background:#EAF0FB;color:var(--navy-800);"><i class="bi bi-inbox"></i></div>
          <div class="label">Unassigned</div>
          <div class="value">{{ $unassignedDispatches ?? 0 }}</div>
          <div class="delta down"><i class="bi bi-arrow-down-short"></i> Needs a vehicle & driver</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#EAF7EF;color:var(--success);"><i class="bi bi-truck-front"></i></div>
          <div class="label">Vehicles Free</div>
          <div class="value">{{ $availableVehicles ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> Ready to dispatch</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FFF3E0;color:var(--warn);"><i class="bi bi-person-check"></i></div>
          <div class="label">Drivers Free</div>
          <div class="value">{{ $availableDrivers ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> Available now</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#E8F4FD;color:var(--navy-600);"><i class="bi bi-signpost-split"></i></div>
          <div class="label">In Transit</div>
          <div class="value">{{ $inTransitDispatches ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> Currently active</div>
        </div>
      </div>
    </div>
    
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <span class="badge-status badge-pending" style="cursor:pointer;" onclick="filterByStatus('all')">All</span> 
      <span class="badge-status badge-pending" style="cursor:pointer;" onclick="filterByStatus('unassigned')">Unassigned</span> 
      <span class="badge-status badge-transit" style="cursor:pointer;" onclick="filterByStatus('assigned')">Assigned</span> 
      <span class="badge-status badge-transit" style="cursor:pointer;" onclick="filterByStatus('in_transit')">In Transit</span> 
      <span class="badge-status badge-delivered" style="cursor:pointer;" onclick="filterByStatus('delivered')">Delivered</span> 
      <span class="badge-status badge-cancelled" style="cursor:pointer;" onclick="filterByStatus('cancelled')">Cancelled</span>
    </div>
    
    <div class="panel">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
          <div class="panel-title mb-0">Dispatch Log</div>
          <div class="panel-sub mb-0">All job assignments and dispatches</div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-striped" id="dispatchTable">
          <thead>
            <tr>
              <th>Job #</th>
              <th>Description</th>
              <th>Vehicle</th>
              <th>Driver</th>
              <th>Pickup Location</th>
              <th>Delivery Location</th>
              <th>Dispatch Time</th>
              <th>Completion Time</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @isset($dispatches)
              @foreach($dispatches as $dispatch)
            <tr>
              <td><span class='mono fw-semibold'>{{ $dispatch->job_number }}</span></td>
              <td>{{ $dispatch->job_description }}</td>
              <td>{{ $dispatch->vehicle ? $dispatch->vehicle->reg_no : 'Unassigned' }}</td>
              <td>{{ $dispatch->driver ? $dispatch->driver->name : 'Unassigned' }}</td>
              <td>{{ $dispatch->pickup_location ?? 'N/A' }}</td>
              <td>{{ $dispatch->delivery_location ?? 'N/A' }}</td>
              <td>{{ $dispatch->dispatch_time ? \Carbon\Carbon::parse($dispatch->dispatch_time)->format('d M Y H:i') : 'N/A' }}</td>
              <td>{{ $dispatch->completion_time ? \Carbon\Carbon::parse($dispatch->completion_time)->format('d M Y H:i') : 'N/A' }}</td>
              <td>
                @switch($dispatch->status)
                  @case('unassigned')
                    <span class="badge-status badge-pending">Unassigned</span>
                    @break
                  @case('assigned')
                    <span class="badge-status badge-transit">Assigned</span>
                    @break
                  @case('in_transit')
                    <span class="badge-status badge-transit">In Transit</span>
                    @break
                  @case('delivered')
                    <span class="badge-status badge-delivered">Delivered</span>
                    @break
                  @case('cancelled')
                    <span class="badge-status badge-cancelled">Cancelled</span>
                    @break
                  @default
                    <span class="badge-status badge-pending">{{ ucfirst(str_replace('_', ' ', $dispatch->status)) }}</span>
                @endswitch
              </td>
              <td>
                <div class="btn-group btn-group-sm">
                  <button type="button" class="btn btn-outline-primary" onclick="editDispatch({{ $dispatch->id }})" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button type="button" class="btn btn-outline-info" onclick="viewDispatch({{ $dispatch->id }})" title="View">
                    <i class="bi bi-eye"></i>
                  </button>
                  <button type="button" class="btn btn-outline-danger" onclick="deleteDispatch({{ $dispatch->id }})" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            @endforeach
            @else
            <tr><td colspan="10" class="text-center">No dispatches found</td></tr>
            @endisset
          </tbody>
        </table>
      </div>
    </div>
    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
      &copy; 2026 Super Ittefaq Logistics &middot; Transport Management System
    </div>
  </div>

  <!-- Create/Edit Dispatch Modal -->
  <div class="modal fade" id="dispatchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="dispatchModalLabel">New Dispatch</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="dispatchForm" action="{{ route('dispatch.store') }}" method="POST">
          @csrf
          <div class="modal-body">
            <input type="hidden" name="dispatch_id" id="dispatch_id">
            <input type="hidden" name="_method" id="_method" value="POST">
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="job_number" class="form-label">Job Number</label>
                <input type="text" class="form-control" name="job_number" id="job_number" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="job_description" class="form-label">Job Description</label>
                <input type="text" class="form-control" name="job_description" id="job_description" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="vehicle_id" class="form-label">Vehicle (Optional)</label>
                <select class="form-select" name="vehicle_id" id="vehicle_id">
                  <option value="">Select Vehicle</option>
                  @if(isset($vehicles))
                    @foreach($vehicles as $vehicle)
                      <option value="{{ $vehicle->id }}">{{ $vehicle->reg_no }} - {{ $vehicle->make_model }}</option>
                    @endforeach
                  @else
                    @foreach(\App\Models\Vehicle::all() as $vehicle)
                      <option value="{{ $vehicle->id }}">{{ $vehicle->reg_no }} - {{ $vehicle->make_model }}</option>
                    @endforeach
                  @endif
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="driver_id" class="form-label">Driver (Optional)</label>
                <select class="form-select" name="driver_id" id="driver_id">
                  <option value="">Select Driver</option>
                  @if(isset($drivers))
                    @foreach($drivers as $driver)
                      <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                    @endforeach
                  @else
                    @foreach(\App\Models\Driver::all() as $driver)
                      <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                    @endforeach
                  @endif
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="pickup_location" class="form-label">Pickup Location</label>
                <input type="text" class="form-control" name="pickup_location" id="pickup_location">
              </div>
              <div class="col-md-6 mb-3">
                <label for="delivery_location" class="form-label">Delivery Location</label>
                <input type="text" class="form-control" name="delivery_location" id="delivery_location">
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="dispatch_time" class="form-label">Dispatch Time</label>
                <input type="datetime-local" class="form-control" name="dispatch_time" id="dispatch_time">
              </div>
              <div class="col-md-6 mb-3">
                <label for="completion_time" class="form-label">Completion Time</label>
                <input type="datetime-local" class="form-control" name="completion_time" id="completion_time">
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" name="status" id="status" required>
                  <option value="unassigned">Unassigned</option>
                  <option value="assigned">Assigned</option>
                  <option value="in_transit">In Transit</option>
                  <option value="delivered">Delivered</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="job_id" class="form-label">Associated Job (Optional)</label>
                <select class="form-select" name="job_id" id="job_id">
                  <option value="">Select Job</option>
                  @foreach(\App\Models\Job::all() as $job)
                    <option value="{{ $job->id }}">{{ $job->job_number }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="mb-3">
              <label for="notes" class="form-label">Notes</label>
              <textarea class="form-control" name="notes" id="notes" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Dispatch</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- View Dispatch Modal -->
  <div class="modal fade" id="viewDispatchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Dispatch Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="viewDispatchContent">
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
    $('#dispatchTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: 9 } // Actions column
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search dispatches..."
        }
    });

    // Form submission
    $('#dispatchForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var dispatchId = $('#dispatch_id').val();
        var url = dispatchId ? '/dispatch/' + dispatchId : '{{ route("dispatch.store") }}';
        var method = 'POST';
        
        // Set _method to PUT for updates
        if (dispatchId) {
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
                $('#dispatchModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                var errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                var errorMessage = 'Error saving dispatch';
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
    $('#dispatchForm')[0].reset();
    $('#dispatch_id').val('');
    $('#_method').val('POST');
    $('#dispatchModalLabel').text('New Dispatch');
}

function editDispatch(id) {
    $.ajax({
        url: '/dispatch/' + id + '/edit',
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(dispatch) {
            fillDispatchForm(dispatch);
            $('#dispatchModal').modal('show');
        },
        error: function() {
            alert('Error loading dispatch data');
        }
    });
}

function fillDispatchForm(dispatch) {
    $('#dispatch_id').val(dispatch.id);
    $('#job_number').val(dispatch.job_number);
    $('#job_description').val(dispatch.job_description);
    $('#vehicle_id').val(dispatch.vehicle_id);
    $('#driver_id').val(dispatch.driver_id);
    $('#pickup_location').val(dispatch.pickup_location);
    $('#delivery_location').val(dispatch.delivery_location);
    $('#status').val(dispatch.status);
    $('#job_id').val(dispatch.job_id);
    $('#notes').val(dispatch.notes);
    
    // Format datetime for input type="datetime-local"
    if (dispatch.dispatch_time) {
        var dispatchDate = new Date(dispatch.dispatch_time);
        var formattedDispatch = dispatchDate.toISOString().slice(0, 16);
        $('#dispatch_time').val(formattedDispatch);
    }
    
    if (dispatch.completion_time) {
        var completionDate = new Date(dispatch.completion_time);
        var formattedCompletion = completionDate.toISOString().slice(0, 16);
        $('#completion_time').val(formattedCompletion);
    }
    
    $('#dispatchModalLabel').text('Edit Dispatch');
}

function viewDispatch(id) {
    $.ajax({
        url: '/dispatch/' + id,
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(data) {
            var dispatch = data.dispatch || data;
            var html = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Dispatch Information</h6>
                        <p><strong>Job Number:</strong> ${dispatch.job_number}</p>
                        <p><strong>Description:</strong> ${dispatch.job_description}</p>
                        <p><strong>Pickup Location:</strong> ${dispatch.pickup_location || 'N/A'}</p>
                        <p><strong>Delivery Location:</strong> ${dispatch.delivery_location || 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Assignment Details</h6>
                        <p><strong>Vehicle:</strong> ${dispatch.vehicle ? dispatch.vehicle.reg_no + ' - ' + dispatch.vehicle.make_model : 'Unassigned'}</p>
                        <p><strong>Driver:</strong> ${dispatch.driver ? dispatch.driver.name : 'Unassigned'}</p>
                        <p><strong>Status:</strong> ${dispatch.status}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6>Timing</h6>
                        <p><strong>Dispatch Time:</strong> ${dispatch.dispatch_time ? new Date(dispatch.dispatch_time).toLocaleString() : 'N/A'}</p>
                        <p><strong>Completion Time:</strong> ${dispatch.completion_time ? new Date(dispatch.completion_time).toLocaleString() : 'N/A'}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Notes</h6>
                        <p>${dispatch.notes || 'No notes provided'}</p>
                    </div>
                </div>
            `;
            $('#viewDispatchContent').html(html);
            $('#viewDispatchModal').modal('show');
        },
        error: function() {
            alert('Error loading dispatch details');
        }
    });
}

function deleteDispatch(id) {
    if (confirm('Are you sure you want to delete this dispatch?')) {
        $.ajax({
            url: '/dispatch/' + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            },
            error: function() {
                alert('Error deleting dispatch');
            }
        });
    }
}

function filterByStatus(status) {
    var table = $('#dispatchTable').DataTable();
    if (status === 'all') {
        table.column(8).search('').draw();
    } else {
        table.column(8).search(status).draw();
    }
}

function exportDispatch() {
    alert('Export functionality will be implemented to export dispatch data as CSV/Excel');
}
</script>
@endpush

