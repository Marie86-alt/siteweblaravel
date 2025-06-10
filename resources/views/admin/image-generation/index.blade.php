@extends('layouts.app')

@section('title', 'Génération d\'Images IA - Administration')

@push('styles')
<style>
    .admin-header {
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: white;
        padding: 30px 0;
        margin-bottom: 30px;
    }

    .admin-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .admin-table {
        margin: 0;
    }

    .admin-table th {
        background: #f8f9fa;
        border: none;
        padding: 15px;
        font-weight: 600;
        color: #333;
    }

    .admin-table td {
        padding: 15px;
        vertical-align: middle;
        border-top: 1px solid #eee;
    }

    .product-thumb {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: var(--light-green);
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .status-generated {
        background: #d4edda;
        color: #155724;
    }

    .status-missing {
        background: #f8d7da;
        color: #721c24;
    }

    .action-btn {
        padding: 8px;
        margin: 0 2px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.9rem;
    }

    .btn-generate {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .btn-generate:hover {
        background: #2e7d32;
        color: white;
    }

    .btn-regenerate {
        background: #fff3e0;
        color: #f57c00;
    }

    .btn-regenerate:hover {
        background: #f57c00;
        color: white;
    }

    .stats-cards {
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        transition: all 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        color: var(--primary-green);
        margin-bottom: 5px;
    }

    .stat-label {
        color: #666;
        font-size: 0.9rem;
    }

    .generation-controls {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .ai-icon {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
    }

    .progress-container {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #eee;
        display: none;
    }

    .progress-container.show {
        display: block;
    }
</style>
@endpush

@section('content')
<!-- Header Admin -->
<div class="admin-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="mb-2">
                    <div class="ai-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    Génération d'Images IA
                </h1>
                <p class="mb-0 opacity-75">Générez automatiquement des images professionnelles pour vos produits</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-light me-2">
                    <i class="fas fa-arrow-left me-2"></i>
                    Retour aux produits
                </a>
                <button type="button" class="btn btn-success btn-lg" onclick="openBatchModal()">
                    <i class="fas fa-magic me-2"></i>
                    Génération en lot
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Statistiques -->
    <div class="stats-cards">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-number">{{ $totalProducts }}</div>
                    <div class="stat-label">Total produits</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-number">{{ $productsWithImages }}</div>
                    <div class="stat-label">Avec images</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-number">{{ $productsWithoutImages }}</div>
                    <div class="stat-label">Sans images</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-number">{{ $totalProducts > 0 ? round(($productsWithImages / $totalProducts) * 100) : 0 }}%</div>
                    <div class="stat-label">Couverture</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contrôles de génération -->
    <div class="generation-controls">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="mb-2">
                    <i class="fas fa-cogs me-2"></i>
                    Contrôles de génération
                </h5>
                <p class="mb-0 text-muted">
                    Générez des images professionnelles pour vos produits grâce à l'intelligence artificielle
                </p>
            </div>
            <div class="col-md-4 text-end">
                <button type="button" class="btn btn-primary me-2" onclick="generateMissingImages()">
                    <i class="fas fa-robot me-2"></i>
                    Générer les manquantes
                </button>
                <button type="button" class="btn btn-warning" onclick="regenerateAllImages()">
                    <i class="fas fa-sync me-2"></i>
                    Tout régénérer
                </button>
            </div>
        </div>
    </div>

    <!-- Barre de progression -->
    <div class="progress-container" id="progressContainer">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Génération en cours...</h6>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="cancelGeneration()">
                <i class="fas fa-stop me-1"></i>
                Annuler
            </button>
        </div>
        <div class="progress mb-3">
            <div class="progress-bar progress-bar-striped progress-bar-animated"
                 id="progressBar" role="progressbar" style="width: 0%"></div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="generation-log" id="generationLog" style="max-height: 200px; overflow-y: auto; background: #f8f9fa; border-radius: 8px; padding: 15px; font-family: monospace; font-size: 0.85rem;">
                    <div class="log-entry">Préparation de la génération...</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <div class="spinner-border text-primary mb-2" role="status"></div>
                    <div id="currentProduct">En attente...</div>
                    <small class="text-muted" id="progressText">0 / 0</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des produits -->
    <div class="admin-card">
        <div class="card-header bg-white border-0 p-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">Produits et leurs images</h5>
                </div>
                <div class="col-md-6 text-end">
                    <div class="input-group" style="max-width: 300px; margin-left: auto;">
                        <input type="text" class="form-control" placeholder="Rechercher un produit..." id="searchInput">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th width="80">Aperçu</th>
                        <th>Produit</th>
                        <th>Catégorie</th>
                        <th>Images</th>
                        <th>Statut</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody id="productsTable">
                    @foreach($products as $product)
                        <tr class="product-row" data-product-name="{{ strtolower($product->name) }}">
                            <td>
                                <div class="image-preview">
                                    @if($product->images && count($product->images) > 0)
                                        <img src="{{ asset('storage/products/' . $product->images[0]) }}"
                                             alt="{{ $product->name }}" class="product-thumb">
                                    @else
                                        <div class="product-thumb">
                                            @switch($product->category->name ?? 'default')
                                                @case('Fruits')
                                                @case('Fruits rouges')
                                                @case('Agrumes')
                                                    🍎
                                                    @break
                                                @case('Légumes')
                                                @case('Légumes verts')
                                                @case('Légumes racines')
                                                    🥕
                                                    @break
                                                @case('Herbes aromatiques')
                                                    🌿
                                                    @break
                                                @default
                                                    🥗
                                            @endswitch
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $product->name }}</strong>
                                    @if(str_contains(json_encode($product->images), 'generated_'))
                                        <span class="badge bg-success ms-1">IA</span>
                                    @endif
                                </div>
                                <small class="text-muted">ID: #{{ $product->id }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $product->category->name ?? 'Aucune' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $product->images && count($product->images) > 0 ? 'success' : 'secondary' }}">
                                    {{ $product->images ? count($product->images) : 0 }} image(s)
                                </span>
                            </td>
                            <td>
                                @if($product->images && count($product->images) > 0)
                                    <span class="status-badge status-generated">
                                        <i class="fas fa-check me-1"></i>
                                        Avec image
                                    </span>
                                @else
                                    <span class="status-badge status-missing">
                                        <i class="fas fa-exclamation me-1"></i>
                                        Sans image
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex">
                                    @if(!$product->images || count($product->images) == 0)
                                        <button type="button" class="action-btn btn-generate"
                                                onclick="generateSingleImage({{ $product->id }})"
                                                title="Générer image"
                                                id="btn-generate-{{ $product->id }}">
                                            <i class="fas fa-magic"></i>
                                        </button>
                                    @else
                                        <button type="button" class="action-btn btn-regenerate"
                                                onclick="regenerateSingleImage({{ $product->id }})"
                                                title="Régénérer image"
                                                id="btn-regenerate-{{ $product->id }}">
                                            <i class="fas fa-sync"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                       class="action-btn btn-edit" title="Modifier produit" style="background: #fff3e0; color: #f57c00;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal génération en lot -->
<div class="modal fade" id="batchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-magic me-2"></i>
                    Génération en lot
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="batchForm">
                    <div class="mb-3">
                        <label class="form-label">Nombre de produits à traiter</label>
                        <select class="form-select" name="limit">
                            <option value="5">5 produits</option>
                            <option value="10" selected>10 produits</option>
                            <option value="20">20 produits</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="force" id="forceRegenerate">
                            <label class="form-check-label" for="forceRegenerate">
                                Régénérer même si des images existent
                            </label>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        La génération peut prendre plusieurs minutes selon le nombre de produits.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success" onclick="startBatchGeneration()">
                    <i class="fas fa-magic me-2"></i>
                    Commencer la génération
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let generationInProgress = false;

    // Recherche en temps réel
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('.product-row');

        rows.forEach(row => {
            const productName = row.dataset.productName;
            if (productName.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Ouvrir modal génération en lot
    function openBatchModal() {
        new bootstrap.Modal(document.getElementById('batchModal')).show();
    }

    // Générer image unique
    async function generateSingleImage(productId) {
        const btn = document.getElementById(`btn-generate-${productId}`);
        const originalContent = btn.innerHTML;

        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        try {
            const response = await fetch(`/admin/image-generation/generate/${productId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                showToast('Succès', `Image générée pour le produit #${productId}`, 'success');
                setTimeout(() => location.reload(), 2000);
            } else {
                showToast('Erreur', data.message, 'error');
            }
        } catch (error) {
            showToast('Erreur', 'Erreur technique lors de la génération', 'error');
            console.error('Erreur:', error);
        } finally {
            btn.innerHTML = originalContent;
            btn.disabled = false;
        }
    }

    // Régénérer image unique
    async function regenerateSingleImage(productId) {
        if (!confirm('Êtes-vous sûr de vouloir régénérer cette image ?')) {
            return;
        }

        const btn = document.getElementById(`btn-regenerate-${productId}`);
        const originalContent = btn.innerHTML;

        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        try {
            const response = await fetch(`/admin/image-generation/regenerate/${productId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                showToast('Succès', `Image régénérée pour le produit #${productId}`, 'success');
                setTimeout(() => location.reload(), 2000);
            } else {
                showToast('Erreur', data.message, 'error');
            }
        } catch (error) {
            showToast('Erreur', 'Erreur technique lors de la régénération', 'error');
            console.error('Erreur:', error);
        } finally {
            btn.innerHTML = originalContent;
            btn.disabled = false;
        }
    }

    // Génération en lot
    async function startBatchGeneration() {
        const form = document.getElementById('batchForm');
        const formData = new FormData(form);

        // Fermer la modal
        bootstrap.Modal.getInstance(document.getElementById('batchModal')).hide();

        // Afficher la barre de progression
        document.getElementById('progressContainer').classList.add('show');
        generationInProgress = true;

        try {
            const response = await fetch('/admin/image-generation/batch', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showGenerationProgress(data.results);
                showToast('Succès', data.message, 'success');
            } else {
                showToast('Erreur', data.message, 'error');
            }
        } catch (error) {
            showToast('Erreur', 'Erreur technique lors de la génération en lot', 'error');
            console.error('Erreur:', error);
        } finally {
            generationInProgress = false;
            setTimeout(() => {
                document.getElementById('progressContainer').classList.remove('show');
            }, 3000);
        }
    }

    // Afficher le progrès de génération
    function showGenerationProgress(results) {
        const progressBar = document.getElementById('progressBar');
        const currentProduct = document.getElementById('currentProduct');
        const progressText = document.getElementById('progressText');
        const generationLog = document.getElementById('generationLog');

        let completed = 0;
        const total = results.length;

        results.forEach((result, index) => {
            setTimeout(() => {
                completed++;
                const progress = (completed / total) * 100;

                progressBar.style.width = `${progress}%`;
                currentProduct.textContent = result.product_name;
                progressText.textContent = `${completed} / ${total}`;

                const icon = result.success ? '✓' : '✗';
                const message = result.success ? 'Généré avec succès' : 'Échec de génération';

                generationLog.innerHTML += `<div class="log-entry">${icon} ${result.product_name}: ${message}</div>`;
                generationLog.scrollTop = generationLog.scrollHeight;

                if (completed === total) {
                    currentProduct.textContent = 'Terminé !';
                    setTimeout(() => location.reload(), 2000);
                }
            }, index * 2000);
        });
    }

    // Générer les images manquantes
    function generateMissingImages() {
        if (confirm('Générer des images pour tous les produits qui n\'en ont pas ?')) {
            startBatchGeneration();
        }
    }

    // Régénérer toutes les images
    function regenerateAllImages() {
        if (confirm('Attention ! Cela va régénérer TOUTES les images. Continuer ?')) {
            document.getElementById('forceRegenerate').checked = true;
            startBatchGeneration();
        }
    }

    // Annuler génération
    function cancelGeneration() {
        if (generationInProgress && confirm('Voulez-vous vraiment annuler la génération en cours ?')) {
            generationInProgress = false;
            document.getElementById('progressContainer').classList.remove('show');
            showToast('Info', 'Génération annulée', 'info');
        }
    }

    // Système de notifications toast
    function showToast(title, message, type = 'info') {
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <strong>${title}:</strong> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        let toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toastContainer';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }

        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        const toastElement = toastContainer.lastElementChild;
        const toast = new bootstrap.Toast(toastElement);
        toast.show();

        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }
</script>
@endpush
