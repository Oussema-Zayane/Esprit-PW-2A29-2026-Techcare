<?php include __DIR__ . '/../layout/header.php'; ?>

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
            
            <?php if (!empty($types)): ?>
                <?php foreach ($types as $index => $type): ?>
                    <?php 
                    $isPopular = ($type['nom_type'] == 'Mensuel');
                    $isBestValue = ($type['nom_type'] == 'Annuel');
                    $prixBase = floatval($type['prix_base']);
                    $duree = intval($type['duree_jours']);
                    $icone = !empty($type['icone']) ? $type['icone'] : 'tag';
                    $description = !empty($type['description']) ? explode(',', $type['description']) : [];
                    ?>
                    
                    <div class="offre-card <?= $isBestValue ? 'featured' : '' ?>">
                        <?php if ($isPopular): ?>
                            <div class="offre-badge">POPULAIRE</div>
                        <?php elseif ($isBestValue): ?>
                            <div class="offre-badge">BEST VALUE</div>
                        <?php endif; ?>
                        
                        <div class="offre-icon">
                            <i class="fa-solid fa-<?= $icone ?>"></i>
                        </div>
                        <h3><?= ucfirst($type['nom_type']) ?></h3>
                        <div class="offre-price">
                            <span class="price"><?= number_format($prixBase, 2) ?></span>
                            <span class="period">
                                <?php if ($type['nom_type'] == 'Mensuel'): ?>
                                    DT / mois
                                <?php elseif ($type['nom_type'] == 'Annuel'): ?>
                                    DT / an
                                <?php else: ?>
                                    DT
                                <?php endif; ?>
                            </span>
                            <?php if ($isBestValue): ?>
                                <span class="saving"><i class="fa-solid fa-tag"></i> Économisez 20%</span>
                            <?php endif; ?>
                        </div>
                        <ul class="offre-features">
                            <?php if (!empty($description)): ?>
                                <?php foreach ($description as $feature): ?>
                                    <li><i class="fa-solid fa-check"></i> <?= trim(htmlspecialchars($feature)) ?></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li><i class="fa-solid fa-check"></i> Offre <?= ucfirst($type['nom_type']) ?></li>
                                <li><i class="fa-solid fa-check"></i> Durée: <?= $duree ?> jours</li>
                                <li><i class="fa-solid fa-check"></i> Support inclus</li>
                            <?php endif; ?>
                        </ul>
                        <a href="index.php?page=souscrire&type=<?= $type['id_type'] ?>" class="btn-primary">
                            Souscrire <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-offres">
                    <p>Aucune offre disponible pour le moment</p>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</section>

<style>
/* Reset et styles de base */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
    min-height: 100vh;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Hero Section */
.hero-section {
    text-align: center;
    padding: 60px 0 30px;
    background: linear-gradient(135deg, #0A1F44 0%, #1a3a6e 100%);
    margin-bottom: 40px;
    border-radius: 0 0 30px 30px;
}
.hero-section h1 {
    font-size: 2.5rem;
    color: white;
    margin-bottom: 1rem;
    font-weight: 700;
}
.hero-section p {
    font-size: 1.2rem;
    color: rgba(255,255,255,0.85);
}

/* Offres Grid - ALIGNEMENT HORIZONTAL */
.offres-section {
    padding: 20px 0 80px;
}
.offres-grid {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: stretch;
    gap: 30px;
    flex-wrap: wrap;
}

/* Cartes d'offres */
.offre-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 2rem 1.8rem;
    text-align: center;
    position: relative;
    transition: all 0.3s ease;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    flex: 1;
    min-width: 280px;
    max-width: 350px;
    display: flex;
    flex-direction: column;
    border: 1px solid rgba(0,0,0,0.05);
}
.offre-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 25px 40px rgba(0,0,0,0.12);
}
.offre-card.featured {
    border: 2px solid #00A86B;
    transform: scale(1.02);
    background: linear-gradient(135deg, #fff 0%, #f0fff5 100%);
}
.offre-card.featured:hover {
    transform: scale(1.02) translateY(-8px);
}

/* Badges */
.offre-badge {
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    background: #00A86B;
    color: white;
    padding: 5px 15px;
    border-radius: 25px;
    font-size: 0.7rem;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(0,168,107,0.3);
}

/* Icônes */
.offre-icon {
    font-size: 3.5rem;
    color: #00A86B;
    margin-bottom: 1rem;
}

/* Titres */
.offre-card h3 {
    font-size: 1.6rem;
    color: #0A1F44;
    margin-bottom: 0.5rem;
    font-weight: 700;
}

/* Prix */
.offre-price {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f0f0f0;
}
.price {
    font-size: 2.5rem;
    font-weight: 800;
    color: #0A1F44;
}
.period {
    font-size: 0.85rem;
    color: #888;
}
.saving {
    display: inline-block;
    font-size: 0.7rem;
    color: #00A86B;
    margin-top: 8px;
    font-weight: 600;
    background: rgba(0,168,107,0.1);
    padding: 3px 10px;
    border-radius: 20px;
}

/* Caractéristiques */
.offre-features {
    list-style: none;
    margin: 1.5rem 0;
    text-align: left;
    flex: 1;
}
.offre-features li {
    padding: 10px 0;
    color: #555;
    border-bottom: 1px solid #f0f0f0;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
}
.offre-features li i {
    color: #00A86B;
    margin-right: 10px;
    width: 18px;
    font-size: 0.8rem;
}

/* Bouton */
.btn-primary {
    display: inline-block;
    background: #00A86B;
    color: white;
    padding: 12px 20px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
    width: 100%;
    border: none;
    cursor: pointer;
    text-align: center;
    font-size: 0.95rem;
    margin-top: auto;
}
.btn-primary:hover {
    background: #008f5a;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,168,107,0.3);
}
.btn-primary:active {
    transform: translateY(0);
    color: black;
}

.no-offres {
    text-align: center;
    padding: 60px;
    background: white;
    border-radius: 20px;
    color: #666;
}

/* Responsive */
@media (max-width: 900px) {
    .offres-grid {
        gap: 20px;
    }
    .offre-card {
        min-width: 260px;
        padding: 1.5rem;
    }
    .hero-section h1 {
        font-size: 2rem;
    }
}

@media (max-width: 768px) {
    .offres-grid {
        flex-direction: column;
        align-items: center;
    }
    .offre-card {
        width: 100%;
        max-width: 400px;
    }
    .offre-card.featured {
        transform: scale(1);
    }
    .offre-card.featured:hover {
        transform: translateY(-8px);
    }
    .hero-section {
        padding: 40px 0 20px;
    }
    .hero-section h1 {
        font-size: 1.8rem;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 0 15px;
    }
    .offre-card {
        padding: 1.5rem;
    }
    .price {
        font-size: 2rem;
    }
    .offre-card h3 {
        font-size: 1.4rem;
    }
    .offre-icon {
        font-size: 2.5rem;
    }
    .hero-section h1 {
        font-size: 1.5rem;
    }
}
</style>

<?php include __DIR__ . '/../layout/footer.php'; ?>