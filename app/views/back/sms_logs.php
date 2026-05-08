<?php
/* ═══════════════════════════════════════════════════════════
   TechCare – views/back/sms_logs.php
   Logs SMS + envoi manuel + rappels automatiques
═══════════════════════════════════════════════════════════ */

// Helpers badges
function badgeTypeSms(string $type): string {
    $map = [
        'bienvenue'  => ['sms-badge-bienvenue', 'fa-star',         'Bienvenue'],
        'rappel_j7'  => ['sms-badge-rappel',    'fa-bell',         'Rappel J-7'],
        'rappel_j3'  => ['sms-badge-rappel',    'fa-bell',         'Rappel J-3'],
        'expiration' => ['sms-badge-expiration','fa-clock',        'Expiration'],
        'manuel'     => ['sms-badge-manuel',    'fa-pen',          'Manuel'],
    ];
    [$cls, $icon, $label] = $map[$type] ?? ['sms-badge-manuel', 'fa-question', $type];
    return "<span class='sms-badge $cls'><i class='fa-solid $icon'></i> $label</span>";
}

function badgeStatutSms(string $statut): string {
    $map = [
        'envoye' => ['sms-badge-envoye', 'fa-circle-check', 'Envoyé'],
        'echoue' => ['sms-badge-echoue', 'fa-circle-xmark', 'Échoué'],
        'simule' => ['sms-badge-simule', 'fa-flask',        'Simulé'],
    ];
    [$cls, $icon, $label] = $map[$statut] ?? ['sms-badge-manuel', 'fa-question', $statut];
    return "<span class='sms-badge $cls'><i class='fa-solid $icon'></i> $label</span>";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SMS Logs – TechCare Admin</title>
  <link rel="stylesheet" href="assets/css/style.css?v=1.1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="assets/js/theme.js"></script>
</head>
<body class="admin-body">

<?php include __DIR__ . '/layout/sidebar.php'; ?>

<div class="admin-wrapper">
  <?php include __DIR__ . '/layout/topbar.php'; ?>

  <div class="admin-tab active-tab">

    <!-- ── En-tête ──────────────────────────────────────── -->
    <div class="page-header">
      <div class="page-header-left">
        <h1><i class="fa-solid fa-comment-sms"></i> Logs SMS</h1>
        <p>Historique des SMS — Mode actuel :
          <span class="mode-badge">
            <?= defined('SMS_MODE') && SMS_MODE === 'reel'
                ? '<i class="fa-solid fa-wifi"></i> Envoi réel'
                : '<i class="fa-solid fa-flask"></i> Simulation' ?>
          </span>
        </p>
      </div>
      <div class="page-header-right">
        <a href="index.php?action=smsRappels" class="btn-outline"
           onclick="return confirm('Lancer les rappels automatiques J-7 et J-3 maintenant ?')">
          <i class="fa-solid fa-bell"></i> Rappels auto
        </a>
        <button class="btn-primary" onclick="ouvrirModal('modal-sms')">
          <i class="fa-solid fa-paper-plane"></i> Envoyer SMS
        </button>
      </div>
    </div>

    <div class="abo-content">

      <!-- ── Flash message ────────────────────────────────── -->
      <?php if (!empty($flash)): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
          <i class="fa-solid fa-<?= $flash['type'] === 'success' ? 'circle-check' : 'circle-exclamation' ?>"></i>
          <?= htmlspecialchars($flash['msg']) ?>
        </div>
      <?php endif; ?>

      <!-- ── Mini stats ────────────────────────────────────── -->
      <div class="abo-stats">
        <div class="abo-stat-card">
          <div class="abo-stat-icon total"><i class="fa-solid fa-comment-sms"></i></div>
          <div>
            <span class="abo-stat-val"><?= intval($stats['total']     ?? 0) ?></span>
            <span class="abo-stat-label">Total SMS</span>
          </div>
        </div>
        <div class="abo-stat-card">
          <div class="abo-stat-icon actif"><i class="fa-solid fa-circle-check"></i></div>
          <div>
            <span class="abo-stat-val"><?= intval($stats['envoyes']   ?? 0) ?></span>
            <span class="abo-stat-label">Envoyés</span>
          </div>
        </div>
        <div class="abo-stat-card">
          <div class="abo-stat-icon expire"><i class="fa-solid fa-circle-xmark"></i></div>
          <div>
            <span class="abo-stat-val"><?= intval($stats['echoues']   ?? 0) ?></span>
            <span class="abo-stat-label">Échoués</span>
          </div>
        </div>
        <div class="abo-stat-card">
          <div class="abo-stat-icon suspendu"><i class="fa-solid fa-flask"></i></div>
          <div>
            <span class="abo-stat-val"><?= intval($stats['simules']   ?? 0) ?></span>
            <span class="abo-stat-label">Simulés</span>
          </div>
        </div>
        <div class="abo-stat-card">
          <div class="abo-stat-icon total"><i class="fa-solid fa-bell"></i></div>
          <div>
            <span class="abo-stat-val"><?= intval($stats['rappels']   ?? 0) ?></span>
            <span class="abo-stat-label">Rappels</span>
          </div>
        </div>
        <div class="abo-stat-card">
          <div class="abo-stat-icon actif"><i class="fa-solid fa-star"></i></div>
          <div>
            <span class="abo-stat-val"><?= intval($stats['bienvenues'] ?? 0) ?></span>
            <span class="abo-stat-label">Bienvenus</span>
          </div>
        </div>
      </div>

      <!-- ── Filtres ───────────────────────────────────────── -->
      <div class="admin-card" style="margin-bottom: 1.2rem;">
        <form method="GET" action="index.php">
          <input type="hidden" name="action" value="smsLogs">
          <div class="sms-filter-row">
            <div class="mini-search">
              <i class="fa-solid fa-magnifying-glass"></i>
              <input type="text" name="search"
                     placeholder="Nom ou téléphone…"
                     value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <select name="type_sms" class="sms-select">
              <option value="">Tous les types</option>
              <?php foreach (['bienvenue','rappel_j7','rappel_j3','expiration','manuel'] as $t): ?>
                <option value="<?= $t ?>"
                  <?= ($_GET['type_sms'] ?? '') === $t ? 'selected' : '' ?>>
                  <?= ucfirst(str_replace('_', ' ', $t)) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <select name="statut" class="sms-select">
              <option value="">Tous statuts</option>
              <option value="envoye" <?= ($_GET['statut'] ?? '') === 'envoye' ? 'selected' : '' ?>>Envoyé</option>
              <option value="echoue" <?= ($_GET['statut'] ?? '') === 'echoue' ? 'selected' : '' ?>>Échoué</option>
              <option value="simule" <?= ($_GET['statut'] ?? '') === 'simule' ? 'selected' : '' ?>>Simulé</option>
            </select>
            <button type="submit" class="btn-primary" style="padding:0.45rem 1.1rem;">
              <i class="fa-solid fa-filter"></i> Filtrer
            </button>
            <a href="index.php?action=smsLogs" class="btn-outline" style="padding:0.45rem 1rem;">
              <i class="fa-solid fa-xmark"></i> Reset
            </a>
          </div>
        </form>
      </div>

      <!-- ── Table des logs ─────────────────────────────────── -->
      <div class="admin-card">
        <div class="card-head">
          <h3><i class="fa-solid fa-list"></i> Historique — <?= $total ?> SMS</h3>
          <div class="card-actions">
            <span style="font:0.8rem var(--font);color:var(--txt-dim);">
              Page <?= $page ?> / <?= $pages ?>
            </span>
          </div>
        </div>

        <div class="table-scroll">
          <table class="data-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Client</th>
                <th>Téléphone</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Message</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($logs)): ?>
                <tr>
                  <td colspan="8" class="table-empty">
                    <i class="fa-solid fa-comment-slash"></i>
                    <p>Aucun SMS enregistré</p>
                    <button class="btn-primary" onclick="ouvrirModal('modal-sms')">
                      <i class="fa-solid fa-paper-plane"></i> Envoyer le premier SMS
                    </button>
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($logs as $i => $log): ?>
                  <tr style="animation: cardIn 0.3s ease <?= $i * 0.03 ?>s both">
                    <td><span style="color:var(--txt-dim);font-size:0.8rem">#<?= $log['id'] ?></span></td>

                    <td>
                      <div class="abo-client-cell">
                        <div class="abo-avatar">
                          <?= strtoupper(substr($log['nom_client'], 0, 2)) ?>
                        </div>
                        <div>
                          <div class="abo-client-name"><?= htmlspecialchars($log['nom_client']) ?></div>
                          <?php if (!empty($log['abonnement_id'])): ?>
                            <div class="abo-client-since">Abo #<?= $log['abonnement_id'] ?></div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </td>

                    <td style="font-family:monospace;font-size:0.84rem;color:var(--txt-muted)">
                      <?= htmlspecialchars($log['telephone']) ?>
                    </td>

                    <td><?= badgeTypeSms($log['type_sms']) ?></td>
                    <td><?= badgeStatutSms($log['statut']) ?></td>

                    <td>
                      <span class="sms-msg-preview"
                            title="<?= htmlspecialchars($log['message']) ?>"
                            onclick="afficherMessage(this)"
                            style="cursor:pointer;">
                        <?= htmlspecialchars(mb_substr($log['message'], 0, 50)) ?>…
                      </span>
                    </td>

                    <td style="font-size:0.8rem;color:var(--txt-dim);white-space:nowrap">
                      <?= date('d/m/Y', strtotime($log['created_at'])) ?><br>
                      <span style="font-size:0.72rem"><?= date('H:i', strtotime($log['created_at'])) ?></span>
                    </td>

                    <td>
                      <div class="tbl-actions">
                        <!-- Renvoyer SMS -->
                        <?php if (!empty($log['abonnement_id'])): ?>
                          <a href="index.php?action=smsRenvoyer&id=<?= $log['abonnement_id'] ?>"
                             class="btn-sm-green"
                             title="Renvoyer SMS de bienvenue"
                             onclick="return confirm('Renvoyer le SMS ?')">
                            <i class="fa-solid fa-rotate-right"></i>
                          </a>
                        <?php endif; ?>
                        <!-- Supprimer log -->
                        <a href="index.php?action=smsDeleteLog&id=<?= $log['id'] ?>"
                           class="btn-del"
                           title="Supprimer ce log"
                           onclick="return confirm('Supprimer ce log SMS ?')">
                          <i class="fa-solid fa-trash"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- ── Pagination ───────────────────────────────────── -->
        <?php if ($pages > 1): ?>
          <div class="sms-pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
              <a href="index.php?action=smsLogs&page=<?= $i ?>&<?= http_build_query(array_filter([
                'type_sms' => $_GET['type_sms'] ?? '',
                'statut'   => $_GET['statut']   ?? '',
                'search'   => $_GET['search']   ?? '',
              ])) ?>"
                 class="sms-page-btn <?= $i === $page ? 'active' : '' ?>">
                <?= $i ?>
              </a>
            <?php endfor; ?>
          </div>
        <?php endif; ?>
      </div>

    </div><!-- /abo-content -->
  </div><!-- /admin-tab -->
