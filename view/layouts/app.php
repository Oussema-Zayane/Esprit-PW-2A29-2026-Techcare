<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'TechCare'; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="<?= $bodyClass ?? ''; ?>">

<?= $content; ?>

<script>
(function () {
    if (localStorage.getItem('techcareTheme') === 'light') {
        document.body.classList.add('light-mode');
    }

    document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.body.classList.toggle('light-mode');
            localStorage.setItem('techcareTheme', document.body.classList.contains('light-mode') ? 'light' : 'dark');
            btn.textContent = document.body.classList.contains('light-mode') ? '🌙 Mode noir' : '☀️ Mode blanc';
        });
        btn.textContent = document.body.classList.contains('light-mode') ? '🌙 Mode noir' : '☀️ Mode blanc';
    });
})();
</script>
</body>
</html>
