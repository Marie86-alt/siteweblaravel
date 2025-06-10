@extends('layouts.app')

@section('title', 'Nos Produits')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1>Nos Produits</h1>
        <p class="text-muted">Page en cours de développement</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card shadow-sm">
                <div class="card-body py-5">
                    <i class="fas fa-tools fa-4x text-muted mb-4"></i>
                    <h3>Page en construction</h3>
                    <p class="text-muted mb-4">
                        Cette page sera bientôt disponible avec tous nos délicieux fruits et légumes !
                    </p>
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
