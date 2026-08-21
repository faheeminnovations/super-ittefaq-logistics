<!-- Sidebar extracted from original HTML -->
<aside id="sidebar">
  <div class="sidebar-brand">
    <img src="{{ asset('logo.png') }}" alt="Super Ittefaq Logistics">
    <div class="brand">SUPER ITTEFAQ<span>LOGISTICS &amp; TRANSPORT</span></div>
  </div>
  
  <!-- Module Search -->
  <div class="sidebar-search">
    <div class="search-input-wrapper">
      <i class="bi bi-search search-icon"></i>
      <input type="text" id="moduleSearch" class="search-input" placeholder="Search modules..." onkeyup="filterModules()">
    </div>
  </div>
  
  <nav class="sidebar-nav" id="sidebarNav">
    @if(auth()->check())
    <div class="nav-section-label">Overview</div>
    <a href="{{ url('/dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" data-module="dashboard"><i class="bi bi-speedometer2"></i> Dashboard </a>
    
      {{-- Operations Access (Admin, Dispatcher, Manager) --}}
      @if(auth()->user()->hasAnyRole(['admin', 'dispatcher', 'manager']))
      <div class="nav-section-label">Operations</div>
      <a href="{{ url('/jobs') }}" class="nav-link {{ request()->is('jobs') ? 'active' : '' }}" data-module="jobs bookings"><i class="bi bi-clipboard2-check"></i> Jobs / Bookings </a>
      <a href="{{ url('/dispatch') }}" class="nav-link {{ request()->is('dispatch') ? 'active' : '' }}" data-module="dispatch planning"><i class="bi bi-diagram-3"></i> Planning / Dispatch </a>
      <a href="{{ url('/trips') }}" class="nav-link {{ request()->is('trips') ? 'active' : '' }}" data-module="trips"><i class="bi bi-signpost-split"></i> Trips </a>
      <a href="{{ url('/tracking') }}" class="nav-link {{ request()->is('tracking') ? 'active' : '' }}" data-module="tracking live tracking"><i class="bi bi-geo-alt"></i> Live Tracking </a>
      <a href="{{ url('/pod') }}" class="nav-link {{ request()->is('pod') ? 'active' : '' }}" data-module="pod proof of delivery"><i class="bi bi-file-earmark-check"></i> Proof of Delivery </a>
      
      <div class="nav-section-label">Fleet & People</div>
      <a href="{{ url('/vehicles') }}" class="nav-link {{ request()->is('vehicles') ? 'active' : '' }}" data-module="vehicles fleet"><i class="bi bi-truck-front"></i> Vehicles / Fleet </a>
      <a href="{{ url('/drivers') }}" class="nav-link {{ request()->is('drivers') ? 'active' : '' }}" data-module="drivers"><i class="bi bi-person-badge"></i> Drivers </a>
      <a href="{{ url('/maintenance') }}" class="nav-link {{ request()->is('maintenance') ? 'active' : '' }}" data-module="maintenance"><i class="bi bi-tools"></i> Maintenance </a>
      
      <div class="nav-section-label">Accounts</div>
      <a href="{{ url('/customers') }}" class="nav-link {{ request()->is('customers') ? 'active' : '' }}" data-module="customers"><i class="bi bi-people"></i> Customers </a>
      @endif
      
      {{-- Financial Access (Admin, Accounts, Manager) --}}
      @if(auth()->user()->hasAnyRole(['admin', 'accounts', 'manager']))
      @if(!auth()->user()->hasAnyRole(['admin', 'dispatcher', 'manager']))
      <div class="nav-section-label">Accounts</div>
      @endif
      <a href="{{ url('/billing') }}" class="nav-link {{ request()->is('billing') ? 'active' : '' }}" data-module="billing management"><i class="bi bi-table"></i> Billing Management </a>
      <a href="{{ url('/invoices') }}" class="nav-link {{ request()->is('invoices') ? 'active' : '' }}" data-module="invoices"><i class="bi bi-receipt"></i> Invoices </a>
      <a href="{{ url('/expenses') }}" class="nav-link {{ request()->is('expenses') ? 'active' : '' }}" data-module="expenses"><i class="bi bi-cash-coin"></i> Expenses </a>
      @endif
      
      <div class="nav-section-label">System</div>
      
      {{-- Notifications for all authenticated users --}}
      <a href="{{ url('/notifications') }}" class="nav-link {{ request()->is('notifications') ? 'active' : '' }}" data-module="notifications"><i class="bi bi-bell"></i> Notifications </a>
      
      {{-- Operations Access for Documents --}}
      @if(auth()->user()->hasAnyRole(['admin', 'dispatcher', 'manager']))
      <a href="{{ url('/documents') }}" class="nav-link {{ request()->is('documents') ? 'active' : '' }}" data-module="documents"><i class="bi bi-folder2-open"></i> Documents </a>
      @endif
      
      {{-- Financial Access for Reports --}}
      @if(auth()->user()->hasAnyRole(['admin', 'accounts', 'manager']))
      <a href="{{ url('/reports') }}" class="nav-link {{ request()->is('reports') ? 'active' : '' }}" data-module="reports"><i class="bi bi-bar-chart-line"></i> Reports </a>
      @endif
      
      {{-- Notification Settings for all users --}}
      <a href="{{ url('/notifications/settings') }}" class="nav-link {{ request()->is('notifications/settings') ? 'active' : '' }}" data-module="notification settings"><i class="bi bi-bell-slash"></i> Notification Settings </a>
      
      {{-- Admin Only --}}
      @if(auth()->user()->isAdmin())
      <a href="{{ url('/users') }}" class="nav-link {{ request()->is('users') ? 'active' : '' }}" data-module="users permissions"><i class="bi bi-shield-lock"></i> Users & Permissions </a>
      <a href="{{ url('/settings') }}" class="nav-link {{ request()->is('settings') ? 'active' : '' }}" data-module="settings"><i class="bi bi-sliders"></i> System Settings </a>
      @endif
    @endif
  </nav>
  <div class="sidebar-foot">
    Transport Management System<br>v1.0 &middot; Laravel 12
  </div>
