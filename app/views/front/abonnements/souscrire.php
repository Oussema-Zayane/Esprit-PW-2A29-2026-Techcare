<?php include __DIR__ . '/../layout/header.php'; ?>

<section class="hero-section">
    <div class="container">
        <h1>Souscrire à un abonnement</h1>
        <p>Choisissez votre formule et complétez votre inscription</p>
    </div>
</section>

<section class="offres-section">
    <div class="container">
        <div style="max-width: 600px; margin: 0 auto;">
            <div class="form-section">
                <h2>Complétez votre inscription</h2>

                <?php if (isset($erreur)): ?>
                    <div class="alert alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <div><?= $erreur ?></div>
                    </div>
                <?php endif; ?>

                <form method="post" action="index.php?page=souscrire" id="form-souscrire" novalidate>

                    <div class="form-group">
                        <label>Nom complet *</label>
                        <input type="text" name="nom_client" id="nom_client"
                               placeholder="Jean Dupont"
                               value="<?= htmlspecialchars($_POST['nom_client'] ?? '') ?>">
                        <span class="error-msg" id="error-nom_client"></span>
                    </div>

                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" id="email"
                               placeholder="jean@example.com"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        <span class="error-msg" id="error-email"></span>
                    </div>

                    <!-- ═══ CHAMP TÉLÉPHONE AJOUTÉ ═══ -->
                    <div class="form-group">
                        <label>
                            Téléphone
                            <span class="label-hint">
                                <i class="fa-solid fa-comment-sms"></i>
                                Pour recevoir vos SMS de confirmation
                            </span>
                        </label>
                        <div class="tel-input-wrap">
                            <span class="tel-prefix">+216</span>
                            <input type="tel" name="telephone" id="telephone"
                                   placeholder="98 000 000"
                                   maxlength="8"
                                   value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
                        </div>
                        <span class="error-msg" id="error-telephone"></span>
                        <small class="field-hint">
                            <i class="fa-solid fa-circle-info"></i>
                            Optionnel — vous recevrez un SMS de bienvenue et des rappels avant expiration
                        </small>
                    </div>
                    <!-- ═══════════════════════════════ -->

                    <div class="form-group">
                        <label>Type d'abonnement *</label>
                        <select name="type_abonnement" id="type_abonnement">
                            <option value="">-- Sélectionner --</option>
                            <option value="mensuel"  <?= ($_POST['type_abonnement'] ?? '') === 'mensuel'  ? 'selected' : '' ?>>
                                Mensuel (29.90 DT/mois)
                            </option>
                            <option value="annuel"   <?= ($_POST['type_abonnement'] ?? '') === 'annuel'   ? 'selected' : '' ?>>
                                Annuel (299 DT/an)
                            </option>
                            <option value="ponctuel" <?= ($_POST['type_abonnement'] ?? '') === 'ponctuel' ? 'selected' : '' ?>>
                                Ponctuel (49.90 DT)
                            </option>
                        </select>
                        <span class="error-msg" id="error-type_abonnement"></span>
                    </div>

                    <!-- Récapitulatif dynamique -->
                    <div class="recap-box" id="recap-box" style="display:none;">
                        <div class="recap-title">
                            <i class="fa-solid fa-receipt"></i> Récapitulatif
                        </div>
                        <div class="recap-grid">
                            <span>Type</span>      <strong id="recap-type">—</strong>
                            <span>Prix</span>       <strong id="recap-prix" style="color:var(--green)">—</strong>
                            <span>Début</span>      <strong id="recap-debut">—</strong>
                            <span>Fin</span>        <strong id="recap-fin">—</strong>
                            <span>SMS</span>        <strong id="recap-sms">—</strong>
                        </div>
                    </div>

                    <!-- Champs cachés dates -->
                    <input type="hidden" name="date_debut" id="date_debut" value="<?php echo date('Y-m-d'); ?>">
                    <input type="hidden" name="date_fin"   id="date_fin">

                    <div class="form-actions">
                        <a href="index.php?page=offres" class="btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i> Annuler
                        </a>
                        <button type="submit" class="btn-primary" id="btn-submit">
                            <i class="fa-solid fa-check"></i> Souscrire
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</section>

<style>
/* ── Form section ──────────────────────────────────────── */
.form-section {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--r-lg);
    padding: 2rem;
    margin: 2rem 0;
}
.form-section h2 {
    font-size: 1.2rem;
    color: var(--txt);
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* ── Form group ────────────────────────────────────────── */
.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
    margin-bottom: 1.2rem;
}
.form-group label {
    font: 500 0.85rem var(--font);
    color: var(--txt-muted);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}
