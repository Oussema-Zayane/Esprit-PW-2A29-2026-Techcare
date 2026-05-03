// assets/js/theme.js
(function() {
    const currentTheme = localStorage.getItem('techcare_theme') || 'dark';
    document.documentElement.setAttribute('data-theme', currentTheme);

    window.addEventListener('DOMContentLoaded', () => {
        
        // --- Injection Automatique du Bouton ---
        
        // 1. Back Office: Inject into .user-info if it exists
        const userInfo = document.querySelector('.user-info');
        if (userInfo && !document.querySelector('.theme-toggle-back')) {
            const toggleBtn = document.createElement('a');
            toggleBtn.href = "#";
            toggleBtn.className = "theme-toggle theme-toggle-back";
            toggleBtn.style.cssText = "color: var(--txt); font-size: 1.2rem; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; transition: all 0.3s; text-decoration: none; margin-right: 10px;";
            toggleBtn.innerHTML = '<i class="fa-solid fa-moon"></i>';
            userInfo.prepend(toggleBtn);
            
            // Hover effect
            toggleBtn.addEventListener('mouseenter', () => toggleBtn.style.background = 'var(--surface2)');
            toggleBtn.addEventListener('mouseleave', () => toggleBtn.style.background = 'transparent');
        }

        // 2. Front Office: Inject into .header-actions if it exists
        const headerActions = document.querySelector('.header-actions');
        if (headerActions && !document.querySelector('.theme-toggle-front')) {
            const toggleBtn = document.createElement('a');
            toggleBtn.href = "#";
            toggleBtn.className = "btn-secondary theme-toggle theme-toggle-front";
            toggleBtn.innerHTML = '<i class="fa-solid fa-moon"></i>';
            headerActions.prepend(toggleBtn);
        }

        // --- Logique de Basculement ---
        const themeToggles = document.querySelectorAll('.theme-toggle');
        
        function updateIcons(theme) {
            themeToggles.forEach(toggle => {
                const icon = toggle.querySelector('i');
                if (icon) {
                    if (theme === 'light') {
                        icon.className = 'fa-solid fa-moon';
                        toggle.title = "Passer au mode sombre";
                    } else {
                        icon.className = 'fa-solid fa-sun';
                        toggle.title = "Passer au mode clair";
                    }
                }
            });
        }

        updateIcons(currentTheme);

        themeToggles.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                let theme = document.documentElement.getAttribute('data-theme');
                let newTheme = theme === 'light' ? 'dark' : 'light';
                
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('techcare_theme', newTheme);
                updateIcons(newTheme);
            });
        });
    });
})();
