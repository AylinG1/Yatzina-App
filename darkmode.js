/**
 * Sistema de Dark Mode para Yätzina App
 * Maneja el cambio entre tema claro y oscuro con persistencia en localStorage
 */

class DarkModeManager {
    constructor() {
        this.storageKey = 'yatzina-theme';
        this.init();
    }

    init() {
        // Cargar tema guardado o usar el del sistema
        const savedTheme = localStorage.getItem(this.storageKey);
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        let isDark;
        if (savedTheme !== null) {
            isDark = savedTheme === 'dark';
        } else {
            isDark = systemPrefersDark;
        }

        this.setTheme(isDark, false);
        this.setupSystemThemeListener();
    }

    setTheme(isDark, saveToStorage = true) {
        const html = document.documentElement;
        
        if (isDark) {
            html.classList.remove('light');
            html.classList.add('dark');
            html.setAttribute('data-theme', 'dark');
        } else {
            html.classList.remove('dark');
            html.classList.add('light');
            html.setAttribute('data-theme', 'light');
        }

        if (saveToStorage) {
            localStorage.setItem(this.storageKey, isDark ? 'dark' : 'light');
        }

        // Disparar evento personalizado para que otros componentes puedan reaccionar
        window.dispatchEvent(new CustomEvent('themeChanged', { 
            detail: { isDark, theme: isDark ? 'dark' : 'light' }
        }));
    }

    toggle() {
        const currentIsDark = document.documentElement.classList.contains('dark');
        this.setTheme(!currentIsDark);
    }

    getCurrentTheme() {
        return document.documentElement.classList.contains('dark') ? 'dark' : 'light';
    }

    isDarkMode() {
        return document.documentElement.classList.contains('dark');
    }

    setupSystemThemeListener() {
        // Escuchar cambios en la preferencia del sistema
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            // Solo cambiar automáticamente si no hay preferencia guardada
            const savedTheme = localStorage.getItem(this.storageKey);
            if (savedTheme === null) {
                this.setTheme(e.matches, false);
            }
        });
    }

    // Crear el botón toggle para las páginas
    createToggleButton(options = {}) {
        const {
            position = 'header', // 'header', 'floating', 'custom'
            parent = null,
            className = 'theme-toggle-btn',
            showLabel = true
        } = options;

        const button = document.createElement('button');
        button.className = `${className} ${this.getButtonClasses(position)}`;
        button.setAttribute('aria-label', 'Cambiar tema');
        button.setAttribute('title', 'Alternar entre modo claro y oscuro');

        this.updateButtonContent(button, showLabel);

        button.addEventListener('click', () => {
            this.toggle();
            this.updateButtonContent(button, showLabel);
        });

        // Escuchar cambios de tema para actualizar el botón
        window.addEventListener('themeChanged', () => {
            this.updateButtonContent(button, showLabel);
        });

        if (parent) {
            parent.appendChild(button);
        } else if (position === 'floating') {
            document.body.appendChild(button);
        }

        return button;
    }

    getButtonClasses(position) {
        const baseClasses = 'transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary/50';
        
        switch (position) {
            case 'floating':
                return `${baseClasses} fixed bottom-6 right-6 z-50 bg-white dark:bg-gray-800 text-gray-800 dark:text-white rounded-full p-3 shadow-lg hover:shadow-xl transform hover:scale-105 border border-gray-200 dark:border-gray-700`;
            case 'header':
                return `${baseClasses} bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg px-3 py-2 text-sm font-medium flex items-center gap-2`;
            default:
                return `${baseClasses} bg-primary/10 hover:bg-primary/20 text-primary rounded-lg px-4 py-2 font-medium flex items-center gap-2`;
        }
    }

    updateButtonContent(button, showLabel) {
        const isDark = this.isDarkMode();
        const icon = isDark ? '☀️' : '🌙';
        const label = isDark ? 'Modo Claro' : 'Modo Oscuro';
        
        button.innerHTML = showLabel 
            ? `<span class="text-lg">${icon}</span><span class="hidden sm:inline">${label}</span>`
            : `<span class="text-lg">${icon}</span>`;
    }

    // Método estático para inicialización rápida
    static init(createButton = true, buttonOptions = {}) {
        const manager = new DarkModeManager();
        
        if (createButton) {
            // Esperar a que el DOM esté listo
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    manager.createToggleButton(buttonOptions);
                });
            } else {
                manager.createToggleButton(buttonOptions);
            }
        }
        
        return manager;
    }
}

// Auto-inicialización si se incluye en una página con data-auto-init
document.addEventListener('DOMContentLoaded', () => {
    const script = document.currentScript || document.querySelector('script[src*="darkmode.js"]');
    if (script && script.hasAttribute('data-auto-init')) {
        const position = script.getAttribute('data-position') || 'floating';
        const showLabel = script.getAttribute('data-show-label') !== 'false';
        
        DarkModeManager.init(true, { position, showLabel });
    }
});

// Exportar para uso global
window.DarkModeManager = DarkModeManager;