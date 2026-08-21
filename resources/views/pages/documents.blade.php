@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">System</div>
        <h1>Documents</h1>
        <div class="sub">Driver, vehicle, customer & job documents</div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-navy" onclick="exportDocuments()"><i class="bi bi-download me-1"></i> Export</button>
        <button class="btn btn-navy" data-bs-toggle="modal" data-bs-target="#documentModal" onclick="resetForm()"><i class="bi bi-plus-lg me-1"></i> Upload Document</button>
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
          <div class="icon-badge" style="background:#EAF0FB;color:var(--navy-800);"><i class="bi bi-folder2-open"></i></div>
          <div class="label">Total Documents</div>
          <div class="value">{{ $totalDocuments ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> All categories</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FFF3E0;color:var(--warn);"><i class="bi bi-exclamation-triangle"></i></div>
          <div class="label">Expiring Soon</div>
          <div class="value">{{ $expiringSoonDocuments ?? 0 }}</div>
          <div class="delta down"><i class="bi bi-arrow-down-short"></i> Renew soon</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FBE9E7;color:var(--danger);"><i class="bi bi-file-earmark-x"></i></div>
          <div class="label">Expired</div>
          <div class="value">{{ $expiredDocuments ?? 0 }}</div>
          <div class="delta down"><i class="bi bi-arrow-down-short"></i> Action required</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#EAF7EF;color:var(--success);"><i class="bi bi-cloud-check"></i></div>
          <div class="label">Verified</div>
          <div class="value">{{ $verifiedDocuments ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> Valid documents</div>
        </div>
      </div>
    </div>
    
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <span class="badge-status badge-pending" style="cursor:pointer;" onclick="filterByStatus('all')">All</span> 
      <span class="badge-status badge-delivered" style="cursor:pointer;" onclick="filterByStatus('verified')">Verified</span> 
      <span class="badge-status badge-delayed" style="cursor:pointer;" onclick="filterByStatus('expiring_soon')">Expiring Soon</span> 
      <span class="badge-status badge-transit" style="cursor:pointer;" onclick="filterByStatus('expired')">Expired</span> 
      <span class="badge-status badge-pending" style="cursor:pointer;" onclick="filterByStatus('pending')">Pending</span> 
    </div>
    
    <div class="panel">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
          <div class="panel-title mb-0">Document Vault</div>
          <div class="panel-sub mb-0">Centralised storage for all compliance documents</div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-striped" id="documentsTable">
          <thead>
            <tr>
              <th>Document</th>
              <th>Category</th>
              <th>Related To</th>
              <th>Expiry</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @isset($documents)
              @foreach($documents as $document)
            <tr>
              <td>{{ $document->document_name }}</td>
              <td>{{ $document->category }}</td>
              <td>{{ $document->related_entity_type ?? 'N/A' }} {{ $document->related_entity_id ?? '' }}</td>
              <td>{{ $document->expiry_date ? \Carbon\Carbon::parse($document->expiry_date)->format('d M Y') : 'N/A' }}</td>
              <td>
                @switch($document->status)
                  @case('verified')
                    <span class="badge-status badge-delivered">Verified</span>
                    @break
                  @case('expiring_soon')
                    <span class="badge-status badge-delayed">Expiring Soon</span>
                    @break
                  @case('expired')
                    <span class="badge-status badge-transit">Expired</span>
                    @break
                  @case('pending')
                    <span class="badge-status badge-pending">Pending</span>
                    @break
                  @default
                    <span class="badge-status badge-pending">{{ ucfirst(str_replace('_', ' ', $document->status)) }}</span>
                @endswitch
              </td>
              <td>
                <div class="btn-group btn-group-sm">
                  <button type="button" class="btn btn-outline-primary" onclick="editDocument({{ $document->id }})" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <a href="{{ route('documents.show', $document->id) }}" target="_blank" class="btn btn-outline-info" title="View in new tab">
                    <i class="bi bi-eye"></i>
                  </a>
                  @if($document->file_path)
                  <a href="{{ route('documents.download', $document->id) }}" class="btn btn-outline-success" title="Download">
                    <i class="bi bi-download"></i>
                  </a>
                  @endif
                  <button type="button" class="btn btn-outline-danger" onclick="deleteDocument({{ $document->id }})" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            @endforeach
            @else
            <tr><td colspan="6" class="text-center">No documents found</td></tr>
            @endisset
          </tbody>
        </table>
      </div>
    </div>
    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
      &copy; 2026 Super Ittefaq Logistics &middot; Transport Management System
    </div>
  </div>

  <!-- Create/Edit Document Modal -->
  <div class="modal fade" id="documentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="documentModalLabel">Upload Document</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="documentForm" action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <input type="hidden" name="document_id" id="document_id">
            <input type="hidden" name="_method" id="_method" value="POST">
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="document_name" class="form-label">Document Name</label>
                <input type="text" class="form-control" name="document_name" id="document_name" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" name="category" id="category" required>
                  <option value="">Select Category</option>
                  <option value="vehicle">Vehicle</option>
                  <option value="driver">Driver</option>
                  <option value="customer">Customer</option>
                  <option value="job">Job</option>
                  <option value="company">Company</option>
                  <option value="other">Other</option>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="related_entity_type" class="form-label">Related Entity Type</label>
                <input type="text" class="form-control" name="related_entity_type" id="related_entity_type">
              </div>
              <div class="col-md-6 mb-3">
                <label for="related_entity_id" class="form-label">Related Entity ID</label>
                <input type="number" class="form-control" name="related_entity_id" id="related_entity_id">
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="expiry_date" class="form-label">Expiry Date</label>
                <input type="date" class="form-control" name="expiry_date" id="expiry_date">
              </div>
              <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" name="status" id="status" required>
                  <option value="pending">Pending</option>
                  <option value="verified">Verified</option>
                  <option value="expiring_soon">Expiring Soon</option>
                  <option value="expired">Expired</option>
                </select>
              </div>
            </div>

            <div class="mb-3">
              <label for="file_path" class="form-label">Upload File</label>
              <input type="file" class="form-control" name="file_path" id="file_path">
              <input type="hidden" name="file_path_hidden" id="file_path_hidden">
              <small class="text-muted">Supported formats: PDF, JPG, PNG, DOC, DOCX</small>
            </div>

            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control" name="description" id="description" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Document</button>
          </div>
        </form>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#documentsTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: 5 } // Actions column
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search documents..."
        }
    });

    // Form submission
    $('#documentForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var documentId = $('#document_id').val();
        var url = documentId ? '/documents/' + documentId : '{{ route("documents.store") }}';
        var method = 'POST';
        
        // Set _method to PUT for updates
        if (documentId) {
            formData.append('_method', 'PUT');
        }
        
        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#documentModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                var errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                var errorMessage = 'Error saving document';
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
    $('#documentForm')[0].reset();
    $('#document_id').val('');
    $('#_method').val('POST');
    $('#documentModalLabel').text('Upload Document');
}

function editDocument(id) {
    $.ajax({
        url: '/documents/' + id + '/edit',
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(document) {
            fillDocumentForm(document);
            $('#documentModal').modal('show');
        },
        error: function() {
            alert('Error loading document data');
        }
    });
}

function fillDocumentForm(document) {
    $('#document_id').val(document.id);
    $('#document_name').val(document.document_name);
    $('#category').val(document.category);
    $('#related_entity_type').val(document.related_entity_type);
    $('#related_entity_id').val(document.related_entity_id);
    $('#expiry_date').val(document.expiry_date);
    $('#status').val(document.status);
    // Note: File input cannot be set programmatically for security reasons
    // User will need to re-upload file if they want to change it
    $('#description').val(document.description);
    
    $('#documentModalLabel').text('Edit Document');
}

function deleteDocument(id) {
    if (confirm('Are you sure you want to delete this document?')) {
        $.ajax({
            url: '/documents/' + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            },
            error: function() {
                alert('Error deleting document');
            }
        });
    }
}

function filterByStatus(status) {
    var table = $('#documentsTable').DataTable();
    if (status === 'all') {
        table.column(4).search('').draw();
    } else {
        table.column(4).search(status).draw();
    }
}

function exportDocuments() {
    alert('Export functionality will be implemented to export documents data as CSV/Excel');
}
</script>
@endpush

