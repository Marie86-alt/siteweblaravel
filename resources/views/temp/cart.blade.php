@extends('layouts.app')

@section('title', 'Mon Panier')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1>Mon Panier</h1>
        <p class="text-muted">Page en cours de développement</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card shadow-sm">
                <div class="card-body py-5">
                    <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                    <h3>Panier temporaire</h3>
                    <p class="text-muted mb-4">
                        Le système de panier sera bientôt opérationnel !
                    </p>

                    @if(count($cart ?? []) > 0)
                        <div class="alert alert-info">
                            Vous avez {{ count($cart) }} article(s) en attente
                        </div>
                    @endif

                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="fas fa-home me-2"></i>
                        Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
