@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">Accounts</div>
        <h1>Billing Management</h1>
        <div class="sub">Monthly billing records, dues & payment tracking</div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-navy" onclick="exportBilling()"><i class="bi bi-download me-1"></i> Export CSV</button>
        <button class="btn btn-outline-navy" onclick="showMonthlySummary()"><i class="bi bi-bar-chart me-1"></i> Monthly Summary</button>
        <button class="btn btn-navy" data-bs-toggle="modal" data-bs-target="#billingModal" onclick="resetBillingForm()"><i class="bi bi-plus-lg me-1"></i> Add Record</button>
      </div>
    </div>
    
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <!-- Summary Statistics -->
    <div class="row g-3 mb-3">
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#EAF0FB;color:var(--navy-800);"><i class="bi bi-currency-rupee"></i></div>
          <div class="label">Total Rent</div>
          <div class="value">{{ number_format($totalRent, 2) }}</div>
          <div class="delta">This month</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="icon-badge" style="background:#EAF7EF;color:var(--success);"><i class="bi bi-check-circle"></i></div>
        <div class="label">Paid Records</div>
        <div class="value">{{ $paidCount }}</div>
        <div class="delta up">Completed payments</div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FBE9E7;color:var(--danger);"><i class="bi bi-exclamation-circle"></i></div>
          <div class="label">Pending Records</div>
          <div class="value">{{ $pendingCount }}</div>
          <div class="delta down">Awaiting payment</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FFF3E0;color:var(--warning);"><i class="bi bi-wallet"></i></div>
          <div class="label">Total Dues</div>
          <div class="value">{{ number_format($totalDues, 2) }}</div>
          <div class="delta">Outstanding amount</div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Billing Month</label>
            <input type="month" class="form-control" id="filter_month" value="{{ $currentMonth }}" onchange="filterBillings()">
          </div>
          <div class="col-md-3">
            <label class="form-label">Vehicle No</label>
            <select class="form-select" id="filter_vehicle" onchange="filterBillings()">
              <option value="">All Vehicles</option>
              @foreach($vehicles as $vehicle)
                <option value="{{ $vehicle->reg_no }}">{{ strtoupper($vehicle->reg_no) }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Customer</label>
            <select class="form-select" id="filter_customer" onchange="filterBillings()">
              <option value="">All Customers</option>
              @foreach($customers as $customer)
                <option value="{{ $customer->name }}">{{ ucwords(strtolower($customer->name)) }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Status</label>
            <select class="form-select" id="filter_status" onchange="filterBillings()">
              <option value="all">All Status</option>
              <option value="paid">Paid</option>
              <option value="pending">Pending</option>
              <option value="partial">Partial</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Billing Table -->
    <div class="card">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Sr</th>
                <th>Date</th>
                <th>Vhl No</th>
                <th>Name</th>
                <th>Number</th>
                <th>Bag</th>
                <th>Drop/Delivery Point</th>
                <th>Km Cover</th>
                <th>Rent</th>
                <th>Advance</th>
                <th>Advance Date</th>
                <th>Guarantor</th>
                <th>Dues</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="billingTableBody">
              @foreach($billings as $billing)
                <tr>
                  <td>{{ $billing->formatted_sr }}</td>
                  <td>{{ $billing->formatted_date }}</td>
                  <td>{{ $billing->formatted_vehicle_no }}</td>
                  <td>{{ $billing->formatted_customer_name }}</td>
                  <td>{{ $billing->formatted_contact_number }}</td>
                  <td>{{ $billing->formatted_bags }}</td>
                  <td>{{ $billing->formatted_delivery_point }}</td>
                  <td>{{ $billing->formatted_km_covered }}</td>
                  <td>{{ $billing->formatted_rent_amount }}</td>
                  <td>{{ $billing->formatted_advance_amount }}</td>
                  <td>{{ $billing->formatted_advance_date }}</td>
                  <td>{{ $billing->formatted_guarantor }}</td>
                  <td>{{ $billing->formatted_dues_amount }}</td>
                  <td>
                    <span class="badge bg-{{ $billing->status_badge_class }}">
                      {{ $billing->formatted_status }}
                    </span>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="editBilling({{ $billing->id }})">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteBilling({{ $billing->id }})">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Billing Modal -->
  <div class="modal fade" id="billingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="billingModalTitle">Add Billing Record</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="billingForm">
            <input type="hidden" id="billing_id">
            <div class="row g-3">
              <div class="col-md-2">
                <label class="form-label">Sr <span class="text-muted">(Serial No)</span></label>
                <input type="number" class="form-control" id="billing_sr" placeholder="1">
              </div>
              <div class="col-md-2">
                <label class="form-label">Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="billing_date" required>
              </div>
              <div class="col-md-3">
                <label class="form-label">Vehicle No <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="billing_vehicle_no" placeholder="ABC-123" required>
              </div>
              <div class="col-md-5">
                <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="billing_customer_name" placeholder="Customer Name" required>
              </div>
              <div class="col-md-3">
                <label class="form-label">Contact Number</label>
                <input type="text" class="form-control" id="billing_contact_number" placeholder="0300-1234567">
              </div>
              <div class="col-md-2">
                <label class="form-label">Bags</label>
                <input type="number" class="form-control" id="billing_bags" value="0" min="0">
              </div>
              <div class="col-md-4">
                <label class="form-label">Delivery Point <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="billing_delivery_point" placeholder="Delivery Location" required>
              </div>
              <div class="col-md-3">
                <label class="form-label">Km Covered</label>
                <input type="number" step="0.01" class="form-control" id="billing_km_covered" value="0" min="0">
              </div>
              <div class="col-md-2">
                <label class="form-label">Rent (PKR) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" class="form-control" id="billing_rent" placeholder="0.00" required min="0">
              </div>
              <div class="col-md-2">
                <label class="form-label">Advance (PKR)</label>
                <input type="number" step="0.01" class="form-control" id="billing_advance" value="0" min="0">
              </div>
              <div class="col-md-2">
                <label class="form-label">Advance Date</label>
                <input type="date" class="form-control" id="billing_advance_date">
              </div>
              <div class="col-md-3">
                <label class="form-label">Guarantor</label>
                <input type="text" class="form-control" id="billing_guarantor" placeholder="Guarantor Name">
              </div>
              <div class="col-md-2">
                <label class="form-label">Dues (PKR)</label>
                <input type="number" step="0.01" class="form-control" id="billing_dues" value="0" min="0">
              </div>
              <div class="col-md-2">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select class="form-select" id="billing_status" required>
                  <option value="Pending">Pending</option>
                  <option value="Paid">Paid</option>
                  <option value="Partial">Partial</option>
                </select>
              </div>
              <div class="col-md-12">
                <label class="form-label">Billing Month <span class="text-danger">*</span></label>
                <input type="month" class="form-control" id="billing_month" required>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="saveBilling()">Save Record</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Monthly Summary Modal -->
  <div class="modal fade" id="summaryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Monthly Billing Summary</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="summaryContent">
            <!-- Summary will be loaded here -->
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    let billingModal;

    document.addEventListener('DOMContentLoaded', function() {
      billingModal = new bootstrap.Modal(document.getElementById('billingModal'));
      
      // Set default billing month to current month
      document.getElementById('billing_month').value = '{{ $currentMonth }}';
      document.getElementById('billing_date').valueAsDate = new Date();
    });

    function resetBillingForm() {
      document.getElementById('billingForm').reset();
      document.getElementById('billing_id').value = '';
      document.getElementById('billingModalTitle').textContent = 'Add Billing Record';
      document.getElementById('billing_month').value = document.getElementById('filter_month').value;
      document.getElementById('billing_date').valueAsDate = new Date();
    }

    function editBilling(id) {
      // Fetch billing record and populate form
      fetch(`/api/billing/${id}`)
        .then(response => response.json())
        .then(data => {
          document.getElementById('billing_id').value = data.id;
          document.getElementById('billing_sr').value = data.sr || '';
          document.getElementById('billing_date').value = data.date || '';
          document.getElementById('billing_vehicle_no').value = data.vehicle_no || '';
          document.getElementById('billing_customer_name').value = data.customer_name || '';
          document.getElementById('billing_contact_number').value = data.contact_number || '';
          document.getElementById('billing_bags').value = data.bags || 0;
          document.getElementById('billing_delivery_point').value = data.delivery_point || '';
          document.getElementById('billing_km_covered').value = data.km_covered || 0;
          document.getElementById('billing_rent').value = data.rent || 0;
          document.getElementById('billing_advance').value = data.advance || 0;
          document.getElementById('billing_advance_date').value = data.advance_date || '';
          document.getElementById('billing_guarantor').value = data.guarantor || '';
          document.getElementById('billing_dues').value = data.dues || 0;
          document.getElementById('billing_status').value = data.status || 'Pending';
          document.getElementById('billing_month').value = data.billing_month || '';
          
          document.getElementById('billingModalTitle').textContent = 'Edit Billing Record';
          billingModal.show();
        })
        .catch(error => {
          console.error('Error fetching billing record:', error);
          alert('Error loading billing record');
        });
    }

    function saveBilling() {
      const id = document.getElementById('billing_id').value;
      
      // Get form values with proper handling
      const sr = document.getElementById('billing_sr').value;
      const date = document.getElementById('billing_date').value;
      const vehicleNo = document.getElementById('billing_vehicle_no').value;
      const customerName = document.getElementById('billing_customer_name').value;
      const contactNumber = document.getElementById('billing_contact_number').value;
      const bags = document.getElementById('billing_bags').value;
      const deliveryPoint = document.getElementById('billing_delivery_point').value;
      const kmCovered = document.getElementById('billing_km_covered').value;
      const rent = document.getElementById('billing_rent').value;
      const advance = document.getElementById('billing_advance').value;
      const advanceDate = document.getElementById('billing_advance_date').value;
      const guarantor = document.getElementById('billing_guarantor').value;
      const dues = document.getElementById('billing_dues').value;
      const status = document.getElementById('billing_status').value;
      const billingMonth = document.getElementById('billing_month').value;

      // Basic validation
      if (!date || !vehicleNo || !customerName || !deliveryPoint || !rent || !status || !billingMonth) {
        alert('Please fill in all required fields');
        return;
      }

      const formData = {
        sr: sr ? parseInt(sr) : null,
        date: date,
        vehicle_no: vehicleNo.toUpperCase(),
        customer_name: customerName,
        contact_number: contactNumber,
        bags: bags ? parseInt(bags) : 0,
        delivery_point: deliveryPoint,
        km_covered: kmCovered ? parseFloat(kmCovered) : 0,
        rent: parseFloat(rent),
        advance: advance ? parseFloat(advance) : 0,
        advance_date: advanceDate || null,
        guarantor: guarantor || null,
        dues: dues ? parseFloat(dues) : 0,
        status: status,
        billing_month: billingMonth
      };

      const url = id ? `/billing/${id}` : '/billing';
      const method = id ? 'PUT' : 'POST';

      fetch(url, {
        method: method,
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(formData)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          billingModal.hide();
          filterBillings();
          alert(data.message);
        } else {
          alert('Error: ' + (data.message || JSON.stringify(data)));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error saving billing record: ' + error.message);
      });
    }

    function deleteBilling(id) {
      if (confirm('Are you sure you want to delete this billing record?')) {
        fetch(`/billing/${id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            filterBillings();
            alert(data.message);
          } else {
            alert('Error deleting record');
          }
        });
      }
    }

    function filterBillings() {
      const month = document.getElementById('filter_month').value;
      const vehicle = document.getElementById('filter_vehicle').value;
      const customer = document.getElementById('filter_customer').value;
      const status = document.getElementById('filter_status').value;

      fetch('/billing/filter', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
          billing_month: month,
          vehicle_no: vehicle,
          customer_name: customer,
          status: status
        })
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        updateBillingTable(data.billings);
        updateSummaryStats(data);
      })
      .catch(error => {
        console.error('Error filtering billings:', error);
        alert('Error filtering billings: ' + error.message);
      });
    }

    function updateBillingTable(billings) {
      const tbody = document.getElementById('billingTableBody');
      tbody.innerHTML = '';
      
      billings.forEach(billing => {
        // Use formatted data from the backend or format locally
        const statusClass = billing.status === 'Paid' ? 'success' : (billing.status === 'Pending' ? 'danger' : 'warning');
        const sr = billing.sr || '-';
        const date = billing.date ? new Date(billing.date).toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: '2-digit'}) : '-';
        const vehicleNo = billing.vehicle_no ? billing.vehicle_no.toUpperCase() : '-';
        const customerName = billing.customer_name ? billing.customer_name : '-';
        const contactNumber = billing.contact_number || '-';
        const bags = billing.bags || 0;
        const deliveryPoint = billing.delivery_point || '-';
        const kmCovered = Number(billing.km_covered || 0).toFixed(2);
        const rent = Number(billing.rent || 0).toFixed(2);
        const advance = Number(billing.advance || 0).toFixed(2);
        const advanceDate = billing.advance_date ? new Date(billing.advance_date).toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: '2-digit'}) : '-';
        const guarantor = billing.guarantor || '-';
        const dues = Number(billing.dues || 0).toFixed(2);
        const status = billing.status || 'Pending';
        
        const row = `
          <tr>
            <td>${sr}</td>
            <td>${date}</td>
            <td>${vehicleNo}</td>
            <td>${customerName}</td>
            <td>${contactNumber}</td>
            <td>${bags}</td>
            <td>${deliveryPoint}</td>
            <td>${kmCovered}</td>
            <td>${rent}</td>
            <td>${advance}</td>
            <td>${advanceDate}</td>
            <td>${guarantor}</td>
            <td>${dues}</td>
            <td><span class="badge bg-${statusClass}">${status}</span></td>
            <td>
              <button class="btn btn-sm btn-outline-primary" onclick="editBilling(${billing.id})">
                <i class="bi bi-pencil"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger" onclick="deleteBilling(${billing.id})">
                <i class="bi bi-trash"></i>
              </button>
            </td>
          </tr>
        `;
        tbody.innerHTML += row;
      });
    }

    function updateSummaryStats(data) {
      // Update the summary statistics in the stat cards
      document.querySelector('.stat-card:nth-child(1) .value').textContent = Number(data.totalRent).toFixed(2);
      document.querySelector('.stat-card:nth-child(2) .value').textContent = data.paidCount;
      document.querySelector('.stat-card:nth-child(3) .value').textContent = data.pendingCount;
      document.querySelector('.stat-card:nth-child(4) .value').textContent = Number(data.totalDues).toFixed(2);
    }

    function exportBilling() {
      const month = document.getElementById('filter_month').value;
      if (!month) {
        alert('Please select a billing month first');
        return;
      }
      
      // Create a temporary link to trigger download
      const link = document.createElement('a');
      link.href = `/billing/export?month=${month}`;
      link.download = `billing_export_${month}.csv`;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }

    function showMonthlySummary() {
      const month = document.getElementById('filter_month').value;
      
      fetch(`/billing/monthly-summary?month=${month}`)
        .then(response => response.json())
        .then(data => {
          const summaryContent = document.getElementById('summaryContent');
          summaryContent.innerHTML = `
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body">
                    <h6 class="card-title">Overall Summary</h6>
                    <table class="table table-sm">
                      <tr><td>Total Records:</td><td>${data.summary.total_records}</td></tr>
                      <tr><td>Total Rent:</td><td>${Number(data.summary.total_rent).toFixed(2)}</td></tr>
                      <tr><td>Total Advance:</td><td>${Number(data.summary.total_advance).toFixed(2)}</td></tr>
                      <tr><td>Total Dues:</td><td>${Number(data.summary.total_dues).toFixed(2)}</td></tr>
                      <tr><td>Total Bags:</td><td>${data.summary.total_bags}</td></tr>
                      <tr><td>Total KM:</td><td>${Number(data.summary.total_km).toFixed(2)}</td></tr>
                      <tr><td>Paid:</td><td>${data.summary.paid_count}</td></tr>
                      <tr><td>Pending:</td><td>${data.summary.pending_count}</td></tr>
                      <tr><td>Partial:</td><td>${data.summary.partial_count}</td></tr>
                      <tr><td>Pending Dues:</td><td>${Number(data.summary.pending_dues).toFixed(2)}</td></tr>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body">
                    <h6 class="card-title">By Customer</h6>
                    <table class="table table-sm">
                      <thead><tr><th>Customer</th><th>Trips</th><th>Rent</th><th>Dues</th><th>Pending</th></tr></thead>
                      <tbody>
                        ${Object.entries(data.by_customer).map(([name, stats]) => `
                          <tr>
                            <td>${name}</td>
                            <td>${stats.total_trips}</td>
                            <td>${Number(stats.total_rent).toFixed(2)}</td>
                            <td>${Number(stats.total_dues).toFixed(2)}</td>
                            <td>${stats.pending_records}</td>
                          </tr>
                        `).join('')}
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body">
                    <h6 class="card-title">By Vehicle</h6>
                    <table class="table table-sm">
                      <thead><tr><th>Vehicle</th><th>Trips</th><th>Rent</th><th>KM</th></tr></thead>
                      <tbody>
                        ${Object.entries(data.by_vehicle).map(([no, stats]) => `
                          <tr>
                            <td>${no}</td>
                            <td>${stats.total_trips}</td>
                            <td>${Number(stats.total_rent).toFixed(2)}</td>
                            <td>${Number(stats.total_km).toFixed(2)}</td>
                          </tr>
                        `).join('')}
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          `;
          
          new bootstrap.Modal(document.getElementById('summaryModal')).show();
        });
    }
  </script>
@endsection