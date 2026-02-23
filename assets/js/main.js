/**
 * main.js - NeoBank Global Utilities
 * SQAP 2.4 - Feedback Visual & Prevención de Doble Gasto
 */

// --- THEME INITIALIZATION ---
// Ejecutar inmediatamente para evitar parpadeo blanco/negro
const savedTheme = localStorage.getItem('neobank-theme') || 'light';
if (savedTheme === 'dark') {
    document.documentElement.setAttribute('data-theme', 'dark');
} else {
    document.documentElement.removeAttribute('data-theme');
}

document.addEventListener('DOMContentLoaded', () => {
    // --- THEME TOGGLE LOGIC ---
    const themeBtns = document.querySelectorAll('.theme-toggle-btn');
    themeBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const currentTheme = document.documentElement.getAttribute('data-theme');
            let newTheme = 'light';

            if (currentTheme === 'dark') {
                document.documentElement.removeAttribute('data-theme');
            } else {
                document.documentElement.setAttribute('data-theme', 'dark');
                newTheme = 'dark';
            }
            localStorage.setItem('neobank-theme', newTheme);
        });
    });

    // 1. MOBILE SIDEBAR TOGGLE
    const sidebar = document.getElementById('sidebar');
    const sidebarCollapse = document.getElementById('sidebarCollapse');

    // Create overlay for mobile
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);

    if (sidebarCollapse && sidebar) {
        sidebarCollapse.addEventListener('click', (e) => {
            e.preventDefault();
            sidebar.classList.add('active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden'; // Evita scroll atrás
        });

        // Click outside to close (or tap on overlay)
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = 'auto'; // Restaura scroll
        });
    }

    // 2. GENERIC BUTTON FEEDBACK (PREVENT DOUBLE CLICK)
    // Se aplica a formularios tradicionales
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function (e) {
            // Evitamos hacer esto si el form maneja su propio fetch (AJAX)
            // Para formularios normales de submit (ej. Login)
            if (!this.hasAttribute('id') || this.id === '') {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    spinnerBtn(submitBtn);
                }
            }
        });
    });

    // Se aplica a scripts fetch manuales (exportamos la función para que la usen otros js)
    window.btnLoader = spinnerBtn;
});

/**
 * Deshabilita un botón y le pone un spinner de carga.
 * @param {HTMLElement} btn - Botón a deshabilitar
 * @param {string} loadingText - Texto opcional
 */
function spinnerBtn(btn, loadingText = 'Procesando...') {
    if (!btn || btn.disabled) return false;

    // Guardar estado original
    btn.dataset.originalText = btn.innerHTML;

    // Deshabilitar y poner spinner
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>${loadingText}`;

    // Retorna función para restaurar
    return function restoreBtn() {
        btn.disabled = false;
        btn.innerHTML = btn.dataset.originalText;
    }
}
