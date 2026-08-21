@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">System</div>
        <h1>Document Details</h1>
        <div class="sub">View document information and file</div>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('documents.index') }}" class="btn btn-outline-navy">
          <i class="bi bi-arrow-left me-1"></i> Back to Documents
        </a>
        @if($document->file_path)
        <a href="{{ route('documents.download', $document->id) }}" class="btn btn-navy">
          <i class="bi bi-download me-1"></i> Download File
        </a>
        @endif
      </div>
    </div>
    
    <div class="row g-3 mb-3">
      <div class="col-12">
        <div class="panel">
          <div class="panel-title mb-3">Document Information</div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label text-muted">Document Name</label>
              <div class="form-control-plaintext">{{ $document->document_name }}</div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label text-muted">Category</label>
              <div class="form-control-plaintext">{{ ucfirst($document->category) }}</div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label text-muted">Related Entity Type</label>
              <div class="form-control-plaintext">{{ $document->related_entity_type ?? 'N/A' }}</div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label text-muted">Related Entity ID</label>
              <div class="form-control-plaintext">{{ $document->related_entity_id ?? 'N/A' }}</div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label text-muted">Expiry Date</label>
              <div class="form-control-plaintext">{{ $document->expiry_date ? \Carbon\Carbon::parse($document->expiry_date)->format('d M Y') : 'N/A' }}</div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label text-muted">Status</label>
              <div class="form-control-plaintext">
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
              </div>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label text-muted">Description</label>
              <div class="form-control-plaintext">{{ $document->description ?? 'No description provided' }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    @if($document->file_path)
    <div class="row g-3 mb-3">
      <div class="col-12">
        <div class="panel">
          <div class="panel-title mb-3">Document File</div>
          <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            File is stored at: <code>{{ $document->file_path }}</code>
          </div>
          <div class="d-flex gap-2">
            <a href="{{ route('documents.download', $document->id) }}" class="btn btn-success">
              <i class="bi bi-download me-1"></i> Download File
            </a>
            @if(in_array(strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION)), ['pdf', 'jpg', 'jpeg', 'png', 'gif']))
            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="btn btn-outline-primary">
              <i class="bi bi-eye me-1"></i> View in Browser
            </a>
            @endif
          </div>
        </div>
      </div>
    </div>
    @else
    <div class="row g-3 mb-3">
      <div class="col-12">
        <div class="panel">
          <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            No file is associated with this document.
          </div>
        </div>
      </div>
    </div>
    @endif

    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
      &copy; 2026 Super Ittefaq Logistics &middot; Transport Management System
    </div>
  </div>
@endsection