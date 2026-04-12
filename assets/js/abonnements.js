/* ═══════════════════════════════════════════════════════════
   TechCare – abonnements.js
   Gestion complète des abonnements (liste, ajout, modif, détail)
═══════════════════════════════════════════════════════════ */

/* ══════════════════════════════════════════════════════════
   1. UTILITAIRES GLOBAUX
══════════════════════════════════════════════════════════ */

/**
 * Affiche un toast de notification
 * @param {string} message
 * @param {'success'|'error'|'info'|'warn'} type
 */
function showToast(message, type = 'success') {
  let container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  const icons = {
    success : 'fa-circle-check',
    error   : 'fa-circle-xmark',
    info    : 'fa-circle-info',
    warn    : 'fa-triangle-exclamation'
  };

  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.innerHTML = `<i class="fa-solid ${icons[type]}"></i> ${message}`;
  container.appendChild(toast);

  setTimeout(() => {
    toast.classList.add('toast-hide');
    setTimeout(() => toast.remove(), 350);
  }, 3500);
}

/**
 * Boîte de confirmation personnalisée (remplace confirm())
 * @param {string} message
 * @param {Function} onConfirm
 * @param {string} titre
 */
function showConfirm(message, onConfirm, titre = 'Confirmer la suppression') {
  // Supprimer une ancienne dialog si elle existe
  document.querySelector('.confirm-overlay')?.remove();

  const overlay = document.createElement('div');
  overlay.className = 'confirm-overlay';
  overlay.innerHTML = `
    <div class="confirm-box">
      <div class="confirm-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
      <h3>${titre}</h3>
      <p>${message}</p>
      <div class="confirm-actions">
        <button class="btn-outline" id="confirm-cancel">Annuler</button>
        <button class="btn-danger"  id="confirm-ok">
          <i class="fa-solid fa-trash"></i> Supprimer
        </button>
      </div>
    </div>
  `;
  document.body.appendChild(overlay);

  overlay.querySelector('#confirm-cancel').addEventListener('click', () => overlay.remove());
  overlay.querySelector('#confirm-ok').addEventListener('click', () => {
    overlay.remove();
    onConfirm();
  });

  // Fermer en cliquant hors de la boîte
  overlay.addEventListener('click', e => { if (e.target === overlay) overlay.remove(); });
}

/**
 * Formate une date ISO en dd/mm/yyyy
 * @param {string} dateStr
 */
function formatDate(dateStr) {
  if (!dateStr) return '—';
  const [y, m, d] = dateStr.split('-');
  return `${d}/${m}/${y}`;
}

/**
 * Calcule les jours restants entre aujourd'hui et une date
 * @param {string} dateFin
 * @returns {number} négatif si expiré
 */
function joursRestants(dateFin) {
  const fin = new Date(dateFin);
  const auj = new Date();
  auj.setHours(0, 0, 0, 0);
  fin.setHours(0, 0, 0, 0);
  return Math.round((fin - auj) / (1000 * 60 * 60 * 24));
}

/**
 * Retourne le HTML du badge statut
 * @param {string} statut
 */
function badgeStatut(statut) {
  const icons = {
    actif    : 'fa-circle-check',
    suspendu : 'fa-pause-circle',
    'expiré' : 'fa-clock',
    'annulé' : 'fa-ban'
  };
  const icon = icons[statut] || 'fa-circle';
  return `<span class="badge badge-${statut}">
            <i class="fa-solid ${icon}"></i> ${statut}
          </span>`;
}

/**
 * Retourne le HTML du badge type
 * @param {string} type
 */
function badgeType(type) {
  return `<span class="type-badge type-${type}">${type}</span>`;
}

/**
 * Retourne le HTML des jours restants
 * @param {string} dateFin
 * @param {string} statut
 */
function badgeDaysLeft(dateFin, statut) {
  if (statut === 'annulé' || statut === 'expiré') {
    return `<span class="days-left danger">Expiré</span>`;
  }
  const jours = joursRestants(dateFin);
  if (jours < 0)  return `<span class="days-left danger">Expiré</span>`;
  if (jours === 0) return `<span class="days-left danger">Expire aujourd'hui</span>`;
  if (jours <= 7)  return `<span class="days-left warning">${jours}j restants</span>`;
  return `<span class="days-left ok">${jours}j restants</span>`;
}

/**
 * Initiales d'un nom (ex: "Ahmed Ben" → "AB")
 * @param {string} nom
 */
