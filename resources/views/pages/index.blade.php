@extends('layouts.app')

@section('content')
  <div class="page-wrap">


    <!-- Page head -->
    <div class="page-head">
      <div>
        <div class="eyebrow">Wednesday, 19 August 2026</div>
        <h1>Operations Dashboard</h1>
        <div class="sub">Fleet, drivers, jobs and revenue at a glance</div>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('jobs.index') }}" class="btn btn-navy"><i class="bi bi-plus-lg me-1"></i> New Job / Booking</a>
      </div>
    </div>

    <!-- Stat cards -->
    <div class="row g-3 mb-3">
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#EAF0FB;color:var(--navy-800);"><i class="bi bi-signpost-split"></i></div>
          <div class="label">Active Trips</div>
          <div class="value">{{ $activeTrips ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> {{ $totalTrips ?? 0 }} total trips</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#EAF7EF;color:var(--success);"><i class="bi bi-truck-front"></i></div>
          <div class="label">Vehicles Available</div>
          <div class="value">{{ $availableVehicles ?? 0 }} / {{ $totalVehicles ?? 0 }}</div>
          <div class="delta down"><i class="bi bi-arrow-down-short"></i> {{ $totalVehicles - $availableVehicles ?? 0 }} unavailable</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FFF3E0;color:var(--warn);"><i class="bi bi-person-badge"></i></div>
          <div class="label">Drivers On Duty</div>
          <div class="value">{{ $onDutyDrivers ?? 0 }} / {{ $totalDrivers ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> {{ $totalDrivers - $onDutyDrivers ?? 0 }} off duty</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#F3E3B8;color:#8A6512;"><i class="bi bi-cash-stack"></i></div>
          <div class="label">Jobs Status</div>
          <div class="value mono">{{ $deliveredJobs ?? 0 }} / {{ $totalJobs ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> {{ $pendingJobs ?? 0 }} pending</div>
        </div>
      </div>
    </div>

    <!-- Route strip (signature) + Revenue chart -->
    <div class="row g-3 mb-3">
      <div class="col-lg-7">
        <div class="route-card h-100">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="eyebrow" style="color:var(--gold);">Today's Pipeline</div>
              <h3 class="mb-0" style="font-size:16px;font-weight:700;">Job Flow &middot; Pickup to Delivery</h3>
            </div>
            <span class="badge-status badge-pending" style="background:rgba(255,255,255,.1);color:#fff;">{{ $totalJobs ?? 0 }} jobs today</span>
          </div>

          <div class="route-strip">
            <div class="route-node done">
              <div class="line"></div>
              <div class="dot"></div>
              <div class="r-count">{{ $totalJobs ?? 0 }}</div>
              <div class="r-label">Booked</div>
            </div>
            <div class="route-node done">
              <div class="line"></div>
              <div class="dot"></div>
              <div class="r-count">{{ $assignedJobs ?? 0 }}</div>
              <div class="r-label">Assigned</div>
            </div>
            <div class="route-node active">
              <div class="line"></div>
              <div class="dot"></div>
              <div class="r-count">{{ $inTransitJobs ?? 0 }}</div>
              <div class="r-label">In Transit</div>
            </div>
            <div class="route-node">
              <div class="line"></div>
              <div class="dot"></div>
              <div class="r-count">{{ $deliveredJobs ?? 0 }}</div>
              <div class="r-label">Delivered</div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="panel h-100">
          <div class="panel-title">Revenue vs Expenses</div>
          <div class="panel-sub">Last 6 months (PKR)</div>
          <canvas id="revenueChart" height="150"></canvas>
        </div>
      </div>
    </div>

    <!-- Recent trips + expiry alerts -->
    <div class="row g-3 mb-3">
      <div class="col-lg-8">
        <div class="panel">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <div>
              <div class="panel-title mb-0">Recent Trips</div>
              <div class="panel-sub mb-0">Latest pickup &amp; delivery activity</div>
            </div>
            <a href="{{ route('trips.index') }}" class="btn btn-outline-navy btn-sm">View all</a>
          </div>
          <div class="table-responsive">
            <table class="tbl">
              <thead>
                <tr>
                  <th>Job #</th>
                  <th>Customer</th>
                  <th>Route</th>
                  <th>Driver</th>
                  <th>Vehicle</th>
                  <th>Status</th>
                  <th class="text-end">ETA</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentTrips ?? [] as $trip)
                <tr>
                  <td class="mono fw-semibold">{{ $trip->job_number ?? 'N/A' }}</td>
                  <td>{{ $trip->job->customer->name ?? 'Unknown' }}</td>
                  <td>{{ $trip->pickup_location ?? 'N/A' }} &rarr; {{ $trip->delivery_location ?? 'N/A' }}</td>
                  <td><div class="driver-mini"><div class="av">{{ substr($trip->driver->name ?? 'NA', 0, 2) }}</div> {{ $trip->driver->name ?? 'Unknown' }}</div></td>
                  <td class="mono">{{ $trip->vehicle->reg_no ?? 'N/A' }}</td>
                  <td><span class="badge-status badge-{{ $trip->status }}">{{ ucfirst(str_replace('_', ' ', $trip->status)) }}</span></td>
                  <td class="text-end mono">{{ $trip->delivery_time ? $trip->delivery_time->format('h:i A') : 'TBD' }}</td>
                </tr>
                @empty
                <tr>
                  <td colspan="7" class="text-center text-muted">No recent trips found</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="panel h-100">
          <div class="panel-title">Expiring Soon</div>
          <div class="panel-sub">Documents, MOT &amp; insurance</div>

          @forelse($expiringDocuments ?? [] as $doc)
          <div class="expiry-item">
            <div class="ico" style="background:{{ $doc['color'] }};color:{{ $doc['textColor'] }};"><i class="bi {{ $doc['icon'] }}"></i></div>
            <div class="txt">
              <div class="t1">{{ $doc['title'] }}</div>
              <div class="t2">{{ $doc['type'] }}</div>
            </div>
            <span class="when" style="background:{{ $doc['color'] }};color:{{ $doc['textColor'] }};">{{ $doc['days'] }} days</span>
          </div>
          @empty
          <div class="text-center text-muted py-4">
            <i class="bi bi-check-circle fs-4"></i>
            <p class="mb-0 mt-2">No documents expiring soon</p>
          </div>
          @endforelse
        </div>
      </div>
    </div>

    <!-- Modules overview -->
    <div class="panel mb-3">
      <div class="panel-title">All Modules</div>
      <div class="panel-sub">Every part of the transport management system</div>
      <div class="row g-3">
        <div class="col-md-4 col-lg-3">
          <a href="{{ route('customers.index') }}" class="module-tile"><div class="ico"><i class="bi bi-people"></i></div><div><div class="t1">Customers</div><div class="t2">Profiles, contacts, addresses, credit &amp; payment history</div></div></a>
        </div>
        <div class="col-md-4 col-lg-3">
          <a href="{{ route('vehicles.index') }}" class="module-tile"><div class="ico"><i class="bi bi-truck-front"></i></div><div><div class="t1">Vehicles / Fleet</div><div class="t2">Registration, MOT, insurance &amp; service records</div></div></a>
        </div>
        <div class="col-md-4 col-lg-3">
          <a href="{{ route('drivers.index') }}" class="module-tile"><div class="ico"><i class="bi bi-person-badge"></i></div><div><div class="t1">Drivers</div><div class="t2">Licence, CPC, documents &amp; expiry dates</div></div></a>
        </div>
        <div class="col-md-4 col-lg-3">
          <a href="{{ route('jobs.index') }}" class="module-tile"><div class="ico"><i class="bi bi-clipboard2-check"></i></div><div><div class="t1">Jobs / Bookings</div><div class="t2">Create customer transport requests</div></div></a>
        </div>
        <div class="col-md-4 col-lg-3">
          <a href="{{ route('dispatch.index') }}" class="module-tile"><div class="ico"><i class="bi bi-diagram-3"></i></div><div><div class="t1">Planning / Dispatch</div><div class="t2">Assign jobs to vehicle &amp; driver</div></div></a>
        </div>
        <div class="col-md-4 col-lg-3">
          <a href="{{ route('trips.index') }}" class="module-tile"><div class="ico"><i class="bi bi-signpost-split"></i></div><div><div class="t1">Trips</div><div class="t2">Pickup &rarr; Transit &rarr; Delivery tracking</div></div></a>
        </div>
        <div class="col-md-4 col-lg-3">
          <a href="{{ route('pod.index') }}" class="module-tile"><div class="ico"><i class="bi bi-file-earmark-check"></i></div><div><div class="t1">Proof of Delivery</div><div class="t2">Signature, photo, delivery time</div></div></a>
        </div>
        <div class="col-md-4 col-lg-3">
          <a href="{{ route('tracking.index') }}" class="module-tile"><div class="ico"><i class="bi bi-geo-alt"></i></div><div><div class="t1">Tracking</div><div class="t2">Vehicle GPS, location &amp; status</div></div></a>
        </div>
        <div class="col-md-4 col-lg-3">
          <a href="{{ route('invoices.index') }}" class="module-tile"><div class="ico"><i class="bi bi-receipt"></i></div><div><div class="t1">Invoices</div><div class="t2">Customer invoices, VAT &amp; payment status</div></div></a>
        </div>
        <div class="col-md-4 col-lg-3">
          <a href="{{ route('expenses.index') }}" class="module-tile"><div class="ico"><i class="bi bi-cash-coin"></i></div><div><div class="t1">Expenses</div><div class="t2">Fuel, tolls, parking, repairs</div></div></a>
        </div>
        <div class="col-md-4 col-lg-3">
          <a href="{{ route('maintenance.index') }}" class="module-tile"><div class="ico"><i class="bi bi-tools"></i></div><div><div class="t1">Maintenance</div><div class="t2">Service, repairs, MOT, inspections</div></div></a>
        </div>
        <div class="col-md-4 col-lg-3">
          <a href="{{ route('reports.index') }}" class="module-tile"><div class="ico"><i class="bi bi-bar-chart-line"></i></div><div><div class="t1">Reports</div><div class="t2">Revenue, profit, mileage &amp; performance</div></div></a>
        </div>
      </div>
    </div>

    <div class="text-center text-muted" style="font-size:11.5px;">
      &copy; 2026 Super Ittefaq Logistics &middot; Transport Management System
    </div>

    </div>
</div>

<style>
.module-tile {
  text-decoration: none;
  color: inherit;
  transition: transform 0.2s, box-shadow 0.2s;
}

.module-tile:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueChart');
const chartData = {
  labels: @json($chartMonths ?? []),
  revenue: @json($revenueData ?? []),
  expenses: @json($expenseData ?? [])
};

new Chart(ctx, {
  type: 'bar',
  data: {
    labels: chartData.labels,
    datasets: [
      { label: 'Revenue (PKR)', data: chartData.revenue, backgroundColor: '#1B2A4A', borderRadius: 6, maxBarThickness: 22 },
      { label: 'Expenses (PKR)', data: chartData.expenses, backgroundColor: '#D4A537', borderRadius: 6, maxBarThickness: 22 }
    ]
  },
  options: {
    responsive: true,
    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } },
    scales: {
      y: { 
        grid: { color: '#EEF1F6' }, 
        ticks: { 
          font: { size: 10 },
          callback: function(value) {
            return 'PKR ' + (value / 1000000).toFixed(1) + 'M';
          }
        } 
      },
      x: { grid: { display: false }, ticks: { font: { size: 10 } } }
    }
  }
});
</script>
@endsection