</div><!-- /admin-wrapper -->

<!-- ══════════════════════════════════════════════════════
     MODAL — Envoi SMS manuel
══════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="modal-sms" style="display:none;">
  <div class="modal-box" style="max-width:520px;">
    <button class="modal-close" onclick="fermerModal('modal-sms')">
      <i class="fa-solid fa-xmark"></i>
    </button>
    <div class="modal-body">
      <h2 style="margin-bottom:1.5rem;font-size:1.15rem;display:flex;align-items:center;gap:0.5rem;">
        <i class="fa-solid fa-paper-plane" style="color:var(--green)"></i>
        Envoyer un SMS manuel
      </h2>

      <form method="POST" action="index.php?action=smsEnvoyer" id="form-sms-manuel">

        <div class="form-group">
          <label><i class="fa-solid fa-phone"></i> Téléphone *</label>
          <div class="tel-input-wrap">
            <span class="tel-prefix">+216</span>
            <input type="tel" name="telephone" id="modal-tel"
                   placeholder="98 000 000" maxlength="8" required>
          </div>
          <span class="error-msg" id="modal-err-tel"></span>
        </div>

        <div class="form-group">
          <label><i class="fa-solid fa-user"></i> Nom du client</label>
          <input type="text" name="nom_client" placeholder="Nom complet (optionnel)">
        </div>

        <div class="form-group">
          <label><i class="fa-solid fa-hashtag"></i> ID Abonnement (optionnel)</label>
          <input type="number" name="abonnement_id" placeholder="Ex: 12" min="1">
        </div>

        <!-- Templates prédéfinis -->
        <div class="form-group">
          <label><i class="fa-solid fa-wand-magic-sparkles"></i> Template rapide</label>
          <select id="template-select" onchange="appliquerTemplate(this.value)">
            <option value="">-- Choisir un template --</option>
            <option value="bienvenue">🎉 Bienvenue</option>
            <option value="rappel">🔔 Rappel expiration</option>
            <option value="promo">🎁 Promotion</option>
          </select>
        </div>

        <div class="form-group">
          <label>
            <i class="fa-solid fa-message"></i> Message *
            <span id="modal-char-count" style="margin-left:auto;color:var(--txt-dim);font-weight:400;">
              0/160
            </span>
          </label>
          <textarea name="message" id="modal-message"
                    rows="4" maxlength="160"
                    placeholder="Votre message SMS…"
                    style="resize:vertical" required></textarea>
          <span class="error-msg" id="modal-err-msg"></span>
        </div>

        <div style="display:flex;gap:0.75rem;margin-top:0.5rem;">
          <button type="submit" class="btn-primary" style="flex:1;">
            <i class="fa-solid fa-paper-plane"></i> Envoyer
          </button>
          <button type="button" class="btn-outline"
                  onclick="fermerModal('modal-sms')" style="flex:1;">
            Annuler
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════
     MODAL — Affichage message complet