function initiales(nom) {
  return nom.trim().split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
}


/* ══════════════════════════════════════════════════════════
   2. PAGE LISTE – index.php
══════════════════════════════════════════════════════════ */

class ListeAbonnements {
  constructor() {
    this.tbody       = document.querySelector('#abo-tbody');
    this.searchInput = document.querySelector('#abo-search');
    this.filterBtns  = document.querySelectorAll('.chip[data-filter]');
    this.sortSelect  = document.querySelector('#abo-sort');
    this.countBadge  = document.querySelector('#abo-count');
    this.data        = [];
    this.filtre      = 'tous';

    if (!this.tbody) return;
    this.init();
  }

  async init() {
    await this.charger();
    this.bindEvents();
  }

  // ── Chargement depuis l'API ──────────────────────────
  async charger() {
    try {
      this.tbody.innerHTML = `
        <tr><td colspan="8" style="text-align:center;padding:3rem;color:var(--txt-dim);">
          <i class="fa-solid fa-spinner fa-spin"></i> Chargement…
        </td></tr>`;

      const res  = await fetch('api/abonnements.php?action=liste');
      const json = await res.json();

      if (json.error) throw new Error(json.error);
      this.data = json;
      this.render();
    } catch (err) {
      this.tbody.innerHTML = `
        <tr><td colspan="8" class="table-empty">
          <i class="fa-solid fa-circle-exclamation" style="color:var(--danger)"></i>
          <p>Erreur de chargement : ${err.message}</p>
        </td></tr>`;
    }
  }

  // ── Filtrage + tri + rendu ───────────────────────────
  render() {
    let liste = [...this.data];
    const q   = this.searchInput?.value.toLowerCase().trim() ?? '';

    // Filtre statut
    if (this.filtre !== 'tous') {
      liste = liste.filter(a => a.statut === this.filtre);
    }

    // Recherche texte
    if (q) {
      liste = liste.filter(a =>
        a.nom_client.toLowerCase().includes(q) ||
        a.type_abonnement.toLowerCase().includes(q) ||
        String(a.id).includes(q)
      );
    }

    // Tri
    const tri = this.sortSelect?.value ?? 'date_desc';
    const tris = {
      date_desc  : (a, b) => new Date(b.date_debut) - new Date(a.date_debut),
      date_asc   : (a, b) => new Date(a.date_debut) - new Date(b.date_debut),
      prix_desc  : (a, b) => b.prix - a.prix,
      prix_asc   : (a, b) => a.prix - b.prix,
      nom_asc    : (a, b) => a.nom_client.localeCompare(b.nom_client),
      fin_asc    : (a, b) => new Date(a.date_fin) - new Date(b.date_fin),
    };
    liste.sort(tris[tri] || tris.date_desc);

    // Mise à jour compteur
    if (this.countBadge) this.countBadge.textContent = liste.length;

    // Rendu tableau
    if (liste.length === 0) {
      this.tbody.innerHTML = `
        <tr><td colspan="8" class="table-empty">
          <i class="fa-solid fa-inbox"></i>
          <p>Aucun abonnement trouvé</p>
          <a href="index.php?action=ajouter" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Ajouter
          </a>
        </td></tr>`;
      return;
    }

    this.tbody.innerHTML = liste.map((a, i) => `
      <tr style="animation: cardIn 0.3s ease ${i * 0.04}s both">
        <td><span style="color:var(--txt-dim);font-size:0.8rem">#${a.id}</span></td>
        <td>
          <div class="abo-client-cell">
            <div class="abo-avatar">${initiales(a.nom_client)}</div>
            <div>
              <div class="abo-client-name">${a.nom_client}</div>
              <div class="abo-client-since">depuis ${formatDate(a.date_debut)}</div>
            </div>
          </div>
        </td>
        <td>${badgeType(a.type_abonnement)}</td>
        <td class="abo-price">${parseFloat(a.prix).toFixed(2)} DT</td>
        <td>
          <div class="abo-dates">
            <span>${formatDate(a.date_debut)}</span>
            <small>→ ${formatDate(a.date_fin)}</small>
          </div>
        </td>
        <td>${badgeDaysLeft(a.date_fin, a.statut)}</td>
        <td>${badgeStatut(a.statut)}</td>
        <td>
          <div class="tbl-actions">
            <a href="index.php?action=detail&id=${a.id}"   class="btn-sm-green">
              <i class="fa-solid fa-eye"></i>
            </a>
            <a href="index.php?action=modifier&id=${a.id}" class="btn-edit">
              <i class="fa-solid fa-pen"></i>
            </a>
            <button class="btn-del" onclick="liste.supprimer(${a.id}, '${a.nom_client}')">
              <i class="fa-solid fa-trash"></i>
            </button>
          </div>
        </td>
      </tr>`).join('');

    this.updateStats();
  }

