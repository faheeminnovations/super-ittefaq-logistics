@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">Operations</div>
        <h1>Proof of Delivery</h1>
        <div class="sub">Signature, photo & delivery confirmation</div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-navy" onclick="exportPods()"><i class="bi bi-download me-1"></i> Export</button>
        <button class="btn btn-navy" data-bs-toggle="modal" data-bs-target="#podModal" onclick="resetForm()"><i class="bi bi-plus-lg me-1"></i> New POD Entry</button>
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
          <div class="icon-badge" style="background:#EAF0FB;color:var(--navy-800);"><i class="bi bi-file-earmark-check"></i></div>
          <div class="label">Total PODs</div>
          <div class="value">{{ $totalPods ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> All records</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#EAF7EF;color:var(--success);"><i class="bi bi-check-circle"></i></div>
          <div class="label">Complete</div>
          <div class="value">{{ $completePods ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> {{ $totalPods ? round(($completePods / $totalPods) * 100) : 0 }}% complete</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FFF3E0;color:var(--warn);"><i class="bi bi-pen"></i></div>
          <div class="label">Missing Signature</div>
          <div class="value">{{ $missingSignaturePods ?? 0 }}</div>
          <div class="delta down"><i class="bi bi-arrow-down-short"></i> Follow up required</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FBE9E7;color:var(--danger);"><i class="bi bi-camera"></i></div>
          <div class="label">Missing Photo</div>
          <div class="value">{{ $missingPhotoPods ?? 0 }}</div>
          <div class="delta down"><i class="bi bi-arrow-down-short"></i> Action required</div>
        </div>
      </div>
    </div>
    
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <span class="badge-status badge-pending" style="cursor:pointer;" onclick="filterByStatus('all')">All</span> 
      <span class="badge-status badge-delivered" style="cursor:pointer;" onclick="filterByStatus('complete')">Complete</span> 
      <span class="badge-status badge-delayed" style="cursor:pointer;" onclick="filterByStatus('missing_signature')">Missing Signature</span> 
      <span class="badge-status badge-transit" style="cursor:pointer;" onclick="filterByStatus('missing_photo')">Missing Photo</span> 
      <span class="badge-status badge-pending" style="cursor:pointer;" onclick="filterByStatus('pending')">Pending</span> 
    </div>
    
    <div class="panel">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
          <div class="panel-title mb-0">Delivery Confirmations</div>
          <div class="panel-sub mb-0">Captured proof for completed jobs</div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-striped" id="podsTable">
          <thead>
            <tr>
              <th>Job #</th>
              <th>Customer</th>
              <th>Delivered By</th>
              <th>Date / Time</th>
              <th>Signature</th>
              <th>Photo</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @isset($pods)
              @foreach($pods as $pod)
            <tr>
              <td><span class='mono fw-semibold'>{{ $pod->job_number ?? 'N/A' }}</span></td>
              <td>{{ $pod->customer ? $pod->customer->name : 'N/A' }}</td>
              <td>{{ $pod->driver ? $pod->driver->name : 'N/A' }}</td>
              <td>{{ \Carbon\Carbon::parse($pod->delivery_datetime)->format('d M, H:i') }}</td>
              <td>
                @if($pod->has_signature && $pod->signature_path)
                  <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewSignature({{ $pod->id }})" title="View Signature">
                    <i class="bi bi-eye"></i> View
                  </button>
                @else
                  <i class="bi bi-x-circle text-danger"></i>
                @endif
              </td>
              <td>
                @if($pod->has_photo && $pod->photo_path)
                  <button type="button" class="btn btn-sm btn-outline-success" onclick="viewPhoto({{ $pod->id }})" title="View Photo">
                    <i class="bi bi-eye"></i> View
                  </button>
                @else
                  <i class="bi bi-x-circle text-danger"></i>
                @endif
              </td>
              <td>
                @switch($pod->status)
                  @case('complete')
                    <span class="badge-status badge-delivered">Complete</span>
                    @break
                  @case('missing_signature')
                    <span class="badge-status badge-delayed">Missing Signature</span>
                    @break
                  @case('missing_photo')
                    <span class="badge-status badge-transit">Missing Photo</span>
                    @break
                  @case('pending')
                    <span class="badge-status badge-pending">Pending</span>
                    @break
                  @default
                    <span class="badge-status badge-pending">{{ ucfirst(str_replace('_', ' ', $pod->status)) }}</span>
                @endswitch
              </td>
              <td>
                <div class="btn-group btn-group-sm">
                  <button type="button" class="btn btn-outline-primary" onclick="editPod({{ $pod->id }})" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button type="button" class="btn btn-outline-info" onclick="viewPod({{ $pod->id }})" title="View">
                    <i class="bi bi-eye"></i>
                  </button>
                  <button type="button" class="btn btn-outline-danger" onclick="deletePod({{ $pod->id }})" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            @endforeach
            @else
            <tr><td colspan="8" class="text-center">No PODs found</td></tr>
            @endisset
          </tbody>
        </table>
      </div>
    </div>
    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
      &copy; 2026 Super Ittefaq Logistics &middot; Transport Management System
    </div>
  </div>

  <!-- Create/Edit POD Modal -->
  <div class="modal fade" id="podModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="podModalLabel">New POD Entry</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="podForm" action="{{ route('pod.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <input type="hidden" name="pod_id" id="pod_id">
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
                  @foreach($customers ?? [] as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="driver_id" class="form-label">Driver</label>
                <select class="form-select" name="driver_id" id="driver_id" required>
                  <option value="">Select Driver</option>
                  @foreach($drivers ?? [] as $driver)
                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="delivery_datetime" class="form-label">Delivery Date/Time</label>
                <input type="datetime-local" class="form-control" name="delivery_datetime" id="delivery_datetime" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" name="status" id="status" required>
                  <option value="complete">Complete</option>
                  <option value="missing_signature">Missing Signature</option>
                  <option value="missing_photo">Missing Photo</option>
                  <option value="pending">Pending</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="job_id" class="form-label">Job ID (Optional)</label>
                <input type="number" class="form-control" name="job_id" id="job_id">
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="has_signature" id="has_signature" value="1">
                  <label class="form-check-label" for="has_signature">
                    Has Signature
                  </label>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="has_photo" id="has_photo" value="1">
                  <label class="form-check-label" for="has_photo">
                    Has Photo
                  </label>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="signature_upload" class="form-label">Signature Upload</label>
              <div class="signature-upload-container">
                <input type="file" class="form-control" name="signature_upload" id="signature_upload" accept="image/*" onchange="previewSignature(this)">
                <input type="hidden" name="signature_path" id="signature_path">
                <div id="signature_preview" class="mt-2 signature-preview"></div>
                <small class="text-muted">Accepted formats: JPG, PNG, GIF. Max size: 5MB</small>
              </div>
            </div>

            <div class="mb-3">
              <label for="photo_upload" class="form-label">Photo Upload</label>
              <div class="photo-upload-container">
                <input type="file" class="form-control" name="photo_upload" id="photo_upload" accept="image/*" onchange="previewPhoto(this)">
                <input type="hidden" name="photo_path" id="photo_path">
                <div id="photo_preview" class="mt-2 photo-preview"></div>
                <small class="text-muted">Accepted formats: JPG, PNG, GIF. Max size: 5MB</small>
              </div>
            </div>

            <div class="mb-3">
              <label for="delivery_confirmation" class="form-label">Delivery Confirmation</label>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="delivery_confirmation" id="delivery_confirmation" value="1">
                <label class="form-check-label" for="delivery_confirmation">
                  Confirm delivery was completed successfully
                </label>
              </div>
            </div>

            <div class="mb-3">
              <label for="notes" class="form-label">Notes</label>
              <textarea class="form-control" name="notes" id="notes" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save POD</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- View POD Modal -->
  <div class="modal fade" id="viewPodModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">POD Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="viewPodContent">
          <!-- Content will be loaded dynamically -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View Signature Modal -->
  <div class="modal fade" id="viewSignatureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Delivery Signature</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center" id="viewSignatureContent">
          <!-- Signature image will be loaded dynamically -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View Photo Modal -->
  <div class="modal fade" id="viewPhotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Delivery Photo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center" id="viewPhotoContent">
          <!-- Photo image will be loaded dynamically -->
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
    $('#podsTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: 7 } // Actions column
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search PODs..."
        }
    });

    // Form submission
    $('#podForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var podId = $('#pod_id').val();
        var url = podId ? '/pod/' + podId : '{{ route("pod.store") }}';
        var method = podId ? 'PUT' : 'POST';
        
        // Add _method field for Laravel to recognize PUT requests
        if (podId) {
            formData.append('_method', 'PUT');
        }
        
        console.log('Submitting POD form to:', url);
        console.log('Form data:', formData);
        
        $.ajax({
            url: url,
            type: 'POST', // Always use POST for file uploads
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('POD saved successfully:', response);
                $('#podModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                console.error('Error saving POD:', xhr);
                var errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                var errorMessage = 'Error saving POD';
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
    $('#podForm')[0].reset();
    $('#pod_id').val('');
    $('#_method').val('POST');
    $('#podModalLabel').text('New POD Entry');
    $('#signature_preview').html('');
    $('#photo_preview').html('');
}

function editPod(id) {
    $.ajax({
        url: '/pod/' + id + '/edit',
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(pod) {
            fillPodForm(pod);
            $('#podModal').modal('show');
        },
        error: function() {
            alert('Error loading POD data');
        }
    });
}

function fillPodForm(pod) {
    $('#pod_id').val(pod.id);
    $('#job_number').val(pod.job_number);
    $('#customer_id').val(pod.customer_id);
    $('#driver_id').val(pod.driver_id);
    $('#delivery_datetime').val(pod.delivery_datetime);
    $('#status').val(pod.status);
    $('#job_id').val(pod.job_id);
    $('#has_signature').prop('checked', pod.has_signature == 1);
    $('#has_photo').prop('checked', pod.has_photo == 1);
    $('#delivery_confirmation').prop('checked', pod.delivery_confirmation == 1);
    $('#signature_path').val(pod.signature_path);
    $('#photo_path').val(pod.photo_path);
    $('#notes').val(pod.notes);
    
    // Display existing signature if available
    if (pod.signature_path) {
        $('#signature_preview').html(`
            <div class="preview-container">
                <img src="/storage/${pod.signature_path}" alt="Existing Signature" class="img-thumbnail preview-image" style="max-height: 150px; border: 2px solid #007bff;">
                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="clearSignature()">Remove</button>
            </div>
        `);
    } else {
        $('#signature_preview').html('');
    }
    
    // Display existing photo if available
    if (pod.photo_path) {
        $('#photo_preview').html(`
            <div class="preview-container">
                <img src="/storage/${pod.photo_path}" alt="Existing Photo" class="img-thumbnail preview-image" style="max-height: 200px; border: 2px solid #28a745;">
                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="clearPhoto()">Remove</button>
            </div>
        `);
    } else {
        $('#photo_preview').html('');
    }
    
    $('#podModalLabel').text('Edit POD');
}

function viewPod(id) {
    $.ajax({
        url: '/pod/' + id,
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(data) {
            var pod = data.pod || data;
            var html = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Delivery Information</h6>
                        <p><strong>Job Number:</strong> ${pod.job_number || 'N/A'}</p>
                        <p><strong>Customer ID:</strong> ${pod.customer_id || 'N/A'}</p>
                        <p><strong>Driver ID:</strong> ${pod.driver_id || 'N/A'}</p>
                        <p><strong>Delivery Date/Time:</strong> ${pod.delivery_datetime}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>POD Status</h6>
                        <p><strong>Status:</strong> ${pod.status}</p>
                        <p><strong>Has Signature:</strong> ${pod.has_signature ? 'Yes' : 'No'}</p>
                        <p><strong>Has Photo:</strong> ${pod.has_photo ? 'Yes' : 'No'}</p>
                        <p><strong>Delivery Confirmed:</strong> ${pod.delivery_confirmation ? 'Yes' : 'No'}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Additional Information</h6>
                        <p><strong>Notes:</strong> ${pod.notes || 'No notes provided'}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6>Signature</h6>
                        ${pod.signature_path ? 
                            `<div class="signature-display">
                                <img src="/storage/${pod.signature_path}" alt="Signature" class="img-fluid img-thumbnail" style="max-height: 200px; border: 2px solid #007bff;">
                             </div>` : 
                            '<p class="text-muted">No signature uploaded</p>'
                        }
                    </div>
                    <div class="col-md-6">
                        <h6>Photo</h6>
                        ${pod.photo_path ? 
                            `<div class="photo-display">
                                <img src="/storage/${pod.photo_path}" alt="Photo" class="img-fluid img-thumbnail" style="max-height: 250px; border: 2px solid #28a745;">
                             </div>` : 
                            '<p class="text-muted">No photo uploaded</p>'
                        }
                    </div>
                </div>
            `;
            $('#viewPodContent').html(html);
            $('#viewPodModal').modal('show');
        },
        error: function() {
            alert('Error loading POD details');
        }
    });
}

function deletePod(id) {
    if (confirm('Are you sure you want to delete this POD?')) {
        $.ajax({
            url: '/pod/' + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            },
            error: function() {
                alert('Error deleting POD');
            }
        });
    }
}

function filterByStatus(status) {
    var table = $('#podsTable').DataTable();
    if (status === 'all') {
        table.column(6).search('').draw();
    } else {
        table.column(6).search(status).draw();
    }
}

function exportPods() {
    fetch('/pod/export', {
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
        a.download = 'pod_export_' + new Date().toISOString().split('T')[0] + '.csv';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    })
    .catch(error => {
        console.error('Export error:', error);
        alert('Error exporting POD data');
    });
}

function previewSignature(input) {
    const preview = document.getElementById('signature_preview');
    const pathInput = document.getElementById('signature_path');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <div class="preview-container">
                    <img src="${e.target.result}" alt="Signature Preview" class="img-thumbnail preview-image" style="max-height: 150px; border: 2px solid #007bff;">
                    <button type="button" class="btn btn-sm btn-danger mt-2" onclick="clearSignature()">Remove</button>
                </div>
            `;
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = '';
        pathInput.value = '';
    }
}

function previewPhoto(input) {
    const preview = document.getElementById('photo_preview');
    const pathInput = document.getElementById('photo_path');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <div class="preview-container">
                    <img src="${e.target.result}" alt="Photo Preview" class="img-thumbnail preview-image" style="max-height: 200px; border: 2px solid #28a745;">
                    <button type="button" class="btn btn-sm btn-danger mt-2" onclick="clearPhoto()">Remove</button>
                </div>
            `;
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = '';
        pathInput.value = '';
    }
}

function clearSignature() {
    document.getElementById('signature_upload').value = '';
    document.getElementById('signature_path').value = '';
    document.getElementById('signature_preview').innerHTML = '';
    document.getElementById('has_signature').checked = false;
}

function clearPhoto() {
    document.getElementById('photo_upload').value = '';
    document.getElementById('photo_path').value = '';
    document.getElementById('photo_preview').innerHTML = '';
    document.getElementById('has_photo').checked = false;
}

function viewSignature(id) {
    $.ajax({
        url: '/pod/' + id,
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(data) {
            var pod = data.pod || data;
            if (pod.signature_path) {
                $('#viewSignatureContent').html(`
                    <div class="signature-view-container">
                        <img src="/storage/${pod.signature_path}" alt="Delivery Signature" class="img-fluid img-thumbnail" style="max-height: 400px; border: 3px solid #007bff; border-radius: 8px;">
                        <div class="mt-3">
                            <p class="text-muted"><strong>Job Number:</strong> ${pod.job_number}</p>
                            <p class="text-muted"><strong>Customer:</strong> ${pod.customer ? pod.customer.name : 'N/A'}</p>
                            <p class="text-muted"><strong>Delivery Date:</strong> ${pod.delivery_datetime}</p>
                        </div>
                    </div>
                `);
                $('#viewSignatureModal').modal('show');
            } else {
                alert('No signature available for this POD');
            }
        },
        error: function() {
            alert('Error loading signature');
        }
    });
}

function viewPhoto(id) {
    $.ajax({
        url: '/pod/' + id,
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(data) {
            var pod = data.pod || data;
            if (pod.photo_path) {
                $('#viewPhotoContent').html(`
                    <div class="photo-view-container">
                        <img src="/storage/${pod.photo_path}" alt="Delivery Photo" class="img-fluid img-thumbnail" style="max-height: 500px; border: 3px solid #28a745; border-radius: 8px;">
                        <div class="mt-3">
                            <p class="text-muted"><strong>Job Number:</strong> ${pod.job_number}</p>
                            <p class="text-muted"><strong>Customer:</strong> ${pod.customer ? pod.customer.name : 'N/A'}</p>
                            <p class="text-muted"><strong>Delivery Date:</strong> ${pod.delivery_datetime}</p>
                        </div>
                    </div>
                `);
                $('#viewPhotoModal').modal('show');
            } else {
                alert('No photo available for this POD');
            }
        },
        error: function() {
            alert('Error loading photo');
        }
    });
}
</script>

<style>
.signature-upload-container, .photo-upload-container {
    border: 2px dashed #dee2e6;
    padding: 15px;
    border-radius: 8px;
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.signature-upload-container:hover, .photo-upload-container:hover {
    border-color: #007bff;
    background-color: #e9f2ff;
}

.preview-image {
    max-width: 100%;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.preview-container {
    text-align: center;
}
</style>
@endpush

