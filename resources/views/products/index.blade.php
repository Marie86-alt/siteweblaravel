@extends('layouts.app')

@section('title', 'Nos Produits - Fruits & Légumes Bio')
@section('description', 'Découvrez notre large sélection de fruits et légumes frais, bio et locaux. Qualité premium garantie.')

@push('styles')
<style>
    .product-filters {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
    }

    .filter-section {
        margin-bottom: 20px;
    }

    .filter-section:last-child {
        margin-bottom: 0;
    }

    .filter-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .product-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        overflow: hidden;
        height: 100%;
        position: relative;
    }

    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .product-image-container {
        position: relative;
        height: 220px;
        overflow: hidden;
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .product-card:hover .product-image {
        transform: scale(1.05);
    }

    .product-emoji {
        font-size: 4rem;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: var(--light-green);
    }

    .product-badges {
        position: absolute;
        top: 15px;
        left: 15px;
        right: 15px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .badge-bio {
        background: var(--primary-green);
        color: white;
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-promo {
        background: var(--red);
        color: white;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .stock-indicator {
        position: absolute;
        bottom: 15px;
        right: 15px;
        padding: 5px 10px;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .stock-low {
        background: rgba(255, 193, 7, 0.9);
        color: #333;
    }

    .stock-out {
        background: rgba(220, 53, 69, 0.9);
        color: white;
    }

    .product-info {
        padding: 20px;
    }

    .product-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        line-height: 1.2;
    }

    .product-description {
        color: #666;
        font-size: 0.9rem;
        line-height: 1.4;
        margin-bottom: 15px;
        height: 2.8rem;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .product-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .product-price {
        font-size: 1.1rem;
        font-weight: bold;
        color: var(--primary-green);
    }

    .product-price-old {
        font-size: 0.9rem;
        color: #999;
        text-decoration: line-through;
        margin-right: 5px;
    }

    .product-origin {
        font-size: 0.8rem;
        color: #666;
        font-style: italic;
    }

    .product-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .btn-add-cart {
        flex: 1;
        background: var(--primary-green);
        border: none;
        color: white;
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-add-cart:hover {
        background: var(--dark-green);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(39, 174, 96, 0.3);
        color: white;
    }

    .btn-add-cart:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .quantity-selector {
        display: flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 20px;
        overflow: hidden;
        width: 100px;
    }

    .quantity-btn {
        background: white;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
        font-weight: bold;
        transition: background 0.2s;
    }

    .quantity-btn:hover {
        background: #f8f9fa;
    }

    .quantity-input {
        border: none;
        text-align: center;
        width: 40px;
        padding: 5px;
        font-size: 0.9rem;
    }

    .sort-controls {
        display: flex;
        gap: 15px;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .results-info {
        color: #666;
        font-size: 0.9rem;
    }

    .filter-tags {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .filter-tag {
        background: var(--primary-green);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-tag .remove {
        cursor: pointer;
        font-weight: bold;
    }

    @media (max-width: 768px) {
        .product-filters {
            padding: 20px;
        }

        .sort-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .product-actions {
            flex-direction: column;
        }

        .quantity-selector {
            width: 100%;
            justify-content: center;
        }
    }
    .pagination .page-link {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    margin: 0 2px;
    border: 1px solid #dee2e6;
    color: var(--primary-green);
}

.pagination .page-link:hover {
    background-color: var(--primary-green);
    border-color: var(--primary-green);
    color: white;
}

.pagination .page-item.active .page-link {
    background-color: var(--primary-green);
    border-color: var(--primary-green);
    color: white;
}

.pagination .page-link i {
    font-size: 12px;
}
</style>
@endpush

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-2">Nos Produits</h1>
            <p class="text-muted">Découvrez notre sélection de fruits et légumes frais, bio et locaux</p>
        </div>
        <div class="col-md-4 text-md-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                    <li class="breadcrumb-item active">Produits</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Filtres -->
        <div class="col-lg-3 mb-4">
            <div class="product-filters">
                <h5 class="mb-3">
                    <i class="fas fa-filter me-2"></i>
                    Filtres
                </h5>

                <form method="GET" id="filter-form">
                    <!-- Recherche -->
                    <div class="filter-section">
                        <div class="filter-title">Recherche</div>
                        <div class="input-group">
                            <input type="text" class="form-control" name="search"
                                   value="{{ request('search') }}" placeholder="Rechercher un produit...">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Catégories -->
                    <div class="filter-section">
                        <div class="filter-title">Catégories</div>
                        <select class="form-select" name="category" onchange="this.form.submit()">
                            <option value="">Toutes les catégories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                        {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Prix -->
                    <div class="filter-section">
                        <div class="filter-title">Prix (€)</div>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" class="form-control form-control-sm"
                                       name="min_price" value="{{ request('min_price') }}"
                                       placeholder="Min" step="0.1">
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control form-control-sm"
                                       name="max_price" value="{{ request('max_price') }}"
                                       placeholder="Max" step="0.1">
                            </div>
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="filter-section">
                        <div class="filter-title">Options</div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bio" value="1"
                                   {{ request('bio') ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="form-check-label">
                                <i class="fas fa-leaf text-success me-1"></i>
                                Produits bio uniquement
                            </label>
                        </div>
                    </div>

                    <!-- Bouton reset -->
                    <div class="filter-section">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="fas fa-times me-2"></i>
                            Effacer les filtres
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Produits -->
        <div class="col-lg-9">
            <!-- Filtres actifs -->
            @if(request()->hasAny(['search', 'category', 'min_price', 'max_price', 'bio']))
                <div class="filter-tags">
                    @if(request('search'))
                        <span class="filter-tag">
                            Recherche: "{{ request('search') }}"
                            <span class="remove" onclick="removeFilter('search')">×</span>
                        </span>
                    @endif
                    @if(request('category'))
                        <span class="filter-tag">
                            Catégorie: {{ $categories->find(request('category'))->name ?? 'Inconnue' }}
                            <span class="remove" onclick="removeFilter('category')">×</span>
                        </span>
                    @endif
                    @if(request('bio'))
                        <span class="filter-tag">
                            Produits bio
                            <span class="remove" onclick="removeFilter('bio')">×</span>
                        </span>
                    @endif
                    @if(request('min_price') || request('max_price'))
                        <span class="filter-tag">
                            Prix: {{ request('min_price', '0') }}€ - {{ request('max_price', '∞') }}€
                            <span class="remove" onclick="removeFilter(['min_price', 'max_price'])">×</span>
                        </span>
                    @endif
                </div>
            @endif

            <!-- Contrôles de tri et résultats -->
            <div class="sort-controls">
                <div class="results-info flex-grow-1">
                    <strong>{{ $products->total() }}</strong> produit(s) trouvé(s)
                    @if($products->hasPages())
                        (page {{ $products->currentPage() }} sur {{ $products->lastPage() }})
                    @endif
                </div>

                <div class="d-flex gap-2 align-items-center">
                    <label for="sort-select" class="form-label mb-0 text-nowrap">Trier par:</label>
                    <select class="form-select form-select-sm" id="sort-select" name="sort"
                            onchange="updateSort(this.value)" style="width: auto;">
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nom A-Z</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Plus récents</option>
                        <option value="featured" {{ request('sort') == 'featured' ? 'selected' : '' }}>En vedette</option>
                    </select>
                </div>
            </div>

            <!-- Grille de produits -->
            @if($products->count() > 0)
                <div class="row">
                    @foreach($products as $product)
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card product-card">
                                <!-- Image et badges -->
                                <div class="product-image-container">
                                    @if($product->images && count($product->images) > 0)
                                        <img src="{{ asset('storage/products/' . $product->images[0]) }}"
                                             alt="{{ $product->name }}" class="product-image">
                                    @else
                                        <div class="product-emoji">
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

                                    <!-- Badges -->
                                    <div class="product-badges">
                                        @if($product->is_bio)
                                            <span class="badge-bio">BIO</span>
                                        @endif

                                        @if($product->compare_price && $product->compare_price > $product->price)
                                            <span class="badge-promo">
                                                -{{ round((($product->compare_price - $product->price) / $product->compare_price) * 100) }}%
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Indicateur de stock -->
                                    @if($product->stock_quantity <= $product->min_stock && $product->stock_quantity > 0)
                                        <div class="stock-indicator stock-low">
                                            Stock faible
                                        </div>
                                    @elseif($product->stock_quantity == 0)
                                        <div class="stock-indicator stock-out">
                                            Rupture
                                        </div>
                                    @endif
                                </div>

                                <!-- Informations produit -->
                                <div class="product-info">
                                    <h5 class="product-title">{{ $product->name }}</h5>
                                    <p class="product-description">
                                        {{ $product->short_description ?? Str::limit($product->description, 80) }}
                                    </p>

                                    <div class="product-meta">
                                        <div>
                                            @if($product->compare_price && $product->compare_price > $product->price)
                                                <span class="product-price-old">{{ number_format($product->compare_price, 2, ',', ' ') }}€</span>
                                            @endif
                                            <span class="product-price">{{ number_format($product->price, 2, ',', ' ') }}€</span>
                                            <small class="text-muted">/ {{ $product->unit }}</small>
                                        </div>
                                    </div>

                                    @if($product->origin)
                                        <div class="product-origin mb-3">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ $product->origin }}
                                        </div>
                                    @endif

                                    <!-- Actions -->
                                    <div class="product-actions">
                                        @if($product->stock_quantity > 0)
                                            <form action="{{ route('cart.add', $product) }}" method="POST" class="d-flex gap-2 w-100">
                                                @csrf
                                                <div class="quantity-selector">
                                                    <button type="button" class="quantity-btn" onclick="decreaseQuantity(this)">-</button>
                                                    <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}" class="quantity-input">
                                                    <button type="button" class="quantity-btn" onclick="increaseQuantity(this)">+</button>
                                                </div>
                                                <button type="submit" class="btn-add-cart">
                                                    <i class="fas fa-shopping-cart"></i>
                                                    Ajouter
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn-add-cart" disabled>
                                                <i class="fas fa-times"></i>
                                                Non disponible
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Lien vers détail -->
                                    <div class="text-center mt-2">
                                        <a href="{{ route('products.show', $product->slug) }}" class="btn btn-link btn-sm">
                                            Voir les détails <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

               <!-- Pagination personnalisée -->
@if($products->hasPages())
    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="Pagination">
            <ul class="pagination">
                {{-- Bouton précédent --}}
                @if ($products->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $products->previousPageUrl() }}">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Numéros de pages --}}
                @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                    @if ($page == $products->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                {{-- Bouton suivant --}}
                @if ($products->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $products->nextPageUrl() }}">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif
                <!-- Aucun produit trouvé -->
                <div class="text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                    <h4>Aucun produit trouvé</h4>
                    <p class="text-muted mb-4">
                        Essayez de modifier vos critères de recherche ou
                        <a href="{{ route('products.index') }}">voir tous nos produits</a>.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Gestion des quantités
    function increaseQuantity(btn) {
        const input = btn.parentElement.querySelector('.quantity-input');
        const max = parseInt(input.getAttribute('max'));
        const current = parseInt(input.value);
        if (current < max) {
            input.value = current + 1;
        }
    }

    function decreaseQuantity(btn) {
        const input = btn.parentElement.querySelector('.quantity-input');
        const current = parseInt(input.value);
        if (current > 1) {
            input.value = current - 1;
        }
    }

    // Mise à jour du tri
    function updateSort(value) {
        const url = new URL(window.location);
        url.searchParams.set('sort', value);
        window.location = url;
    }

    // Suppression de filtres
    function removeFilter(filterName) {
        const url = new URL(window.location);
        if (Array.isArray(filterName)) {
            filterName.forEach(name => url.searchParams.delete(name));
        } else {
            url.searchParams.delete(filterName);
        }
        window.location = url;
    }

    // Soumission automatique du formulaire de filtre
    document.querySelectorAll('#filter-form input[type="number"]').forEach(input => {
        input.addEventListener('change', function() {
            this.form.submit();
        });
    });
</script>
@endpush
