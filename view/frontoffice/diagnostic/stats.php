<?php
$pageTitle = "TechCare - Statistiques Diagnostics";
$bodyClass = "front-body";

$statsDiagnostic = $controller->getDiagnosticStats();
$total = (int) ($statsDiagnostic['total'] ?? 0);
$rawTypeStats = $statsDiagnostic['parTypeObjet'] ?? [];

$typeObjets = [
    'Téléphone',
    'PC',
    'Ordinateur bureau',
    'Machine à laver',
    'Tablette',
    'iPad',
    'Smart watch',
    'Imprimante',
    'Autre'
];

$colors = [
    '#00e0a4',
    '#2f80ed',
    '#ff4d6d',
    '#ffb703',
    '#9b5de5',
    '#00bbf9',
    '#f15bb5',
    '#fb8500',
    '#8d99ae'
];

$typeCounts = array_fill_keys($typeObjets, 0);

function normalizeTypeObjetStats($type)
{
    $value = trim($type ?? '');
    $value = function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
    $value = str_replace(
        ['é', 'è', 'ê', 'à', 'â', 'î'],
        ['e', 'e', 'e', 'a', 'a', 'i'],
        $value
    );

    if (in_array($value, ['telephone', 'tel', 'phone', 'smartphone'])) {
        return 'Téléphone';
    }

    if ($value === 'pc') {
        return 'PC';
    }

    if (in_array($value, ['ordinateur bureau', 'ordinateur de bureau', 'desktop'])) {
        return 'Ordinateur bureau';
    }

    if (in_array($value, ['machine a laver', 'machine laver', 'lave linge'])) {
        return 'Machine à laver';
    }

    if ($value === 'tablette') {
        return 'Tablette';
    }

    if ($value === 'ipad') {
        return 'iPad';
    }

    if (in_array($value, ['smart watch', 'smartwatch', 'montre connectee', 'watch'])) {
        return 'Smart watch';
    }

    if ($value === 'imprimante') {
        return 'Imprimante';
    }

    return 'Autre';
}

foreach ($rawTypeStats as $stat) {
    $type = normalizeTypeObjetStats($stat['typeObjet'] ?? '');
    $typeCounts[$type] += (int) ($stat['total'] ?? 0);
}

$typeStats = [];
foreach ($typeCounts as $type => $count) {
    if ($count > 0) {
        $typeStats[] = [
            'typeObjet' => $type,
            'total' => $count,
            'percent' => $total > 0 ? round(($count / $total) * 100) : 0
        ];
    }
}

usort($typeStats, function ($a, $b) {
    return $b['total'] <=> $a['total'];
});

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
        <span class="hero-badge">Métier 1</span>
        <h1 class="hero-title">
            Statistiques <span class="gradient-text">des diagnostics</span>
        </h1>
        <p class="hero-subtitle">
            Répartition des diagnostics par type d’objet : téléphone, PC, machine à laver, tablette et autres appareils.
        </p>
    </div>

    <div class="hero-visual">
        <div class="hero-main-icon">📊</div>
    </div>
</section>

