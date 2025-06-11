
@extends('customer.layout')

@section('customer-content')
<div class="customer-header">
    <h1 class="customer-title">
        <i class="fas fa-map-marker-alt me-3"></i>
        Mes Adresses
    </h1>
    <p class="customer-subtitle">
        Gérez vos adresses de livraison et de facturation pour faciliter vos commandes.
    </p>
</div>

@if(session('address_success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('address_success') }}
    </div>
@endif

<div class="row">
    <!-- Adresse de facturation -->
    <div class="col-lg-6">
        <div class="address-section">
            <h3 class="section-title">
                <i class="fas fa-file-invoice me-2"></i>
                Adresse de facturation
            </h3>

            <form action="{{ route('customer.addresses.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Adresse complète *</label>
                    <textarea name="billing_address" class="form-control @error('billing_address') is-invalid @enderror"
                              rows="3" placeholder="Numéro, rue, appartement...">{{ old('billing_address', $user->billing_address) }}</textarea>
                    @error('billing_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Ville *</label>
                        <input type="text" name="billing_city" class="form-control @error('billing_city') is-invalid @enderror"
                               value="{{ old('billing_city', $user->billing_city) }}" placeholder="Paris">
                        @error('billing_city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Code postal *</label>
                        <input type="text" name="billing_postal_code" class="form-control @error('billing_postal_code') is-invalid @enderror"
                               value="{{ old('billing_postal_code', $user->billing_postal_code) }}" placeholder="75001">
                        @error('billing_postal_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Pays</label>
                    <select name="billing_country" class="form-control @error('billing_country') is-invalid @enderror">
                        <option value="France" {{ old('billing_country', $user->billing_country) == 'France' ? 'selected' : '' }}>France</option>
                        <option value="Belgique" {{ old('billing_country', $user->billing_country) == 'Belgique' ? 'selected' : '' }}>Belgique</option>
                        <option value="Suisse" {{ old('billing_country', $user->billing_country) == 'Suisse' ? 'selected' : '' }}>Suisse</option>
                        <option value="Luxembourg" {{ old('billing_country', $user->billing_country) == 'Luxembourg' ? 'selected' : '' }}>Luxembourg</option>
                        <option value="Autre" {{ old('billing_country', $user->billing_country) == 'Autre' ? 'selected' : '' }}>Autre</option>
                    </select>
                    @error('billing_country')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Prévisualisation adresse de facturation -->
                @if($user->billing_address)
                <div class="address-preview">
                    <h5>Aperçu de l'adresse :</h5>
                    <div class="preview-card">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <span>
                            {{ $user->billing_address }}<br>
                            {{ $user->billing_postal_code }} {{ $user->billing_city }}<br>
                            {{ $user->billing_country }}
                        </span>
                    </div>
                </div>
                @endif

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        Sauvegarder l'adresse de facturation
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Adresse de livraison -->
    <div class="col-lg-6">
        <div class="address-section">
            <h3 class="section-title">
                <i class="fas fa-truck me-2"></i>
                Adresse de livraison
            </h3>

            <form action="{{ route('customer.addresses.update') }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Option copier depuis facturation -->
                <div class="copy-option">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="copyBilling" onchange="copyBillingAddress()">
                        <label class="form-check-label" for="copyBilling">
                            <i class="fas fa-copy me-2"></i>
                            Utiliser la même adresse que la facturation
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Adresse complète</label>
                    <textarea name="delivery_address" id="delivery_address" class="form-control @error('delivery_address') is-invalid @enderror"
                              rows="3" placeholder="Numéro, rue, appartement...">{{ old('delivery_address', $user->delivery_address) }}</textarea>
                    @error('delivery_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Ville</label>
                        <input type="text" name="delivery_city" id="delivery_city" class="form-control @error('delivery_city') is-invalid @enderror"
                               value="{{ old('delivery_city', $user->delivery_city) }}" placeholder="Paris">
                        @error('delivery_city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Code postal</label>
                        <input type="text" name="delivery_postal_code" id="delivery_postal_code" class="form-control @error('delivery_postal_code') is-invalid @enderror"
                               value="{{ old('delivery_postal_code', $user->delivery_postal_code) }}" placeholder="75001">
                        @error('delivery_postal_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Pays</label>
                    <select name="delivery_country" id="delivery_country" class="form-control @error('delivery_country') is-invalid @enderror">
                        <option value="France" {{ old('delivery_country', $user->delivery_country) == 'France' ? 'selected' : '' }}>France</option>
                        <option value="Belgique" {{ old('delivery_country', $user->delivery_country) == 'Belgique' ? 'selected' : '' }}>Belgique</option>
                        <option value="Suisse" {{ old('delivery_country', $user->delivery_country) == 'Suisse' ? 'selected' : '' }}>Suisse</option>
                        <option value="Luxembourg" {{ old('delivery_country', $user->delivery_country) == 'Luxembourg' ? 'selected' : '' }}>Luxembourg</option>
                        <option value="Autre" {{ old('delivery_country', $user->delivery_country) == 'Autre' ? 'selected' : '' }}>Autre</option>
                    </select>
                    @error('delivery_country')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Instructions de livraison -->
                <div class="form-group">
                    <label class="form-label">Instructions de livraison (optionnel)</label>
                    <textarea name="delivery_instructions" class="form-control"
                              rows="2" placeholder="Code d'accès, digicode, étage, instructions spéciales...">{{ old('delivery_instructions', $user->delivery_instructions ?? '') }}</textarea>
                    <small class="form-text text-muted">Ces informations aideront le livreur à vous trouver plus facilement.</small>
                </div>

                <!-- Prévisualisation adresse de livraison -->
                @if($user->delivery_address)
                <div class="address-preview">
                    <h5>Aperçu de l'adresse :</h5>
                    <div class="preview-card">
                        <i class="fas fa-truck me-2"></i>
                        <span>
                            {{ $user->delivery_address }}<br>
                            {{ $user->delivery_postal_code }} {{ $user->delivery_city }}<br>
                            {{ $user->delivery_country }}
                        </span>
                    </div>
                </div>
                @endif

                <div class="form-actions">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>
                        Sauvegarder l'adresse de livraison
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Aide et informations -->
<div class="row mt-4">
    <div class="col-12">
        <div class="help-section">
            <h4><i class="fas fa-info-circle me-2"></i>Informations utiles</h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="help-item">
                        <i class="fas fa-clock"></i>
                        <h5>Horaires de livraison</h5>
                        <p>Livraisons du lundi au vendredi de 8h à 18h</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="help-item">
                        <i class="fas fa-map"></i>
                        <h5>Zones de livraison</h5>
                        <p>Livraison en France métropolitaine et pays limitrophes</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="help-item">
                        <i class="fas fa-phone"></i>
                        <h5>Besoin d'aide ?</h5>
                        <p>Contactez-nous au 01 23 45 67 89</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .address-section {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        height: fit-content;
    }

    .section-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f8f9fa;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

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

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }

    .copy-option {
        margin-bottom: 2rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 10px;
    }

    .form-check-input:checked {
        background-color: var(--primary-green);
        border-color: var(--primary-green);
    }

    .form-check-label {
        font-weight: 500;
        color: #333;
    }

    .address-preview {
        margin: 2rem 0;
        padding: 1.5rem;
        background: rgba(120, 230, 166, 0.1);
        border-radius: 10px;
        border-left: 4px solid var(--primary-green);
    }

    .address-preview h5 {
        margin-bottom: 1rem;
        color: var(--dark-green);
        font-weight: 600;
    }

    .preview-card {
        background: white;
        padding: 1rem;
        border-radius: 8px;
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .preview-card i {
        color: var(--primary-green);
        margin-top: 0.2rem;
    }

    .form-actions {
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #f8f9fa;
    }

    .help-section {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .help-section h4 {
        margin-bottom: 2rem;
        color: #333;
        font-weight: 600;
    }

    .help-item {
        text-align: center;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 10px;
        height: 100%;
    }

    .help-item i {
        font-size: 2.5rem;
        color: var(--primary-green);
        margin-bottom: 1rem;
    }

    .help-item h5 {
        font-weight: 600;
        margin-bottom: 1rem;
        color: #333;
    }

    .help-item p {
        color: #666;
        margin: 0;
        font-size: 0.9rem;
    }

    .form-text {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.5rem;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .address-section {
            padding: 1.5rem;
        }

        .help-section {
            padding: 1.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
function copyBillingAddress() {
    const checkbox = document.getElementById('copyBilling');

    if (checkbox.checked) {
        // Copier les valeurs de facturation vers livraison
        document.getElementById('delivery_address').value = document.querySelector('textarea[name="billing_address"]').value;
        document.getElementById('delivery_city').value = document.querySelector('input[name="billing_city"]').value;
        document.getElementById('delivery_postal_code').value = document.querySelector('input[name="billing_postal_code"]').value;
        document.getElementById('delivery_country').value = document.querySelector('select[name="billing_country"]').value;

        // Désactiver les champs
        document.getElementById('delivery_address').disabled = true;
        document.getElementById('delivery_city').disabled = true;
        document.getElementById('delivery_postal_code').disabled = true;
        document.getElementById('delivery_country').disabled = true;
    } else {
        // Réactiver les champs
        document.getElementById('delivery_address').disabled = false;
        document.getElementById('delivery_city').disabled = false;
        document.getElementById('delivery_postal_code').disabled = false;
        document.getElementById('delivery_country').disabled = false;
    }
}

// Validation côté client
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('input[required], textarea[required], select[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires.');
            }
        });
    });
});
</script>
@endpush
@endsection