  // ── Statistiques mini-cards ──────────────────────────
  updateStats() {
    const counts = { actif: 0, suspendu: 0, 'expiré': 0, 'annulé': 0 };
    this.data.forEach(a => { if (counts[a.statut] !== undefined) counts[a.statut]++; });

    const set = (id, val) => {
      const el = document.querySelector(id);
      if (el) el.textContent = val;
    };
    set('#stat-total',    this.data.length);
    set('#stat-actif',    counts['actif']);
    set('#stat-suspendu', counts['suspendu']);
    set('#stat-expire',   counts['expiré'] + counts['annulé']);
  }

  // ── Suppression ──────────────────────────────────────
  supprimer(id, nom) {
    showConfirm(
      `Voulez-vous vraiment supprimer l'abonnement de <strong>${nom}</strong> ? Cette action est irréversible.`,
      async () => {
        try {
          const res  = await fetch(`api/abonnements.php?action=supprimer&id=${id}`);
          const json = await res.json();
          if (json.success) {
            showToast(`Abonnement de ${nom} supprimé.`, 'success');
            await this.charger();
          } else {
            showToast('Erreur lors de la suppression.', 'error');
          }
        } catch {
          showToast('Erreur réseau.', 'error');
        }
      }
    );
  }

  // ── Événements ───────────────────────────────────────
  bindEvents() {
    // Recherche live
    this.searchInput?.addEventListener('input', () => this.render());

    // Chips filtre statut
    this.filterBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        this.filterBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        this.filtre = btn.dataset.filter;
        this.render();
      });
    });

    // Tri
    this.sortSelect?.addEventListener('change', () => this.render());
  }
}


/* ══════════════════════════════════════════════════════════
   3. PAGE AJOUTER – ajouter.php
══════════════════════════════════════════════════════════ */

class FormulaireAjouter {
  constructor() {
    this.form = document.querySelector('#form-ajouter');
    if (!this.form) return;
    this.init();
  }

  init() {
    this.setDateMin();
    this.bindDateFin();
    this.bindSubmit();
    this.bindTypeChangePrix();
  }

  // Date début = aujourd'hui minimum
  setDateMin() {
    const debut = this.form.querySelector('[name="date_debut"]');
    if (debut) debut.min = new Date().toISOString().split('T')[0];
  }

  // Date fin doit être après date début
  bindDateFin() {
    const debut = this.form.querySelector('[name="date_debut"]');
    const fin   = this.form.querySelector('[name="date_fin"]');
    if (!debut || !fin) return;

    debut.addEventListener('change', () => {
      fin.min = debut.value;
      // Auto-calcul date fin selon type
      this.autoDateFin(debut.value);
    });
  }

  // Suggère une date de fin selon le type choisi
  autoDateFin(dateDebut) {
    const type = this.form.querySelector('[name="type_abonnement"]')?.value;
    const fin  = this.form.querySelector('[name="date_fin"]');
    if (!type || !fin || !dateDebut) return;

    const d = new Date(dateDebut);
    if (type === 'mensuel')  d.setMonth(d.getMonth() + 1);
    if (type === 'annuel')   d.setFullYear(d.getFullYear() + 1);
    if (type === 'ponctuel') d.setDate(d.getDate() + 7);

    fin.value = d.toISOString().split('T')[0];
  }

  // Prix suggéré selon type
  bindTypeChangePrix() {
    const type = this.form.querySelector('[name="type_abonnement"]');
    const prix = this.form.querySelector('[name="prix"]');
    if (!type || !prix) return;

    const tarifs = { mensuel: '29.90', annuel: '299.00', ponctuel: '49.90' };
    type.addEventListener('change', () => {
      if (!prix.value || prix.dataset.manual !== 'true') {
        prix.value = tarifs[type.value] ?? '';
      }
      // Recalcule date fin
      const debut = this.form.querySelector('[name="date_debut"]')?.value;
      if (debut) this.autoDateFin(debut);
    });
    prix.addEventListener('input', () => { prix.dataset.manual = 'true'; });
  }