</aside>

<style>
.sidebar-search {
  padding: 15px;
  border-bottom: 1px solid rgba(255,255,255,0.1);
}

.search-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 10px;
  color: rgba(255,255,255,0.5);
  font-size: 14px;
}

.search-input {
  width: 100%;
  padding: 8px 10px 8px 35px;
  background: rgba(255,255,255,0.1);
  border: 1px solid rgba(255,255,255,0.2);
  border-radius: 6px;
  color: white;
  font-size: 13px;
  outline: none;
  transition: all 0.3s ease;
}

.search-input::placeholder {
  color: rgba(255,255,255,0.5);
}

.search-input:focus {
  background: rgba(255,255,255,0.15);
  border-color: rgba(255,255,255,0.4);
}

.nav-link.hidden {
  display: none !important;
}

.nav-section-label.hidden {
  display: none !important;
}

.no-results {
  padding: 15px;
  text-align: center;
  color: rgba(255,255,255,0.5);
  font-size: 13px;
  display: none;
}
</style>

<script>
function filterModules() {
  const searchInput = document.getElementById('moduleSearch');
  const filter = searchInput.value.toLowerCase();
  const navLinks = document.querySelectorAll('#sidebarNav .nav-link');
  const sectionLabels = document.querySelectorAll('#sidebarNav .nav-section-label');
  
  // Show/hide navigation links based on search
  navLinks.forEach(link => {
    const moduleNames = link.getAttribute('data-module') || link.textContent.toLowerCase();
    if (moduleNames.includes(filter)) {
      link.classList.remove('hidden');
    } else {
      link.classList.add('hidden');
    }
  });
  
  // Show/hide section labels based on visible links
  sectionLabels.forEach(label => {
    const nextElement = label.nextElementSibling;
    let hasVisibleLinks = false;
    
    // Check if any links after this label are visible
    let currentElement = nextElement;
    while (currentElement && !currentElement.classList.contains('nav-section-label')) {
      if (currentElement.classList.contains('nav-link') && !currentElement.classList.contains('hidden')) {
        hasVisibleLinks = true;
        break;
      }
      currentElement = currentElement.nextElementSibling;
    }
    
    if (hasVisibleLinks || filter === '') {
      label.classList.remove('hidden');
    } else {
      label.classList.add('hidden');
    }
  });
  
  // Show "no results" message if no links are visible
  const visibleLinks = document.querySelectorAll('#sidebarNav .nav-link:not(.hidden)');
  let noResultsMsg = document.querySelector('.no-results');
  
  if (visibleLinks.length === 0 && filter !== '') {
    if (!noResultsMsg) {
      noResultsMsg = document.createElement('div');
      noResultsMsg.className = 'no-results';
      noResultsMsg.textContent = 'No modules found';
      document.getElementById('sidebarNav').appendChild(noResultsMsg);
    }
    noResultsMsg.style.display = 'block';
  } else {
    if (noResultsMsg) {
      noResultsMsg.style.display = 'none';
    }
  }
}

// Add keyboard shortcut for search (Ctrl/Cmd + K)
document.addEventListener('keydown', function(e) {
  if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
    e.preventDefault();
    document.getElementById('moduleSearch').focus();
  }
});
</script>
