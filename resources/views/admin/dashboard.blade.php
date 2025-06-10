<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Fruits & Légumes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #2c3e50, #34495e);
        }
        .sidebar .nav-link {
            color: #bdc3c7;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 2px 10px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(52, 152, 219, 0.2);
            color: #3498db;
        }
        .stats-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        .bg-primary-gradient { background: linear-gradient(45deg, #3498db, #2980b9); }
        .bg-success-gradient { background: linear-gradient(45deg, #27ae60, #2ecc71); }
        .bg-warning-gradient { background: linear-gradient(45deg, #f39c12, #e67e22); }
        .bg-danger-gradient { background: linear-gradient(45deg, #e74c3c, #c0392b); }
        .bg-info-gradient { background: linear-gradient(45deg, #9b59b6, #8e44ad); }

        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }

        .navbar-admin {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-4 text-center">
                    <h4 class="text-light">
                        <i class="fas fa-leaf text-success"></i>
                        Admin Panel
                    </h4>
                </div>

                <nav class="nav flex-column">
                    <a class="nav-link active" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-chart-line me-2"></i> Dashboard
                    </a>
                    <a class="nav-link" href="#">
                        <i class="fas fa-apple-alt me-2"></i> Produits
                    </a>
                    <a class="nav-link" href="#">
                        <i class="fas fa-tags me-2"></i> Catégories
                    </a>
                    <a class="nav-link" href="#">
                        <i class="fas fa-shopping-cart me-2"></i> Commandes
                    </a>
                    <a class="nav-link" href="#">
                        <i class="fas fa-users me-2"></i> Clients
                    </a>
                    <a class="nav-link" href="#">
                        <i class="fas fa-star me-2"></i> Avis
                    </a>

                    <hr class="text-light mx-3">

                    <a class="nav-link" href="{{ route('home') }}" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i> Voir le site
                    </a>
                    <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                    </a>
                </nav>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content p-0">
                <!-- Top Navbar -->
                <nav class="navbar navbar-expand-lg navbar-admin">
                    <div class="container-fluid">
                        <h3 class="mb-0">Dashboard</h3>
                        <div class="navbar-nav ms-auto">
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle"></i> {{ auth()->user()->name }}
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Profil</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            Déconnexion
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Dashboard Content -->
                <div class="container-fluid p-4">
                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body d-flex align-items-center">
                                    <div class="stats-icon bg-primary-gradient me-3">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-1">{{ $stats['total_orders'] ?? 0 }}</h5>
                                        <p class="card-text text-muted">Commandes totales</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body d-flex align-items-center">
                                    <div class="stats-icon bg-success-gradient me-3">
                                        <i class="fas fa-euro-sign"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-1">{{ number_format($stats['total_revenue'] ?? 0, 2, ',', ' ') }}€</h5>
                                        <p class="card-text text-muted">Chiffre d'affaires</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body d-flex align-items-center">
                                    <div class="stats-icon bg-warning-gradient me-3">
                                        <i class="fas fa-apple-alt"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-1">{{ $stats['active_products'] ?? 0 }}</h5>
                                        <p class="card-text text-muted">Produits actifs</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body d-flex align-items-center">
                                    <div class="stats-icon bg-danger-gradient me-3">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-1">{{ $stats['low_stock_products'] ?? 0 }}</h5>
                                        <p class="card-text text-muted">Stock faible</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Commandes récentes -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        <i class="fas fa-shopping-bag me-2"></i>
                                        Commandes récentes
                                    </h5>
                                    <small class="text-muted">{{ $stats['pending_orders'] ?? 0 }} en attente</small>
                                </div>
                                <div class="card-body">
                                    @if(isset($recentOrders) && $recentOrders->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>N° Commande</th>
                                                        <th>Client</th>
                                                        <th>Montant</th>
                                                        <th>Statut</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($recentOrders as $order)
                                                        <tr>
                                                            <td>
                                                                <strong>{{ $order->order_number }}</strong>
                                                            </td>
                                                            <td>{{ $order->user->name }}</td>
                                                            <td>{{ number_format($order->total_amount, 2, ',', ' ') }}€</td>
                                                            <td>
                                                                @php
                                                                    $statusClasses = [
                                                                        'pending' => 'bg-warning',
                                                                        'confirmed' => 'bg-info',
                                                                        'preparing' => 'bg-primary',
                                                                        'shipped' => 'bg-success',
                                                                        'delivered' => 'bg-success',
                                                                        'cancelled' => 'bg-danger'
                                                                    ];
                                                                    $statusLabels = [
                                                                        'pending' => 'En attente',
                                                                        'confirmed' => 'Confirmée',
                                                                        'preparing' => 'Préparation',
                                                                        'shipped' => 'Expédiée',
                                                                        'delivered' => 'Livrée',
                                                                        'cancelled' => 'Annulée'
                                                                    ];
                                                                @endphp
                                                                <span class="badge {{ $statusClasses[$order->status] ?? 'bg-secondary' }}">
                                                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Aucune commande récente</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Produits en rupture -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                        Stock faible
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if(isset($lowStockProducts) && $lowStockProducts->count() > 0)
                                        @foreach($lowStockProducts as $product)
                                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                                <div>
                                                    <strong>{{ $product->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        Stock: {{ $product->stock_quantity }} / Min: {{ $product->min_stock }}
                                                    </small>
                                                </div>
                                                <span class="badge bg-{{ $product->stock_quantity == 0 ? 'danger' : 'warning' }}">
                                                    {{ $product->stock_quantity == 0 ? 'Rupture' : 'Faible' }}
                                                </span>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-3">
                                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                            <p class="text-muted mb-0">Tous les stocks sont OK</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
