@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">Operations</div>
        <h1>Trips</h1>
        <div class="sub">Pickup, transit & delivery tracking</div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-navy" onclick="exportTrips()"><i class="bi bi-download me-1"></i> Export</button>
        <button class="btn btn-navy" data-bs-toggle="modal" data-bs-target="#tripModal" onclick="resetForm()"><i class="bi bi-plus-lg me-1"></i> Start Trip</button>
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
          <div class="icon-badge" style="background:#EAF0FB;color:var(--navy-800);"><i class="bi bi-signpost-split"></i></div>
          <div class="label">Total Trips</div>
          <div class="value">{{ $totalTrips ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> All time</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#E8F4FD;color:var(--navy-600);"><i class="bi bi-truck-front"></i></div>
          <div class="label">In Transit</div>
          <div class="value">{{ $inTransitTrips ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> Currently active</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#EAF7EF;color:var(--success);"><i class="bi bi-check2-circle"></i></div>
          <div class="label">Delivered</div>
          <div class="value">{{ $deliveredTrips ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> Completed trips</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FBE9E7;color:var(--danger);"><i class="bi bi-exclamation-triangle"></i></div>
          <div class="label">Delayed</div>
          <div class="value">{{ $delayedTrips ?? 0 }}</div>
          <div class="delta down"><i class="bi bi-arrow-down-short"></i> Need attention</div>
        </div>
      </div>
    </div>
    
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <span class="badge-status badge-pending" style="cursor:pointer;" onclick="filterByStatus('all')">All</span> 
      <span class="badge-status badge-pending" style="cursor:pointer;" onclick="filterByStatus('pickup')">Pickup</span> 
      <span class="badge-status badge-transit" style="cursor:pointer;" onclick="filterByStatus('in_transit')">In Transit</span> 
      <span class="badge-status badge-delivered" style="cursor:pointer;" onclick="filterByStatus('delivered')">Delivered</span> 
      <span class="badge-status badge-delayed" style="cursor:pointer;" onclick="filterByStatus('delayed')">Delayed</span> 
      <span class="badge-status badge-cancelled" style="cursor:pointer;" onclick="filterByStatus('cancelled')">Cancelled</span>
    </div>
    
    <div class="panel">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
          <div class="panel-title mb-0">Trip Log</div>
          <div class="panel-sub mb-0">Live and completed trips</div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-striped" id="tripsTable">
          <thead>
            <tr>
              <th>Trip #</th>
              <th>Job #</th>
              <th>Vehicle</th>
              <th>Driver</th>
              <th>Pickup Location</th>
              <th>Delivery Location</th>
              <th>Pickup Time</th>
              <th>Delivery Time</th>
              <th>Distance</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @isset($trips)
              @foreach($trips as $trip)
            <tr>
              <td><span class='mono fw-semibold'>{{ $trip->trip_number }}</span></td>
              <td>{{ $trip->job ? $trip->job->job_number : ($trip->job_number ?? 'N/A') }}</td>
              <td>{{ $trip->vehicle ? $trip->vehicle->reg_no : 'Unassigned' }}</td>
              <td>{{ $trip->driver ? $trip->driver->name : 'Unassigned' }}</td>
              <td>{{ $trip->pickup_location ?? 'N/A' }}</td>
              <td>{{ $trip->delivery_location ?? 'N/A' }}</td>
              <td>{{ $trip->pickup_time ? \Carbon\Carbon::parse($trip->pickup_time)->format('d M Y H:i') : 'N/A' }}</td>
              <td>{{ $trip->delivery_time ? \Carbon\Carbon::parse($trip->delivery_time)->format('d M Y H:i') : 'N/A' }}</td>
              <td>{{ $trip->distance ? $trip->distance . ' km' : 'N/A' }}</td>
              <td>
                @switch($trip->status)
                  @case('pickup')
                    <span class="badge-status badge-pending">Pickup</span>
                    @break
                  @case('in_transit')
                    <span class="badge-status badge-transit">In Transit</span>
                    @break
                  @case('delivered')
                    <span class="badge-status badge-delivered">Delivered</span>
                    @break
                  @case('delayed')
                    <span class="badge-status badge-delayed">Delayed</span>
                    @break
                  @case('cancelled')
                    <span class="badge-status badge-cancelled">Cancelled</span>
                    @break
                  @default
                    <span class="badge-status badge-pending">{{ ucfirst(str_replace('_', ' ', $trip->status)) }}</span>
                @endswitch
              </td>
              <td>
                <div class="btn-group btn-group-sm">
                  <button type="button" class="btn btn-outline-primary" onclick="editTrip({{ $trip->id }})" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button type="button" class="btn btn-outline-info" onclick="viewTrip({{ $trip->id }})" title="View">
                    <i class="bi bi-eye"></i>
                  </button>
                  <button type="button" class="btn btn-outline-danger" onclick="deleteTrip({{ $trip->id }})" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            @endforeach
            @else
            <tr><td colspan="11" class="text-center">No trips found</td></tr>
            @endisset
          </tbody>
        </table>
      </div>
    </div>
    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
      &copy; 2026 Super Ittefaq Logistics &middot; Transport Management System
    </div>
  </div>

  <!-- Create/Edit Trip Modal -->
  <div class="modal fade" id="tripModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tripModalLabel">New Trip</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="tripForm" action="{{ route('trips.store') }}" method="POST">
          @csrf
          <div class="modal-body">
            <input type="hidden" name="trip_id" id="trip_id">
            <input type="hidden" name="_method" id="_method" value="POST">
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="trip_number" class="form-label">Trip Number</label>
                <input type="text" class="form-control" name="trip_number" id="trip_number" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="job_number" class="form-label">Job Number (Optional)</label>
                <input type="text" class="form-control" name="job_number" id="job_number">
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="vehicle_id" class="form-label">Vehicle</label>
                <select class="form-select" name="vehicle_id" id="vehicle_id" required>
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
                <label for="driver_id" class="form-label">Driver</label>
                <select class="form-select" name="driver_id" id="driver_id" required>
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
                <label for="pickup_time" class="form-label">Pickup Time</label>
                <input type="datetime-local" class="form-control" name="pickup_time" id="pickup_time" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="delivery_time" class="form-label">Delivery Time (Optional)</label>
                <input type="datetime-local" class="form-control" name="delivery_time" id="delivery_time">
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="distance" class="form-label">Distance (km) (Optional)</label>
                <input type="number" step="0.01" class="form-control" name="distance" id="distance">
              </div>
              <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" name="status" id="status" required>
                  <option value="pickup">Pickup</option>
                  <option value="in_transit">In Transit</option>
                  <option value="delivered">Delivered</option>
                  <option value="delayed">Delayed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>
            </div>

            <div class="row">
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
            <button type="submit" class="btn btn-primary">Save Trip</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- View Trip Modal -->
  <div class="modal fade" id="viewTripModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Trip Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="viewTripContent">
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
    $('#tripsTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: 10 } // Actions column
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search trips..."
        }
    });

    // Form submission
    $('#tripForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var tripId = $('#trip_id').val();
        var url = tripId ? '/trips/' + tripId : '{{ route("trips.store") }}';
        var method = 'POST';
        
        // Set _method to PUT for updates
        if (tripId) {
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
                $('#tripModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                var errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                var errorMessage = 'Error saving trip';
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
    $('#tripForm')[0].reset();
    $('#trip_id').val('');
    $('#_method').val('POST');
    $('#tripModalLabel').text('New Trip');
}

function editTrip(id) {
    $.ajax({
        url: '/trips/' + id + '/edit',
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(trip) {
            fillTripForm(trip);
            $('#tripModal').modal('show');
        },
        error: function() {
            alert('Error loading trip data');
        }
    });
}

function fillTripForm(trip) {
    $('#trip_id').val(trip.id);
    $('#trip_number').val(trip.trip_number);
    $('#job_number').val(trip.job_number);
    $('#vehicle_id').val(trip.vehicle_id);
    $('#driver_id').val(trip.driver_id);
    $('#pickup_location').val(trip.pickup_location);
    $('#delivery_location').val(trip.delivery_location);
    $('#status').val(trip.status);
    $('#distance').val(trip.distance);
    $('#job_id').val(trip.job_id);
    $('#notes').val(trip.notes);
    
    // Format datetime for input type="datetime-local"
    if (trip.pickup_time) {
        var pickupDate = new Date(trip.pickup_time);
        var formattedPickup = pickupDate.toISOString().slice(0, 16);
        $('#pickup_time').val(formattedPickup);
    }
    
    if (trip.delivery_time) {
        var deliveryDate = new Date(trip.delivery_time);
        var formattedDelivery = deliveryDate.toISOString().slice(0, 16);
        $('#delivery_time').val(formattedDelivery);
    }
    
    $('#tripModalLabel').text('Edit Trip');
}

function viewTrip(id) {
    $.ajax({
        url: '/trips/' + id,
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(data) {
            var trip = data.trip || data;
            var html = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Trip Information</h6>
                        <p><strong>Trip Number:</strong> ${trip.trip_number}</p>
                        <p><strong>Job Number:</strong> ${trip.job_number || 'N/A'}</p>
                        <p><strong>Pickup Location:</strong> ${trip.pickup_location || 'N/A'}</p>
                        <p><strong>Delivery Location:</strong> ${trip.delivery_location || 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Assignment Details</h6>
                        <p><strong>Vehicle:</strong> ${trip.vehicle ? trip.vehicle.reg_no + ' - ' + trip.vehicle.make_model : 'Unassigned'}</p>
                        <p><strong>Driver:</strong> ${trip.driver ? trip.driver.name : 'Unassigned'}</p>
                        <p><strong>Distance:</strong> ${trip.distance ? trip.distance + ' km' : 'N/A'}</p>
                        <p><strong>Status:</strong> ${trip.status}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6>Timing</h6>
                        <p><strong>Pickup Time:</strong> ${trip.pickup_time ? new Date(trip.pickup_time).toLocaleString() : 'N/A'}</p>
                        <p><strong>Delivery Time:</strong> ${trip.delivery_time ? new Date(trip.delivery_time).toLocaleString() : 'N/A'}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Notes</h6>
                        <p>${trip.notes || 'No notes provided'}</p>
                    </div>
                </div>
            `;
            $('#viewTripContent').html(html);
            $('#viewTripModal').modal('show');
        },
        error: function() {
            alert('Error loading trip details');
        }
    });
}

function deleteTrip(id) {
    if (confirm('Are you sure you want to delete this trip?')) {
        $.ajax({
            url: '/trips/' + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            },
            error: function() {
                alert('Error deleting trip');
            }
        });
    }
}

function filterByStatus(status) {
    var table = $('#tripsTable').DataTable();
    if (status === 'all') {
        table.column(9).search('').draw();
    } else {
        table.column(9).search(status).draw();
    }
}

function exportTrips() {
    alert('Export functionality will be implemented to export trips data as CSV/Excel');
}
</script>
@endpush

