@extends('layouts.admin')

@section('title', 'Modifier la catégorie')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Modifier la catégorie</h1>
                    <p class="text-muted">{{ $category->name }}</p>
                </div>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                </a>
            </div>

            <!-- Formulaire -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informations de la catégorie</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <!-- Nom -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom de la catégorie <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name', $category->name) }}"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description"
                                              name="description"
                                              rows="4"
                                              placeholder="Description de la catégorie...">{{ old('description', $category->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Upload d'image -->
                                <div class="mb-3">
                                    <label for="image" class="form-label">Image de la catégorie</label>
                                    <input type="file"
                                           class="form-control @error('image') is-invalid @enderror"
                                           id="image"
                                           name="image"
                                           accept="image/*">
                                    <div class="form-text">Formats acceptés: JPG, PNG, WEBP. Taille max: 2MB</div>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Statut -->
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="is_active"
                                               name="is_active"
                                               value="1"
                                               {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Catégorie active
                                        </label>
                                    </div>
                                </div>

                                <!-- Boutons -->
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Enregistrer les modifications
                                    </button>
                                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                                        Annuler
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Image actuelle -->
                    @if($category->image)
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Image actuelle</h6>
                        </div>
                        <div class="card-body text-center">
                            <img src="{{ asset('storage/' . $category->image) }}"
                                 alt="{{ $category->name }}"
                                 class="img-fluid rounded"
                                 style="max-height: 200px;">

                            @if($category->ai_generated)
                                <div class="mt-2">
                                    <span class="badge bg-info">
                                        <i class="fas fa-robot me-1"></i>Générée par IA
                                    </span>
                                </div>
                                @if($category->ai_prompt)
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <strong>Prompt:</strong> {{ $category->ai_prompt }}
                                        </small>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Actions IA -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Actions IA</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.categories.generate-image', $category) }}" method="POST" class="mb-2">
                                @csrf
                                <div class="mb-2">
                                    <label for="style" class="form-label">Style d'image</label>
                                    <select name="style" id="style" class="form-select form-select-sm">
                                        <option value="professional">Professionnel</option>
                                        <option value="artistic">Artistique</option>
                                        <option value="natural">Naturel</option>
                                        <option value="modern">Moderne</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    <i class="fas fa-wand-magic-sparkles me-1"></i>Générer une image IA
                                </button>
                            </form>

                            @if($category->image && $category->ai_generated)
                                <form action="{{ route('admin.categories.delete-image', $category) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                            onclick="return confirm('Supprimer cette image ?')">
                                        <i class="fas fa-trash me-1"></i>Supprimer l'image
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Statistiques</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h4 class="mb-0 text-primary">{{ $category->products_count ?? 0 }}</h4>
                                        <small class="text-muted">Produits</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4 class="mb-0 text-success">{{ $category->orders_count ?? 0 }}</h4>
                                    <small class="text-muted">Commandes</small>
                                </div>
                            </div>

                            <div class="mt-3">
                                <small class="text-muted">
                                    Créée le {{ $category->created_at->format('d/m/Y') }}
                                </small>
                            </div>
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
// Prévisualisation de l'image
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Créer ou mettre à jour l'aperçu
            let preview = document.getElementById('image-preview');
            if (!preview) {
                preview = document.createElement('img');
                preview.id = 'image-preview';
                preview.className = 'img-fluid rounded mt-2';
                preview.style.maxHeight = '150px';
                e.target.parentNode.appendChild(preview);
            }
            preview.src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
