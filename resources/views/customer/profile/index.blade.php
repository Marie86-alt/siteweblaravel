@extends('customer.layout')


@section('customer-content')
<div class="customer-header">
    <h1 class="customer-title">
        <i class="fas fa-user me-3"></i>
        Mon Profil
    </h1>
    <p class="customer-subtitle">
        Gérez vos informations personnelles et paramètres de sécurité.
    </p>
</div>

<div class="row">
    <!-- Informations personnelles -->
    <div class="col-lg-8">
        <div class="profile-section">
            <h3 class="section-title">
                <i class="fas fa-user-edit me-2"></i>
                Informations personnelles
            </h3>

            @if(session('profile_success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('profile_success') }}
                </div>
            @endif

            <form action="{{ route('customer.profile.update') }}" method="POST" class="profile-form">
                @csrf
                @method('PUT')

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Prénom *</label>
                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                               value="{{ old('first_name', $user->first_name) }}" required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nom *</label>
                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                               value="{{ old('last_name', $user->last_name) }}" required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Téléphone</label>
                        <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone', $user->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date de naissance</label>
                        <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror"
                               value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}">
                        @error('birth_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Entreprise</label>
                    <input type="text" name="company" class="form-control @error('company') is-invalid @enderror"
                           value="{{ old('company', $user->company) }}">
                    @error('company')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        Sauvegarder les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Avatar et statistiques -->
    <div class="col-lg-4">
        <div class="profile-sidebar">
            <!-- Avatar -->
            <div class="avatar-section">
                <div class="avatar-large">
                    {{ $user->initials }}
                </div>
                <h4>{{ $user->full_name ?: $user->name }}</h4>
                <p class="text-muted">Membre depuis {{ $user->created_at->format('M Y') }}</p>
                <button class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-camera me-2"></i>
                    Changer la photo
                </button>
            </div>

            <!-- Statistiques -->
            <div class="stats-section">
                <h5>Mes statistiques</h5>
                <div class="stat-item">
                    <i class="fas fa-shopping-bag"></i>
                    <span>{{ $user->total_orders }} commandes</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-euro-sign"></i>
                    <span>{{ number_format($user->total_spent, 2) }}€ dépensés</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-calendar"></i>
                    <span>Dernière commande : {{ $user->orders()->latest()->first()?->created_at->diffForHumans() ?? 'Jamais' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Changement de mot de passe -->
<div class="row mt-4">
    <div class="col-lg-8">
        <div class="profile-section">
            <h3 class="section-title">
                <i class="fas fa-lock me-2"></i>
                Changer le mot de passe
            </h3>

            @if(session('password_success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('password_success') }}
                </div>
            @endif

            <form action="{{ route('customer.password.change') }}" method="POST" class="password-form">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Mot de passe actuel *</label>
                    <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                    @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nouveau mot de passe *</label>
                        <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" required>
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirmer le mot de passe *</label>
                        <input type="password" name="new_password_confirmation" class="form-control" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-key me-2"></i>
                        Changer le mot de passe
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .profile-section {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
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

    .profile-form, .password-form {
        max-width: 100%;
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

    .form-actions {
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #f8f9fa;
    }

    .profile-sidebar {
        position: sticky;
        top: 120px;
    }

    .avatar-section {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        text-align: center;
        margin-bottom: 2rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .avatar-large {
        width: 120px;
        height: 120px;
        background: var(--primary-green);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        color: var(--dark-green);
        font-size: 3rem;
        font-weight: bold;
    }

    .stats-section {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .stats-section h5 {
        margin-bottom: 1.5rem;
        color: #333;
        font-weight: 600;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid #f8f9fa;
    }

    .stat-item:last-child {
        border-bottom: none;
    }

    .stat-item i {
        width: 20px;
        color: var(--primary-green);
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .profile-sidebar {
            position: static;
        }
    }
</style>
@endpush
@endsection
