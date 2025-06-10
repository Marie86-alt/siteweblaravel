@extends('layouts.app')

@section('title', 'Gestion des Produits - Administration')

@push('styles')
<style>
    .admin-header {
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: white;
        padding: 30px 0;
        margin-bottom: 30px;
    }

    .admin-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .admin-table {
        margin: 0;
    }

    .admin-table th {
        background: #f8f9fa;
        border: none;
        padding: 15px;
        font-weight: 600;
        color: #333;
    }

    .admin-table td {
        padding: 15px;
        vertical-align: middle;
        border-top: 1px solid #eee;
    }

    .product-thumb {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: var(--light-green);
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .status-active {
        background: #d4edda;
        color: #155724;
    }

    .status-inactive {
        background: #f8d7da;
        color: #721c24;
    }

    .stock-indicator {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .stock-good {
        background: #d4edda;
        color: #155724;
    }

    .stock-low {
        background: #fff3cd;
        color: #856404;
    }

    .stock-out {
        background: #f8d7da;
        color: #721c24;
    }

    .action-btn {
        padding: 8px;
        margin: 0 2px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.9rem;
    }

    .btn-view {
        background: #e3f2fd;
        color: #1976d2;
    }

    .btn-view:hover {
        background: #1976d2;
        color: white;
    }

    .btn-edit {
        background: #fff3e0;
        color: #f57c00;
    }

    .btn-edit:hover {
        background: #f57c00;
        color: white;
    }

    .btn-delete {
        background: #ffebee;
        color: #d32f2f;
    }

    .btn-delete:hover {
        background: #d32f2f;
        color: white;
    }

    .btn-toggle {
        background: #f3e5f5;
        color: #7b1fa2;
    }

    .btn-toggle:hover {
        background: #7b1fa2;
        color: white;
    }

    .filters-section {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .stats-cards {
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        transition: all 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        color: var(--primary-green);
        margin-bottom: 5px;
    }

    .stat-label {
        color: #666;
        font-size: 0.9rem;
    }

    .bulk-actions {
        background: white;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid #eee;
        display: none;
    }

    .bulk-actions.show {
        display: block;
    }

    @media (max-width: 768px) {
        .admin-table {
            font-size: 0.8rem;
        }

        .action-btn {
            padding: 6px;
            font-size: 0.8rem;
        }

        .product-thumb {
            width: 40px;
            height: 40px;
        }
    }
</style>
@endpush

@section('content')
<!-- Header Admin -->
<div class="admin-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="mb-2">
                    <i class="fas fa-apple-alt me-3"></i>
                    Gestion des Produits
                </h1>
                <p class="mb-0 opacity-75">Gérez votre catalogue de fruits et légumes</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.products.create') }}" class="btn btn-success btn-lg">
                    <i class="fas fa-plus me-2"></i>
                    Nouveau Produit
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Statistiques -->
    <div class="stats-cards">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-number">{{ $stats['total'] ?? 0 }}</div>
                    <div class="stat-label">Total produits</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-number">{{ $stats['active'] ?? 0 }}</div>
                    <div class="stat-label">Produits actifs</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-number">{{ $stats['low_stock'] ?? 0 }}</div>
                    <div class="stat-label">Stock faible</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-number">{{ $stats['out_of_stock'] ?? 0 }}</div>
                    <div class="stat-label">Rupture de stock</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="filters-section">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Recherche</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                       placeholder="Nom du produit...">
            </div>
            <div class="col-md-2">
                <label class="form-label">Catégorie</label>
                <select class="form-select" name="category">
                    <option value="">Toutes</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Statut</label>
                <select class="form-select" name="status">
                    <option value="">Tous</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actifs</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactifs</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Stock</label>
                <select class="form-select" name="stock">
                    <option value="">Tous</option>
                    <option value="low" {{ request('stock') == 'low' ? 'selected' : '' }}>Stock faible</option>
                    <option value="out" {{ request('stock') == 'out' ? 'selected' : '' }}>Rupture</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Actions en lot -->
    <div class="bulk-actions" id="bulkActions">
        <div class="row align-items-center">
            <div class="col-md-6">
                <span id="selectedCount">0</span> produit(s) sélectionné(s)
            </div>
            <div class="col-md-6 text-end">
                <button type="button" class="btn btn-success btn-sm me-2" onclick="bulkAction('activate')">
                    <i class="fas fa-check me-1"></i> Activer
                </button>
                <button type="button" class="btn btn-warning btn-sm me-2" onclick="bulkAction('deactivate')">
                    <i class="fas fa-pause me-1"></i> Désactiver
                </button>
                <button type="button" class="btn btn-danger btn-sm" onclick="bulkAction('delete')">
                    <i class="fas fa-trash me-1"></i> Supprimer
                </button>
            </div>
        </div>
    </div>

    <!-- Liste des produits -->
    <div class="admin-card">
        <div class="table-responsive">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th width="50">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        </th>
                        <th width="80">Image</th>
                        <th>Produit</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Statut</th>
                        <th>Bio</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <input type="checkbox" class="product-checkbox" value="{{ $product->id }}" onchange="updateBulkActions()">
                            </td>
                            <td>
                                @if($product->images && count($product->images) > 0)
                                    <img src="{{ asset('storage/products/' . $product->images[0]) }}"
                                         alt="{{ $product->name }}" class="product-thumb">
                                @else
                                    <div class="product-thumb">
                                        @switch($product->category->name ?? 'default')
                                            @case('Fruits')
                                            @case('Fruits rouges')
                                            @case('Agrumes')
                                                🍎
                                                @break
                                            @case('Légumes')
                                            @case('Légumes verts')
                                            @case('Légumes racines')
                                                🥕
                                                @break
                                            @case('Herbes aromatiques')
                                                🌿
                                                @break
                                            @default
                                                🥗
                                        @endswitch
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $product->name }}</strong>
                                    @if($product->is_featured)
                                        <span class="badge bg-warning ms-1">Vedette</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ Str::limit($product->short_description ?? $product->description, 50) }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $product->category->name ?? 'Aucune' }}</span>
                            </td>
                            <td>
                                <strong>{{ number_format($product->price, 2, ',', ' ') }}€</strong>
                                <small class="text-muted d-block">/ {{ $product->unit }}</small>
                            </td>
                            <td>
                                @if($product->stock_quantity <= 0)
                                    <span class="stock-indicator stock-out">Rupture</span>
                                @elseif($product->stock_quantity <= $product->min_stock)
                                    <span class="stock-indicator stock-low">{{ $product->stock_quantity }}</span>
                                @else
                                    <span class="stock-indicator stock-good">{{ $product->stock_quantity }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge {{ $product->is_active ? 'status-active' : 'status-inactive' }}">
                                    {{ $product->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td>
                                @if($product->is_bio)
                                    <i class="fas fa-leaf text-success" title="Produit bio"></i>
                                @else
                                    <i class="fas fa-times text-muted" title="Non bio"></i>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex">
                                    <a href="{{ route('products.show', $product->slug) }}"
                                       class="action-btn btn-view" title="Voir" target="_blank">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.products.show', $product) }}"
                                       class="action-btn btn-view" title="Détails">
                                        <i class="fas fa-info"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                       class="action-btn btn-edit" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.products.toggleStatus', $product) }}"
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="action-btn btn-toggle"
                                                title="{{ $product->is_active ? 'Désactiver' : 'Activer' }}">
                                            <i class="fas fa-{{ $product->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.products.destroy', $product) }}"
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn btn-delete"
                                                title="Supprimer"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-apple-alt fa-3x text-muted mb-3"></i>
                                <h5>Aucun produit trouvé</h5>
                                <p class="text-muted">Commencez par ajouter votre premier produit.</p>
                                <a href="{{ route('admin.products.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus me-2"></i>
                                    Ajouter un produit
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="d-flex justify-content-center p-3">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Sélection multiple
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.product-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });

        updateBulkActions();
    }

    function updateBulkActions() {
        const checkboxes = document.querySelectorAll('.product-checkbox:checked');
        const bulkActions = document.getElementById('bulkActions');
        const selectedCount = document.getElementById('selectedCount');

        selectedCount.textContent = checkboxes.length;

        if (checkboxes.length > 0) {
            bulkActions.classList.add('show');
        } else {
            bulkActions.classList.remove('show');
        }
    }

    // Actions en lot
    function bulkAction(action) {
        const checkboxes = document.querySelectorAll('.product-checkbox:checked');
        const ids = Array.from(checkboxes).map(cb => cb.value);

        if (ids.length === 0) {
            alert('Veuillez sélectionner au moins un produit');
            return;
        }

        let message = '';
        switch(action) {
            case 'activate':
                message = `Activer ${ids.length} produit(s) ?`;
                break;
            case 'deactivate':
                message = `Désactiver ${ids.length} produit(s) ?`;
                break;
            case 'delete':
                message = `Supprimer définitivement ${ids.length} produit(s) ?`;
                break;
        }

        if (confirm(message)) {
            // Créer et soumettre le formulaire
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/products/bulk-${action}`;

            // Token CSRF
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(csrfInput);

            // IDs des produits
            const idsInput = document.createElement('input');
            idsInput.type = 'hidden';
            idsInput.name = 'ids';
            idsInput.value = JSON.stringify(ids);
            form.appendChild(idsInput);

            document.body.appendChild(form);
            form.submit();
        }
    }

    // Auto-submit sur changement de filtre
    document.querySelectorAll('select[name="category"], select[name="status"], select[name="stock"]').forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
</script>
@endpush
