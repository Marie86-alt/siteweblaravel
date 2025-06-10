@extends('layouts.app')

@section('title', 'Nos Catégories - Fruits & Légumes en Gros')
@section('description', 'Découvrez notre large gamme de catégories : fruits frais, légumes de saison, produits bio et spécialités pour professionnels.')

@push('styles')
<style>
    :root {
        --primary-green: rgb(120, 230, 166);
        --secondary-green: #2ecc71;
        --dark-green: rgb(62, 109, 82);
        --light-green: #a9dfbf;
        --orange: #f39c12;
        --red: #e74c3c;
    }

    /* === PAGE HEADER === */
    .page-header {
        background: linear-gradient(135deg, rgba(62, 109, 82, 0.9), rgba(46, 204, 113, 0.85)),
                    url('https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2126&q=80');
        background-size: cover;
        background-position: center;
        padding: 6rem 0 4rem;
        color: white;
        text-align: center;
        position: relative;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.4);
    }

    .page-header .container {
        position: relative;
        z-index: 2;
    }

    .page-title {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .page-subtitle {
        font-size: 1.2rem;
        opacity: 0.95;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
    }

    /* === STATS BAR === */
    .stats-bar {
        background: white;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        border-radius: 15px;
        margin: -30px auto 4rem;
        max-width: 800px;
        padding: 2rem;
        position: relative;
        z-index: 3;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 2rem;
        text-align: center;
    }

    .stat-item {
        padding: 1rem;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        color: var(--primary-green);
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: #666;
        font-weight: 500;
    }

    /* === CATEGORIES GRID === */
    .categories-section {
        padding: 2rem 0 5rem;
        background: #fafafa;
    }

    .categories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .category-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        position: relative;
        height: 350px;
    }

    .category-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .category-image {
        height: 200px;
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        position: relative;
        overflow: hidden;
    }

    .category-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .category-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: var(--primary-green);
        color: white;
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .category-content {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 150px;
    }

    .category-name {
        font-size: 1.4rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .category-description {
        color: #666;
        font-size: 0.95rem;
        line-height: 1.5;
        margin-bottom: 1rem;
        flex-grow: 1;
    }

    .category-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        font-size: 0.9rem;
        color: #888;
    }

    .category-cta {
        background: var(--primary-green);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        text-align: center;
        display: inline-block;
    }

    .category-cta:hover {
        background: var(--dark-green);
        color: white;
        transform: translateY(-2px);
    }

    /* === FILTER BAR === */
    .filter-bar {
        background: white;
        padding: 1.5rem;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        margin-bottom: 2rem;
    }

    .filter-controls {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-select {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 8px 12px;
        background: white;
        transition: all 0.3s;
    }

    .filter-select:focus {
        border-color: var(--primary-green);
        outline: none;
    }

    .filter-tag {
        background: var(--light-green);
        color: var(--dark-green);
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
    }

    .filter-tag:hover {
        background: var(--primary-green);
        color: white;
    }

    .filter-tag.active {
        background: var(--primary-green);
        color: white;
    }

    /* === CTA SECTION === */
    .cta-section {
        background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
        padding: 4rem 0;
        color: white;
        text-align: center;
    }

    .cta-title {
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .cta-description {
        font-size: 1.1rem;
        margin-bottom: 2rem;
        opacity: 0.95;
    }

    .cta-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-cta-primary {
        background: white;
        color: var(--primary-green);
        border: none;
        padding: 12px 30px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-cta-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        color: var(--primary-green);
    }

    .btn-cta-secondary {
        background: rgba(255,255,255,0.2);
        color: white;
        border: 2px solid rgba(255,255,255,0.3);
        padding: 10px 30px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-cta-secondary:hover {
        background: rgba(255,255,255,0.3);
        color: white;
        transform: translateY(-2px);
    }

    /* === RESPONSIVE === */
    @media (max-width: 768px) {
        .page-title {
            font-size: 2.2rem;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .categories-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .filter-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .cta-buttons {
            flex-direction: column;
            align-items: center;
        }
    }

    /* === ANIMATIONS === */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }

    .fade-in-up:nth-child(odd) {
        animation-delay: 0.1s;
    }

    .fade-in-up:nth-child(even) {
        animation-delay: 0.2s;
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Nos Catégories</h1>
        <p class="page-subtitle">
            Découvrez notre large gamme de produits frais organisée par catégories
            pour faciliter votre approvisionnement professionnel
        </p>
    </div>
</section>

<!-- Stats Bar -->
<div class="container">
    <div class="stats-bar">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">8</div>
                <div class="stat-label">Catégories</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">500+</div>
                <div class="stat-label">Références</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">100%</div>
                <div class="stat-label">Frais</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">24h</div>
                <div class="stat-label">Livraison</div>
            </div>
        </div>
    </div>
</div>

<!-- Categories Section -->
<section class="categories-section">
    <div class="container">

        <!-- Filter Bar -->
        <div class="filter-bar">
            <div class="filter-controls">
                <div class="filter-item">
                    <label>Trier par :</label>
                    <select class="filter-select">
                        <option>Nom A-Z</option>
                        <option>Popularité</option>
                        <option>Nouveautés</option>
                    </select>
                </div>
                <div class="filter-item">
                    <span class="filter-tag active">Tous</span>
                    <span class="filter-tag">Bio</span>
                    <span class="filter-tag">Saisonnier</span>
                    <span class="filter-tag">Exotique</span>
                </div>
            </div>
        </div>

        <!-- Categories Grid -->
        <div class="categories-grid">

            @if(isset($categories) && $categories->count() > 0)
                @foreach($categories as $category)
                    <div class="category-card fade-in-up">
                        <div class="category-image">
                            @if($category->image)
                                <img src="{{ asset('storage/categories/' . $category->image) }}"
                                     alt="{{ $category->name }}">
                            @else
                                @switch($category->name)
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
                            @endif
                            @if($category->is_featured)
                                <span class="category-badge">Populaire</span>
                            @endif
                        </div>
                        <div class="category-content">
                            <h3 class="category-name">{{ $category->name }}</h3>
                            <p class="category-description">
                                {{ $category->description ?? 'Découvrez notre sélection de produits frais dans cette catégorie.' }}
                            </p>
                            <div class="category-meta">
                                <span>{{ $category->products_count ?? 0 }} produits</span>
                                <span>• Disponible toute l'année</span>
                            </div>
                            <a href="{{ route('products.index', ['category' => $category->id]) }}"
                               class="category-cta">
                                Voir les produits
                            </a>
                        </div>
                    </div>
                @endforeach
            @else
                <!-- Catégories exemples -->
                <div class="category-card fade-in-up">
                    <div class="category-image">
                        🍎
                        <span class="category-badge">Populaire</span>
                    </div>
                    <div class="category-content">
                        <h3 class="category-name">Fruits de Saison</h3>
                        <p class="category-description">
                            Large sélection de fruits frais selon les saisons. Pommes, poires, agrumes et fruits exotiques.
                        </p>
                        <div class="category-meta">
                            <span>120 produits</span>
                            <span>• Disponible toute l'année</span>
                        </div>
                        <a href="#" class="category-cta">Voir les produits</a>
                    </div>
                </div>

                <div class="category-card fade-in-up">
                    <div class="category-image">
                        🥕
                        <span class="category-badge">Bio</span>
                    </div>
                    <div class="category-content">
                        <h3 class="category-name">Légumes Frais</h3>
                        <p class="category-description">
                            Légumes de saison cultivés localement. Carottes, tomates, courgettes et légumes racines.
                        </p>
                        <div class="category-meta">
                            <span>85 produits</span>
                            <span>• Saisonnier</span>
                        </div>
                        <a href="#" class="category-cta">Voir les produits</a>
                    </div>
                </div>

                <div class="category-card fade-in-up">
                    <div class="category-image">
                        🌿
                    </div>
                    <div class="category-content">
                        <h3 class="category-name">Herbes Aromatiques</h3>
                        <p class="category-description">
                            Herbes fraîches et aromatiques pour sublimer vos plats. Basilic, persil, coriandre.
                        </p>
                        <div class="category-meta">
                            <span>25 produits</span>
                            <span>• Ultra-frais</span>
                        </div>
                        <a href="#" class="category-cta">Voir les produits</a>
                    </div>
                </div>

                <div class="category-card fade-in-up">
                    <div class="category-image">
                        🥬
                    </div>
                    <div class="category-content">
                        <h3 class="category-name">Salades & Verdures</h3>
                        <p class="category-description">
                            Salades croquantes et verdures fraîches. Laitue, roquette, épinards et mélanges.
                        </p>
                        <div class="category-meta">
                            <span>30 produits</span>
                            <span>• Livraison quotidienne</span>
                        </div>
                        <a href="#" class="category-cta">Voir les produits</a>
                    </div>
                </div>

                <div class="category-card fade-in-up">
                    <div class="category-image">
                        🍓
                        <span class="category-badge">Saisonnier</span>
                    </div>
                    <div class="category-content">
                        <h3 class="category-name">Fruits Rouges</h3>
                        <p class="category-description">
                            Fruits rouges délicats et savoureux. Fraises, framboises, myrtilles et cassis.
                        </p>
                        <div class="category-meta">
                            <span>15 produits</span>
                            <span>• Avril à Octobre</span>
                        </div>
                        <a href="#" class="category-cta">Voir les produits</a>
                    </div>
                </div>

                <div class="category-card fade-in-up">
                    <div class="category-image">
                        🥥
                    </div>
                    <div class="category-content">
                        <h3 class="category-name">Fruits Exotiques</h3>
                        <p class="category-description">
                            Fruits exotiques importés avec soin. Mangues, ananas, fruits de la passion.
                        </p>
                        <div class="category-meta">
                            <span>40 produits</span>
                            <span>• Import premium</span>
                        </div>
                        <a href="#" class="category-cta">Voir les produits</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h2 class="cta-title">Besoin d'un devis personnalisé ?</h2>
        <p class="cta-description">
            Contactez notre équipe commerciale pour un devis adapté à vos besoins professionnels
        </p>
        <div class="cta-buttons">
            <a href="#contact" class="btn-cta-primary">
                <i class="fas fa-phone me-2"></i>
                Nous contacter
            </a>
            <a href="{{ route('products.index') }}" class="btn-cta-secondary">
                <i class="fas fa-list me-2"></i>
                Voir tous les produits
            </a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation au scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observer les cartes catégories
        document.querySelectorAll('.fade-in-up').forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = `all 0.6s ease-out ${index * 0.1}s`;
            observer.observe(el);
        });

        // Gestion des filtres
        const filterTags = document.querySelectorAll('.filter-tag');
        filterTags.forEach(tag => {
            tag.addEventListener('click', function() {
                // Retirer active de tous
                filterTags.forEach(t => t.classList.remove('active'));
                // Ajouter active au cliqué
                this.classList.add('active');

                // Ici on pourrait ajouter la logique de filtrage
                console.log('Filtre sélectionné:', this.textContent);
            });
        });

        // Gestion du tri
        const sortSelect = document.querySelector('.filter-select');
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                console.log('Tri sélectionné:', this.value);
                // Ici on pourrait ajouter la logique de tri
            });
        }
    });
</script>
@endpush
