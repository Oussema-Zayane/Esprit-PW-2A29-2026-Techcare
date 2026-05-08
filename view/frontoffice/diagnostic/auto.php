<?php
$pageTitle = "TechCare - Auto Amélioration";
$bodyClass = "front-body";

$data = $controller->getAutoAmeliorationData();

function percentAuto($value, $total)
{
    return $total > 0 ? round(($value / $total) * 100) : 0;
}

ob_start();
?>

<nav class="navbar">
    <a href="index.php?action=front_show" class="navbar-brand">
        <img src="assets/images/logo.png" alt="TechCare Logo" style="width:32px;height:32px;object-fit:contain;">
        <span class="brand-accent">TechCare</span>
    </a>

    <button class="hamburger" type="button">☰</button>

    <div class="navbar-menu">
        <a class="nav-link" href="#">Boutique</a>
        <a class="nav-link" href="#">Gamification</a>
        <a class="nav-link active" href="index.php?action=front_show">Diagnostic</a>
        <a class="nav-link" href="index.php?action=solution_front_show">Solution</a>
        <a class="nav-link" href="#">Abonnement</a>
        <a class="nav-link" href="index.php?action=back_show">Administration</a>
    </div>
</nav>

<section class="hero metier-hero-small">
    <div class="hero-content">
        <span class="hero-badge">Métier 5</span>
        <h1 class="hero-title">Auto <span class="gradient-text">amélioration</span></h1>
        <p class="hero-subtitle">
            Le système analyse les anciens diagnostics pour détecter les tendances et proposer des améliorations.
        </p>
    </div>
    <div class="hero-visual"><div class="hero-main-icon">🔁</div></div>
</section>

<section class="products-section metier-page-wrap">
    <div class="metier-page-actions">
        <a class="btn-outline" href="index.php?action=front_show">← Retour aux diagnostics</a>
        <a class="metier-link-btn" href="index.php?action=diagnostic_stats"><span class="metier-icon">📊</span>Statistiques</a>
        <a class="metier-link-btn" href="index.php?action=diagnostic_filter"><span class="metier-icon">🔎</span>Filtrage intelligent</a>
        <a class="metier-link-btn" href="index.php?action=diagnostic_tri"><span class="metier-icon">↕</span>Tri</a>
        <a class="metier-link-btn" href="index.php?action=diagnostic_score"><span class="metier-icon">🧠</span>Calcul intelligent</a>
        <a class="metier-link-btn active" href="index.php?action=diagnostic_auto"><span class="metier-icon">🔁</span>Auto amélioration</a>
        <button type="button" class="btn-primary print-btn" onclick="window.print()">📄 Enregistrer PDF</button>
    </div>

    <div class="metier-title-row mt-6">
        <div>
            <span class="hero-badge">Analyse</span>
            <h2 class="section-title">Tendances détectées</h2>
        </div>
        <span class="count-badge"><?= (int) $data['total']; ?></span>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:22px; margin-bottom:28px;">
        <div class="admin-card" style="padding:24px;">
            <h3 style="color:white;margin:0 0 8px;">Type le plus fréquent</h3>
            <strong style="font-size:28px;color:#00e0a4;"><?= htmlspecialchars($data['typeFrequent']); ?></strong>
        </div>
        <div class="admin-card" style="padding:24px;">
            <h3 style="color:white;margin:0 0 8px;">Problème le plus fréquent</h3>
            <strong style="font-size:28px;color:#00e0a4;"><?= htmlspecialchars($data['categorieFrequente']); ?></strong>
        </div>
        <div class="admin-card" style="padding:24px;">
            <h3 style="color:white;margin:0 0 8px;">Urgence dominante</h3>
            <strong style="font-size:28px;color:#00e0a4;"><?= htmlspecialchars($data['urgenceFrequente']); ?></strong>
        </div>
    </div>

    <div class="admin-card metier-page-card" style="padding:24px;margin-bottom:28px;">
        <div class="card-head"><h3>Recommandations automatiques</h3></div>
        <?php foreach ($data['recommendations'] as $rec): ?>
            <div style="padding:14px 16px;border-radius:14px;background:#0d1524;border:1px solid rgba(0,224,164,.22);color:#dbeafe;margin-bottom:12px;">
                <?= htmlspecialchars($rec); ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="admin-card metier-page-card" style="padding:24px;">
        <div class="card-head"><h3>Répartition utilisée pour l'auto-amélioration</h3></div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:24px;">
            <?php
            $blocks = [
                'Par type objet' => $data['parType'],
                'Par catégorie' => $data['parCategorie'],
                'Par urgence' => $data['parUrgence']
            ];
            ?>

            <?php foreach ($blocks as $title => $rows): ?>
                <div style="background:#0d1524;border:1px solid rgba(255,255,255,.07);border-radius:18px;padding:18px;">
                    <h3 style="color:white;margin:0 0 16px;"><?= htmlspecialchars($title); ?></h3>

                    <?php if (!empty($rows)): ?>
                        <?php foreach ($rows as $label => $count): ?>
                            <?php $percent = percentAuto($count, $data['total']); ?>
                            <div style="margin-bottom:14px;">
                                <div style="display:flex;justify-content:space-between;color:white;margin-bottom:7px;gap:10px;">
                                    <span><?= htmlspecialchars($label); ?></span>
                                    <strong><?= (int) $count; ?> - <?= (int) $percent; ?>%</strong>
                                </div>
                                <div style="height:10px;background:#1b2a40;border-radius:999px;overflow:hidden;">
                                    <div style="height:100%;width:<?= (int) $percent; ?>%;background:#00e0a4;border-radius:999px;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color:#8ea3c6;">Aucune donnée.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
const hamburger = document.querySelector('.hamburger');
const menu = document.querySelector('.navbar-menu');
if (hamburger && menu) hamburger.addEventListener('click', () => menu.classList.toggle('open'));
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>
