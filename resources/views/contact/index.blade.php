@extends('layouts.app')

@section('title', 'Nous Contacter - Fruits & Légumes en Gros')
@section('description', 'Contactez-nous pour vos commandes en gros, devis personnalisés et informations. Service client dédié aux professionnels.')

@push('styles')
<style>
    :root {
        --primary-green: rgb(120, 230, 166);
        --secondary-green: #2ecc71;
        --dark-green: rgb(62, 109, 82);
        --light-green: #a9dfbf;
        --orange: #f39c12;
        --red: #e74c3c;
    }

    /* === PAGE HEADER === */
    .contact-header {
        background: linear-gradient(135deg, rgba(62, 109, 82, 0.9), rgba(46, 204, 113, 0.85)),
                    url('https://images.unsplash.com/photo-1521791136064-7986c2920216?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2069&q=80');
        background-size: cover;
        background-position: center;
        padding: 6rem 0 4rem;
        color: white;
        text-align: center;
        position: relative;
    }

    .contact-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.4);
    }

    .contact-header .container {
        position: relative;
        z-index: 2;
    }

    .contact-title {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .contact-subtitle {
        font-size: 1.2rem;
        opacity: 0.95;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
    }

    /* === CONTACT SECTION === */
    .contact-section {
        padding: 5rem 0;
        background: #fafafa;
    }

    .contact-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        align-items: start;
    }

    /* === CONTACT INFO === */
    .contact-info {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .contact-info h3 {
        font-size: 1.8rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 1.5rem;
    }

    .contact-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 2rem;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 15px;
        transition: all 0.3s ease;
    }

    .contact-item:hover {
        background: rgba(120, 230, 166, 0.1);
        transform: translateY(-2px);
    }

    .contact-icon {
        width: 50px;
        height: 50px;
        background: var(--primary-green);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .contact-details h4 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .contact-details p {
        color: #666;
        margin: 0;
        line-height: 1.5;
    }

    .contact-details a {
        color: var(--dark-green);
        text-decoration: none;
        font-weight: 500;
    }

    .contact-details a:hover {
        color: var(--primary-green);
    }

    /* === CONTACT FORM === */
    .contact-form {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .contact-form h3 {
        font-size: 1.8rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 1.5rem;
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
        background: #f8f9fa;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-green);
        background: white;
        box-shadow: 0 0 0 3px rgba(120, 230, 166, 0.2);
    }

    .form-control.textarea {
        resize: vertical;
        min-height: 120px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .btn-submit {
        background: var(--primary-green);
        color: var(--dark-green);
        border: none;
        padding: 15px 40px;
        border-radius: 25px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
        justify-content: center;
        width: 100%;
    }

    .btn-submit:hover {
        background: var(--dark-green);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(62, 109, 82, 0.3);
    }

    .btn-submit:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
    }

    /* === BUSINESS HOURS === */
    .business-hours {
        background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
        border-radius: 15px;
        padding: 2rem;
        margin-top: 2rem;
        color: var(--dark-green);
    }

    .business-hours h4 {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 1rem;
        text-align: center;
    }

    .hours-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .hours-item {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(62, 109, 82, 0.3);
    }

    .hours-item:last-child {
        border-bottom: none;
    }

    .hours-day {
        font-weight: 500;
    }

    .hours-time {
        font-weight: 600;
    }

    /* === MAP SECTION === */
    .map-section {
        padding: 3rem 0;
        background: white;
    }

    .map-container {
        background: #f8f9fa;
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        min-height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .map-placeholder {
        background: linear-gradient(135deg, var(--light-green), var(--primary-green));
        color: var(--dark-green);
        padding: 3rem;
        border-radius: 15px;
        max-width: 500px;
    }

    .map-placeholder h4 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .map-placeholder p {
        margin-bottom: 1.5rem;
        line-height: 1.6;
    }

    .btn-directions {
        background: var(--dark-green);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 25px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .btn-directions:hover {
        background: #333;
        color: white;
        transform: translateY(-2px);
    }

    /* === FAQ SECTION === */
    .faq-section {
        padding: 4rem 0;
        background: #fafafa;
    }

    .faq-title {
        text-align: center;
        font-size: 2.5rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 3rem;
    }

    .faq-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 2rem;
    }

    .faq-item {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }

    .faq-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    }

    .faq-question {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--dark-green);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .faq-answer {
        color: #666;
        line-height: 1.6;
    }

    /* === RESPONSIVE === */
    @media (max-width: 768px) {
        .contact-title {
            font-size: 2.2rem;
        }

        .contact-grid {
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .contact-info,
        .contact-form {
            padding: 2rem;
        }

        .faq-grid {
            grid-template-columns: 1fr;
        }
    }

    /* === ANIMATIONS === */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }

    .success-message {
        background: rgba(120, 230, 166, 0.1);
        border: 2px solid var(--primary-green);
        color: var(--dark-green);
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1rem;
        text-align: center;
        font-weight: 600;
    }

    .error-message {
        background: rgba(231, 76, 60, 0.1);
        border: 2px solid var(--red);
        color: #721c24;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1rem;
        text-align: center;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<!-- Contact Header -->
<section class="contact-header">
    <div class="container">
        <h1 class="contact-title">Nous Contacter</h1>
        <p class="contact-subtitle">
            Notre équipe commerciale est à votre disposition pour tous vos besoins d'approvisionnement professionnel
        </p>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section">
    <div class="container">
        <div class="contact-grid">

            <!-- Contact Information -->
            <div class="contact-info fade-in-up">
                <h3>Informations de Contact</h3>

                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="contact-details">
                        <h4>Téléphone</h4>
                        <p>Service commercial : <a href="tel:+33123456789">01 23 45 67 89</a></p>
                        <p>Urgences : <a href="tel:+33123456790">01 23 45 67 90</a></p>
                        <p>Du lundi au vendredi, 6h à 18h</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="contact-details">
                        <h4>Email</h4>
                        <p>Commercial : <a href="mailto:commercial@fruits-legumes.fr">commercial@fruits-legumes.fr</a></p>
                        <p>Support : <a href="mailto:support@fruits-legumes.fr">support@fruits-legumes.fr</a></p>
                        <p>Réponse sous 2h en moyenne</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="contact-details">
                        <h4>Adresse</h4>
                        <p>123 Avenue du Marché<br>
                        Zone Industrielle Sud<br>
                        75001 Paris, France</p>
                        <p>Accès livraison 24h/24</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="contact-details">
                        <h4>Zone de Livraison</h4>
                        <p>Île-de-France et régions limitrophes</p>
                        <p>Livraison express disponible</p>
                        <p>Frais de port offerts dès 200€ HT</p>
                    </div>
                </div>

                <!-- Business Hours -->
                <div class="business-hours">
                    <h4><i class="fas fa-clock me-2"></i>Horaires d'Ouverture</h4>
                    <ul class="hours-list">
                        <li class="hours-item">
                            <span class="hours-day">Lundi - Vendredi</span>
                            <span class="hours-time">6h00 - 18h00</span>
                        </li>
                        <li class="hours-item">
                            <span class="hours-day">Samedi</span>
                            <span class="hours-time">7h00 - 15h00</span>
                        </li>
                        <li class="hours-item">
                            <span class="hours-day">Dimanche</span>
                            <span class="hours-time">Fermé</span>
                        </li>
                        <li class="hours-item">
                            <span class="hours-day">Urgences</span>
                            <span class="hours-time">24h/7j</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form fade-in-up">
                <h3>Demande de Devis</h3>

                <!-- Messages Flash -->
                @if(session('success'))
                    <div class="success-message">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Erreurs détectées :</strong>
                        <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('contact.store') }}" method="POST" id="contactForm">
                    @csrf

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Prénom *</label>
                            <input type="text" name="first_name" class="form-control"
                                   value="{{ old('first_name') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nom *</label>
                            <input type="text" name="last_name" class="form-control"
                                   value="{{ old('last_name') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Entreprise *</label>
                        <input type="text" name="company" class="form-control"
                               value="{{ old('company') }}" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ old('email') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Téléphone *</label>
                            <input type="tel" name="phone" class="form-control"
                                   value="{{ old('phone') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Type de demande *</label>
                        <select name="request_type" class="form-control" required>
                            <option value="">Sélectionnez...</option>
                            <option value="devis" {{ old('request_type') == 'devis' ? 'selected' : '' }}>Demande de devis</option>
                            <option value="partenariat" {{ old('request_type') == 'partenariat' ? 'selected' : '' }}>Partenariat commercial</option>
                            <option value="livraison" {{ old('request_type') == 'livraison' ? 'selected' : '' }}>Information livraison</option>
                            <option value="produit" {{ old('request_type') == 'produit' ? 'selected' : '' }}>Question produit</option>
                            <option value="autre" {{ old('request_type') == 'autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Volume mensuel estimé</label>
                        <select name="volume" class="form-control">
                            <option value="">Sélectionnez...</option>
                            <option value="small" {{ old('volume') == 'small' ? 'selected' : '' }}>Moins de 500€ HT</option>
                            <option value="medium" {{ old('volume') == 'medium' ? 'selected' : '' }}>500€ - 2000€ HT</option>
                            <option value="large" {{ old('volume') == 'large' ? 'selected' : '' }}>2000€ - 5000€ HT</option>
                            <option value="enterprise" {{ old('volume') == 'enterprise' ? 'selected' : '' }}>Plus de 5000€ HT</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Message *</label>
                        <textarea name="message" class="form-control textarea"
                                  placeholder="Décrivez vos besoins, produits souhaités, quantités..."
                                  required>{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-paper-plane"></i>
                        Envoyer la demande
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="map-section">
    <div class="container">
        <div class="map-container">
            <div class="map-placeholder">
                <h4><i class="fas fa-map-marked-alt me-2"></i>Notre Localisation</h4>
                <p>
                    Située au cœur de la zone industrielle, notre entreprise bénéficie d'un accès privilégié
                    aux axes de transport pour des livraisons rapides dans toute la région.
                </p>
                <a href="https://maps.google.com" target="_blank" class="btn-directions">
                    <i class="fas fa-directions"></i>
                    Obtenir l'itinéraire
                </a>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section">
    <div class="container">
        <h2 class="faq-title">Questions Fréquentes</h2>

        <div class="faq-grid">
            <div class="faq-item fade-in-up">
                <h4 class="faq-question">
                    <i class="fas fa-question-circle text-primary me-2"></i>
                    Quels sont vos délais de livraison ?
                </h4>
                <p class="faq-answer">
                    Nous livrons dans les 24h pour les commandes passées avant 14h.
                    Livraison express possible en 4h pour les urgences.
                </p>
            </div>

            <div class="faq-item fade-in-up">
                <h4 class="faq-question">
                    <i class="fas fa-question-circle text-primary me-2"></i>
                    Quelle est la commande minimum ?
                </h4>
                <p class="faq-answer">
                    Commande minimum de 100€ HT. Frais de port offerts à partir de 200€ HT.
                    Tarifs dégressifs selon les volumes.
                </p>
            </div>

            <div class="faq-item fade-in-up">
                <h4 class="faq-question">
                    <i class="fas fa-question-circle text-primary me-2"></i>
                    Proposez-vous des produits bio ?
                </h4>
                <p class="faq-answer">
                    Oui, nous avons une gamme complète de produits bio certifiés.
                    Consultez notre catalogue ou contactez-nous pour plus d'informations.
                </p>
            </div>

            <div class="faq-item fade-in-up">
                <h4 class="faq-question">
                    <i class="fas fa-question-circle text-primary me-2"></i>
                    Comment passer commande ?
                </h4>
                <p class="faq-answer">
                    Commande en ligne, par téléphone ou email. Nos commerciaux vous accompagnent
                    pour vos première commandes et besoins spécifiques.
                </p>
            </div>

            <div class="faq-item fade-in-up">
                <h4 class="faq-question">
                    <i class="fas fa-question-circle text-primary me-2"></i>
                    Proposez-vous des contrats annuels ?
                </h4>
                <p class="faq-answer">
                    Oui, nous établissons des contrats de partenariat avec tarifs préférentiels
                    pour les gros volumes et engagements annuels.
                </p>
            </div>

            <div class="faq-item fade-in-up">
                <h4 class="faq-question">
                    <i class="fas fa-question-circle text-primary me-2"></i>
                    Garantissez-vous la fraîcheur ?
                </h4>
                <p class="faq-answer">
                    Garantie fraîcheur 48h. Chaîne du froid respectée, conditionnement adapté.
                    Possibilité de retour si non-conformité.
                </p>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Script contact chargé');

        // Animation au scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observer les éléments
        document.querySelectorAll('.fade-in-up').forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = `all 0.6s ease-out ${index * 0.1}s`;
            observer.observe(el);
        });

        // Gestion du formulaire
        const form = document.getElementById('contactForm');
        const submitBtn = document.getElementById('submitBtn');

        if (form && submitBtn) {
            form.addEventListener('submit', function(e) {
                // Ne pas empêcher la soumission - laisser Laravel traiter
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';
            });

            // Validation en temps réel
            const requiredFields = form.querySelectorAll('input[required], select[required], textarea[required]');
            requiredFields.forEach(field => {
                field.addEventListener('blur', function() {
                    if (this.value.trim() === '') {
                        this.style.borderColor = '#e74c3c';
                    } else {
                        this.style.borderColor = '#78e6a6';
                    }
                });
            });
        }
    });
</script>
@endpush
