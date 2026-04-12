    </main>
</div>

<script src="assets/js/abonnements.js"></script>
<script>
    // Responsive sidebar toggle
    document.addEventListener('DOMContentLoaded', function() {
        const menuBtn = document.querySelector('.back-header .fa-bars');
        if (menuBtn) {
            menuBtn.style.display = window.innerWidth <= 768 ? 'block' : 'none';
        }
        
        window.addEventListener('resize', function() {
            const menuBtn = document.querySelector('.back-header .fa-bars');
            if (menuBtn) {
                menuBtn.style.display = window.innerWidth <= 768 ? 'block' : 'none';
            }
        });
    });
</script>
</body>
</html>