══════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="modal-msg" style="display:none;">
  <div class="modal-box" style="max-width:420px;">
    <button class="modal-close" onclick="fermerModal('modal-msg')">
      <i class="fa-solid fa-xmark"></i>
    </button>
    <div class="modal-body">
      <h3 style="margin-bottom:1rem;font-size:1rem;color:var(--green);">
        <i class="fa-solid fa-message"></i> Message complet
      </h3>
      <p id="modal-msg-content"
         style="color:var(--txt-muted);font:0.9rem/1.7 var(--font);
                background:var(--surface2);padding:1rem;border-radius:var(--r-md);">
      </p>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════
     STYLES SPÉCIFIQUES SMS
══════════════════════════════════════════════════════ -->
<style>
/* Badges SMS */
.sms-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.28rem;
    padding: 0.2rem 0.65rem;
    border-radius: var(--r-full);
    font: 600 0.72rem var(--font);
    white-space: nowrap;
}
.sms-badge-bienvenue  { background: rgba(0,168,107,0.14);   color: var(--green-lt); }
.sms-badge-rappel     { background: rgba(243,156,18,0.14);  color: #fbbf24; }
.sms-badge-expiration { background: rgba(231,76,60,0.14);   color: #f87171; }
.sms-badge-manuel     { background: rgba(124,58,237,0.14);  color: #a78bfa; }
.sms-badge-envoye     { background: rgba(0,168,107,0.14);   color: var(--green-lt); }
.sms-badge-echoue     { background: rgba(231,76,60,0.14);   color: #f87171; }
.sms-badge-simule     { background: rgba(8,145,178,0.14);   color: #22d3ee; }

/* Mode badge */
.mode-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: rgba(0,168,107,0.1);
    border: 1px solid rgba(0,168,107,0.2);
    color: var(--green-lt);
    font: 600 0.75rem var(--font);
    padding: 0.18rem 0.6rem;
    border-radius: var(--r-full);
}

/* Filtre row */
.sms-filter-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.2rem;
    flex-wrap: wrap;
}
.sms-select {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: var(--r-sm);
    color: var(--txt);
    padding: 0.42rem 0.85rem;
    font: 0.85rem var(--font);
    outline: none;
    cursor: pointer;
    transition: var(--t);
}
.sms-select:focus { border-color: var(--green); }
.sms-select option { background: var(--surface); }

/* Preview message */
.sms-msg-preview {
    font-size: 0.8rem;
    color: var(--txt-muted);
    max-width: 200px;
    display: inline-block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    transition: color 0.2s;
}
.sms-msg-preview:hover { color: var(--green-lt); }

/* Pagination */
.sms-pagination {
    display: flex;
    gap: 0.4rem;
    padding: 1rem 1.4rem;
    border-top: 1px solid var(--border);
    justify-content: center;
    flex-wrap: wrap;
}
.sms-page-btn {
    width: 34px; height: 34px;
    display: flex; align-items: center; justify-content: center;
    border-radius: var(--r-sm);
    border: 1px solid var(--border);
    color: var(--txt-muted);
    font: 500 0.85rem var(--font);
    text-decoration: none;
    transition: var(--t);
}
.sms-page-btn:hover,
.sms-page-btn.active {
    background: var(--green);
    border-color: var(--green);
    color: #fff;
}

/* Téléphone dans modal */
.tel-input-wrap {
    display: flex;
    align-items: center;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: var(--r-sm);
    overflow: hidden;
    transition: all 0.25s;
}
.tel-input-wrap:focus-within {
    border-color: var(--green);
    box-shadow: 0 0 0 3px rgba(0,168,107,0.1);
}
.tel-prefix {
    background: rgba(0,168,107,0.1);
    color: var(--green);
    font: 600 0.88rem var(--font);
    padding: 0.7rem 0.85rem;
    border-right: 1px solid var(--border);
    flex-shrink: 0;
}
.tel-input-wrap input {
    border: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    background: transparent !important;
    flex: 1;
    padding: 0.7rem 0.85rem;
    color: var(--txt);
    font: 0.9rem var(--font);
    outline: none;
}

/* Error msg */
.error-msg {
    display: none;
    color: #f87171;
    font: 0.75rem var(--font);
    margin-top: 0.25rem;
    padding: 0.28rem 0.6rem;
    background: rgba(231,76,60,0.1);
    border-radius: 6px;
}
.error-msg.show { display: block; }
</style>

<!-- ══════════════════════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════════════════════ -->
<script>
// ── Modals ───────────────────────────────────────────────
function ouvrirModal(id) {
    document.getElementById(id).style.display = 'flex';
}
function fermerModal(id) {
    document.getElementById(id).style.display = 'none';
}

// Fermer en cliquant dehors
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', e => {
        if (e.target === overlay) overlay.style.display = 'none';
    });
});

