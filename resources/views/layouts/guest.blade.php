<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Super Ittefaq Logistics') }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&family=Roboto+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link href="{{ asset('css/guest.css') }}" rel="stylesheet">
</head>
<body class="guest-body">
    <div class="guest-container">
        <div class="guest-card">
            <div class="guest-header">
                <div class="logo-section">
                    <img src="/logo.png" alt="Super Ittefaq Logistics" class="company-logo">
                    <div class="brand-text">
                        <div class="brand-main">SUPER ITTEFAQ</div>
                        <div class="brand-sub">LOGISTICS & TRANSPORT</div>
                    </div>
                </div>
                <div class="page-title">
                    <h1>{{ $title ?? 'Welcome Back' }}</h1>
                    <p class="subtitle">{{ $subtitle ?? 'Sign in to access your transport management dashboard' }}</p>
                </div>
            </div>
            
            <div class="guest-content">
                {{ $slot }}
            </div>
            
            <div class="guest-footer">
                <div class="footer-text">
                    <i class="bi bi-shield-check"></i> Secure Login
                    <span class="separator">•</span>
                    Transport Management System v1.0
                </div>
            </div>
        </div>
        
        <div class="guest-sidebar">
            <div class="sidebar-content">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="bi bi-truck-front"></i>
                    </div>
                    <div class="feature-text">
                        <div class="feature-title">Fleet Management</div>
                        <div class="feature-desc">Track vehicles, maintenance, and availability</div>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <div class="feature-text">
                        <div class="feature-title">Live Tracking</div>
                        <div class="feature-desc">Real-time GPS tracking and route optimization</div>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="bi bi-clipboard2-check"></i>
                    </div>
                    <div class="feature-text">
                        <div class="feature-title">Job Management</div>
                        <div class="feature-desc">Streamlined booking and dispatch operations</div>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="bi bi-bar-chart-line"></i>
                    </div>
                    <div class="feature-text">
                        <div class="feature-title">Analytics & Reports</div>
                        <div class="feature-desc">Comprehensive business insights and performance metrics</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
