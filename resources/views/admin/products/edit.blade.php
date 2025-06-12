@extends('layouts.admin')

@section('title', 'Modifier le produit')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Modifier le produit</h1>
                    <p class="text-muted">{{ $product->name }}</p>
                </div>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                </a>
            </div>

            <!-- Formulaire -->
            <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-lg-8">
                        <!-- Informations principales -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Informations principales</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <!-- Nom -->
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Nom du produit <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   class="form-control @error('name') is-invalid @enderror"
                                                   id="name"
                                                   name="name"
                                                   value="{{ old('name', $product->name) }}"
                                                   required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <!-- Catégorie -->
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Catégorie <span class="text-danger">*</span></label>
                                            <select class="form-select @error('category_id') is-invalid @enderror"
                                                    id="category_id"
                                                    name="category_id"
                                                    required>
                                                <option value="">Sélectionner une catégorie</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description"
                                              name="description"
                                              rows="4"
                                              placeholder="Description détaillée du produit...">{{ old('description', $product->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Prix et Stock -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Prix (€) <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number"
                                                       class="form-control @error('price') is-invalid @enderror"
                                                       id="price"
                                                       name="price"
                                                       value="{{ old('price', $product->price) }}"
                                                       step="0.01"
                                                       min="0"
                                                       required>
                                                <span class="input-group-text">€</span>
                                                @error('price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="stock" class="form-label">Stock <span class="text-danger">*</span></label>
                                            <input type="number"
                                                   class="form-control @error('stock') is-invalid @enderror"
                                                   id="stock"
                                                   name="stock"
                                                   value="{{ old('stock', $product->stock) }}"
                                                   min="0"
                                                   required>
                                            @error('stock')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="unit" class="form-label">Unité</label>
                                            <select class="form-select @error('unit') is-invalid @enderror"
                                                    id="unit"
                                                    name="unit">
                                                <option value="kg" {{ old('unit', $product->unit) == 'kg' ? 'selected' : '' }}>Kilogramme (kg)</option>
                                                <option value="piece" {{ old('unit', $product->unit) == 'piece' ? 'selected' : '' }}>Pièce</option>
                                                <option value="bunch" {{ old('unit', $product->unit) == 'bunch' ? 'selected' : '' }}>Botte</option>
                                                <option value="box" {{ old('unit', $product->unit) == 'box' ? 'selected' : '' }}>Boîte</option>
                                            </select>
                                            @error('unit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Images -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Images du produit</h5>
                            </div>
                            <div class="card-body">
                                <!-- Upload d'images -->
                                <div class="mb-3">
                                    <label for="images" class="form-label">Ajouter des images</label>
                                    <input type="file"
                                           class="form-control @error('images.*') is-invalid @enderror"
                                           id="images"
                                           name="images[]"
                                           accept="image/*"
                                           multiple>
                                    <div class="form-text">Formats acceptés: JPG, PNG, WEBP. Taille max: 2MB par image</div>
                                    @error('images.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Images existantes -->
                                @if($product->images && count($product->images) > 0)
                                    <div class="mb-3">
                                        <label class="form-label">Images actuelles</label>
                                        <div class="row" id="existing-images">
                                            @foreach($product->images as $index => $image)
                                                <div class="col-md-3 mb-2" data-image-index="{{ $index }}">
                                                    <div class="card">
                                                        <img src="{{ asset('storage/' . $image) }}"
                                                             class="card-img-top"
                                                             style="height: 120px; object-fit: cover;">
                                                        <div class="card-body p-2">
                                                            <div class="form-check mb-1">
                                                                <input class="form-check-input"
                                                                       type="radio"
                                                                       name="main_image_index"
                                                                       value="{{ $index }}"
                                                                       {{ $index == 0 ? 'checked' : '' }}>
                                                                <label class="form-check-label small">
                                                                    Image principale
                                                                </label>
                                                            </div>
                                                            <button type="button"
                                                                    class="btn btn-outline-danger btn-sm w-100"
                                                                    onclick="removeImage({{ $index }})">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                            <input type="hidden" name="existing_images[]" value="{{ $image }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Options avancées -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Options avancées</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- Origine -->
                                        <div class="mb-3">
                                            <label for="origin" class="form-label">Origine</label>
                                            <input type="text"
                                                   class="form-control @error('origin') is-invalid @enderror"
                                                   id="origin"
                                                   name="origin"
                                                   value="{{ old('origin', $product->origin) }}"
                                                   placeholder="France, Espagne, Local...">
                                            @error('origin')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Saison -->
                                        <div class="mb-3">
                                            <label for="season" class="form-label">Saison</label>
                                            <input type="text"
                                                   class="form-control @error('season') is-invalid @enderror"
                                                   id="season"
                                                   name="season"
                                                   value="{{ old('season', $product->season) }}"
                                                   placeholder="Été, Hiver, Toute l'année...">
                                            @error('season')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Statuts -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   id="is_active"
                                                   name="is_active"
                                                   value="1"
                                                   {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Produit actif
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   id="is_organic"
                                                   name="is_organic"
                                                   value="1"
                                                   {{ old('is_organic', $product->is_organic) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_organic">
                                                Produit bio
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   id="is_featured"
                                                   name="is_featured"
                                                   value="1"
                                                   {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_featured">
                                                Produit vedette
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Actions -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Actions</h6>
                            </div>
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-save me-2"></i>Enregistrer les modifications
                                </button>
                                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary w-100 mb-2">
                                    Annuler
                                </a>
                                <a href="{{ route('admin.products.show', $product) }}" class="btn btn-outline-info w-100">
                                    <i class="fas fa-eye me-2"></i>Voir le produit
                                </a>
                            </div>
                        </div>

                        <!-- Statistiques -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Statistiques</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h5 class="mb-0 text-success">{{ $product->orders_count ?? 0 }}</h5>
                                            <small class="text-muted">Commandes</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="mb-0 text-info">{{ $product->views_count ?? 0 }}</h5>
                                        <small class="text-muted">Vues</small>
                                    </div>
                                </div>

                                <hr>

                                <div class="small text-muted">
                                    <div class="d-flex justify-content-between">
                                        <span>Créé le:</span>
                                        <span>{{ $product->created_at->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Modifié le:</span>
                                        <span>{{ $product->updated_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Alert -->
                        @if($product->stock < 10)
                            <div class="card border-warning mb-3">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Stock faible
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-2">Stock actuel: <strong>{{ $product->stock }}</strong></p>
                                    <p class="mb-0 small text-muted">
                                        Pensez à réapprovisionner ce produit.
                                    </p>
                                </div>
                            </div>
                        @endif

                        <!-- Image principale actuelle -->
                        @if($product->images && count($product->images) > 0)
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Image principale</h6>
                                </div>
                                <div class="card-body text-center">
                                    <img src="{{ asset('storage/' . $product->images[0]) }}"
                                         alt="{{ $product->name }}"
                                         class="img-fluid rounded"
                                         style="max-height: 200px;">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Prévisualisation des nouvelles images
document.getElementById('images').addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    const previewContainer = document.getElementById('image-preview');

    // Créer le conteneur de prévisualisation s'il n'existe pas
    if (!previewContainer) {
        const newContainer = document.createElement('div');
        newContainer.id = 'image-preview';
        newContainer.className = 'row mt-3';
        e.target.parentNode.appendChild(newContainer);
    }

    // Vider le conteneur
    document.getElementById('image-preview').innerHTML = '';

    files.forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 mb-2';
                col.innerHTML = `
                    <div class="card">
                        <img src="${e.target.result}" class="card-img-top" style="height: 120px; object-fit: cover;">
                        <div class="card-body p-2">
                            <small class="text-muted">Nouvelle image ${index + 1}</small>
                        </div>
                    </div>
                `;
                document.getElementById('image-preview').appendChild(col);
            }
            reader.readAsDataURL(file);
        }
    });
});

// Supprimer une image existante
function removeImage(index) {
    if (confirm('Supprimer cette image ?')) {
        const imageDiv = document.querySelector(`[data-image-index="${index}"]`);
        if (imageDiv) {
            imageDiv.remove();
        }
    }
}

// Validation côté client
document.querySelector('form').addEventListener('submit', function(e) {
    const price = parseFloat(document.getElementById('price').value);
    const stock = parseInt(document.getElementById('stock').value);

    if (price <= 0) {
        alert('Le prix doit être supérieur à 0');
        e.preventDefault();
        return;
    }

    if (stock < 0) {
        alert('Le stock ne peut pas être négatif');
        e.preventDefault();
        return;
    }
});
</script>
@endpush
