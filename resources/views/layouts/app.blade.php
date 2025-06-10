<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Fruits & Légumes Bio - Frais et Local')</title>
    <meta name="description" content="@yield('description', 'Découvrez notre sélection de fruits et légumes bio, frais et locaux. Livraison rapide dans toute la région.')">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-green:rgb(120, 230, 166);
            --secondary-green: #2ecc71;
            --dark-green:rgb(62, 109, 82);
            --light-green: #a9dfbf;
            --orange: #f39c12;
            --red: #e74c3c;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 80px; /* Pour compenser la navbar fixe */
        }

        /* Navigation */
        .navbar {
            background: white !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-weight: bold;
            color: var(--primary-green) !important;
            font-size: 1.5rem;
        }

        .navbar-nav .nav-link {
            color: #333 !important;
            font-weight: 500;
            margin: 0 10px;
            transition: color 0.3s;
            position: relative;
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary-green) !important;
        }

        .navbar-nav .nav-link.active {
            color: var(--primary-green) !important;
        }

        .navbar-nav .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 3px;
            background: var(--primary-green);
            border-radius: 2px;
        }

        /* Boutons */
        .btn-primary {
            background: var(--primary-green);
            border: var(--primary-green);
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: var(--dark-green);
            border: var(--dark-green);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(39, 174, 96, 0.3);
        }

        .btn-outline-primary {
            color: var(--primary-green);
            border-color: var(--primary-green);
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-outline-primary:hover {
            background: var(--primary-green);
            border-color: var(--primary-green);
            transform: translateY(-2px);
        }

        /* Panier */
        .cart-badge {
            background: var(--red);
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.8rem;
            position: absolute;
            top: -8px;
            right: -8px;
            min-width: 18px;
            text-align: center;
        }

        .cart-link {
            position: relative;
            display: inline-block;
        }

        /* Messages flash */
        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 20px;
        }

        .alert-success {
            background: linear-gradient(135deg, var(--secondary-green), var(--primary-green));
            color: white;
        }

        .alert-error {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        /* Footer */
        .footer {
            background: #2c3e50;
            color: white;
            padding: 50px 0 20px;
            margin-top: 80px;
        }

        .footer h5, .footer h6 {
            color: white;
            margin-bottom: 20px;
        }

        .footer a {
            color: #bdc3c7;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer a:hover {
            color: var(--secondary-green);
        }

        .footer .social-links a {
            color: white;
            font-size: 1.2rem;
            margin-right: 15px;
            transition: all 0.3s;
        }

        .footer .social-links a:hover {
            color: var(--secondary-green);
            transform: translateY(-2px);
        }

        /* Utilitaires */
        .section-title {
            text-align: center;
            margin-bottom: 50px;
            position: relative;
        }

        .section-title h2 {
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: var(--primary-green);
            margin: 0 auto;
            border-radius: 2px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }

            .navbar {
                padding: 10px 0;
            }

            .navbar-nav .nav-link {
                margin: 5px 0;
            }
        }

        /* Loading */
        .loading {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            z-index: 9999;
        }

        .loading-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2rem;
            color: var(--primary-green);
        }

 /* === PAGINATION CORRECTIVE === */
.pagination {
    margin: 0 !important;
    gap: 5px !important;
}

.pagination .page-item {
    margin: 0 !important;
}

.pagination .page-link {
    color: var(--primary-green) !important;
    border: 1px solid #dee2e6 !important;
    padding: 0 !important;
    font-size: 0.9rem !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 40px !important;
    height: 40px !important;
    min-width: 40px !important;
    min-height: 40px !important;
    border-radius: 8px !important;
    transition: all 0.2s !important;
    line-height: 1 !important;
}

.pagination .page-link:hover {
    color: white !important;
    background-color: var(--primary-green) !important;
    border-color: var(--primary-green) !important;
    transform: translateY(-1px) !important;
}

.pagination .page-item.active .page-link {
    background-color: var(--primary-green) !important;
    border-color: var(--primary-green) !important;
    color: white !important;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d !important;
    background-color: #f8f9fa !important;
    border-color: #dee2e6 !important;
}

/* FORCER LA TAILLE DES SVG */
.pagination .page-link svg,
.pagination .page-link .svg-inline--fa {
    width: 12px !important;
    height: 12px !important;
    max-width: 12px !important;
    max-height: 12px !important;
    font-size: 12px !important;
}

