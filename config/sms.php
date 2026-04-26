<?php
/* ═══════════════════════════════════════════════════════════
   TechCare – config/sms.php
   MODE SANDBOX (100% gratuit, sans inscription)
═══════════════════════════════════════════════════════════ */

// ── MODE ─────────────────────────────────────────────────
// 'sandbox' → simulation uniquement (logs en BDD)
// 'reel'    → envoie via provider SMS (nécessite TextBee)
define('SMS_MODE', 'reel'); // Remplacez par 'reel' une fois configuré

// Provider
define('SMS_PROVIDER', 'httpsms');

// ═══════════════════════════════════════════════════════════
// CONFIGURATION HTTPSMS (100% Gratuit via Android)
// ═══════════════════════════════════════════════════════════
define('HTTPSMS_API_KEY',      'uk_0otvnTWywB4MD4TWFM-fw_cHKh1LIMQYdfAiXNMn4bbrCNMZ-7qHJu0UTxgmjtmX');
define('HTTPSMS_PHONE_NUMBER', '+21699509671');

// Numéro de l'administrateur (VOUS) pour recevoir les notifications
define('SMS_ADMIN_NUMBER',     '+21699509671');

define('SMS_LOG_ENABLED', true);
define('SMS_FROM_NAME',   'TechCare');

// Numéro par défaut pour les tests (optionnel)
define('SMS_TEST_NUMBER', '+21600000000');

// ── Templates messages ───────────────────────────────────
define('SMS_TPL_BIENVENUE',
    "Bonjour {nom} ! Bienvenue chez TechCare. " .
    "Abonnement {type} actif jusqu'au {date_fin}. Merci !"
);

define('SMS_TPL_RAPPEL_J7',
    "TechCare : Bonjour {nom}, votre abonnement {type} " .
    "expire dans 7 jours ({date_fin}). Renouvelez sur techcare.tn"
);

define('SMS_TPL_RAPPEL_J3',
    "TechCare URGENT : Bonjour {nom}, votre abonnement " .
    "expire dans 3 jours ! Renouvelez sur techcare.tn"
);

define('SMS_TPL_EXPIRATION',
    "TechCare : Votre abonnement {type} a expire le {date_fin}. " .
    "Reabonnez-vous sur techcare.tn"
);
?>