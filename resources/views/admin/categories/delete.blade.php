@extends('layouts.admin')

@section('title', 'Supprimer la catégorie')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirmer la suppression
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}"
                                 alt="{{ $category->name }}"
                                 class="img-fluid rounded mb-3"
                                 style="max-height: 120px;">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3"
                                 style="height: 120px; width: 120px; margin: 0 auto;">
                                <i class="fas fa-layer-group fa-3x text-muted"></i>
                            </div>
                        @endif

                        <h4 class="text-danger">{{ $category->name }}</h4>

                        @if($category->description)
                            <p class="text-muted">{{ Str::limit($category->description, 100) }}</p>
                        @endif
                    </div>

                    <!-- Avertissements -->
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-2"></i>Attention !
                        </h6>
                        <p class="mb-2">Cette action est <strong>irréversible</strong>. La suppression de cette catégorie entraînera :</p>
                        <ul class="mb-0">
                            <li>La suppression définitive de la catégorie</li>
                            @if($category->products_count > 0)
                                <li class="text-danger">
                                    <strong>{{ $category->products_count }} produit(s) associé(s) seront orphelins</strong>
                                </li>
                            @endif
                            @if($category->image)
                                <li>La suppression de l'image associée</li>
                            @endif
                            <li>La perte de toutes les statistiques liées</li>
                        </ul>
                    </div>

                    @if($category->products_count > 0)
                        <div class="alert alert-danger">
                            <h6 class="alert-heading">
                                <i class="fas fa-ban me-2"></i>Produits associés détectés
                            </h6>
                            <p class="mb-2">
                                Cette catégorie contient <strong>{{ $category->products_count }} produit(s)</strong>.
                                Vous devez d'abord :
                            </p>
                            <ul class="mb-3">
                                <li>Déplacer les produits vers une autre catégorie, ou</li>
                                <li>Supprimer les produits individuellement</li>
                            </ul>
                            <a href="{{ route('admin.products.index', ['category' => $category->id]) }}"
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>Voir les produits
                            </a>
                        </div>
                    @endif

                    <!-- Statistiques -->
                    <div class="row text-center mb-4">
                        <div class="col-4">
                            <div class="border-end">
                                <h5 class="mb-0 text-primary">{{ $category->products_count ?? 0 }}</h5>
                                <small class="text-muted">Produits</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-end">
                                <h5 class="mb-0 text-success">{{ $category->orders_count ?? 0 }}</h5>
                                <small class="text-muted">Commandes</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0 text-info">
                                {{ $category->created_at->diffForHumans() }}
                            </h5>
                            <small class="text-muted">Créée</small>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Annuler
                        </a>

                        @if($category->products_count == 0)
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-danger"
                                        onclick="return confirm('Êtes-vous absolument sûr(e) de vouloir supprimer cette catégorie ?')">
                                    <i class="fas fa-trash me-2"></i>Supprimer définitivement
                                </button>
                            </form>
                        @else
                            <button type="button" class="btn btn-danger" disabled title="Impossible de supprimer : produits associés">
                                <i class="fas fa-ban me-2"></i>Suppression bloquée
                            </button>
                        @endif
                    </div>
                </div>

                <div class="card-footer text-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Créée le {{ $category->created_at->format('d/m/Y à H:i') }}
                        @if($category->updated_at != $category->created_at)
                            • Modifiée le {{ $category->updated_at->format('d/m/Y à H:i') }}
                        @endif
                    </small>
                </div>
            </div>

            <!-- Alternatives -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Alternatives à la suppression
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-edit me-1"></i>Modifier la catégorie
                            </a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <form action="{{ route('admin.categories.toggle', $category) }}" method="POST" class="d-inline w-100">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-warning btn-sm w-100">
                                    <i class="fas fa-eye{{ $category->is_active ? '-slash' : '' }} me-1"></i>
                                    {{ $category->is_active ? 'Désactiver' : 'Activer' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
