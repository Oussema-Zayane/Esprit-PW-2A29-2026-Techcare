<?php include __DIR__ . '/../layout/header.php'; ?>

<section class="hero-section">
    <div class="container">
        <h1>Suivi de mon abonnement</h1>
        <p>Consultez l'état de votre abonnement TechCare</p>
    </div>
</section>

<section class="offres-section">
    <div class="container">
        <div style="max-width: 600px; margin: 0 auto;">
            <div class="form-section">
                <h2>Consulter mon abonnement</h2>
                
                <form method="post" id="form-suivi">
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" id="abo-email" required placeholder="votremail@example.com">
                    </div>

                    <div class="form-actions">
                        <a href="index.php?page=offres" class="btn-secondary">Retour</a>
                        <button type="button" id="btn-suivre" class="btn-primary">
                            <i class="fa-solid fa-magnifying-glass"></i> Consulter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Résultat -->
            <div id="suivi-resultat" style="display: none; margin-top: 2rem;">
                <div class="table-container">
                    <div class="table-header">
                        <h3><i class="fa-solid fa-check-circle" style="color: #00A86B;"></i> Vos abonnements</h3>
                    </div>
                    <table id="table-abos">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Prix</th>
                                <th>Début</th>
                                <th>Fin</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-abos">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('btn-suivre').addEventListener('click', function() {
    const email = document.getElementById('abo-email').value;
    
    if (!email) {
        alert('Veuillez entrer votre email');
        return;
    }
    
    // Afficher un exemple d'abonnement
    const tbody = document.getElementById('tbody-abos');
    tbody.innerHTML = `
        <tr>
            <td>Mensuel</td>
            <td>29.90 DT</td>
            <td>10/04/2026</td>
            <td>10/05/2026</td>
            <td><span class="badge badge-success"><i class="fa-solid fa-check"></i> Actif</span></td>
        </tr>
    `;
    
    document.getElementById('suivi-resultat').style.display = 'block';
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>