// ── Compteur caractères ──────────────────────────────────
document.getElementById('modal-message')?.addEventListener('input', function () {
    const count = this.value.length;
    const el    = document.getElementById('modal-char-count');
    el.textContent = count + '/160';
    el.style.color = count > 140 ? 'var(--danger)' : 'var(--txt-dim)';
});

// ── Formatage téléphone ──────────────────────────────────
document.getElementById('modal-tel')?.addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '').slice(0, 8);
});

// ── Templates prédéfinis ─────────────────────────────────
const TEMPLATES = {
    bienvenue : "Bienvenue chez TechCare ! Votre abonnement est actif. Merci de votre confiance. techcare.tn",
    rappel    : "TechCare : Votre abonnement expire bientot. Renouvelez sur techcare.tn pour continuer a profiter de nos services.",
    promo     : "TechCare : Offre speciale ! -20% sur l'abonnement annuel ce mois-ci. Profitez-en sur techcare.tn",
};

function appliquerTemplate(val) {
    const textarea = document.getElementById('modal-message');
    if (val && TEMPLATES[val]) {
        textarea.value = TEMPLATES[val];
        textarea.dispatchEvent(new Event('input'));
    }
}

// ── Validation formulaire modal ──────────────────────────
document.getElementById('form-sms-manuel')?.addEventListener('submit', function (e) {
    e.preventDefault();
    let ok = true;

    const tel = document.getElementById('modal-tel');
    const msg = document.getElementById('modal-message');
    const errTel = document.getElementById('modal-err-tel');
    const errMsg = document.getElementById('modal-err-msg');

    errTel.classList.remove('show');
    errMsg.classList.remove('show');

    if (!tel.value.trim() || !/^\d{8}$/.test(tel.value.trim())) {
        errTel.textContent = 'Numéro à 8 chiffres obligatoire';
        errTel.classList.add('show');
        ok = false;
    }
    if (!msg.value.trim()) {
        errMsg.textContent = 'Le message est obligatoire';
        errMsg.classList.add('show');
        ok = false;
    }

    if (!ok) return;

    // Construire le numéro complet avant envoi
    tel.value = '+216' + tel.value.trim();
    this.submit();
});

// ── Afficher message complet ─────────────────────────────
function afficherMessage(el) {
    document.getElementById('modal-msg-content').textContent = el.getAttribute('title');
    ouvrirModal('modal-msg');
}

// ── Raccourci clavier Escape ─────────────────────────────
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay').forEach(m => m.style.display = 'none');
    }
});
</script>

</body>
</html>