  // Validation et soumission AJAX
  bindSubmit() {
    this.form.addEventListener('submit', async e => {
      e.preventDefault();
      if (!this.valider()) return;

      const btn = this.form.querySelector('[type="submit"]');
      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Enregistrement…';

      const data = Object.fromEntries(new FormData(this.form));
      data.prix  = parseFloat(data.prix);

      try {
        const res  = await fetch('api/abonnements.php?action=ajouter', {
          method  : 'POST',
          headers : { 'Content-Type': 'application/json' },
          body    : JSON.stringify(data)
        });
        const json = await res.json();

        if (json.success) {
          showToast('Abonnement ajouté avec succès !', 'success');
          setTimeout(() => window.location.href = 'index.php?action=liste', 1200);
        } else {
          showToast(json.error || 'Erreur lors de l\'ajout.', 'error');
          btn.disabled = false;
          btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Enregistrer';
        }
      } catch {
        showToast('Erreur réseau.', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Enregistrer';
      }
    });
  }

  // Validation côté client
  valider() {
    let ok = true;
    const champs = [
      { name: 'nom_client',       msg: 'Le nom du client est obligatoire.' },
      { name: 'type_abonnement',  msg: 'Choisissez un type.' },
      { name: 'prix',             msg: 'Le prix est obligatoire.' },
      { name: 'date_debut',       msg: 'La date de début est obligatoire.' },
      { name: 'date_fin',         msg: 'La date de fin est obligatoire.' },
    ];

    champs.forEach(({ name, msg }) => {
      const input = this.form.querySelector(`[name="${name}"]`);
      const err   = this.form.querySelector(`#err-${name}`);
      if (!input) return;

      const vide = !input.value.trim();
      input.classList.toggle('err', vide);
      if (err) { err.textContent = msg; err.classList.toggle('show', vide); }
      if (vide) ok = false;
    });

    // Prix > 0
    const prixInput = this.form.querySelector('[name="prix"]');
    const prixErr   = this.form.querySelector('#err-prix');
    if (prixInput && parseFloat(prixInput.value) <= 0) {
      prixInput.classList.add('err');
      if (prixErr) { prixErr.textContent = 'Le prix doit être supérieur à 0.'; prixErr.classList.add('show'); }
      ok = false;
    }

    // Date fin >= date début
    const debut = this.form.querySelector('[name="date_debut"]')?.value;
    const fin   = this.form.querySelector('[name="date_fin"]')?.value;
    const finEl = this.form.querySelector('[name="date_fin"]');
    const finErr= this.form.querySelector('#err-date_fin');
    if (debut && fin && fin < debut) {
      finEl?.classList.add('err');
      if (finErr) { finErr.textContent = 'La date de fin doit être après la date de début.'; finErr.classList.add('show'); }
      ok = false;
    }

    return ok;
  }
}


/* ══════════════════════════════════════════════════════════
   4. PAGE MODIFIER – modifier.php
══════════════════════════════════════════════════════════ */

class FormulaireModifier {
  constructor() {
    this.form = document.querySelector('#form-modifier');
    if (!this.form) return;
    this.init();
  }

  init() {
    this.bindDateFin();
    this.bindSubmit();
    this.highlightChanges();
  }

  // Synchronise date_fin min avec date_debut
  bindDateFin() {
    const debut = this.form.querySelector('[name="date_debut"]');
    const fin   = this.form.querySelector('[name="date_fin"]');
    if (debut && fin) {
      fin.min = debut.value;
      debut.addEventListener('change', () => { fin.min = debut.value; });
    }
  }

