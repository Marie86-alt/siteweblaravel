@extends('layouts.admin')

@section('title', 'Nouvelle catégorie')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Nouvelle catégorie</h1>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                </a>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informations de la catégorie</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <!-- Nom -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom de la catégorie <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name') }}"
                                           placeholder="Ex: Fruits, Légumes, Agrumes..."
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
                                              placeholder="Description de la catégorie...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Image -->
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
                                               {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Catégorie active (visible sur le site)
                                        </label>
                                    </div>
                                </div>

                                <!-- Boutons -->
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Créer la catégorie
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
                <div class="col-md-4">
                    <!-- Aperçu image -->
                    <div class="card mb-3" id="preview-card" style="display: none;">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Aperçu de l'image</h6>
                        </div>
                        <div class="card-body text-center">
                            <img id="image-preview" src="" alt="Aperçu" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                    </div>

                    <!-- Génération IA -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-wand-magic-sparkles me-2"></i>Génération IA
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                Vous pourrez générer une image IA après avoir créé la catégorie.
                            </p>

                            <div class="mb-2">
                                <label class="form-label small">Style suggéré :</label>
                                <select class="form-select form-select-sm" disabled>
                                    <option>Professionnel</option>
                                    <option>Artistique</option>
                                    <option>Naturel</option>
                                    <option>Moderne</option>
                                </select>
                            </div>

                            <button type="button" class="btn btn-outline-success btn-sm w-100" disabled>
                                <i class="fas fa-magic me-1"></i>Générer après création
                            </button>
                        </div>
                    </div>

                    <!-- Conseils -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-lightbulb me-2"></i>Conseils
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0 small">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Utilisez des noms courts et clairs
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Décrivez l'utilité de la catégorie
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    L'image peut être ajoutée plus tard
                                </li>
                                <li class="mb-0">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Utilisez la génération IA pour l'image
                                </li>
                            </ul>
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
    const previewCard = document.getElementById('preview-card');
    const preview = document.getElementById('image-preview');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewCard.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        previewCard.style.display = 'none';
    }
});

// Focus automatique sur le nom
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('name').focus();
});
</script>
@endpush
