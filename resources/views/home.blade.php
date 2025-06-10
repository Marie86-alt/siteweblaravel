@extends('layouts.app')

@section('title', 'Fruits & Légumes Bio - Frais et Local')
@section('description', 'Découvrez notre sélection de fruits et légumes bio, frais et locaux. Livraison rapide dans toute la région.')

@push('styles')
<style>
    /* === HERO SECTION === */
    .hero-section {
        background: linear-gradient(135deg, rgba(45, 90, 65, 0.85), rgba(74, 124, 89, 0.8)),
                    url('https://images.unsplash.com/photo-1542838132-92c53300491e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2074&q=80');
        background-size: cover;
        background-position: center;
        min-height: 85vh;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0.6, 0, 0, 0);
        z-index: 1;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        color: white;
        max-width: 600px;
    }

    .hero-title {
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 700;
        line-height: 1.1;
        margin-bottom: 1.5rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .hero-subtitle {
        font-size: 1.25rem;
        margin-bottom: 2rem;
        opacity: 0.95;
        line-height: 1.6;
    }

    .hero-cta {
        display: flex;
        gap: 1rem;
        margin-bottom: 3rem;
    }

    .btn-hero-primary {
        background: white;
        color: var(--primary-green);
        border: none;
        padding: 15px 35px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .btn-hero-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        color: var(--primary-green);
    }

    .btn-hero-secondary {
        background: rgba(255,255,255,0.2);
        color: white;
        border: 2px solid rgba(255,255,255,0.3);
        padding: 13px 35px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        text-decoration: none;
        backdrop-filter: blur(10px);
    }

    .btn-hero-secondary:hover {
        background: rgba(255,255,255,0.3);
        border-color: rgba(255,255,255,0.5);
        color: white;
        transform: translateY(-2px);
    }

    .hero-stats {
        display: flex;
        gap: 2rem;
        margin-top: 2rem;
    }

    .hero-stat {
        text-align: center;
    }

    .hero-stat-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .hero-stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .hero-visual {
        position: absolute;
        right: -10%;
        top: 50%;
        transform: translateY(-50%);
        width: 50%;
        height: 80%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
        z-index: 1;
    }

    /* === FEATURES SECTION === */
    .features-section {
        padding: 5rem 0;
        background: #fafafa;
    }

    .feature-card {
        background: white;
        border-radius: 20px;
        padding: 2.5rem 1.5rem;
        text-align: center;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        height: 100%;
        border: none;
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.12);
    }

    .feature-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 1.8rem;
        color: white;
        box-shadow: 0 5px 15px rgba(45, 90, 65, 0.3);
    }

    .feature-title {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #333;
    }

    .feature-description {
        color: #666;
        line-height: 1.6;
    }

    /* === PRODUCTS PREVIEW === */
    .products-preview {
        padding: 5rem 0;
        background: white;
    }

    .section-header {
        text-align: center;
        margin-bottom: 4rem;
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 1rem;
    }

    .section-subtitle {
        font-size: 1.1rem;
        color: #666;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
    }

    .product-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        position: relative;
        height: 100%;
    }

    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .product-image {
        position: relative;
        height: 200px;
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        overflow: hidden;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: var(--primary-green);
        color: white;
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .product-info {
        padding: 1.5rem;
    }

    .product-name {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #333;
    }

    .product-price {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--primary-green);
        margin-bottom: 1rem;
    }

    .product-cta {
        background: var(--primary-green);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
        width: 100%;
        box-shadow: 0 3px 10px rgba(45, 90, 65, 0.3);
    }

    .product-cta:hover {
        background: var(--dark-green);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(45, 90, 65, 0.4);
    }

    /* === TESTIMONIALS === */
    /* Section supprimée - remplacée par l'image de marché dans la CTA */

    /* === CTA SECTION === */
    .cta-section {
        padding: 5rem 0;
        background: linear-gradient(135deg, rgba(120,230,200), rgba(120,230,200)),
                    url('https://images.unsplash.com/photo-1488459716781-31db52582fee?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        color: white;
        text-align: center;
        position: relative;
    }

    .cta-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.3);
    }

    .cta-section .container {
        position: relative;
        z-index: 2;
    }

    .cta-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .cta-description {
        font-size: 1.2rem;
        margin-bottom: 2rem;
        opacity: 0.95;
    }

    .cta-button {
        background: white;
        color: var(--primary-green);
        border: none;
        padding: 15px 40px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
    }

    .cta-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        color: var(--primary-green);
    }

    /* === RESPONSIVE === */
    @media (max-width: 768px) {
        .hero-section {
            min-height: 70vh;
            text-align: center;
        }

        .hero-title {
            font-size: 2.5rem;
        }

        .hero-cta {
            flex-direction: column;
            align-items: center;
        }

        .hero-stats {
            justify-content: center;
        }

        .hero-visual {
            display: none;
        }

        .feature-card,
        .product-card,
        .testimonial-card {
            margin-bottom: 2rem;
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

    .fade-in-up:nth-child(2) {
        animation-delay: 0.2s;
    }

    .fade-in-up:nth-child(3) {
        animation-delay: 0.4s;
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="hero-content">
                    <h1 class="hero-title">
                        Fruits & Légumes <strong>en gros</strong><br>
                        <span style="color: #a9dfbf;">frais & de qualité</span>
                    </h1>
                    <p class="hero-subtitle">
                        Votre grossiste de confiance pour l'approvisionnement en fruits et légumes frais.
                        Livraison professionnelle pour restaurants, épiceries et collectivités.
                    </p>

                    <div class="hero-cta">
                        <a href="{{ route('products.index') }}" class="btn-hero-primary">
                            <i class="fas fa-store"></i>
                            Voir notre catalogue
                        </a>
                        <a href="#features" class="btn-hero-secondary">
                            Nos services
                        </a>
                    </div>

                    <div class="hero-stats">
                        <div class="hero-stat">
                            <div class="hero-stat-number">50+</div>
                            <div class="hero-stat-label">Références</div>
                        </div>
                        <div class="hero-stat">
                            <div class="hero-stat-number">24h</div>
                            <div class="hero-stat-label">Livraison</div>
                        </div>
                        <div class="hero-stat">
                            <div class="hero-stat-number">10+</div>
                            <div class="hero-stat-label">Années d'expérience</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hero-visual"></div>
</section>

<!-- Features Section -->
<section class="features-section" id="features">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Pourquoi nous choisir ?</h2>
            <p class="section-subtitle">
                Votre partenaire de confiance pour un approvisionnement professionnel en fruits et légumes
            </p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="feature-card fade-in-up">
                    <div class="feature-icon">
                        <i class="fas fa-truck-loading"></i>
                    </div>
                    <h3 class="feature-title">Vente en Gros</h3>
                    <p class="feature-description">
                        Spécialistes de la vente en gros avec des tarifs dégressifs adaptés
                        aux professionnels et gros volumes.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card fade-in-up">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="feature-title">Livraison Rapide</h3>
                    <p class="feature-description">
                        Livraison quotidienne pour restaurants, épiceries et collectivités.
                        Service express disponible.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card fade-in-up">
                    <div class="feature-icon">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <h3 class="feature-title">Fraîcheur Garantie</h3>
                    <p class="feature-description">
                        Approvisionnement direct auprès des producteurs pour garantir
                        la fraîcheur et la qualité de nos produits.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card fade-in-up">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="feature-title">Service Professionnel</h3>
                    <p class="feature-description">
                        Équipe dédiée aux professionnels avec conseils personnalisés
                        et suivi de commandes.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card fade-in-up">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="feature-title">Tarifs Compétitifs</h3>
                    <p class="feature-description">
                        Prix grossiste avantageux avec remises sur volume.
                        Devis personnalisés pour vos besoins spécifiques.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card fade-in-up">
                    <div class="feature-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3 class="feature-title">Large Gamme</h3>
                    <p class="feature-description">
                        Plus de 50 références : fruits, légumes, bio, exotiques.
                        Disponibilité toute l'année grâce à notre réseau.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Products Preview -->
<section class="products-preview">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Notre sélection professionnelle</h2>
            <p class="section-subtitle">
                Découvrez nos produits phares disponibles en gros volumes pour les professionnels
            </p>
        </div>

        <div class="row g-4">
            @if(isset($featuredProducts) && $featuredProducts->count() > 0)
                @foreach($featuredProducts->take(6) as $product)
                    <div class="col-lg-4 col-md-6">
                        <div class="product-card">
                            <div class="product-image">
                                @if($product->images && count($product->images) > 0)
                                    <img src="{{ asset('storage/products/' . $product->images[0]) }}"
                                         alt="{{ $product->name }}">
                                @else
                                    <span style="color: var(--primary-green);">
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
                                    </span>
                                @endif
                                @if($product->is_bio)
                                    <span class="product-badge">Bio</span>
                                @elseif($product->is_featured)
                                    <span class="product-badge" style="background: var(--orange);">Top Vente</span>
                                @endif
                            </div>
                            <div class="product-info">
                                <h3 class="product-name">{{ $product->name }}</h3>
                                <div class="product-price">
                                    {{ number_format($product->price, 2, ',', ' ') }}€ / {{ $product->unit }}
                                </div>
                                <a href="{{ route('products.show', $product->slug) }}" class="product-cta">
                                    Voir le produit
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <!-- Produits exemples si pas de données -->
                <div class="col-lg-4 col-md-6">
                    <div class="product-card">
                        <div class="product-image">
                            🍎
                            <span class="product-badge" style="background: var(--orange);">Top Vente</span>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">Pommes Gala</h3>
                            <div class="product-price">2,50€ / kg</div>
                            <button class="product-cta">Voir le produit</button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="product-card">
                        <div class="product-image">
                            🥕
                            <span class="product-badge">Bio</span>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">Carottes Bio</h3>
                            <div class="product-price">1,80€ / kg</div>
                            <button class="product-cta">Voir le produit</button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="product-card">
                        <div class="product-image">
                            🌿
                            <span class="product-badge">Frais</span>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">Basilic Frais</h3>
                            <div class="product-price">12€ / cagette</div>
                            <button class="product-cta">Voir le produit</button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                Voir tout notre catalogue
                <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>



<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h2 class="cta-title">Devenez notre partenaire</h2>
        <p class="cta-description">
            Rejoignez plus de 50 professionnels qui nous font confiance pour leur approvisionnement quotidien
        </p>
        <a href="{{ route('products.index') }}" class="cta-button">
            <i class="fas fa-handshake"></i>
            Demander un devis
        </a>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Animation au scroll
    document.addEventListener('DOMContentLoaded', function() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observer tous les éléments avec animation
        document.querySelectorAll('.fade-in-up').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.6s ease-out';
            observer.observe(el);
        });
    });

    // Smooth scroll pour les ancres
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
</script>
@endpush
