@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">Accounts</div>
        <h1>Customers</h1>
        <div class="sub">Customer profiles, contacts & credit history</div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-navy" onclick="exportCustomers()"><i class="bi bi-download me-1"></i> Export</button>
        <button class="btn btn-navy" data-bs-toggle="modal" data-bs-target="#customerModal" onclick="resetForm()"><i class="bi bi-plus-lg me-1"></i> Add Customer</button>
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
          <div class="icon-badge" style="background:#EAF0FB;color:var(--navy-800);"><i class="bi bi-people"></i></div>
          <div class="label">Total Customers</div>
          <div class="value">{{ $totalCustomers ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> All registered</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#EAF7EF;color:var(--success);"><i class="bi bi-check-circle"></i></div>
          <div class="label">Active Accounts</div>
          <div class="value">{{ $activeCustomers ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> {{ $totalCustomers ? round(($activeCustomers / $totalCustomers) * 100) : 0 }}% active</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FBE9E7;color:var(--danger);"><i class="bi bi-exclamation-circle"></i></div>
          <div class="label">On Hold</div>
          <div class="value">{{ $onHoldCustomers ?? 0 }}</div>
          <div class="delta down"><i class="bi bi-arrow-down-short"></i> Need attention</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#F3E3B8;color:#8A6512;"><i class="bi bi-cash-stack"></i></div>
          <div class="label">Credit Extended</div>
          <div class="value">{{ \App\Helpers\CurrencyHelper::formatCurrency($totalCredit ?? 0) }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> Across all accounts</div>
        </div>
      </div>
    </div>
    
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <span class="badge-status badge-pending" style="cursor:pointer;" onclick="filterByStatus('all')">All</span> 
      <span class="badge-status badge-delivered" style="cursor:pointer;" onclick="filterByStatus('active')">Active</span> 
      <span class="badge-status badge-delayed" style="cursor:pointer;" onclick="filterByStatus('on_hold')">On Hold</span> 
      <span class="badge-status badge-transit" style="cursor:pointer;" onclick="filterByStatus('new')">New</span> 
      <span class="badge-status badge-transit" style="cursor:pointer;" onclick="filterByStatus('near_limit')">Near Limit</span>
    </div>
    
    <div class="panel">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
          <div class="panel-title mb-0">Customer Directory</div>
          <div class="panel-sub mb-0">All registered customer accounts</div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-striped" id="customersTable">
          <thead>
            <tr>
              <th>Customer</th>
              <th>Contact</th>
              <th>Phone</th>
              <th>City</th>
              <th>Credit Limit</th>
              <th>Balance</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @isset($customers)
              @foreach($customers as $customer)
            <tr>
              <td>
                <div class='driver-mini'>
                  <div class='av'>{{ substr($customer->name, 0, 2) }}</div>
                  {{ $customer->name }}
                </div>
              </td>
              <td>{{ $customer->contact_email }}</td>
              <td>{{ $customer->phone ?? 'N/A' }}</td>
              <td>{{ $customer->city }}</td>
              <td>{{ $customer->formatted_credit_limit }}</td>
              <td>{{ $customer->formatted_balance }}</td>
              <td>
                @switch($customer->status)
                  @case('active')
                    <span class="badge-status badge-delivered">Active</span>
                    @break
                  @case('on_hold')
                    <span class="badge-status badge-delayed">On Hold</span>
                    @break
                  @case('new')
                    <span class="badge-status badge-transit">New</span>
                    @break
                  @case('near_limit')
                    <span class="badge-status badge-pending">Near Limit</span>
                    @break
                  @default
                    <span class="badge-status badge-pending">{{ ucfirst(str_replace('_', ' ', $customer->status)) }}</span>
                @endswitch
              </td>
              <td>
                <div class="btn-group btn-group-sm">
                  <button type="button" class="btn btn-outline-primary" onclick="editCustomer({{ $customer->id }})" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button type="button" class="btn btn-outline-info" onclick="viewCustomer({{ $customer->id }})" title="View">
                    <i class="bi bi-eye"></i>
                  </button>
                  <button type="button" class="btn btn-outline-danger" onclick="deleteCustomer({{ $customer->id }})" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            @endforeach
            @else
            <tr><td colspan="8" class="text-center">No customers found</td></tr>
            @endisset
          </tbody>
        </table>
      </div>
    </div>
    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
      &copy; 2026 Super Ittefaq Logistics &middot; Transport Management System
    </div>
  </div>

  <!-- Create/Edit Customer Modal -->
  <div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="customerModalLabel">New Customer</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="customerForm" action="{{ route('customers.store') }}" method="POST">
          @csrf
          <div class="modal-body">
            <input type="hidden" name="customer_id" id="customer_id">
            <input type="hidden" name="_method" id="_method" value="POST">
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Customer Name</label>
                <input type="text" class="form-control" name="name" id="name" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="contact_email" class="form-label">Email</label>
                <input type="email" class="form-control" name="contact_email" id="contact_email" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" name="phone" id="phone">
              </div>
              <div class="col-md-6 mb-3">
                <label for="city" class="form-label">City</label>
                <input type="text" class="form-control" name="city" id="city" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="credit_limit" class="form-label">Credit Limit</label>
                <input type="number" step="0.01" class="form-control" name="credit_limit" id="credit_limit" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="balance" class="form-label">Balance</label>
                <input type="number" step="0.01" class="form-control" name="balance" id="balance" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" name="status" id="status" required>
                  <option value="active">Active</option>
                  <option value="on_hold">On Hold</option>
                  <option value="new">New</option>
                  <option value="near_limit">Near Limit</option>
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
            <button type="submit" class="btn btn-primary">Save Customer</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- View Customer Modal -->
  <div class="modal fade" id="viewCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Customer Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="viewCustomerContent">
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
    $('#customersTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: 7 } // Actions column
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search customers..."
        }
    });

    // Form submission
    $('#customerForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var customerId = $('#customer_id').val();
        var url = customerId ? '/customers/' + customerId : '{{ route("customers.store") }}';
        var method = 'POST';
        
        // Set _method to PUT for updates
        if (customerId) {
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
                $('#customerModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                var errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                var errorMessage = 'Error saving customer';
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
    $('#customerForm')[0].reset();
    $('#customer_id').val('');
    $('#_method').val('POST');
    $('#customerModalLabel').text('New Customer');
}

function editCustomer(id) {
    $.ajax({
        url: '/customers/' + id + '/edit',
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(customer) {
            fillCustomerForm(customer);
            $('#customerModal').modal('show');
        },
        error: function() {
            alert('Error loading customer data');
        }
    });
}

function fillCustomerForm(customer) {
    $('#customer_id').val(customer.id);
    $('#name').val(customer.name);
    $('#contact_email').val(customer.contact_email);
    $('#phone').val(customer.phone);
    $('#city').val(customer.city);
    $('#credit_limit').val(customer.credit_limit);
    $('#balance').val(customer.balance);
    $('#status').val(customer.status);
    $('#address').val(customer.address);
    
    $('#customerModalLabel').text('Edit Customer');
}

function viewCustomer(id) {
    $.ajax({
        url: '/customers/' + id,
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(data) {
            var customer = data.customer || data;
            var html = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Customer Information</h6>
                        <p><strong>Name:</strong> ${customer.name}</p>
                        <p><strong>Email:</strong> ${customer.contact_email}</p>
                        <p><strong>Phone:</strong> ${customer.phone || 'N/A'}</p>
                        <p><strong>City:</strong> ${customer.city}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Account Details</h6>
                        <p><strong>Credit Limit:</strong> PKR ${parseFloat(customer.credit_limit).toFixed(2)}</p>
                        <p><strong>Balance:</strong> PKR ${parseFloat(customer.balance).toFixed(2)}</p>
                        <p><strong>Status:</strong> ${customer.status}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Address</h6>
                        <p>${customer.address || 'No address provided'}</p>
                    </div>
                </div>
            `;
            $('#viewCustomerContent').html(html);
            $('#viewCustomerModal').modal('show');
        },
        error: function() {
            alert('Error loading customer details');
        }
    });
}

function deleteCustomer(id) {
    if (confirm('Are you sure you want to delete this customer?')) {
        $.ajax({
            url: '/customers/' + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            },
            error: function() {
                alert('Error deleting customer');
            }
        });
    }
}

function filterByStatus(status) {
    var table = $('#customersTable').DataTable();
    if (status === 'all') {
        table.column(6).search('').draw();
    } else {
        table.column(6).search(status).draw();
    }
}

function exportCustomers() {
    fetch('/customers/export', {
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
        a.download = 'customers_export_' + new Date().toISOString().split('T')[0] + '.csv';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    })
    .catch(error => {
        console.error('Export error:', error);
        alert('Error exporting customers data');
    });
}
</script>
@endpush