<section class="products-section" style="max-width:1200px; margin:0 auto; padding:45px 20px;">

    <div style="display:flex; gap:12px; margin-bottom:28px; flex-wrap:wrap; align-items:center;">
        <a class="btn-outline" href="index.php?action=front_show">← Retour aux diagnostics</a>
        <a class="btn-primary" href="index.php?action=diagnostic_stats">Statistiques</a>
        <a class="btn-outline" href="index.php?action=diagnostic_filter">Filtrage intelligent</a>
        <a class="btn-outline" href="index.php?action=diagnostic_tri">Tri</a>
        <a class="btn-outline" href="index.php?action=diagnostic_score">Calcul intelligent</a>
        <a class="btn-outline" href="index.php?action=diagnostic_auto">Auto amélioration</a>
        <button type="button" class="btn-primary print-btn" onclick="window.print()">📄 Enregistrer PDF</button>
    </div>

    <div style="
        background:#141f31;
        border:1px solid rgba(255,255,255,0.08);
        border-radius:24px;
        padding:28px;
        box-shadow:0 18px 40px rgba(0,0,0,0.25);
    ">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; margin-bottom:24px;">
            <div>
                <h2 style="font-size:32px; margin:0 0 8px; color:white;">Répartition par type objet</h2>
                <p style="margin:0; color:#8ea3c6;">
                    Vue graphique des diagnostics enregistrés selon le type d’appareil.
                </p>
            </div>

            <div style="
                min-width:120px;
                text-align:center;
                padding:16px 22px;
                border-radius:18px;
                background:rgba(0,224,164,0.12);
                border:1px solid rgba(0,224,164,0.35);
            ">
                <strong style="display:block; color:#00e0a4; font-size:32px;"><?= $total; ?></strong>
                <span style="color:white;">Diagnostics</span>
            </div>
        </div>

        <?php if ($total > 0): ?>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:28px; align-items:center;">

                <div style="
                    background:#0d1524;
                    border:1px solid rgba(255,255,255,0.07);
                    border-radius:22px;
                    padding:22px;
                    min-height:360px;
                ">
                    <h3 style="color:white; margin:0 0 18px;">Graphique circulaire</h3>
                    <canvas id="typeDonutChart" style="max-height:320px;"></canvas>
                </div>

                <div style="
                    background:#0d1524;
                    border:1px solid rgba(255,255,255,0.07);
                    border-radius:22px;
                    padding:22px;
                    min-height:360px;
                ">
                    <h3 style="color:white; margin:0 0 18px;">Graphique en barres</h3>
                    <canvas id="typeBarChart" style="max-height:320px;"></canvas>
                </div>

            </div>

            <div style="
                margin-top:28px;
                background:#0d1524;
                border:1px solid rgba(255,255,255,0.07);
                border-radius:22px;
                padding:22px;
            ">
                <h3 style="color:white; margin:0 0 18px;">Détails par type</h3>

                <?php foreach ($typeStats as $index => $stat): ?>
                    <?php
                    $color = $colors[$index % count($colors)];
                    $percent = (int) $stat['percent'];
                    ?>
                    <div style="margin-bottom:18px;">
                        <div style="display:flex; justify-content:space-between; gap:10px; color:white; margin-bottom:8px;">
                            <span>
                                <i style="
                                    display:inline-block;
                                    width:12px;
                                    height:12px;
                                    border-radius:50%;
                                    background:<?= htmlspecialchars($color); ?>;
                                    margin-right:8px;
                                "></i>
                                <?= htmlspecialchars($stat['typeObjet']); ?>
                            </span>

                            <strong>
                                <?= htmlspecialchars($stat['total']); ?> diagnostic(s) — <?= htmlspecialchars($percent); ?>%
                            </strong>
                        </div>

                        <div style="
                            height:12px;
                            background:#1b2a40;
                            border-radius:30px;
                            overflow:hidden;
                        ">
                            <div style="
                                height:100%;
                                width:<?= $percent; ?>%;
                                background:<?= htmlspecialchars($color); ?>;
                                border-radius:30px;
                            "></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i>📊</i>
                <p>Aucun diagnostic disponible pour les statistiques.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const labelsTypeObjet = <?= json_encode(array_column($typeStats, 'typeObjet'), JSON_UNESCAPED_UNICODE); ?>;
const valuesTypeObjet = <?= json_encode(array_column($typeStats, 'total')); ?>;
const colorsTypeObjet = <?= json_encode(array_slice($colors, 0, count($typeStats))); ?>;

if (document.getElementById('typeDonutChart')) {
    new Chart(document.getElementById('typeDonutChart'), {
        type: 'doughnut',
        data: {
            labels: labelsTypeObjet,
            datasets: [{
                data: valuesTypeObjet,
                backgroundColor: colorsTypeObjet,
                borderColor: '#0d1524',
                borderWidth: 4,
                hoverOffset: 10
            }]
        },
        options: {
            cutout: '58%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        color: '#ffffff',
                        font: {
                            size: 13,
                            weight: 'bold'
                        },
                        padding: 16
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = valuesTypeObjet.reduce((a, b) => a + Number(b), 0);
                            const value = Number(context.raw);
                            const percent = total > 0 ? Math.round((value / total) * 100) : 0;
                            return context.label + ' : ' + value + ' diagnostic(s) - ' + percent + '%';
                        }
                    }
                }
            }
        }
    });
}

if (document.getElementById('typeBarChart')) {
    new Chart(document.getElementById('typeBarChart'), {
        type: 'bar',
        data: {
            labels: labelsTypeObjet,
            datasets: [{
                label: 'Nombre de diagnostics',
                data: valuesTypeObjet,
                backgroundColor: colorsTypeObjet,
                borderRadius: 10
            }]
        },
        options: {
            scales: {
                x: {
                    ticks: {
                        color: '#ffffff'
                    },
                    grid: {
                        color: 'rgba(255,255,255,0.06)'
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#ffffff',
                        precision: 0
                    },
                    grid: {
                        color: 'rgba(255,255,255,0.06)'
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: '#ffffff'
                    }
                }
            }
        }
    });
}

const hamburger = document.querySelector('.hamburger');
const menu = document.querySelector('.navbar-menu');

if (hamburger && menu) {
    hamburger.addEventListener('click', function () {
        menu.classList.toggle('open');
    });
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>