.label-hint {
    font: 500 0.75rem var(--font);
    color: var(--green);
    background: rgba(0,168,107,0.1);
    border: 1px solid rgba(0,168,107,0.2);
    padding: 0.15rem 0.55rem;
    border-radius: 99px;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}
.form-group input,
.form-group select {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: var(--r-sm);
    padding: 0.7rem 1rem;
    color: var(--txt);
    font: 0.9rem var(--font);
    outline: none;
    transition: all 0.25s ease;
    width: 100%;
}
.form-group input:focus,
.form-group select:focus {
    border-color: var(--green);
    background: rgba(0,168,107,0.04);
    box-shadow: 0 0 0 3px rgba(0,168,107,0.1);
}
.form-group select option { background: var(--surface); }

/* ── Téléphone ─────────────────────────────────────────── */
.tel-input-wrap {
    display: flex;
    align-items: center;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: var(--r-sm);
    overflow: hidden;
    transition: all 0.25s ease;
}
.tel-input-wrap:focus-within {
    border-color: var(--green);
    box-shadow: 0 0 0 3px rgba(0,168,107,0.1);
}
.tel-prefix {
    background: rgba(0,168,107,0.1);
    color: var(--green);
    font: 600 0.88rem var(--font);
    padding: 0.7rem 0.9rem;
    border-right: 1px solid var(--border);
    flex-shrink: 0;
}
.tel-input-wrap input {
    border: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    background: transparent !important;
    flex: 1;
}
.field-hint {
    font: 0.75rem var(--font);
    color: var(--txt-dim);
    display: flex;
    align-items: center;
    gap: 0.35rem;
}
.field-hint i { color: var(--green); }

/* ── Erreurs ───────────────────────────────────────────── */
.error-msg {
    display: none;
    color: #f87171;
    font: 0.76rem var(--font);
    margin-top: 0.25rem;
    padding: 0.3rem 0.6rem;
    background: rgba(231,76,60,0.1);
    border: 1px solid rgba(231,76,60,0.2);
    border-radius: 6px;
}
.error-msg.show { display: block; animation: fadeIn 0.2s ease; }
input.error, select.error {
    border-color: #e74c3c !important;
    background: rgba(231,76,60,0.05) !important;
}

/* ── Récapitulatif ─────────────────────────────────────── */
.recap-box {
    background: rgba(0,168,107,0.06);
    border: 1px solid rgba(0,168,107,0.2);
    border-radius: var(--r-md);
    padding: 1.1rem 1.3rem;
    margin-bottom: 1.3rem;
    animation: fadeIn 0.3s ease;
}
.recap-title {
    font: 600 0.85rem var(--font);
    color: var(--green-lt);
    margin-bottom: 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.recap-grid {
    display: grid;
    grid-template-columns: 80px 1fr;
    gap: 0.4rem 1rem;
    font: 0.85rem var(--font);
}
.recap-grid span  { color: var(--txt-dim); }
.recap-grid strong { color: var(--txt); }

/* ── Alert ─────────────────────────────────────────────── */
.alert-error {
    background: rgba(231,76,60,0.1);
    border: 1px solid rgba(231,76,60,0.3);
    color: #f87171;
    padding: 0.85rem 1.1rem;
    border-radius: var(--r-md);
    margin-bottom: 1.3rem;
    display: flex;
    align-items: center;
    gap: 0.7rem;
    font: 0.87rem var(--font);
}

/* ── Actions ───────────────────────────────────────────── */
.form-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 1.5rem;
    flex-wrap: wrap;
}
.form-actions .btn-primary,
.form-actions .btn-secondary { flex: 1; justify-content: center; }

.btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    background: var(--surface2);
    color: var(--txt-muted);
    border: 1px solid var(--border);
    border-radius: var(--r-sm);
    padding: 0.7rem 1.5rem;
    font: 500 0.9rem var(--font);
    text-decoration: none;
    cursor: pointer;
    transition: all 0.25s ease;
}
.btn-secondary:hover { border-color: var(--txt-muted); color: var(--txt); }

@keyframes fadeIn {
    from { opacity:0; transform:translateY(-4px); }
    to   { opacity:1; transform:translateY(0); }
}
</style>

<script>
/* ══════════════════════════════════════════════════════════
   souscrire.php — JavaScript complet
══════════════════════════════════════════════════════════ */

