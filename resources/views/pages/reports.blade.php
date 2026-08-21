@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">System</div>
        <h1>Reports</h1>
        <div class="sub">Revenue, profit, mileage, fuel & driver performance</div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-navy" onclick="exportReport()"><i class="bi bi-download me-1"></i> Export</button>
        <button class="btn btn-navy" onclick="applyFilters()"><i class="bi bi-funnel me-1"></i> Apply Filters</button>
      </div>
    </div>

    <!-- Filters Section -->
    <div class="panel mb-3">
      <div class="panel-title">
        <i class="bi bi-funnel-fill me-2"></i>Report Filters
        <button class="btn btn-sm btn-outline-secondary ms-auto" onclick="resetFilters()">
          <i class="bi bi-arrow-counterclockwise"></i> Reset
        </button>
      </div>
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Date Range</label>
          <select class="form-select" id="dateRange" onchange="toggleCustomDate()">
            <option value="today">Today</option>
            <option value="week">This Week</option>
            <option value="month" selected>This Month</option>
            <option value="quarter">This Quarter</option>
            <option value="year">This Year</option>
            <option value="custom">Custom Range</option>
          </select>
        </div>
        <div class="col-md-3" id="customDateFrom" style="display:none;">
          <label class="form-label">From Date</label>
          <input type="date" class="form-control" id="fromDate">
        </div>
        <div class="col-md-3" id="customDateTo" style="display:none;">
          <label class="form-label">To Date</label>
          <input type="date" class="form-control" id="toDate">
        </div>
        <div class="col-md-3">
          <label class="form-label">Report Type</label>
          <select class="form-select" id="reportType">
            <option value="all">All Reports</option>
            <option value="revenue">Revenue</option>
            <option value="expenses">Expenses</option>
            <option value="profit">Profit Analysis</option>
            <option value="vehicles">Vehicle Performance</option>
            <option value="drivers">Driver Performance</option>
            <option value="customers">Customer Analysis</option>
            <option value="billing">Billing Reports</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Vehicle</label>
          <select class="form-select" id="vehicleFilter">
            <option value="all">All Vehicles</option>
            @foreach($vehicles ?? [] as $vehicle)
            <option value="{{ $vehicle->id }}">{{ $vehicle->reg_no }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Customer</label>
          <select class="form-select" id="customerFilter">
            <option value="all">All Customers</option>
            @foreach($customers ?? [] as $customer)
            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Driver</label>
          <select class="form-select" id="driverFilter">
            <option value="all">All Drivers</option>
            @foreach($drivers ?? [] as $driver)
            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Status</label>
          <select class="form-select" id="statusFilter">
            <option value="all">All Status</option>
            <option value="completed">Completed</option>
            <option value="pending">Pending</option>
            <option value="in_transit">In Transit</option>
            <option value="delayed">Delayed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#EAF0FB;color:var(--navy-800);"><i class="bi bi-graph-up-arrow"></i></div>
          <div class="label">Revenue (Filtered)</div>
          <div class="value" id="revenueValue">{{ \App\Helpers\CurrencyHelper::formatCurrency($totalRevenue ?? 0) }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> Based on current filters</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FFF3E0;color:var(--warn);"><i class="bi bi-cash-coin"></i></div>
          <div class="label">Expenses (Filtered)</div>
          <div class="value" id="expensesValue">{{ \App\Helpers\CurrencyHelper::formatCurrency($totalExpenses ?? 0) }}</div>
          <div class="delta down"><i class="bi bi-arrow-down-short"></i> Based on current filters</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#EAF7EF;color:var(--success);"><i class="bi bi-pie-chart"></i></div>
          <div class="label">Net Profit (Filtered)</div>
          <div class="value" id="profitValue">{{ \App\Helpers\CurrencyHelper::formatCurrency(($totalRevenue ?? 0) - ($totalExpenses ?? 0)) }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> Based on current filters</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#F3E3B8;color:#8A6512;"><i class="bi bi-speedometer"></i></div>
          <div class="label">Total Trips (Filtered)</div>
          <div class="value" id="tripsValue">{{ $totalTrips ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> Based on current filters</div>
        </div>
      </div>
    </div>

    <!-- Monthly Summary Section -->
    <div class="panel mb-3">
      <div class="panel-title">
        <i class="bi bi-calendar-check me-2"></i>Monthly Billing Summary
        <span class="badge bg-primary ms-2">{{ $currentMonth ?? date('Y-m') }}</span>
      </div>
      <div class="row g-3">
        <div class="col-md-6">
          <div class="card">
            <div class="card-body">
              <h6 class="card-title mb-3">Key Metrics</h6>
              <div class="row g-2">
                <div class="col-6">
                  <div class="d-flex justify-content-between">
                    <span>Total Records:</span>
                    <strong>{{ $monthlySummary['total_records'] ?? 0 }}</strong>
                  </div>
                </div>
                <div class="col-6">
                  <div class="d-flex justify-content-between">
                    <span>Total Bags:</span>
                    <strong>{{ $monthlySummary['total_bags'] ?? 0 }}</strong>
                  </div>
                </div>
                <div class="col-6">
                  <div class="d-flex justify-content-between">
                    <span>Total KM:</span>
                    <strong>{{ number_format($monthlySummary['total_km'] ?? 0, 2) }}</strong>
                  </div>
                </div>
                <div class="col-6">
                  <div class="d-flex justify-content-between">
                    <span>Total Advance:</span>
                    <strong>{{ \App\Helpers\CurrencyHelper::formatCurrency($totalAdvance ?? 0) }}</strong>
                  </div>
                </div>
                <div class="col-6">
                  <div class="d-flex justify-content-between">
                    <span>Total Dues:</span>
                    <strong>{{ \App\Helpers\CurrencyHelper::formatCurrency($totalDues ?? 0) }}</strong>
                  </div>
                </div>
                <div class="col-6">
                  <div class="d-flex justify-content-between">
                    <span>Pending Dues:</span>
                    <strong class="text-danger">{{ \App\Helpers\CurrencyHelper::formatCurrency($totalPendingDues ?? 0) }}</strong>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card">
            <div class="card-body">
              <h6 class="card-title mb-3">Payment Status</h6>
              <div class="d-flex justify-content-around mb-3">
                <div class="text-center">
                  <div class="display-6 text-success">{{ $monthlySummary['paid_count'] ?? 0 }}</div>
                  <div class="small text-muted">Paid</div>
                </div>
                <div class="text-center">
                  <div class="display-6 text-danger">{{ $monthlySummary['pending_count'] ?? 0 }}</div>
                  <div class="small text-muted">Pending</div>
                </div>
                <div class="text-center">
                  <div class="display-6 text-warning">{{ $monthlySummary['partial_count'] ?? 0 }}</div>
                  <div class="small text-muted">Partial</div>
                </div>
              </div>
              <div class="progress" style="height: 20px;">
                @php
                  $total = ($monthlySummary['paid_count'] ?? 0) + ($monthlySummary['pending_count'] ?? 0) + ($monthlySummary['partial_count'] ?? 0);
                  $paidPercent = $total > 0 ? (($monthlySummary['paid_count'] ?? 0) / $total) * 100 : 0;
                  $pendingPercent = $total > 0 ? (($monthlySummary['pending_count'] ?? 0) / $total) * 100 : 0;
                  $partialPercent = $total > 0 ? (($monthlySummary['partial_count'] ?? 0) / $total) * 100 : 0;
                @endphp
                <div class="progress-bar bg-success" style="width: {{ $paidPercent }}%" title="Paid: {{ $paidPercent }}%"></div>
                <div class="progress-bar bg-danger" style="width: {{ $pendingPercent }}%" title="Pending: {{ $pendingPercent }}%"></div>
                <div class="progress-bar bg-warning" style="width: {{ $partialPercent }}%" title="Partial: {{ $partialPercent }}%"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Detailed Reports Table -->
    <div class="panel mb-3">
      <div class="panel-title">
        <i class="bi bi-table me-2"></i>Detailed Report Data
        <div class="ms-auto d-flex gap-2">
          <button class="btn btn-sm btn-outline-primary" onclick="exportToCSV()">
            <i class="bi bi-filetype-csv"></i> CSV
          </button>
          <button class="btn btn-sm btn-outline-success" onclick="exportToPDF()">
            <i class="bi bi-filetype-pdf"></i> PDF
          </button>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover" id="reportTable">
          <thead>
            <tr>
              <th>Date</th>
              <th>Vehicle</th>
              <th>Customer</th>
              <th>Driver</th>
              <th>Type</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Distance</th>
            </tr>
          </thead>
          <tbody id="reportTableBody">
            @foreach($reportData ?? [] as $row)
            <tr>
              <td>{{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}</td>
              <td>{{ $row->vehicle_no ?? 'N/A' }}</td>
              <td>{{ $row->customer_name ?? 'N/A' }}</td>
              <td>{{ $row->driver_name ?? 'N/A' }}</td>
              <td>{{ $row->type ?? 'N/A' }}</td>
              <td>{{ \App\Helpers\CurrencyHelper::formatCurrency($row->amount ?? 0) }}</td>
              <td>
                <span class="badge-status badge-{{ $row->status ?? 'pending' }}">
                  {{ ucfirst($row->status ?? 'pending') }}
                </span>
              </td>
              <td>{{ $row->distance ? number_format($row->distance, 2) . ' km' : 'N/A' }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-lg-6">
        <div class="panel">
          <div class="panel-title">Revenue Trend</div>
          <div class="panel-sub">Based on current filters</div>
          <canvas id="revChart" height="170"></canvas>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="panel">
          <div class="panel-title">Status Distribution</div>
          <div class="panel-sub">Based on current filters</div>
          <canvas id="statusChart" height="170"></canvas>
        </div>
      </div>
    </div>
    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
      &copy; 2026 Super Ittefaq Logistics &middot; Transport Management System
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let revChart, statusChart;

// Initialize charts
function initCharts() {
  try {
    const revCtx = document.getElementById('revChart').getContext('2d');
    const chartLabels = {{ isset($chartLabels) ? json_encode($chartLabels) : "['Mar','Apr','May','Jun','Jul','Aug']" }};
    const chartData = {{ isset($chartData) ? json_encode($chartData) : "[32000,35500,41000,39500,44000,48260]" }};
    
    revChart = new Chart(revCtx, {
      type: 'line',
      data: {
        labels: chartLabels,
        datasets: [{
          label: 'Revenue',
          data: chartData,
          borderColor: '#1B2A4A',
          backgroundColor: 'rgba(27,42,74,0.08)',
          fill: true,
          tension: 0.35
        }]
      },
      options: {
        plugins: { legend: { display: false } },
        scales: {
          y: { grid: { color: '#EEF1F6' } },
          x: { grid: { display: false } }
        }
      }
    });

    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusLabels = {{ isset($statusLabels) ? json_encode($statusLabels) : "['Delivered','In Transit','Pending','Delayed']" }};
    const statusData = {{ isset($statusData) ? json_encode($statusData) : "[58,22,14,6]" }};
    
    statusChart = new Chart(statusCtx, {
      type: 'doughnut',
      data: {
        labels: statusLabels,
        datasets: [{
          data: statusData,
          backgroundColor: ['#2E7D5B','#D4A537','#6B7688','#C0392B']
        }]
      },
      options: {
        plugins: {
          legend: {
            position: 'bottom',
            labels: { boxWidth: 10, font: { size: 11 } }
          }
        }
      }
    });
  } catch (error) {
    console.error('Error initializing charts:', error);
  }
}

// Toggle custom date fields
function toggleCustomDate() {
  const dateRange = document.getElementById('dateRange').value;
  document.getElementById('customDateFrom').style.display = dateRange === 'custom' ? 'block' : 'none';
  document.getElementById('customDateTo').style.display = dateRange === 'custom' ? 'block' : 'none';
}

// Apply filters
function applyFilters() {
  const filters = {
    dateRange: document.getElementById('dateRange').value,
    fromDate: document.getElementById('fromDate').value,
    toDate: document.getElementById('toDate').value,
    reportType: document.getElementById('reportType').value,
    vehicle: document.getElementById('vehicleFilter').value,
    customer: document.getElementById('customerFilter').value,
    driver: document.getElementById('driverFilter').value,
    status: document.getElementById('statusFilter').value
  };

  // Send AJAX request to get filtered data
  fetch('/reports/filter', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify(filters)
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    return response.json();
  })
  .then(data => {
    // Update stats
    document.getElementById('revenueValue').textContent = data.revenue;
    document.getElementById('expensesValue').textContent = data.expenses;
    document.getElementById('profitValue').textContent = data.profit;
    document.getElementById('tripsValue').textContent = data.trips;

    // Update monthly summary if available
    if (data.monthlySummary) {
      updateMonthlySummary(data.monthlySummary);
    }

    // Update table
    const tbody = document.getElementById('reportTableBody');
    tbody.innerHTML = '';
    data.rows.forEach(row => {
      tbody.innerHTML += `
        <tr>
          <td>${row.date}</td>
          <td>${row.vehicle}</td>
          <td>${row.customer}</td>
          <td>${row.driver}</td>
          <td>${row.type}</td>
          <td>${row.amount}</td>
          <td><span class="badge-status badge-${row.status}">${row.status}</span></td>
          <td>${row.distance}</td>
        </tr>
      `;
    });

    // Update charts
    if (revChart && revChart.data) {
      revChart.data.labels = data.chartLabels;
      if (revChart.data.datasets && revChart.data.datasets[0]) {
        revChart.data.datasets[0].data = data.chartData;
      }
      revChart.update();
    }

    if (statusChart && statusChart.data) {
      statusChart.data.labels = data.statusLabels;
      if (statusChart.data.datasets && statusChart.data.datasets[0]) {
        statusChart.data.datasets[0].data = data.statusData;
      }
      statusChart.update();
    }
  });
}

// Reset filters
function resetFilters() {
  document.getElementById('dateRange').value = 'month';
  document.getElementById('reportType').value = 'all';
  document.getElementById('vehicleFilter').value = 'all';
  document.getElementById('customerFilter').value = 'all';
  document.getElementById('driverFilter').value = 'all';
  document.getElementById('statusFilter').value = 'all';
  document.getElementById('fromDate').value = '';
  document.getElementById('toDate').value = '';
  toggleCustomDate();
  applyFilters();
}

// Update monthly summary
function updateMonthlySummary(summary) {
  // Update key metrics
  const metricsContainer = document.querySelector('.card-body .row.g-2');
  if (metricsContainer) {
    metricsContainer.innerHTML = `
      <div class="col-6">
        <div class="d-flex justify-content-between">
          <span>Total Records:</span>
          <strong>${summary.total_records}</strong>
        </div>
      </div>
      <div class="col-6">
        <div class="d-flex justify-content-between">
          <span>Total Bags:</span>
          <strong>${summary.total_bags}</strong>
        </div>
      </div>
      <div class="col-6">
        <div class="d-flex justify-content-between">
          <span>Total KM:</span>
          <strong>${Number(summary.total_km).toFixed(2)}</strong>
        </div>
      </div>
      <div class="col-6">
        <div class="d-flex justify-content-between">
          <span>Pending Dues:</span>
          <strong class="text-danger">${formatCurrency(summary.pending_dues || 0)}</strong>
        </div>
      </div>
    `;
  }

  // Update payment status
  const statusContainer = document.querySelector('.card-body .d-flex.justify-content-around');
  if (statusContainer) {
    statusContainer.innerHTML = `
      <div class="text-center">
        <div class="display-6 text-success">${summary.paid_count}</div>
        <div class="small text-muted">Paid</div>
      </div>
      <div class="text-center">
        <div class="display-6 text-danger">${summary.pending_count}</div>
        <div class="small text-muted">Pending</div>
      </div>
      <div class="text-center">
        <div class="display-6 text-warning">${summary.partial_count}</div>
        <div class="small text-muted">Partial</div>
      </div>
    `;
  }

  // Update progress bar
  const total = summary.paid_count + summary.pending_count + summary.partial_count;
  const paidPercent = total > 0 ? (summary.paid_count / total) * 100 : 0;
  const pendingPercent = total > 0 ? (summary.pending_count / total) * 100 : 0;
  const partialPercent = total > 0 ? (summary.partial_count / total) * 100 : 0;
  
  const progressBar = document.querySelector('.progress');
  if (progressBar) {
    progressBar.innerHTML = `
      <div class="progress-bar bg-success" style="width: ${paidPercent}%" title="Paid: ${paidPercent.toFixed(1)}%"></div>
      <div class="progress-bar bg-danger" style="width: ${pendingPercent}%" title="Pending: ${pendingPercent.toFixed(1)}%"></div>
      <div class="progress-bar bg-warning" style="width: ${partialPercent}%" title="Partial: ${partialPercent.toFixed(1)}%"></div>
    `;
  }
}

// Helper function to format currency
function formatCurrency(amount) {
  return new Intl.NumberFormat('en-PK', {
    style: 'currency',
    currency: 'PKR',
    minimumFractionDigits: 2
  }).format(amount);
}

// Export functions
function exportReport() {
  const filters = {
    dateRange: document.getElementById('dateRange').value,
    reportType: document.getElementById('reportType').value,
    format: 'excel'
  };
  
  fetch('/reports/export', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify(filters)
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Export failed');
    }
    return response.blob();
  })
  .then(blob => {
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'report_export.csv';
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
  })
  .catch(error => {
    console.error('Export error:', error);
    alert('Error exporting report: ' + error.message);
  });
}

function exportToCSV() {
  const table = document.getElementById('reportTable');
  let csv = [];
  const rows = table.querySelectorAll('tr');
  
  rows.forEach(row => {
    const cols = row.querySelectorAll('td, th');
    const rowData = [];
    cols.forEach(col => rowData.push(col.innerText));
    csv.push(rowData.join(','));
  });
  
  const csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
  const downloadLink = document.createElement('a');
  downloadLink.download = 'filtered_report.csv';
  downloadLink.href = window.URL.createObjectURL(csvFile);
  downloadLink.click();
}

function exportToPDF() {
  window.print();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  initCharts();
});
</script>
@endsection

