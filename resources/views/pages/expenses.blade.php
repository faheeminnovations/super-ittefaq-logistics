@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">Accounts</div>
        <h1>Expenses</h1>
        <div class="sub">Fuel, tolls, parking, repairs & driver expenses</div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-navy" onclick="exportExpenses()"><i class="bi bi-download me-1"></i> Export</button>
        <button class="btn btn-navy" data-bs-toggle="modal" data-bs-target="#expenseModal" onclick="resetForm()"><i class="bi bi-plus-lg me-1"></i> Add Expense</button>
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
          <div class="icon-badge" style="background:#EAF0FB;color:var(--navy-800);"><i class="bi bi-cash-coin"></i></div>
          <div class="label">Total Expenses</div>
          <div class="value">{{ \App\Helpers\CurrencyHelper::formatCurrency($totalAmount ?? 0) }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> {{ $totalExpenses ?? 0 }} records</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#EAF7EF;color:var(--success);"><i class="bi bi-check-circle"></i></div>
          <div class="label">Approved</div>
          <div class="value">{{ $approvedExpenses ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> Processed</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FFF3E0;color:var(--warn);"><i class="bi bi-hourglass-split"></i></div>
          <div class="label">Pending Review</div>
          <div class="value">{{ $pendingExpenses ?? 0 }}</div>
          <div class="delta down"><i class="bi bi-arrow-down-short"></i> Awaiting approval</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FBE9E7;color:var(--danger);"><i class="bi bi-x-circle"></i></div>
          <div class="label">Rejected</div>
          <div class="value">{{ $rejectedExpenses ?? 0 }}</div>
          <div class="delta down"><i class="bi bi-arrow-down-short"></i> Declined</div>
        </div>
      </div>
    </div>
    
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <span class="badge-status badge-pending" style="cursor:pointer;" onclick="filterByStatus('all')">All</span> 
      <span class="badge-status badge-delivered" style="cursor:pointer;" onclick="filterByStatus('approved')">Approved</span> 
      <span class="badge-status badge-transit" style="cursor:pointer;" onclick="filterByStatus('pending_review')">Pending Review</span> 
      <span class="badge-status badge-delayed" style="cursor:pointer;" onclick="filterByStatus('rejected')">Rejected</span> 
    </div>
    
    <div class="panel">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
          <div class="panel-title mb-0">Expense Log</div>
          <div class="panel-sub mb-0">All recorded fleet & driver expenses</div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-striped" id="expensesTable">
          <thead>
            <tr>
              <th>Date</th>
              <th>Category</th>
              <th>Vehicle</th>
              <th>Amount</th>
              <th>Submitted By</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @isset($expenses)
              @foreach($expenses as $expense)
            <tr>
              <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</td>
              <td>{{ $expense->category }}</td>
              <td>{{ $expense->vehicle ? $expense->vehicle->plate_number : 'N/A' }}</td>
              <td>{{ $expense->formatted_amount }}</td>
              <td>{{ $expense->submitted_by }}</td>
              <td>
                @switch($expense->status)
                  @case('approved')
                    <span class="badge-status badge-delivered">Approved</span>
                    @break
                  @case('pending_review')
                    <span class="badge-status badge-transit">Pending Review</span>
                    @break
                  @case('rejected')
                    <span class="badge-status badge-delayed">Rejected</span>
                    @break
                  @default
                    <span class="badge-status badge-pending">{{ ucfirst(str_replace('_', ' ', $expense->status)) }}</span>
                @endswitch
              </td>
              <td>
                <div class="btn-group btn-group-sm">
                  <button type="button" class="btn btn-outline-primary" onclick="editExpense({{ $expense->id }})" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button type="button" class="btn btn-outline-info" onclick="viewExpense({{ $expense->id }})" title="View">
                    <i class="bi bi-eye"></i>
                  </button>
                  <button type="button" class="btn btn-outline-danger" onclick="deleteExpense({{ $expense->id }})" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            @endforeach
            @else
            <tr><td colspan="7" class="text-center">No expenses found</td></tr>
            @endisset
          </tbody>
        </table>
      </div>
    </div>
    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
      &copy; 2026 Super Ittefaq Logistics &middot; Transport Management System
    </div>
  </div>

  <!-- Create/Edit Expense Modal -->
  <div class="modal fade" id="expenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="expenseModalLabel">Add Expense</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="expenseForm" action="{{ route('expenses.store') }}" method="POST">
          @csrf
          <div class="modal-body">
            <input type="hidden" name="expense_id" id="expense_id">
            <input type="hidden" name="_method" id="_method" value="POST">
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="expense_date" class="form-label">Expense Date</label>
                <input type="date" class="form-control" name="expense_date" id="expense_date" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" name="category" id="category" required>
                  <option value="">Select Category</option>
                  <option value="fuel">Fuel</option>
                  <option value="toll">Toll</option>
                  <option value="parking">Parking</option>
                  <option value="repair">Repair</option>
                  <option value="maintenance">Maintenance</option>
                  <option value="driver_claim">Driver Claim</option>
                  <option value="other">Other</option>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="vehicle_id" class="form-label">Vehicle</label>
                <select class="form-select" name="vehicle_id" id="vehicle_id">
                  <option value="">Select Vehicle</option>
                  @foreach($vehicles ?? [] as $vehicle)
                    <option value="{{ $vehicle->id }}">{{ $vehicle->plate_number }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="driver_id" class="form-label">Driver</label>
                <select class="form-select" name="driver_id" id="driver_id">
                  <option value="">Select Driver</option>
                  @foreach($drivers ?? [] as $driver)
                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
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
                <label for="submitted_by" class="form-label">Submitted By</label>
                <input type="text" class="form-control" name="submitted_by" id="submitted_by" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" name="status" id="status" required>
                  <option value="pending_review">Pending Review</option>
                  <option value="approved">Approved</option>
                  <option value="rejected">Rejected</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="receipt_url" class="form-label">Receipt URL</label>
                <input type="text" class="form-control" name="receipt_url" id="receipt_url">
              </div>
            </div>

            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control" name="description" id="description" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Expense</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- View Expense Modal -->
  <div class="modal fade" id="viewExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Expense Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="viewExpenseContent">
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
    $('#expensesTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: 6 } // Actions column
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search expenses..."
        }
    });

    // Form submission
    $('#expenseForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var expenseId = $('#expense_id').val();
        var url = expenseId ? '/expenses/' + expenseId : '{{ route("expenses.store") }}';
        var method = 'POST';
        
        // Set _method to PUT for updates
        if (expenseId) {
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
                $('#expenseModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                var errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                var errorMessage = 'Error saving expense';
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
    $('#expenseForm')[0].reset();
    $('#expense_id').val('');
    $('#_method').val('POST');
    $('#expenseModalLabel').text('Add Expense');
}

function editExpense(id) {
    $.ajax({
        url: '/expenses/' + id + '/edit',
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(expense) {
            fillExpenseForm(expense);
            $('#expenseModal').modal('show');
        },
        error: function() {
            alert('Error loading expense data');
        }
    });
}

function fillExpenseForm(expense) {
    $('#expense_id').val(expense.id);
    $('#expense_date').val(expense.expense_date);
    $('#category').val(expense.category);
    $('#vehicle_id').val(expense.vehicle_id);
    $('#driver_id').val(expense.driver_id);
    $('#amount').val(expense.amount);
    $('#submitted_by').val(expense.submitted_by);
    $('#status').val(expense.status);
    $('#receipt_url').val(expense.receipt_url);
    $('#description').val(expense.description);
    
    $('#expenseModalLabel').text('Edit Expense');
}

function viewExpense(id) {
    $.ajax({
        url: '/expenses/' + id,
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(data) {
            var expense = data.expense || data;
            var html = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Expense Information</h6>
                        <p><strong>Date:</strong> ${expense.expense_date}</p>
                        <p><strong>Category:</strong> ${expense.category}</p>
                        <p><strong>Vehicle:</strong> ${expense.vehicle ? expense.vehicle.plate_number : 'N/A'}</p>
                        <p><strong>Driver:</strong> ${expense.driver ? expense.driver.name : 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Payment Details</h6>
                        <p><strong>Amount:</strong> PKR ${parseFloat(expense.amount).toFixed(2)}</p>
                        <p><strong>Submitted By:</strong> ${expense.submitted_by}</p>
                        <p><strong>Status:</strong> ${expense.status}</p>
                        <p><strong>Receipt URL:</strong> ${expense.receipt_url || 'N/A'}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Description</h6>
                        <p>${expense.description || 'No description provided'}</p>
                    </div>
                </div>
            `;
            $('#viewExpenseContent').html(html);
            $('#viewExpenseModal').modal('show');
        },
        error: function() {
            alert('Error loading expense details');
        }
    });
}

function deleteExpense(id) {
    if (confirm('Are you sure you want to delete this expense?')) {
        $.ajax({
            url: '/expenses/' + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            },
            error: function() {
                alert('Error deleting expense');
            }
        });
    }
}

function filterByStatus(status) {
    var table = $('#expensesTable').DataTable();
    if (status === 'all') {
        table.column(5).search('').draw();
    } else {
        table.column(5).search(status).draw();
    }
}

function exportExpenses() {
    fetch('/expenses/export', {
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
        a.download = 'expenses_export_' + new Date().toISOString().split('T')[0] + '.csv';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    })
    .catch(error => {
        console.error('Export error:', error);
        alert('Error exporting expenses data');
    });
}
</script>
@endpush

