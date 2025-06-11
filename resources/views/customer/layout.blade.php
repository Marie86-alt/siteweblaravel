@extends('layouts.app')

@section('title', 'Mon Compte - ' . (auth()->user()->first_name ?? 'Client'))

@push('styles')
<style>
    :root {
        --primary-green: rgb(120, 230, 166);
        --secondary-green: #2ecc71;
        --dark-green: rgb(62, 109, 82);
        --light-green: #a9dfbf;
        --orange: #f39c12;
        --red: #e74c3c;
        --blue: #3498db;
    }

    .customer-container {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 2rem;
        margin-top: 2rem;
        min-height: 70vh;
    }

    /* === SIDEBAR === */
    .customer-sidebar {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        height: fit-content;
        position: sticky;
        top: 100px;
    }

    .customer-profile {
        text-align: center;
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 2px solid #f8f9fa;
    }

    .customer-avatar {
        width: 80px;
        height: 80px;
        background: var(--primary-green);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        color: var(--dark-green);
        font-size: 2rem;
        font-weight: bold;
    }

    .customer-name {
        font-size: 1.2rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .customer-email {
        color: #666;
        font-size: 0.9rem;
    }

    .customer-nav {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .customer-nav li {
        margin-bottom: 0.5rem;
    }

    .customer-nav a {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        color: #666;
        text-decoration: none;
        border-radius: 10px;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .customer-nav a:hover {
        background: rgba(120, 230, 166, 0.1);
        color: var(--dark-green);
        transform: translateX(5px);
    }

    .customer-nav a.active {
        background: var(--primary-green);
        color: var(--dark-green);
    }

    .customer-nav i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }

    /* === CONTENT === */
    .customer-content {
        background: white;
        border-radius: 15px;
        padding: 2.5rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .customer-header {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid #f8f9fa;
    }

    .customer-title {
        font-size: 2rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .customer-subtitle {
        color: #666;
        font-size: 1.1rem;
    }

    /* === STATS CARDS === */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
        color: var(--dark-green);
        padding: 1.5rem;
        border-radius: 15px;
        text-align: center;
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.8;
    }

    /* === RESPONSIVE === */
    @media (max-width: 768px) {
        .customer-container {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .customer-sidebar {
            position: static;
        }

        .customer-content {
            padding: 1.5rem;
        }
    }

    /* === FORMS === */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(120, 230, 166, 0.2);
    }

    .btn-primary {
        background: var(--primary-green);
        color: var(--dark-green);
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: var(--dark-green);
        color: white;
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="customer-container">

        <!-- Sidebar -->
        <aside class="customer-sidebar">
            <div class="customer-profile">
                <div class="customer-avatar">
                    {{ strtoupper(substr(auth()->user()->first_name ?? auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? '', 0, 1)) }}
                </div>
                <div class="customer-name">
                    {{ auth()->user()->first_name ?? auth()->user()->name }} {{ auth()->user()->last_name }}
                </div>
                <div class="customer-email">
                    {{ auth()->user()->email }}
                </div>
            </div>

            <nav>
                <ul class="customer-nav">
                    <li>
                        <a href="{{ route('customer.dashboard') }}" class="{{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('customer.profile') }}" class="{{ request()->routeIs('customer.profile') ? 'active' : '' }}">
                            <i class="fas fa-user"></i>
                            Mon Profil
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('customer.orders') }}" class="{{ request()->routeIs('customer.orders*') ? 'active' : '' }}">
                            <i class="fas fa-shopping-bag"></i>
                            Mes Commandes
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('customer.addresses') }}" class="{{ request()->routeIs('customer.addresses') ? 'active' : '' }}">
                            <i class="fas fa-map-marker-alt"></i>
                            Mes Adresses
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('customer.favorites') }}" class="{{ request()->routeIs('customer.favorites') ? 'active' : '' }}">
                            <i class="fas fa-heart"></i>
                            Mes Favoris
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('customer.settings') }}" class="{{ request()->routeIs('customer.settings') ? 'active' : '' }}">
                            <i class="fas fa-cog"></i>
                            Paramètres
                        </a>
                    </li>
                    <li style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #eee;">
                        <a href="{{ route('home') }}">
                            <i class="fas fa-home"></i>
                            Retour au site
                        </a>
                    </li>
                    <li>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i>
                            Déconnexion
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Content -->
        <main class="customer-content">
            @yield('customer-content')
        </main>

    </div>
</div>
@endsection
