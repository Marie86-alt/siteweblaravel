@extends('layouts.admin')

@section('title', 'Nouveau produit')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Nouveau produit</h1>
                    <p class="text-muted">Ajouter un nouveau produit à votre catalogue</p>
                </div>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                </a>
            </div>

            <!-- Formulaire -->
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

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
                                                   value="{{ old('name') }}"
                                                   placeholder="Ex: Pommes Golden, Tomates cerises..."
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
                                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            @if($categories->isEmpty())
                                                <div class="form-text text-warning">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    <a href="{{ route('admin.categories.create') }}">Créer une catégorie d'abord</a>
                                                </div>
                                            @endif
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
                                              placeholder="Description détaillée du produit, ses bienfaits, son goût...">{{ old('description') }}</textarea>
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
                                                       value="{{ old('price') }}"
                                                       step="0.01"
                                                       min="0"
                                                       placeholder="0.00"
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
                                            <label for="stock" class="form-label">Stock initial <span class="text-danger">*</span></label>
                                            <input type="number"
                                                   class="form-control @error('stock') is-invalid @enderror"
                                                   id="stock"
                                                   name="stock"
                                                   value="{{ old('stock', 0) }}"
                                                   min="0"
                                                   placeholder="0"
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
                                                <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kilogramme (kg)</option>
                                                <option value="piece" {{ old('unit') == 'piece' ? 'selected' : '' }}>Pièce</option>
                                                <option value="bunch" {{ old('unit') == 'bunch' ? 'selected' : '' }}>Botte</option>
                                                <option value="box" {{ old('unit') == 'box' ? 'selected' : '' }}>Boîte</option>
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
                                    <label for="images" class="form-label">Images du produit</label>
                                    <input type="file"
                                           class="form-control @error('images.*') is-invalid @enderror"
                                           id="images"
                                           name="images[]"
                                           accept="image/*"
                                           multiple>
                                    <div class="form-text">
                                        Formats acceptés: JPG, PNG, WEBP. Taille max: 2MB par image.
                                        Vous pouvez sélectionner plusieurs images.
                                    </div>
                                    @error('images.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Zone de prévisualisation -->
                                <div id="images-preview" class="row" style="display: none;">
                                    <div class="col-12">
                                        <label class="form-label">Aperçu des images</label>
                                        <div id="preview-container" class="row"></div>
                                    </div>
                                </div>
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
                                                   value="{{ old('origin') }}"
                                                   placeholder="France, Espagne, Local..."
                                                   list="origins">
                                            <datalist id="origins">
                                                <option value="France">
                                                <option value="Espagne">
                                                <option value="Italie">
                                                <option value="Local">
                                                <option value="Maroc">
                                                <option value="Pays-Bas">
                                            </datalist>
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
                                                   value="{{ old('season') }}"
                                                   placeholder="Été, Hiver, Toute l'année..."
                                                   list="seasons">
                                            <datalist id="seasons">
                                                <option value="Printemps">
                                                <option value="Été">
                                                <option value="Automne">
                                                <option value="Hiver">
                                                <option value="Toute l'année">
                                            </datalist>
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
                                                   {{ old('is_active', true) ? 'checked' : '' }}>
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
                                                   {{ old('is_organic') ? 'checked' : '' }}>
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
                                                   {{ old('is_featured') ? 'checked' : '' }}>
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
                                    <i class="fas fa-save me-2"></i>Créer le produit
                                </button>
                                <button type="submit" name="create_and_new" value="1" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="fas fa-plus me-2"></i>Créer et nouveau
                                </button>
                                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary w-100">
                                    Annuler
                                </a>
                            </div>
                        </div>

                        <!-- Conseils -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-lightbulb me-2"></i>Conseils
                                </h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0 small">
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Utilisez des noms descriptifs
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Ajoutez plusieurs photos
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Précisez l'origine si possible
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Vérifiez le prix et le stock
                                    </li>
                                    <li class="mb-0">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Marquez les produits bio
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Calculateur de prix -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-calculator me-2"></i>Aide au prix
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <label class="form-label small">Prix d'achat (€)</label>
                                    <input type="number" class="form-control form-control-sm" id="cost_price" step="0.01" placeholder="0.00">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Marge souhaitée (%)</label>
                                    <input type="number" class="form-control form-control-sm" id="margin" value="30" min="0" max="100">
                                </div>
                                <div class="text-center">
                                    <button type="button" class="btn btn-outline-info btn-sm" onclick="calculatePrice()">
                                        <i class="fas fa-calculator me-1"></i>Calculer
                                    </button>
                                </div>
                                <div id="calculated_price" class="mt-2 text-center" style="display: none;">
                                    <strong class="text-success">Prix suggéré: <span id="suggested_price">0.00</span>€</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Prévisualisation des images
document.getElementById('images').addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    const previewDiv = document.getElementById('images-preview');
    const container = document.getElementById('preview-container');

    // Vider le conteneur
    container.innerHTML = '';

    if (files.length > 0) {
        previewDiv.style.display = 'block';

        files.forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-3 col-6 mb-2';
                    col.innerHTML = `
                        <div class="card">
                            <img src="${e.target.result}" class="card-img-top" style="height: 100px; object-fit: cover;">
                            <div class="card-body p-2">
                                <small class="text-muted">Image ${index + 1}</small>
                                ${index === 0 ? '<div class="badge bg-primary">Principale</div>' : ''}
                            </div>
                        </div>
                    `;
                    container.appendChild(col);
                }
                reader.readAsDataURL(file);
            }
        });
    } else {
        previewDiv.style.display = 'none';
    }
});

// Calculateur de prix
function calculatePrice() {
    const costPrice = parseFloat(document.getElementById('cost_price').value) || 0;
    const margin = parseFloat(document.getElementById('margin').value) || 0;

    if (costPrice > 0 && margin > 0) {
        const suggestedPrice = costPrice * (1 + margin / 100);
        document.getElementById('suggested_price').textContent = suggestedPrice.toFixed(2);
        document.getElementById('calculated_price').style.display = 'block';

        // Remplir automatiquement le prix
        const priceInput = document.getElementById('price');
        if (confirm('Utiliser ce prix suggéré ?')) {
            priceInput.value = suggestedPrice.toFixed(2);
        }
    } else {
        alert('Veuillez entrer un prix d\'achat et une marge valides');
    }
}

// Validation en temps réel
document.getElementById('name').addEventListener('input', function() {
    validateForm();
});

document.getElementById('price').addEventListener('input', function() {
    validateForm();
});

document.getElementById('category_id').addEventListener('change', function() {
    validateForm();
});

function validateForm() {
    const name = document.getElementById('name').value.trim();
    const price = parseFloat(document.getElementById('price').value);
    const categoryId = document.getElementById('category_id').value;
    const submitBtn = document.querySelector('button[type="submit"]');

    const isValid = name.length >= 2 && price > 0 && categoryId !== '';

    if (submitBtn) {
        submitBtn.disabled = !isValid;
    }
}

// Focus automatique
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('name').focus();
    validateForm();
});
</script>
@endpush
