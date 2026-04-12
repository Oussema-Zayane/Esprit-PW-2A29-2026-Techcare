<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <h1>Nos offres d'abonnement</h1>
        <p>Choisissez la formule qui vous correspond</p>
    </div>
</section>

<section class="offres-section">
    <div class="container">
        <div class="offres-grid">
            
            <!-- Offre Mensuelle -->
            <div class="offre-card">
                <div class="offre-badge">POPULAIRE</div>
                <div class="offre-icon">
                    <i class="fa-solid fa-calendar-week"></i>
                </div>
                <h3>Mensuel</h3>
                <div class="offre-price">
                    <span class="price">29.90</span>
                    <span class="period">DT / mois</span>
                </div>
                <ul class="offre-features">
                    <li><i class="fa-solid fa-check"></i> Support 24/7</li>
                    <li><i class="fa-solid fa-check"></i> Maintenance incluse</li>
                    <li><i class="fa-solid fa-check"></i> Mises à jour</li>
                </ul>
                <a href="index.php?page=souscrire&type=mensuel" class="btn-primary">
                    Souscrire <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <!-- Offre Annuelle (BEST VALUE) -->
            <div class="offre-card featured">
                <div class="offre-badge">BEST VALUE</div>
                <div class="offre-icon">
                    <i class="fa-solid fa-calendar-year"></i>
                </div>
                <h3>Annuel</h3>
                <div class="offre-price">
                    <span class="price">299</span>
                    <span class="period">DT / an</span>
                    <span class="saving"><i class="fa-solid fa-tag"></i> Économisez 20%</span>
                </div>
                <ul class="offre-features">
                    <li><i class="fa-solid fa-check"></i> Tout du mensuel</li>
                    <li><i class="fa-solid fa-check"></i> 2 mois offerts</li>
                    <li><i class="fa-solid fa-check"></i> Support prioritaire</li>
                </ul>
                <a href="index.php?page=souscrire&type=annuel" class="btn-primary">
                    Souscrire <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <!-- Offre Ponctuelle -->
            <div class="offre-card">
                <div class="offre-icon">
                    <i class="fa-solid fa-ticket"></i>
                </div>
                <h3>Ponctuel</h3>
                <div class="offre-price">
                    <span class="price">49.90</span>
                    <span class="period">DT / intervention</span>
                </div>
                <ul class="offre-features">
                    <li><i class="fa-solid fa-check"></i> Sans engagement</li>
                    <li><i class="fa-solid fa-check"></i> Intervention unique</li>
                    <li><i class="fa-solid fa-check"></i> Rapport détaillé</li>
                </ul>
                <a href="index.php?page=souscrire&type=ponctuel" class="btn-primary">
                    Souscrire <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Ajoutez ce CSS si ce n'est pas déjà dans style.css -->
<style>
    .hero-section {
        text-align: center;
        padding: 4rem 0 2rem;
    }
    .hero-section h1 {
        font-size: 2.5rem;
        color: #0A1F44;
        margin-bottom: 1rem;
    }
    .hero-section p {
        font-size: 1.2rem;
        color: #666;
    }
    .offres-section {
        padding: 2rem 0 5rem;
    }
    .offres-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 2rem;
    }
    .offre-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        position: relative;
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    .offre-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    .offre-card.featured {
        border: 2px solid #00A86B;
        transform: scale(1.02);
    }
    .offre-badge {
        position: absolute;
        top: -12px;
        left: 50%;
        transform: translateX(-50%);
        background: #00A86B;
        color: white;
        padding: 0.3rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
    }
    .offre-icon {
        font-size: 3rem;
        color: #00A86B;
        margin-bottom: 1rem;
    }
    .offre-card h3 {
        font-size: 1.8rem;
        color: #0A1F44;
        margin-bottom: 1rem;
    }
    .price {
        font-size: 2.5rem;
        font-weight: bold;
        color: #0A1F44;
    }
    .period {
        font-size: 1rem;
        color: #666;
    }
    .saving {
        display: block;
        font-size: 0.8rem;
        color: #00A86B;
        margin-top: 0.3rem;
    }
    .offre-features {
        list-style: none;
        margin: 1.5rem 0;
        text-align: left;
    }
    .offre-features li {
        padding: 0.5rem 0;
        color: #555;
        border-bottom: 1px solid #eee;
    }
    .offre-features li i {
        color: #00A86B;
        margin-right: 10px;
    }
    .btn-primary {
        display: inline-block;
        background: #00A86B;
        color: white;
        padding: 0.8rem 2rem;
        border-radius: 50px;
        text-decoration: none;
        font-weight: bold;
        transition: background 0.3s;
        width: 100%;
        border: none;
        cursor: pointer;
        text-align: center;
    }
    .btn-primary:hover {
        background: #008f5a;
    }
    @media (max-width: 768px) {
        .offres-grid {
            grid-template-columns: 1fr;
        }
        .hero-section h1 {
            font-size: 1.8rem;
        }
    }
</style>