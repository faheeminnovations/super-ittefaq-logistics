@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">Accounts</div>
        <h1>Invoices</h1>
        <div class="sub">Customer invoices, VAT & payment status</div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-navy" onclick="exportInvoices()"><i class="bi bi-download me-1"></i> Export</button>
        <button class="btn btn-navy" data-bs-toggle="modal" data-bs-target="#invoiceModal" onclick="resetForm()"><i class="bi bi-plus-lg me-1"></i> Create Invoice</button>
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
          <div class="icon-badge" style="background:#EAF0FB;color:var(--navy-800);"><i class="bi bi-receipt"></i></div>
          <div class="label">Total Invoiced</div>
          <div class="value">{{ \App\Helpers\CurrencyHelper::formatCurrency($totalAmount ?? 0) }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> {{ $totalInvoices ?? 0 }} invoices</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#EAF7EF;color:var(--success);"><i class="bi bi-check-circle"></i></div>
          <div class="label">Paid</div>
          <div class="value">{{ $paidInvoices ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> Collected</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FFF3E0;color:var(--warn);"><i class="bi bi-hourglass-split"></i></div>
          <div class="label">Unpaid</div>
          <div class="value">{{ $unpaidInvoices ?? 0 }}</div>
          <div class="delta down"><i class="bi bi-arrow-down-short"></i> Outstanding</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FBE9E7;color:var(--danger);"><i class="bi bi-exclamation-triangle"></i></div>
          <div class="label">Overdue</div>
          <div class="value">{{ $overdueInvoices ?? 0 }}</div>
          <div class="delta down"><i class="bi bi-arrow-down-short"></i> Action required</div>
        </div>
      </div>
    </div>
    
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <span class="badge-status badge-pending" style="cursor:pointer;" onclick="filterByStatus('all')">All</span> 
      <span class="badge-status badge-delivered" style="cursor:pointer;" onclick="filterByStatus('paid')">Paid</span> 
      <span class="badge-status badge-transit" style="cursor:pointer;" onclick="filterByStatus('unpaid')">Unpaid</span> 
      <span class="badge-status badge-delayed" style="cursor:pointer;" onclick="filterByStatus('overdue')">Overdue</span> 
      <span class="badge-status badge-pending" style="cursor:pointer;" onclick="filterByStatus('cancelled')">Cancelled</span> 
    </div>
    
    <div class="panel">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
          <div class="panel-title mb-0">Invoice Register</div>
          <div class="panel-sub mb-0">All customer invoices & VAT breakdown</div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-striped" id="invoicesTable">
          <thead>
            <tr>
              <th>Invoice #</th>
              <th>Customer</th>
              <th>Amount</th>
              <th>VAT</th>
              <th>Due Date</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @isset($invoices)
              @foreach($invoices as $invoice)
            <tr>
              <td><span class='mono fw-semibold'>{{ $invoice->invoice_number }}</span></td>
              <td>{{ $invoice->customer ? $invoice->customer->name : 'N/A' }}</td>
              <td>{{ $invoice->formatted_amount }}</td>
              <td>{{ $invoice->formatted_vat }}</td>
              <td>{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</td>
              <td>
                @switch($invoice->status)
                  @case('paid')
                    <span class="badge-status badge-delivered">Paid</span>
                    @break
                  @case('unpaid')
                    <span class="badge-status badge-transit">Unpaid</span>
                    @break
                  @case('overdue')
                    <span class="badge-status badge-delayed">Overdue</span>
                    @break
                  @case('cancelled')
                    <span class="badge-status badge-pending">Cancelled</span>
                    @break
                  @default
                    <span class="badge-status badge-pending">{{ ucfirst(str_replace('_', ' ', $invoice->status)) }}</span>
                @endswitch
              </td>
              <td>
                <div class="btn-group btn-group-sm">
                  <button type="button" class="btn btn-outline-primary" onclick="editInvoice({{ $invoice->id }})" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button type="button" class="btn btn-outline-info" onclick="viewInvoice({{ $invoice->id }})" title="View">
                    <i class="bi bi-eye"></i>
                  </button>
                  <button type="button" class="btn btn-outline-danger" onclick="deleteInvoice({{ $invoice->id }})" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            @endforeach
            @else
            <tr><td colspan="7" class="text-center">No invoices found</td></tr>
            @endisset
          </tbody>
        </table>
      </div>
    </div>
    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
      &copy; 2026 Super Ittefaq Logistics &middot; Transport Management System
    </div>
  </div>

  <!-- Create/Edit Invoice Modal -->
  <div class="modal fade" id="invoiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="invoiceModalLabel">Create Invoice</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="invoiceForm" action="{{ route('invoices.store') }}" method="POST">
          @csrf
          <div class="modal-body">
            <input type="hidden" name="invoice_id" id="invoice_id">
            <input type="hidden" name="_method" id="_method" value="POST">
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="invoice_number" class="form-label">Invoice Number</label>
                <input type="text" class="form-control" name="invoice_number" id="invoice_number" required>
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
                <label for="amount" class="form-label">Amount</label>
                <input type="number" step="0.01" class="form-control" name="amount" id="amount" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="vat" class="form-label">VAT</label>
                <input type="number" step="0.01" class="form-control" name="vat" id="vat" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="invoice_date" class="form-label">Invoice Date</label>
                <input type="date" class="form-control" name="invoice_date" id="invoice_date" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="due_date" class="form-label">Due Date</label>
                <input type="date" class="form-control" name="due_date" id="due_date" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" name="status" id="status" required>
                  <option value="unpaid">Unpaid</option>
                  <option value="paid">Paid</option>
                  <option value="overdue">Overdue</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="paid_date" class="form-label">Paid Date</label>
                <input type="date" class="form-control" name="paid_date" id="paid_date">
              </div>
            </div>

            <div class="mb-3">
              <label for="notes" class="form-label">Notes</label>
              <textarea class="form-control" name="notes" id="notes" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Invoice</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- View Invoice Modal -->
  <div class="modal fade" id="viewInvoiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Invoice Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="viewInvoiceContent">
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
    $('#invoicesTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: 6 } // Actions column
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search invoices..."
        }
    });

    // Form submission
    $('#invoiceForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var invoiceId = $('#invoice_id').val();
        var url = invoiceId ? '/invoices/' + invoiceId : '{{ route("invoices.store") }}';
        var method = 'POST';
        
        // Set _method to PUT for updates
        if (invoiceId) {
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
                $('#invoiceModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                var errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                var errorMessage = 'Error saving invoice';
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
    $('#invoiceForm')[0].reset();
    $('#invoice_id').val('');
    $('#_method').val('POST');
    $('#invoiceModalLabel').text('Create Invoice');
}

function editInvoice(id) {
    $.ajax({
        url: '/invoices/' + id + '/edit',
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(invoice) {
            fillInvoiceForm(invoice);
            $('#invoiceModal').modal('show');
        },
        error: function() {
            alert('Error loading invoice data');
        }
    });
}

function fillInvoiceForm(invoice) {
    $('#invoice_id').val(invoice.id);
    $('#invoice_number').val(invoice.invoice_number);
    $('#customer_id').val(invoice.customer_id);
    $('#amount').val(invoice.amount);
    $('#vat').val(invoice.vat);
    $('#invoice_date').val(invoice.invoice_date);
    $('#due_date').val(invoice.due_date);
    $('#status').val(invoice.status);
    $('#paid_date').val(invoice.paid_date);
    $('#notes').val(invoice.notes);
    
    $('#invoiceModalLabel').text('Edit Invoice');
}

function viewInvoice(id) {
    $.ajax({
        url: '/invoices/' + id,
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(data) {
            var invoice = data.invoice || data;
            var html = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Invoice Information</h6>
                        <p><strong>Invoice Number:</strong> ${invoice.invoice_number}</p>
                        <p><strong>Customer:</strong> ${invoice.customer ? invoice.customer.name : 'N/A'}</p>
                        <p><strong>Invoice Date:</strong> ${invoice.invoice_date}</p>
                        <p><strong>Due Date:</strong> ${invoice.due_date}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Payment Details</h6>
                        <p><strong>Amount:</strong> PKR ${parseFloat(invoice.amount).toFixed(2)}</p>
                        <p><strong>VAT:</strong> PKR ${parseFloat(invoice.vat).toFixed(2)}</p>
                        <p><strong>Status:</strong> ${invoice.status}</p>
                        <p><strong>Paid Date:</strong> ${invoice.paid_date || 'N/A'}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Notes</h6>
                        <p>${invoice.notes || 'No notes provided'}</p>
                    </div>
                </div>
            `;
            $('#viewInvoiceContent').html(html);
            $('#viewInvoiceModal').modal('show');
        },
        error: function() {
            alert('Error loading invoice details');
        }
    });
}

function deleteInvoice(id) {
    if (confirm('Are you sure you want to delete this invoice?')) {
        $.ajax({
            url: '/invoices/' + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            },
            error: function() {
                alert('Error deleting invoice');
            }
        });
    }
}

function filterByStatus(status) {
    var table = $('#invoicesTable').DataTable();
    if (status === 'all') {
        table.column(5).search('').draw();
    } else {
        table.column(5).search(status).draw();
    }
}

function exportInvoices() {
    fetch('/invoices/export', {
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
        a.download = 'invoices_export_' + new Date().toISOString().split('T')[0] + '.csv';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    })
    .catch(error => {
        console.error('Export error:', error);
        alert('Error exporting invoices data');
    });
}
</script>
@endpush

