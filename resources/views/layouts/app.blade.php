<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Super Ittefaq Logistics') }}</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&family=Roboto+Mono:wght@500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <link href="{{ asset('assets/style.css') }}" rel="stylesheet">
</head>
<body>
  @include('partials.sidebar')
  <div id="main">
    @include('partials.topbar')

    <div class="page-wrap">
      @yield('content')
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
  
  <script>
  // Global Search Functionality - Simplified
  document.addEventListener('DOMContentLoaded', function() {
    const globalSearchInput = document.getElementById('globalSearch');
    const searchResults = document.getElementById('searchResults');
    
    if (!globalSearchInput) {
      console.log('Global search input not found');
      return;
    }
    
    console.log('Global search initialized');
    
    let searchTimeout;
    
    globalSearchInput.addEventListener('input', function() {
      const query = this.value.trim();
      
      clearTimeout(searchTimeout);
      
      if (query.length < 2) {
        searchResults.innerHTML = '';
        searchResults.style.display = 'none';
        return;
      }
      
      searchTimeout = setTimeout(() => {
        performSearch(query);
      }, 500);
    });

    // Hide search results when clicking outside
    document.addEventListener('click', function(e) {
      if (globalSearchInput && searchResults && 
          !globalSearchInput.contains(e.target) && 
          !searchResults.contains(e.target)) {
        searchResults.style.display = 'none';
      }
    });

    function performSearch(query) {
      console.log('Searching for:', query);
      
      fetch(`/api/global-search?q=${encodeURIComponent(query)}`)
        .then(response => {
          console.log('Response status:', response.status);
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          console.log('Search results:', data);
          displayResults(data);
        })
        .catch(error => {
          console.error('Search error:', error);
          searchResults.innerHTML = '<div class="search-result-item">Search failed. Please try again.</div>';
          searchResults.style.display = 'block';
        });
    }

    function displayResults(results) {
      if (!results || results.length === 0) {
        searchResults.innerHTML = '<div class="search-result-item">No results found</div>';
        searchResults.style.display = 'block';
        return;
      }

      const typeIcons = {
        'job': 'bi-clipboard2-check',
        'vehicle': 'bi-truck-front', 
        'driver': 'bi-person-badge',
        'customer': 'bi-people'
      };

      const typeLabels = {
        'job': 'Job',
        'vehicle': 'Vehicle',
        'driver': 'Driver', 
        'customer': 'Customer'
      };

      let html = '';
      results.forEach(result => {
        const icon = result.icon || typeIcons[result.type] || 'bi-search';
        const typeLabel = typeLabels[result.type] || result.type;
        
        html += `
          <a href="${result.url}" class="search-result-item">
            <div class="search-result-icon">
              <i class="bi ${icon}"></i>
            </div>
            <div class="search-result-content">
              <div class="search-result-title">${result.title}</div>
              <div class="search-result-subtitle">${result.subtitle}</div>
              <div class="search-result-extra">${result.extra}</div>
            </div>
            <div class="search-result-type">${typeLabel}</div>
          </a>
        `;
      });

      searchResults.innerHTML = html;
      searchResults.style.display = 'block';
    }
  });
  </script>
  
  @stack('scripts')
</body>
</html>
