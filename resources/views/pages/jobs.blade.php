@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">Operations</div>
        <h1>Jobs / Bookings</h1>
        <div class="sub">Create and manage customer transport requests</div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-navy" onclick="exportJobs()"><i class="bi bi-download me-1"></i> Export</button>
        <button class="btn btn-navy" data-bs-toggle="modal" data-bs-target="#jobModal" onclick="resetForm()"><i class="bi bi-plus-lg me-1"></i> New Job</button>
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
          <div class="icon-badge" style="background:#EAF0FB;color:var(--navy-800);"><i class="bi bi-clipboard2-check"></i></div>
          <div class="label">Total Jobs</div>
          <div class="value">{{ $totalJobs }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> All time</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FFF3E0;color:var(--warn);"><i class="bi bi-hourglass-split"></i></div>
          <div class="label">Pending</div>
          <div class="value">{{ $pendingJobs }}</div>
          <div class="delta down"><i class="bi bi-arrow-down-short"></i> Awaiting assignment</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#E8F4FD;color:var(--navy-600);"><i class="bi bi-truck"></i></div>
          <div class="label">In Transit</div>
          <div class="value">{{ $inTransitJobs }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> Active deliveries</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#EAF7EF;color:var(--success);"><i class="bi bi-check2-circle"></i></div>
          <div class="label">Delivered</div>
          <div class="value">{{ $deliveredJobs }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> Completed jobs</div>
        </div>
      </div>
    </div>
    
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <span class="badge-status badge-pending" style="cursor:pointer;" onclick="filterByStatus('all')">All</span> 
      <span class="badge-status badge-pending" style="cursor:pointer;" onclick="filterByStatus('pending')">Pending</span> 
      <span class="badge-status badge-transit" style="cursor:pointer;" onclick="filterByStatus('assigned')">Assigned</span> 
      <span class="badge-status badge-transit" style="cursor:pointer;" onclick="filterByStatus('in_transit')">In Transit</span> 
      <span class="badge-status badge-delivered" style="cursor:pointer;" onclick="filterByStatus('delivered')">Delivered</span>
      <span class="badge-status badge-delayed" style="cursor:pointer;" onclick="filterByStatus('delayed')">Delayed</span>
      <span class="badge-status badge-cancelled" style="cursor:pointer;" onclick="filterByStatus('cancelled')">Cancelled</span>
    </div>
    
    <div class="panel">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
          <div class="panel-title mb-0">Job Book</div>
          <div class="panel-sub mb-0">All customer transport requests</div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-striped" id="jobsTable">
          <thead>
            <tr>
              <th>Job #</th>
              <th>Customer</th>
              <th>Pickup Location</th>
              <th>Delivery Location</th>
              <th>Date</th>
              <th>Vehicle</th>
              <th>Driver</th>
              <th>Price</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($jobs as $job)
            <tr>
              <td><span class="mono fw-semibold">{{ $job->job_number }}</span></td>
              <td>{{ $job->customer ? $job->customer->name : 'N/A' }}</td>
              <td>{{ $job->pickup_location }}</td>
              <td>{{ $job->delivery_location }}</td>
              <td>{{ \Carbon\Carbon::parse($job->job_date)->format('d M Y') }}</td>
              <td>{{ $job->vehicle ? $job->vehicle->reg_no : 'Unassigned' }}</td>
              <td>{{ $job->driver ? $job->driver->name : 'Unassigned' }}</td>
              <td>{{ $job->quoted_price ? number_format($job->quoted_price, 2) : 'N/A' }}</td>
              <td>
                @switch($job->status)
                  @case('pending')
                    <span class="badge-status badge-pending">Pending</span>
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
                  @case('delayed')
                    <span class="badge-status badge-delayed">Delayed</span>
                    @break
                  @case('cancelled')
                    <span class="badge-status badge-cancelled">Cancelled</span>
                    @break
                  @default
                    <span class="badge-status badge-pending">{{ $job->status }}</span>
                @endswitch
              </td>
              <td>
                <div class="btn-group btn-group-sm">
                  <button type="button" class="btn btn-outline-primary" onclick="editJob({{ $job->id }})" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button type="button" class="btn btn-outline-info" onclick="viewJob({{ $job->id }})" title="View">
                    <i class="bi bi-eye"></i>
                  </button>
                  <button type="button" class="btn btn-outline-danger" onclick="deleteJob({{ $job->id }})" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
      &copy; 2026 Super Ittefaq Logistics &middot; Transport Management System
    </div>
  </div>

  <!-- Create/Edit Job Modal -->
  <div class="modal fade" id="jobModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="jobModalLabel">New Job</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="jobForm" action="{{ route('jobs.store') }}" method="POST">
          @csrf
          <div class="modal-body">
            <input type="hidden" name="job_id" id="job_id">
            <input type="hidden" name="_method" id="_method" value="POST">
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="job_number" class="form-label">Job Number</label>
                <input type="text" class="form-control" name="job_number" id="job_number" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="customer_id" class="form-label">Customer</label>
                <select class="form-select" name="customer_id" id="customer_id" required>
                  <option value="">Select Customer</option>
                  @if(isset($customers))
                    @foreach($customers as $customer)
                      <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                  @else
                    @foreach(\App\Models\Customer::all() as $customer)
                      <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                  @endif
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="pickup_location" class="form-label">Pickup Location</label>
                <input type="text" class="form-control" name="pickup_location" id="pickup_location" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="delivery_location" class="form-label">Delivery Location</label>
                <input type="text" class="form-control" name="delivery_location" id="delivery_location" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="job_date" class="form-label">Job Date</label>
                <input type="date" class="form-control" name="job_date" id="job_date" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" name="status" id="status" required>
                  <option value="pending">Pending</option>
                  <option value="assigned">Assigned</option>
                  <option value="in_transit">In Transit</option>
                  <option value="delivered">Delivered</option>
                  <option value="delayed">Delayed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
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
                <label for="quoted_price" class="form-label">Quoted Price (PKR)</label>
                <input type="number" step="0.01" class="form-control" name="quoted_price" id="quoted_price" placeholder="0.00">
              </div>
              <div class="col-md-6 mb-3">
                <label for="bags" class="form-label">Bags</label>
                <input type="number" class="form-control" name="bags" id="bags" placeholder="0">
              </div>
            </div>

            <div class="row">
              <div class="col-md-4 mb-3">
                <label for="rent" class="form-label">Rent (PKR)</label>
                <input type="number" step="0.01" class="form-control" name="rent" id="rent" placeholder="0.00">
              </div>
              <div class="col-md-4 mb-3">
                <label for="advance" class="form-label">Advance (PKR)</label>
                <input type="number" step="0.01" class="form-control" name="advance" id="advance" placeholder="0.00">
              </div>
              <div class="col-md-4 mb-3">
                <label for="advance_date" class="form-label">Advance Date</label>
                <input type="date" class="form-control" name="advance_date" id="advance_date">
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="dues" class="form-label">Dues (PKR)</label>
                <input type="number" step="0.01" class="form-control" name="dues" id="dues" placeholder="0.00">
              </div>
            </div>

            <div class="mb-3">
              <label for="notes" class="form-label">Notes</label>
              <textarea class="form-control" name="notes" id="notes" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Job</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- View Job Modal -->
  <div class="modal fade" id="viewJobModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Job Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="viewJobContent">
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
    $('#jobsTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: 9 } // Actions column
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search jobs..."
        }
    });

    // Form submission
    $('#jobForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var jobId = $('#job_id').val();
        var url = jobId ? '/jobs/' + jobId : '{{ route("jobs.store") }}';
        var method = jobId ? 'POST' : 'POST'; // Always use POST with _method for Laravel
        
        // Set _method to PUT for updates
        if (jobId) {
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
                $('#jobModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                var errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                var errorMessage = 'Error saving job';
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
    $('#jobForm')[0].reset();
    $('#job_id').val('');
    $('#_method').val('POST');
    $('#jobModalLabel').text('New Job');
}

function editJob(id) {
    // Make a direct AJAX call to get job data
    $.ajax({
        url: '/jobs/' + id + '/edit',
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(job) {
            fillJobForm(job);
            $('#jobModal').modal('show');
        },
        error: function() {
            alert('Error loading job data');
        }
    });
}

function fillJobForm(job) {
    $('#job_id').val(job.id);
    $('#job_number').val(job.job_number);
    $('#customer_id').val(job.customer_id);
    $('#pickup_location').val(job.pickup_location);
    $('#delivery_location').val(job.delivery_location);
    
    // Format date for input type="date"
    if (job.job_date) {
        var date = new Date(job.job_date);
        var formattedDate = date.toISOString().split('T')[0];
        $('#job_date').val(formattedDate);
    }
    
    $('#status').val(job.status);
    $('#vehicle_id').val(job.vehicle_id);
    $('#driver_id').val(job.driver_id);
    $('#quoted_price').val(job.quoted_price);
    $('#bags').val(job.bags);
    $('#rent').val(job.rent);
    $('#advance').val(job.advance);
    
    // Format advance date for input type="date"
    if (job.advance_date) {
        var advanceDate = new Date(job.advance_date);
        var formattedAdvanceDate = advanceDate.toISOString().split('T')[0];
        $('#advance_date').val(formattedAdvanceDate);
    }
    
    $('#dues').val(job.dues);
    $('#notes').val(job.notes);
    
    $('#jobModalLabel').text('Edit Job');
}

function viewJob(id) {
    $.ajax({
        url: '/jobs/' + id,
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(data) {
            var job = data.job || data;
            var html = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Job Information</h6>
                        <p><strong>Job Number:</strong> ${job.job_number}</p>
                        <p><strong>Customer:</strong> ${job.customer ? job.customer.name : 'N/A'}</p>
                        <p><strong>Pickup Location:</strong> ${job.pickup_location}</p>
                        <p><strong>Delivery Location:</strong> ${job.delivery_location}</p>
                        <p><strong>Job Date:</strong> ${new Date(job.job_date).toLocaleDateString()}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Assignment Details</h6>
                        <p><strong>Vehicle:</strong> ${job.vehicle ? job.vehicle.reg_no + ' - ' + job.vehicle.make_model : 'Unassigned'}</p>
                        <p><strong>Driver:</strong> ${job.driver ? job.driver.name : 'Unassigned'}</p>
                        <p><strong>Status:</strong> ${job.status}</p>
                        <p><strong>Quoted Price:</strong> ${job.quoted_price ? parseFloat(job.quoted_price).toFixed(2) + ' PKR' : 'N/A'}</p>
                        <p><strong>Bags:</strong> ${job.bags || 'N/A'}</p>
                        <p><strong>Rent:</strong> ${job.rent ? parseFloat(job.rent).toFixed(2) + ' PKR' : 'N/A'}</p>
                        <p><strong>Advance:</strong> ${job.advance ? parseFloat(job.advance).toFixed(2) + ' PKR' : 'N/A'}</p>
                        <p><strong>Dues:</strong> ${job.dues ? parseFloat(job.dues).toFixed(2) + ' PKR' : 'N/A'}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Notes</h6>
                        <p>${job.notes || 'No notes'}</p>
                    </div>
                </div>
            `;
            $('#viewJobContent').html(html);
            $('#viewJobModal').modal('show');
        },
        error: function() {
            alert('Error loading job details');
        }
    });
}

function deleteJob(id) {
    if (confirm('Are you sure you want to delete this job?')) {
        $.ajax({
            url: '/jobs/' + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            },
            error: function() {
                alert('Error deleting job');
            }
        });
    }
}

function filterByStatus(status) {
    var table = $('#jobsTable').DataTable();
    if (status === 'all') {
        table.column(8).search('').draw();
    } else {
        table.column(8).search(status).draw();
    }
}

function exportJobs() {
    fetch('/jobs/export', {
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
        a.download = 'jobs_export_' + new Date().toISOString().split('T')[0] + '.csv';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    })
    .catch(error => {
        console.error('Export error:', error);
        alert('Error exporting jobs data');
    });
}
</script>
@endpush

