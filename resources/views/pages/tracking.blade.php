@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">Operations</div>
        <h1>Live Tracking</h1>
        <div class="sub">Real-time GPS tracking, routes & geofence alerts</div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-navy"><i class="bi bi-download me-1"></i> Export</button>
        <button class="btn btn-navy"><i class="bi bi-plus-lg me-1"></i> Refresh</button>
      </div>
    </div>
    <div class="row g-3 mb-3">
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#EAF0FB;color:var(--navy-800);"><i class="bi bi-truck-front"></i></div>
          <div class="label">Vehicles Tracked</div>
          <div class="value">{{ $totalVehicles ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> All fleet online</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#EAF7EF;color:var(--success);"><i class="bi bi-broadcast"></i></div>
          <div class="label">Moving</div>
          <div class="value">{{ $movingVehicles ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> Currently en route</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FFF3E0;color:var(--warn);"><i class="bi bi-pause-circle"></i></div>
          <div class="label">Idle</div>
          <div class="value">{{ $idleVehicles ?? 0 }}</div>
          <div class="delta down"><i class="bi bi-arrow-down-short"></i> At depot / loading</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FBE9E7;color:var(--danger);"><i class="bi bi-wifi-off"></i></div>
          <div class="label">Signal Lost</div>
          <div class="value">{{ $offlineVehicles ?? 0 }}</div>
          <div class="delta down"><i class="bi bi-arrow-down-short"></i> Check device connection</div>
        </div>
      </div>
    </div>
    <div class="row g-3 mb-3">
      <div class="col-lg-8">
        <div class="panel" style="padding:16px;">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
              <div class="panel-title mb-0">Live Fleet Map</div>
              <div class="panel-sub mb-0">Real-time vehicle positions &middot; updates every 30s</div>
            </div>
            <div class="d-flex gap-3">
              <span class="legend-chip"><span class="sw" style="background:#2E7D5B;"></span> Moving</span>
              <span class="legend-chip"><span class="sw" style="background:#C9822A;"></span> Delayed</span>
              <span class="legend-chip"><span class="sw" style="background:#6B7688;"></span> Idle</span>
              <span class="legend-chip"><span class="sw" style="background:#C0392B;"></span> Signal Lost</span>
            </div>
          </div>
          <div id="map"></div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="panel h-100" style="padding:16px;">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <div class="panel-title mb-0">Vehicles</div>
            <span class="badge-status badge-delivered" id="liveCount">22 online</span>
          </div>
          <div class="panel-sub mb-1">Click a vehicle to focus it on the map</div>
          <div class="mb-2">
            <input type="text" id="vehicleSearch" class="form-control form-control-sm" placeholder="Search vehicles..." />
          </div>
          <div id="vehList" style="max-height:400px;overflow-y:auto;"></div>
        </div>
      </div>
    </div>
<div class="row g-3">
      <div class="col-lg-6">
        <div class="panel">
          <div class="panel-title">Active Route &middot; JOB-1042</div>
          <div class="panel-sub">LP19 XKT &middot; Ahsan Khan &middot; Manchester &rarr; Leeds</div>
          <div class="route-strip" style="margin-top:14px;">
            <div class="route-node done"><div class="line"></div><div class="dot"></div><div class="r-label" style="color:var(--navy-900);">Manchester</div><div class="r-count" style="font-size:12px;color:var(--muted);">09:10 AM</div></div>
            <div class="route-node active"><div class="line"></div><div class="dot"></div><div class="r-label" style="color:var(--navy-900);">A1(M) J34</div><div class="r-count" style="font-size:12px;color:var(--muted);">Now &middot; 58 mph</div></div>
            <div class="route-node"><div class="line"></div><div class="dot"></div><div class="r-label" style="color:var(--navy-900);">Leeds</div><div class="r-count" style="font-size:12px;color:var(--muted);">ETA 2:40 PM</div></div>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="panel">
          <div class="panel-title">Geofence &amp; Alerts</div>
          <div class="panel-sub">Automatic events from vehicle telematics</div>
          <div class="expiry-item"><div class="ico" style="background:#EAF7EF;color:var(--success);"><i class="bi bi-geo"></i></div><div class="txt"><div class="t1">LR68 DJP entered Bristol Depot</div><div class="t2">Geofence arrival</div></div><span class="when" style="background:#EDEFF5;color:var(--muted);">10 min ago</span></div>
          <div class="expiry-item"><div class="ico" style="background:#FFF3E0;color:var(--warn);"><i class="bi bi-speedometer2"></i></div><div class="txt"><div class="t1">LX21 GHT speeding on M3</div><div class="t2">72 mph in 60 mph zone</div></div><span class="when" style="background:#FFF3E0;color:var(--warn);">24 min ago</span></div>
          <div class="expiry-item"><div class="ico" style="background:#FBE9E7;color:var(--danger);"><i class="bi bi-wifi-off"></i></div><div class="txt"><div class="t1">LT70 BWS lost GPS signal</div><div class="t2">In workshop, device offline</div></div><span class="when" style="background:#FBE9E7;color:var(--danger);">1 hr ago</span></div>
        </div>
      </div>
    </div>
    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
      &copy; 2026 Super Ittefaq Logistics &middot; Transport Management System
    </div>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script>
const vehicles = @json($vehicles ?? []);

// Set default map center, will adjust if vehicles exist
let defaultCenter = [53.6, -1.8];
let defaultZoom = 6;

// If we have vehicles with valid coordinates, center the map on them
if (vehicles.length > 0 && vehicles.some(v => v.lat && v.lng)) {
  const validVehicles = vehicles.filter(v => v.lat && v.lng);
  const avgLat = validVehicles.reduce((sum, v) => sum + v.lat, 0) / validVehicles.length;
  const avgLng = validVehicles.reduce((sum, v) => sum + v.lng, 0) / validVehicles.length;
  defaultCenter = [avgLat, avgLng];
  defaultZoom = 8;
}

const map = L.map('map', {scrollWheelZoom:false}).setView(defaultCenter, defaultZoom);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 18,
  attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

function truckIcon(color){
  return L.divIcon({
    className: '',
    html: `<div class="truck-pin" style="background:${color};"><i class="bi bi-truck-front"></i></div>`,
    iconSize: [30,30],
    iconAnchor: [15,28],
    popupAnchor: [0,-26]
  });
}

const markers = {};
vehicles.forEach(v => {
  if (v.lat && v.lng) {
    const m = L.marker([v.lat, v.lng], {icon: truckIcon(v.color)}).addTo(map);
    m.bindPopup(`<b>${v.reg}</b><br>${v.driver}<br>${v.loc}<br>Speed: ${v.speed}`);
    markers[v.reg] = m;
  }
});

// Route polyline for active job
const route = [[53.483,-2.244],[53.70,-1.90],[53.930,-1.381],[53.80,-1.549]];
L.polyline(route, {color:'#D4A537', weight:4, opacity:.85, dashArray:'1,8'}).addTo(map);

// Vehicle list panel
const statusLabel = {moving:'Moving', delayed:'Delayed', idle:'Idle', offline:'Signal Lost'};
const listEl = document.getElementById('vehList');

if (vehicles.length === 0) {
  listEl.innerHTML = '<div class="text-center text-muted py-3">No vehicles being tracked</div>';
} else {
  vehicles.forEach(v => {
    const row = document.createElement('div');
    row.className = 'veh-row';
    row.innerHTML = `
      <span class="dot-live" style="background:${v.color};"></span>
      <div>
        <div class="v1">${v.reg}</div>
        <div class="v2">${v.driver} &middot; ${v.loc}</div>
      </div>
      <div class="speed">${v.speed}<br><span style="font-weight:500;color:var(--muted);font-size:10.5px;">${statusLabel[v.status] || v.status}</span></div>`;
    row.onclick = () => {
      if (v.lat && v.lng) {
        map.flyTo([v.lat, v.lng], 12, {duration: .8});
        if (markers[v.reg]) {
          markers[v.reg].openPopup();
        }
      }
      document.querySelectorAll('.veh-row').forEach(r => r.classList.remove('active-row'));
      row.classList.add('active-row');
    };
    listEl.appendChild(row);
  });
}

document.getElementById('liveCount').textContent = vehicles.length + ' online';

// Live search functionality
const searchInput = document.getElementById('vehicleSearch');
const vehList = document.getElementById('vehList');

searchInput.addEventListener('input', function() {
    const query = this.value.trim();
    
    if (query.length > 0) {
        fetch(`/api/tracking/search?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                updateVehicleList(data);
            })
            .catch(error => console.error('Search error:', error));
    } else {
        // Reset to original list
        renderOriginalVehicleList();
    }
});

function updateVehicleList(trackingData) {
    vehList.innerHTML = '';
    
    if (trackingData.length === 0) {
        vehList.innerHTML = '<div class="text-center text-muted py-3">No vehicles found</div>';
        return;
    }
    
    trackingData.forEach(track => {
        const row = document.createElement('div');
        row.className = 'veh-row';
        
        const reg = track.vehicle?.reg_no || 'N/A';
        const driver = track.driver?.name || 'Unknown';
        const loc = track.location_description || 'Unknown location';
        const speed = track.speed ? track.speed + ' mph' : '0 mph';
        const status = track.status || 'unknown';
        const color = getStatusColor(status);
        
        row.innerHTML = `
            <span class="dot-live" style="background:${color};"></span>
            <div>
                <div class="v1">${reg}</div>
                <div class="v2">${driver} &middot; ${loc}</div>
            </div>
            <div class="speed">${speed}<br><span style="font-weight:500;color:var(--muted);font-size:10.5px;">${statusLabel[status] || status}</span></div>`;
        
        row.onclick = () => {
            if (track.latitude && track.longitude) {
                map.flyTo([track.latitude, track.longitude], 12, {duration: .8});
            }
            document.querySelectorAll('.veh-row').forEach(r => r.classList.remove('active-row'));
            row.classList.add('active-row');
        };
        
        vehList.appendChild(row);
    });
}

function getStatusColor(status) {
    const colors = {
        'moving': '#2E7D5B',
        'idle': '#6B7688',
        'delayed': '#C9822A',
        'offline': '#C0392B'
    };
    return colors[status] || '#6B7688';
}

function renderOriginalVehicleList() {
    vehList.innerHTML = '';
    vehicles.forEach(v => {
        const row = document.createElement('div');
        row.className = 'veh-row';
        row.innerHTML = `
            <span class="dot-live" style="background:${v.color};"></span>
            <div>
                <div class="v1">${v.reg}</div>
                <div class="v2">${v.driver} &middot; ${v.loc}</div>
            </div>
            <div class="speed">${v.speed}<br><span style="font-weight:500;color:var(--muted);font-size:10.5px;">${statusLabel[v.status]}</span></div>`;
        row.onclick = () => {
            map.flyTo([v.lat, v.lng], 12, {duration: .8});
            markers[v.reg].openPopup();
            document.querySelectorAll('.veh-row').forEach(r => r.classList.remove('active-row'));
            row.classList.add('active-row');
        };
        vehList.appendChild(row);
    });
}
</script>
@endsection