  // Soumission AJAX
  bindSubmit() {
    this.form.addEventListener('submit', async e => {
      e.preventDefault();
      if (!this.valider()) return;

      const btn = this.form.querySelector('[type="submit"]');
      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Enregistrement…';

      const id   = this.form.dataset.id;
      const data = Object.fromEntries(new FormData(this.form));
      data.prix  = parseFloat(data.prix);

      try {
        const res  = await fetch(`api/abonnements.php?action=modifier&id=${id}`, {
          method  : 'POST',
          headers : { 'Content-Type': 'application/json' },
          body    : JSON.stringify(data)
        });
        const json = await res.json();

        if (json.success) {
          showToast('Abonnement modifié avec succès !', 'success');
          setTimeout(() => window.location.href = 'index.php?action=liste', 1200);
        } else {
          showToast(json.error || 'Erreur lors de la modification.', 'error');
          btn.disabled = false;
          btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Enregistrer';
        }
      } catch {
        showToast('Erreur réseau.', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Enregistrer';
      }
    });
  }

  // Validation (même logique que FormulaireAjouter)
  valider() {
    let ok = true;
    ['nom_client', 'prix', 'date_debut', 'date_fin'].forEach(name => {
      const input = this.form.querySelector(`[name="${name}"]`);
      const err   = this.form.querySelector(`#err-${name}`);
      if (!input) return;
      const vide  = !input.value.trim();
      input.classList.toggle('err', vide);
      if (err) err.classList.toggle('show', vide);
      if (vide) ok = false;
    });

    const debut = this.form.querySelector('[name="date_debut"]')?.value;
    const fin   = this.form.querySelector('[name="date_fin"]')?.value;
    if (debut && fin && fin < debut) {
      this.form.querySelector('[name="date_fin"]')?.classList.add('err');
      ok = false;
    }
    return ok;
  }

  // Surligne les champs modifiés (comparaison valeurs originales)
  highlightChanges() {
    this.form.querySelectorAll('input, select, textarea').forEach(input => {
      const original = input.value;
      input.addEventListener('input', () => {
        const changed = input.value !== original;
        input.style.borderColor = changed ? 'var(--green)' : '';
      });
    });
  }
}


/* ══════════════════════════════════════════════════════════
   5. PAGE DETAIL – detail.php
══════════════════════════════════════════════════════════ */

class DetailAbonnement {
  constructor() {
    this.progressFill = document.querySelector('.progress-fill');
    this.pct          = parseFloat(this.progressFill?.dataset.pct ?? 0);
    if (document.querySelector('.detail-card')) this.init();
  }

  init() {
    this.animateProgress();
    this.bindSuppression();
  }

  // Anime la barre de progression au chargement
  animateProgress() {
    if (!this.progressFill) return;
    // Reset à 0 puis anime
    this.progressFill.style.width = '0%';
    requestAnimationFrame(() => {
      setTimeout(() => {
        this.progressFill.style.width = this.pct + '%';
      }, 120);
    });
  }

  // Bouton supprimer dans la page detail
  bindSuppression() {
    const btn = document.querySelector('#btn-supprimer-detail');
    if (!btn) return;

    btn.addEventListener('click', e => {
      e.preventDefault();
      const id  = btn.dataset.id;
      const nom = btn.dataset.nom;

      showConfirm(
        `Voulez-vous vraiment supprimer l'abonnement de <strong>${nom}</strong> ? Cette action est irréversible.`,
        async () => {
          try {
            const res  = await fetch(`api/abonnements.php?action=supprimer&id=${id}`);
            const json = await res.json();
            if (json.success) {
              showToast('Abonnement supprimé.', 'success');
              setTimeout(() => window.location.href = 'index.php?action=liste', 1200);
            } else {
              showToast('Erreur lors de la suppression.', 'error');
            }
          } catch {
            showToast('Erreur réseau.', 'error');
          }
        }
      );
    });
  }
}


/* ══════════════════════════════════════════════════════════
   6. INITIALISATION GLOBALE
══════════════════════════════════════════════════════════ */

let liste; // exposé globalement pour les onclick inline du tableau

document.addEventListener('DOMContentLoaded', () => {
  liste = new ListeAbonnements();
  new FormulaireAjouter();
  new FormulaireModifier();
  new DetailAbonnement();

  // Sidebar toggle admin (si présent)
  const toggleBtn = document.querySelector('.toggle-sidebar-btn');
  const sidebar   = document.querySelector('.sidebar');
  const wrapper   = document.querySelector('.admin-wrapper');

  toggleBtn?.addEventListener('click', () => {
    sidebar?.classList.toggle('collapsed');
    wrapper?.classList.toggle('expanded');
  });

  // Afficher la date courante dans la topbar
  const dateEl = document.querySelector('.topbar-date');
  if (dateEl) {
    dateEl.textContent = new Date().toLocaleDateString('fr-FR', {
      weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    });
  }

  // Fermer toast existant au clic
  document.addEventListener('click', e => {
    if (e.target.closest('.toast')) e.target.closest('.toast').remove();
  });
});