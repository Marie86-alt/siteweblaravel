<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration') - {{ config('app.name') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom Admin CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            background: linear-gradient(180deg, #198754 0%, #157347 100%);
            min-height: 100vh;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.9);
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: 0.375rem;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }

        .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.2);
            color: white;
        }

        .main-content {
            margin-left: 0;
            padding: 1.5rem;
        }

        .navbar-brand {
            font-weight: bold;
            color: #198754 !important;
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }

        .btn-primary {
            background-color: #198754;
            border-color: #198754;
        }

        .btn-primary:hover {
            background-color: #157347;
            border-color: #146c43;
        }

        .table th {
            background-color: #f8f9fa;
            border-top: none;
        }

        .badge {
            font-size: 0.75em;
        }

        @media (min-width: 992px) {
            .main-content {
                margin-left: 250px;
            }
        }

        .stats-card {
            border-left: 4px solid #198754;
        }

        .stats-card .card-body {
            padding: 1.5rem;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: #198754;
        }

        .alert {
            border: none;
            border-radius: 0.5rem;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navigation Top -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container-fluid">
            <!-- Toggle Sidebar Button (Mobile) -->
            <button class="btn btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
                <i class="fas fa-bars"></i>
            </button>

            <a class="navbar-brand ms-3" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-leaf me-2"></i>{{ config('app.name') }} Admin
            </a>

            <div class="navbar-nav ms-auto">
                <!-- Notifications -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <span class="badge bg-danger">3</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Notifications</h6></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-exclamation-triangle text-warning me-2"></i>Stock faible</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-shopping-cart text-info me-2"></i>Nouvelle commande</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Voir toutes</a></li>
                    </ul>
                </div>

                <!-- User Menu -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>{{ auth()->user()->name ?? 'Admin' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('home') }}" target="_blank">
                            <i class="fas fa-external-link-alt me-2"></i>Voir le site
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('customer.profile') }}">
                            <i class="fas fa-user me-2"></i>Mon profil
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="offcanvas-lg offcanvas-start sidebar" id="sidebar" style="top: 56px;">
        <div class="offcanvas-body p-0">
            <nav class="nav flex-column p-3">
                <!-- Dashboard -->
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>

                <!-- Produits -->
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#productsMenu" role="button">
                        <i class="fas fa-apple-alt me-2"></i>Produits
                        <i class="fas fa-chevron-down float-end mt-1"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.products.*') ? 'show' : '' }}" id="productsMenu">
                        <nav class="nav flex-column ms-3">
                            <a class="nav-link" href="{{ route('admin.products.index') }}">
                                <i class="fas fa-list me-2"></i>Tous les produits
                            </a>
                            <a class="nav-link" href="{{ route('admin.products.create') }}">
                                <i class="fas fa-plus me-2"></i>Ajouter un produit
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- Catégories -->
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#categoriesMenu" role="button">
                        <i class="fas fa-layer-group me-2"></i>Catégories
                        <i class="fas fa-chevron-down float-end mt-1"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.categories.*') ? 'show' : '' }}" id="categoriesMenu">
                        <nav class="nav flex-column ms-3">
                            <a class="nav-link" href="{{ route('admin.categories.index') }}">
                                <i class="fas fa-list me-2"></i>Toutes les catégories
                            </a>
                            <a class="nav-link" href="{{ route('admin.categories.create') }}">
                                <i class="fas fa-plus me-2"></i>Ajouter une catégorie
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- Commandes -->
                <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') ?? '#' }}">
                    <i class="fas fa-shopping-cart me-2"></i>Commandes
                </a>

                <!-- Clients -->
                <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
                    <i class="fas fa-users me-2"></i>Clients
                </a>

                <!-- Génération IA -->
                <a class="nav-link {{ request()->routeIs('admin.image-generation.*') ? 'active' : '' }}" href="{{ route('admin.image-generation.index') ?? '#' }}">
                    <i class="fas fa-wand-magic-sparkles me-2"></i>Images IA
                </a>

                <!-- Rapports -->
                <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                    <i class="fas fa-chart-bar me-2"></i>Rapports
                </a>

                <hr class="my-3">

                <!-- Paramètres -->
                <a class="nav-link" href="#" onclick="alert('Paramètres à venir')">
                    <i class="fas fa-cog me-2"></i>Paramètres
                </a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" style="margin-top: 56px;">
        <!-- Messages Flash -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Page Content -->
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Admin JS -->
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // CSRF Token for AJAX requests
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };

        // Setup AJAX CSRF (seulement si axios est disponible)
        document.addEventListener('DOMContentLoaded', function() {
            const token = document.querySelector('meta[name="csrf-token"]');
            if (token && typeof window.axios !== 'undefined') {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
