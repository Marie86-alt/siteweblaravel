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
            text-decoration: none;
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
            cursor: pointer;
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

        .order-link {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s;
        }

        .order-link:hover {
            color: #3498db;
            text-decoration: none;
        }

        .clickable-row {
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .clickable-row:hover {
            background-color: rgba(52, 152, 219, 0.1);
        }

        .product-link {
            color: inherit;
            text-decoration: none;
        }

        .product-link:hover {
            color: #e67e22;
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
                    <a class="nav-link" href="{{ route('admin.products.index') }}">
                        <i class="fas fa-apple-alt me-2"></i> Produits
                    </a>
                    <a class="nav-link" href="{{ route('admin.categories.index') }}">
                        <i class="fas fa-tags me-2"></i> Catégories
                    </a>
                    <a class="nav-link" href="{{ route('admin.orders.index') }}">
                        <i class="fas fa-shopping-cart me-2"></i> Commandes
                    </a>
                     <a class="nav-link" href="{{ route('admin.image-generation.index') }}">
        <i class="fas fa-magic me-2"></i> Génération IA
    </a>
                    <a class="nav-link" href="{{ route('admin.customers.index') }}">
                        <i class="fas fa-users me-2"></i> Clients
                    </a>
                    <a class="nav-link" href="{{ route('admin.reviews.index') }}">
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
                            <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">
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
                            </a>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card stats-card" onclick="window.location.href='{{ route('admin.orders.index') }}'">
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
                            <a href="{{ route('admin.products.index') }}" class="text-decoration-none">
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
                            </a>
                        </div>

                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.products.index') }}?filter=low_stock" class="text-decoration-none">
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
                            </a>
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
                                    <div class="d-flex gap-2">
                                        <small class="text-muted">{{ $stats['pending_orders'] ?? 0 }} en attente</small>
                                        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">
                                            Voir toutes
                                        </a>
                                    </div>
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
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($recentOrders as $order)
                                                        <tr class="clickable-row" onclick="window.location.href='{{ route('admin.orders.show', $order) }}'">
                                                            <td>
                                                                <a href="{{ route('admin.orders.show', $order) }}" class="order-link">
                                                                    <strong>{{ $order->order_number }}</strong>
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('admin.customers.show', $order->user) }}" class="text-decoration-none">
                                                                    {{ $order->user->name }}
                                                                </a>
                                                            </td>
                                                            <td>{{ number_format($order->total_amount, 2, ',', ' ') }}€</td>
                                                            <td>
                                                                @php
                                                                    $statusClasses = [
                                                                        'pending' => 'bg-warning',
                                                                        'confirmed' => 'bg-info',
                                                                        'processing' => 'bg-primary',
                                                                        'shipped' => 'bg-success',
                                                                        'delivered' => 'bg-success',
                                                                        'cancelled' => 'bg-danger'
                                                                    ];
                                                                    $statusLabels = [
                                                                        'pending' => 'En attente',
                                                                        'confirmed' => 'Confirmée',
                                                                        'processing' => 'Préparation',
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
                                                            <td onclick="event.stopPropagation();">
                                                                <div class="btn-group btn-group-sm">
                                                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-primary btn-sm" title="Voir">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                    <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-outline-warning btn-sm" title="Modifier">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Aucune commande récente</p>
                                            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary">
                                                Voir toutes les commandes
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Produits en rupture -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                        Stock faible
                                    </h5>
                                    <a href="{{ route('admin.products.index') }}?filter=low_stock" class="btn btn-sm btn-outline-warning">
                                        Voir tous
                                    </a>
                                </div>
                                <div class="card-body">
                                    @if(isset($lowStockProducts) && $lowStockProducts->count() > 0)
                                        @foreach($lowStockProducts as $product)
                                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded position-relative">
                                                <a href="{{ route('admin.products.show', $product) }}" class="product-link stretched-link text-decoration-none">
                                                    <div>
                                                        <strong>{{ $product->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            Stock: {{ $product->stock_quantity }} / Min: {{ $product->min_stock ?? 5 }}
                                                        </small>
                                                    </div>
                                                </a>
                                                <span class="badge bg-{{ $product->stock_quantity == 0 ? 'danger' : 'warning' }} position-relative" style="z-index: 2;">
                                                    {{ $product->stock_quantity == 0 ? 'Rupture' : 'Faible' }}
                                                </span>
                                            </div>
                                        @endforeach
                                        <div class="text-center mt-3">
                                            <a href="{{ route('admin.products.index') }}?filter=low_stock" class="btn btn-sm btn-warning">
                                                <i class="fas fa-boxes me-1"></i>
                                                Gérer les stocks
                                            </a>
                                        </div>
                                    @else
                                        <div class="text-center py-3">
                                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                            <p class="text-muted mb-0">Tous les stocks sont OK</p>
                                            <small class="text-muted">Aucun produit en rupture</small>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions rapides -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-bolt text-primary me-2"></i>
                                        Actions rapides
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.products.create') }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-plus me-1"></i>
                                            Ajouter un produit
                                        </a>
                                        <a href="{{ route('admin.categories.create') }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-tag me-1"></i>
                                            Nouvelle catégorie
                                        </a>
                                        <a href="{{ route('admin.orders.index') }}?status=pending" class="btn btn-warning btn-sm">
                                            <i class="fas fa-clock me-1"></i>
                                            Commandes en attente
                                        </a>
                                        <a href="{{ route('admin.reports.index') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-chart-bar me-1"></i>
                                            Rapports
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Empêcher la propagation du clic sur les boutons d'action
        document.querySelectorAll('.btn-group .btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });

        // Animation au survol des cartes statistiques
        document.querySelectorAll('.stats-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Notifications de succès (si vous avez des messages flash)
        @if(session('success'))
            // Vous pouvez ajouter ici une notification toast
            console.log('Succès: {{ session("success") }}');
        @endif

        @if(session('error'))
            console.log('Erreur: {{ session("error") }}');
        @endif
    </script>
</body>
</html>