/* Alternative : cacher les SVG et utiliser du texte */
.pagination .page-link svg {
    display: none !important;
}

.pagination .page-item:first-child .page-link::after {
    content: "‹" !important;
    font-size: 16px !important;
    font-weight: bold !important;
}

.pagination .page-item:last-child .page-link::after {
    content: "›" !important;
    font-size: 16px !important;
    font-weight: bold !important;
}
    </style>

    @stack('styles')
</head>
<body>
    <!-- Loading overlay -->
    <div class="loading" id="loading">
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-leaf me-2"></i>
                {{ config('app.name', 'Fruits & Légumes') }}
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                            Produits
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                            Catégories
                        </a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('contact.*') ? 'active' : '' }}" href="{{ route('contact.index') }}">
                Contact
            </a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link cart-link" href="{{ route('cart.index') }}">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-badge" id="cart-count">{{ cart_count() }}</span>
                        </a>
                    </li>

                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt"></i> Connexion
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="fas fa-user-plus"></i> Inscription
                            </a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> {{ Auth::user()->first_name ?? Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu">

                                    <li><a class="dropdown-item" href="#">
                                    <i class="fas fa-user me-2"></i> Mon profil
                                    </a></li>
                                    <li><a class="dropdown-item" href="#">
                                    <i class="fas fa-shopping-bag me-2"></i> Mes commandes
                                    </a></li>
                                    <li><a class="dropdown-item" href="#">
                                    <i class="fas fa-map-marker-alt me-2"></i> Mes adresses
                                    </a></li>
                @if(Auth::user()->is_admin)
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-cog me-2"></i> Administration
        </a></li>
    @endif
    <li><hr class="dropdown-divider"></li>
    <li>
        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
        </a>
    </li>


                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Messages flash -->
    @if(session('success') || session('error') || session('warning') || session('info'))
        <div class="container mt-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
    @endif

    <!-- Contenu principal -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>
                        <i class="fas fa-leaf me-2"></i>
                        {{ config('app.name', 'Fruits & Légumes') }}
                    </h5>
                    <p>Votre partenaire pour une alimentation saine et locale. Des produits frais, bio et de qualité directement chez vous.</p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-md-2 mb-4">
                    <h6>Navigation</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}">Accueil</a></li>
                        <li><a href="{{ route('products.index') }}">Produits</a></li>
                        <li><a href="{{ route('categories.index') }}">Catégories</a></li>
                        <li><a href="{{ route('contact.index') }}">Contact</a></li>
    </ul>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6>Informations</h6>
                    <ul class="list-unstyled">
                        <li><a href="#">Livraison</a></li>
                        <li><a href="#">Paiement</a></li>
                        <li><a href="#">Conditions générales</a></li>
                        <li><a href="#">Politique de confidentialité</a></li>
                        <li><a href="#">Mentions légales</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6>Contact</h6>
                    <p class="mb-1">
                        <i class="fas fa-phone me-2"></i>
                        <a href="tel:+33123456789">01 23 45 67 89</a>
                    </p>
                    <p class="mb-1">
                        <i class="fas fa-envelope me-2"></i>
                        <a href="mailto:contact@fruits-legumes.fr">contact@fruits-legumes.fr</a>
                    </p>
                    <p>
                        <i class="fas fa-map-marker-alt me-2"></i>
                        123 Rue du Marché, 75001 Paris
                    </p>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'Fruits & Légumes') }}. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- Form de déconnexion -->
    @auth
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    @endauth

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Mise à jour du compteur panier
        function updateCartCount() {
            fetch('{{ route("api.cart.count") }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('cart-count').textContent = data.count;
                })
                .catch(error => console.error('Erreur:', error));
        }

        // Auto-dismiss des alertes après 5 secondes
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                if (alert.querySelector('.btn-close')) {
                    alert.querySelector('.btn-close').click();
                }
            });
        }, 5000);

        // Loading overlay pour les formulaires
        function showLoading() {
            document.getElementById('loading').style.display = 'block';
        }

        function hideLoading() {
            document.getElementById('loading').style.display = 'none';
        }

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

    @stack('scripts')
</body>
</html>
