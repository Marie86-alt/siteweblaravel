@extends('customer.layout')

@section('customer-content')
<div class="customer-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="customer-title">
                <i class="fas fa-tachometer-alt me-3"></i>
                Tableau de bord
            </h1>
            <p class="customer-subtitle">
                Bienvenue {{ auth()->user()->first_name ?? auth()->user()->name }} ! Voici un aperçu de votre compte.
            </p>
        </div>

        <!-- Notifications badge -->
        @if($unreadNotifications > 0)
        <div class="notifications-badge">
            <i class="fas fa-bell"></i>
            <span class="badge bg-danger">{{ $unreadNotifications }}</span>
            <span class="ms-2">{{ $unreadNotifications }} nouvelle(s) notification(s)</span>
        </div>
        @endif
    </div>
</div>

<!-- Statistiques améliorées -->
<div class="stats-grid">
    <div class="stat-card orders-card">
        <div class="stat-icon">
            <i class="fas fa-shopping-bag"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $stats['total_orders'] }}</div>
            <div class="stat-label">Commandes totales</div>
            @if(isset($stats['orders_trend']))
            <div class="stat-trend {{ $stats['orders_trend'] >= 0 ? 'positive' : 'negative' }}">
                <i class="fas fa-arrow-{{ $stats['orders_trend'] >= 0 ? 'up' : 'down' }}"></i>
                {{ abs($stats['orders_trend']) }}% ce mois
            </div>
            @endif
        </div>
    </div>

    <div class="stat-card spending-card">
        <div class="stat-icon">
            <i class="fas fa-euro-sign"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ number_format($stats['total_spent'], 0) }}€</div>
            <div class="stat-label">Montant total</div>
            @if(isset($stats['spending_trend']))
            <div class="stat-trend {{ $stats['spending_trend'] >= 0 ? 'positive' : 'negative' }}">
                <i class="fas fa-arrow-{{ $stats['spending_trend'] >= 0 ? 'up' : 'down' }}"></i>
                {{ abs($stats['spending_trend']) }}% ce mois
            </div>
            @endif
        </div>
    </div>

    <div class="stat-card pending-card">
        <div class="stat-icon">
            <i class="fas fa-hourglass-half"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $stats['pending_orders'] }}</div>
            <div class="stat-label">Commandes en cours</div>
            @if($stats['pending_orders'] > 0)
            <div class="stat-action">
                <a href="{{ route('customer.orders') }}?status=pending">Voir détails</a>
            </div>
            @endif
        </div>
    </div>

    <div class="stat-card last-order-card">
        <div class="stat-icon">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">
                @if($stats['last_order'])
                    {{ $stats['last_order']->created_at->diffForHumans() }}
                @else
                    Jamais
                @endif
            </div>
            <div class="stat-label">Dernière commande</div>
            @if($stats['last_order'])
            <div class="stat-info">
                #{{ $stats['last_order']->id }} • {{ number_format($stats['last_order']->total_amount, 2) }}€
            </div>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <!-- Dernières commandes avec graphique -->
    <div class="col-lg-8">
        <!-- Graphique des commandes -->
        <div class="section chart-section">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="fas fa-chart-line me-2"></i>
                    Évolution de vos commandes
                </h3>
                <select id="chartPeriod" class="form-select form-select-sm" onchange="updateChart()">
                    <option value="6months">6 derniers mois</option>
                    <option value="year">12 derniers mois</option>
                    <option value="all">Toute la période</option>
                </select>
            </div>
            <div class="chart-container">
                <canvas id="ordersChart"></canvas>
            </div>
        </div>

        <!-- Dernières commandes -->
        <div class="section">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="fas fa-shopping-bag me-2"></i>
                    Dernières commandes
                </h3>
                <a href="{{ route('customer.orders') }}" class="btn btn-outline-primary btn-sm">
                    Voir toutes
                </a>
            </div>

            @if($recent_orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Commande</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_orders as $order)
                            <tr>
                                <td>
                                    <strong>#{{ $order->id }}</strong><br>
                                    <small class="text-muted">{{ $order->orderItems->count() }} article(s)</small>
                                </td>
                                <td>
                                    {{ $order->created_at->format('d/m/Y') }}<br>
                                    <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $order->status }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td><strong>{{ number_format($order->total_amount, 2) }}€</strong></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($order->status == 'delivered')
                                        <button class="btn btn-outline-success" title="Commander à nouveau">
                                            <i class="fas fa-redo"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                    <h5>Aucune commande</h5>
                    <p class="text-muted">Vous n'avez pas encore passé de commande.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i> Découvrir nos produits
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Sidebar droite améliorée -->
    <div class="col-lg-4">
        <!-- Notifications récentes -->
        @if($notifications->count() > 0)
        <div class="section notifications-section">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="fas fa-bell me-2"></i>
                    Notifications
                </h3>
                @if($unreadNotifications > 0)
                <button class="btn btn-link btn-sm text-primary" onclick="markAllAsRead()">
                    Tout marquer lu
                </button>
                @endif
            </div>

            <div class="notifications-list">
                @foreach($notifications as $notification)
                <div class="notification-item {{ $notification->read_at ? '' : 'unread' }}"
                     onclick="markAsRead('{{ $notification->id }}')">
                    <div class="notification-icon">
                        <i class="fas fa-{{ $notification->data['type'] == 'order' ? 'shopping-bag' : 'info-circle' }}"></i>
                    </div>
                    <div class="notification-content">
                        <p>{{ $notification->data['message'] }}</p>
                        <small>{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                    @if(!$notification->read_at)
                    <div class="notification-badge"></div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Actions rapides -->
        <div class="section">
            <h3 class="section-title">
                <i class="fas fa-bolt me-2"></i>
                Actions rapides
            </h3>

            <div class="quick-actions">
                <a href="{{ route('products.index') }}" class="quick-action">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Nouvelle commande</span>
                </a>
                <a href="{{ route('customer.profile') }}" class="quick-action">
                    <i class="fas fa-user"></i>
                    <span>Mon profil</span>
                </a>
                <a href="{{ route('customer.addresses') }}" class="quick-action">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Mes adresses</span>
                </a>
                <a href="{{ route('contact.index') }}" class="quick-action">
                    <i class="fas fa-headset"></i>
                    <span>Support</span>
                </a>
            </div>
        </div>

        <!-- Recommandations améliorées -->
        <div class="section">
            <h3 class="section-title">
                <i class="fas fa-star me-2"></i>
                Recommandations pour vous
            </h3>

            @if($recommended_products->count() > 0)
                <div class="recommendations-grid">
                    @foreach($recommended_products as $product)
                    <div class="recommendation-card">
                        <div class="recommendation-image">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                            @else
                                <div class="no-image">
                                    <i class="fas fa-apple-alt"></i>
                                </div>
                            @endif
                        </div>
                        <div class="recommendation-info">
                            <h6>{{ $product->name }}</h6>
                            <div class="price">{{ number_format($product->price, 2) }}€</div>
                            <div class="recommendation-actions">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary">
                                    Voir
                                </a>
                                <button class="btn btn-sm btn-primary" onclick="addToCart({{ $product->id }})">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="empty-recommendations">
                    <i class="fas fa-seedling fa-2x text-muted mb-2"></i>
                    <p class="text-muted">Découvrez nos produits frais !</p>
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-success">
                        Parcourir le catalogue
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Styles existants améliorés */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        gap: 1.5rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
    }

    .orders-card .stat-icon {
        background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .spending-card .stat-icon {
        background: linear-gradient(135deg, #f093fb, #f5576c);
    }

    .pending-card .stat-icon {
        background: linear-gradient(135deg, #ffecd2, #fcb69f);
        color: #d69e2e;
    }

    .last-order-card .stat-icon {
        background: linear-gradient(135deg, #a8edea, #fed6e3);
        color: #319795;
    }

    .stat-content {
        flex: 1;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        color: #666;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .stat-trend {
        font-size: 0.85rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .stat-trend.positive { color: #10b981; }
    .stat-trend.negative { color: #ef4444; }

    .stat-action a {
        color: var(--primary-green);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .stat-info {
        font-size: 0.8rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    .section {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .section-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #333;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .chart-section {
        margin-bottom: 2rem;
    }

    .chart-container {
        height: 300px;
        margin-top: 1rem;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-pending { background: #fef3c7; color: #92400e; }
    .status-confirmed { background: #dbeafe; color: #1e40af; }
    .status-preparing { background: #fce7f3; color: #be185d; }
    .status-shipped { background: #ecfdf5; color: #059669; }
    .status-delivered { background: #dcfce7; color: #16a34a; }
    .status-cancelled { background: #fee2e2; color: #dc2626; }

    .notifications-section {
        max-height: 400px;
    }

    .notifications-list {
        max-height: 300px;
        overflow-y: auto;
    }

    .notification-item {
        display: flex;
        gap: 1rem;
        padding: 1rem;
        margin: 0 -1rem 1rem -1rem;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        position: relative;
    }

    .notification-item:hover {
        background: #f8f9fa;
    }

    .notification-item.unread {
        background: #f0f9ff;
        border-left: 4px solid #3b82f6;
    }

    .notification-icon {
        width: 40px;
        height: 40px;
        background: var(--primary-green);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
    }

    .notification-content p {
        margin: 0 0 0.25rem 0;
        font-weight: 500;
        color: #374151;
        font-size: 0.9rem;
    }

    .notification-content small {
        color: #6b7280;
    }

    .notification-badge {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        width: 8px;
        height: 8px;
        background: #ef4444;
        border-radius: 50%;
    }

    .quick-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .quick-action {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 1.5rem 1rem;
        background: #f8f9fa;
        border-radius: 10px;
        text-decoration: none;
        color: #374151;
        transition: all 0.3s ease;
    }

    .quick-action:hover {
        background: var(--light-green);
        color: var(--dark-green);
        transform: translateY(-2px);
    }

    .quick-action i {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .quick-action span {
        font-weight: 500;
        font-size: 0.9rem;
    }

    .recommendation-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1rem;
        display: flex;
        gap: 1rem;
        align-items: center;
        margin-bottom: 1rem;
        transition: transform 0.3s ease;
    }

    .recommendation-card:hover {
        transform: translateY(-1px);
        background: white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .recommendation-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .empty-state, .empty-recommendations {
        text-align: center;
        padding: 2rem 1rem;
        color: #6b7280;
    }

    .notifications-badge {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #dc2626;
        font-weight: 500;
    }

    .table {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: none;
        border: none;
    }

    .table th {
        background: #f8f9fa;
        border: none;
        font-weight: 600;
        color: #374151;
    }

    .table td {
        border: none;
        border-bottom: 1px solid #f1f5f9;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .stat-card {
            flex-direction: column;
            text-align: center;
        }

        .quick-actions {
            grid-template-columns: 1fr;
        }

        .chart-container {
            height: 250px;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Données pour le graphique
const chartData = @json($chartData ?? ['labels' => [], 'orders' => [], 'amounts' => []]);

let ordersChart;

function initChart() {
    const ctx = document.getElementById('ordersChart').getContext('2d');

    ordersChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Commandes',
                data: chartData.orders,
                borderColor: '#4ade80',
                backgroundColor: 'rgba(74, 222, 128, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }, {
                label: 'Montant (€)',
                data: chartData.amounts,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    position: 'left',
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });
}

function updateChart() {
    const period = document.getElementById('chartPeriod').value;
    console.log('Période sélectionnée:', period);
    // Ici vous pouvez ajouter un appel AJAX pour charger de nouvelles données
}

function markAsRead(notificationId) {
    fetch(`/mon-compte/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    }).then(() => {
        location.reload();
    });
}

function markAllAsRead() {
    fetch('/mon-compte/notifications/read-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    }).then(() => {
        location.reload();
    });
}

function addToCart(productId) {
    fetch(`/panier/ajouter/${productId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ quantity: 1 })
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              // Mettre à jour le compteur du panier
              // updateCartCount();
              alert('Produit ajouté au panier !');
          }
      });
}

// Initialiser le graphique
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('ordersChart')) {
        initChart();
    }
});
</script>
@endpush
@endsection