// ── Tarifs et durées par type ────────────────────────────
const TARIFS = {
    mensuel  : { prix: '29.90 DT/mois', jours: 30,  label: 'Mensuel' },
    annuel   : { prix: '299.00 DT/an',  jours: 365, label: 'Annuel'  },
    ponctuel : { prix: '49.90 DT',      jours: 7,   label: 'Ponctuel'},
};

// ── Calcul date de fin ───────────────────────────────────
function calculerDateFin(type, dateDebut) {
    const d = new Date(dateDebut);
    if (type === 'mensuel')  d.setMonth(d.getMonth() + 1);
    if (type === 'annuel')   d.setFullYear(d.getFullYear() + 1);
    if (type === 'ponctuel') d.setDate(d.getDate() + 7);
    return d.toISOString().split('T')[0];
}

// ── Format date dd/mm/yyyy ───────────────────────────────
function formatDate(dateStr) {
    const [y, m, d] = dateStr.split('-');
    return `${d}/${m}/${y}`;
}

// ── Mise à jour récapitulatif ────────────────────────────
function majRecap() {
    const type      = document.getElementById('type_abonnement').value;
    const tel       = document.getElementById('telephone').value.trim();
    const dateDebut = document.getElementById('date_debut').value;
    const recapBox  = document.getElementById('recap-box');

    if (!type) { recapBox.style.display = 'none'; return; }

    const dateFin = calculerDateFin(type, dateDebut);
    document.getElementById('date_fin').value = dateFin;

    document.getElementById('recap-type').textContent  = TARIFS[type]?.label  ?? type;
    document.getElementById('recap-prix').textContent  = TARIFS[type]?.prix   ?? '—';
    document.getElementById('recap-debut').textContent = formatDate(dateDebut);
    document.getElementById('recap-fin').textContent   = formatDate(dateFin);
    document.getElementById('recap-sms').textContent   =
        tel ? '✅ SMS activé (+216' + tel + ')' : '❌ Non renseigné (optionnel)';

    recapBox.style.display = 'block';
}

// ── Validation formulaire ────────────────────────────────
function valider() {
    let ok = true;

    // Réinitialiser
    document.querySelectorAll('.error-msg').forEach(e => e.classList.remove('show'));
    document.querySelectorAll('input, select').forEach(e => e.classList.remove('error'));

    // Nom
    const nom = document.getElementById('nom_client');
    if (!nom.value.trim()) {
        afficherErreur('nom_client', 'Le nom complet est obligatoire');
        ok = false;
    }

    // Email
    const email = document.getElementById('email');
    const emailVal = email.value.trim();
    if (!emailVal) {
        afficherErreur('email', "L'email est obligatoire");
        ok = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
        afficherErreur('email', 'Email invalide (ex: jean@example.com)');
        ok = false;
    }

    // Téléphone (optionnel mais validé si renseigné)
    const tel = document.getElementById('telephone');
    if (tel.value.trim() && !/^\d{8}$/.test(tel.value.trim())) {
        afficherErreur('telephone', 'Le numéro doit contenir exactement 8 chiffres');
        ok = false;
    }

    // Type
    const type = document.getElementById('type_abonnement');
    if (!type.value) {
        afficherErreur('type_abonnement', "Veuillez sélectionner un type d'abonnement");
        ok = false;
    }

    return ok;
}

function afficherErreur(champ, msg) {
    const input = document.getElementById(champ);
    const err   = document.getElementById('error-' + champ);
    if (input) input.classList.add('error');
    if (err)   { err.textContent = msg; err.classList.add('show'); }
}

// ── Formatage automatique du numéro ─────────────────────
document.getElementById('telephone').addEventListener('input', function () {
    // Garder uniquement les chiffres
    this.value = this.value.replace(/\D/g, '').slice(0, 8);
    // Construire le numéro complet pour le hidden
    majRecap();
});

// ── Mise à jour au changement de type ───────────────────
document.getElementById('type_abonnement').addEventListener('change', majRecap);

// ── Soumission ───────────────────────────────────────────
document.getElementById('form-souscrire').addEventListener('submit', function (e) {
    e.preventDefault();

    if (!valider()) return;

    // Construire le numéro complet +216XXXXXXXX
    const telInput = document.getElementById('telephone');
    const telVal   = telInput.value.trim();
    if (telVal) {
        telInput.value = '+216' + telVal;
    }

    // Animation bouton
    const btn = document.getElementById('btn-submit');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Traitement…';

    this.submit();
});

// ── Init au chargement ───────────────────────────────────
document.addEventListener('DOMContentLoaded', majRecap);
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>