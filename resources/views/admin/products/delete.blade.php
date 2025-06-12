@extends('layouts.admin')

@section('title', 'Supprimer le produit')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirmer la suppression du produit
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Informations du produit -->
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            @if($product->images && count($product->images) > 0)
                                <img src="{{ asset('storage/' . $product->images[0]) }}"
                                     alt="{{ $product->name }}"
                                     class="img-fluid rounded mb-3"
                                     style="max-height: 200px;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3"
                                     style="height: 200px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif

                            <!-- Badges -->
                            <div class="mb-2">
                                @if($product->is_featured)
                                    <span class="badge bg-warning text-dark me-1">Vedette</span>
                                @endif
                                @if($product->is_organic)
                                    <span class="badge bg-success me-1">Bio</span>
                                @endif
                                @if(!$product->is_active)
                                    <span class="badge bg-secondary me-1">Inactif</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-8">
                            <h3 class="text-danger mb-3">{{ $product->name }}</h3>

                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <strong>Catégorie:</strong><br>
                                    <span class="text-muted">{{ $product->category->name ?? 'Non définie' }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Prix:</strong><br>
                                    <span class="text-success h5">{{ number_format($product->price, 2) }}€</span>
                                    @if($product->unit)
                                        <small class="text-muted">/ {{ $product->unit }}</small>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <strong>Stock:</strong><br>
                                    <span class="text-{{ $product->stock > 10 ? 'success' : ($product->stock > 0 ? 'warning' : 'danger') }}">
                                        {{ $product->stock }}
                                    </span>
                                    @if($product->unit)
                                        <small class="text-muted">{{ $product->unit }}</small>
                                    @endif
                                </div>
                                <div class="col-sm-6">
                                    <strong>Origine:</strong><br>
                                    <span class="text-muted">{{ $product->origin ?? 'Non spécifiée' }}</span>
                                </div>
                            </div>

                            @if($product->description)
                                <div class="mb-3">
                                    <strong>Description:</strong><br>
                                    <p class="text-muted mb-0">{{ Str::limit($product->description, 200) }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Avertissements -->
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-2"></i>Attention !
                        </h6>
                        <p class="mb-2">Cette action est <strong>irréversible</strong>. La suppression de ce produit entraînera :</p>
                        <ul class="mb-0">
                            <li>La suppression définitive du produit</li>
                            @if($product->images && count($product->images) > 0)
                                <li>La suppression de {{ count($product->images) }} image(s) associée(s)</li>
                            @endif
                            @if($product->orders_count > 0)
                                <li class="text-danger">
                                    <strong>Impact sur {{ $product->orders_count }} commande(s) existante(s)</strong>
                                </li>
                            @endif
                            <li>La perte de toutes les statistiques de vente</li>
                            <li>La suppression des avis clients associés</li>
                        </ul>
                    </div>

                    @if($product->orders_count > 0)
                        <div class="alert alert-danger">
                            <h6 class="alert-heading">
                                <i class="fas fa-shopping-cart me-2"></i>Commandes associées détectées
                            </h6>
                            <p class="mb-2">
                                Ce produit a été commandé <strong>{{ $product->orders_count }} fois</strong>.
                                Sa suppression pourrait affecter :
                            </p>
                            <ul class="mb-3">
                                <li>L'historique des commandes clients</li>
                                <li>Les rapports de vente</li>
                                <li>Les statistiques de performance</li>
                            </ul>
                            <p class="mb-0">
                                <strong>Recommandation :</strong> Plutôt que de supprimer, considérez
                                <a href="{{ route('admin.products.edit', $product) }}" class="alert-link">désactiver le produit</a>.
                            </p>
                        </div>
                    @endif

                    <!-- Statistiques détaillées -->
                    <div class="row text-center mb-4">
                        <div class="col-md-3">
                            <div class="border-end">
                                <h5 class="mb-0 text-primary">{{ $product->views_count ?? 0 }}</h5>
                                <small class="text-muted">Vues</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border-end">
                                <h5 class="mb-0 text-success">{{ $product->orders_count ?? 0 }}</h5>
                                <small class="text-muted">Commandes</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border-end">
                                <h5 class="mb-0 text-warning">{{ $product->reviews_count ?? 0 }}</h5>
                                <small class="text-muted">Avis</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5 class="mb-0 text-info">
                                {{ $product->created_at->diffForHumans() }}
                            </h5>
                            <small class="text-muted">Créé</small>
                        </div>
                    </div>

                    <!-- Images du produit -->
                    @if($product->images && count($product->images) > 1)
                        <div class="mb-4">
                            <h6 class="mb-3">Images qui seront supprimées ({{ count($product->images) }})</h6>
                            <div class="row">
                                @foreach($product->images as $image)
                                    <div class="col-md-2 col-4 mb-2">
                                        <img src="{{ asset('storage/' . $image) }}"
                                             alt="Image produit"
                                             class="img-fluid rounded"
                                             style="height: 80px; width: 100%; object-fit: cover;">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Annuler
                        </a>

                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-danger"
                                    onclick="return confirm('Êtes-vous absolument sûr(e) de vouloir supprimer ce produit ?\n\nCette action est irréversible et supprimera :\n- Le produit\n- Toutes ses images\n- Ses statistiques\n\nTapez SUPPRIMER pour confirmer')"
                                    data-product-name="{{ $product->name }}">
                                <i class="fas fa-trash me-2"></i>Supprimer définitivement
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row text-center">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-calendar-plus me-1"></i>
                                Créé le {{ $product->created_at->format('d/m/Y à H:i') }}
                            </small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-calendar-edit me-1"></i>
                                Modifié le {{ $product->updated_at->format('d/m/Y à H:i') }}
                            </small>
                        </div>
                    </div>
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
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-edit me-1"></i>Modifier
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <form action="{{ route('admin.products.toggle', $product) }}" method="POST" class="d-inline w-100">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-warning btn-sm w-100">
                                    <i class="fas fa-eye{{ $product->is_active ? '-slash' : '' }} me-1"></i>
                                    {{ $product->is_active ? 'Désactiver' : 'Activer' }}
                                </button>
                            </form>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.products.duplicate', $product) }}" class="btn btn-outline-info btn-sm w-100">
                                <i class="fas fa-copy me-1"></i>Dupliquer
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-outline-success btn-sm w-100" target="_blank">
                                <i class="fas fa-external-link-alt me-1"></i>Voir en ligne
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Confirmation avancée pour la suppression
document.querySelector('form[action*="destroy"]').addEventListener('submit', function(e) {
    e.preventDefault();

    const productName = this.getAttribute('data-product-name') || 'ce produit';
    const ordersCount = {{ $product->orders_count ?? 0 }};

    let confirmMessage = `Êtes-vous absolument sûr(e) de vouloir supprimer "${productName}" ?\n\n`;
    confirmMessage += `Cette action est IRRÉVERSIBLE et supprimera :\n`;
    confirmMessage += `- Le produit et toutes ses informations\n`;
    confirmMessage += `- Toutes les images associées\n`;
    confirmMessage += `- Les statistiques de vente\n`;

    if (ordersCount > 0) {
        confirmMessage += `- L'historique de ${ordersCount} commande(s)\n\n`;
        confirmMessage += `⚠️ ATTENTION: Ce produit a déjà été commandé ${ordersCount} fois !\n`;
        confirmMessage += `Recommandation: Désactivez plutôt que supprimez.\n\n`;
    }

    confirmMessage += `Pour confirmer, tapez exactement: SUPPRIMER`;

    const userInput = prompt(confirmMessage);

    if (userInput === 'SUPPRIMER') {
        this.submit();
    } else if (userInput !== null) {
        alert('Suppression annulée. Le texte de confirmation ne correspond pas.');
    }
});
</script>
@endpush
