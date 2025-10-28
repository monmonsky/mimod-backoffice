/**
 * Customization Panel Handler
 * Handles theme, sidebar, font-family, and direction changes from rightbar drawer
 */

const STORAGE_KEY = "__NEXUS_CONFIG_v3.0__";

// Get current configuration from localStorage
function getConfig() {
    try {
        const stored = localStorage.getItem(STORAGE_KEY);
        if (stored) {
            return JSON.parse(stored);
        }
    } catch (err) {
        console.error('Error reading config from localStorage:', err);
    }
    return {
        theme: null, // null = system
        sidebarTheme: 'light',
        fontFamily: 'inclusive',
        direction: 'ltr'
    };
}

// Save configuration to localStorage
function saveConfig(config) {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(config));
    } catch (err) {
        console.error('Error saving config to localStorage:', err);
    }
}

// Apply theme to HTML element
function applyTheme(theme) {
    const html = document.documentElement;

    if (!theme || theme === 'system') {
        html.removeAttribute('data-theme');
    } else {
        html.setAttribute('data-theme', theme);
    }
}

// Apply sidebar theme
function applySidebarTheme(sidebarTheme) {
    const html = document.documentElement;
    html.setAttribute('data-sidebar-theme', sidebarTheme);
}

// Apply font family
function applyFontFamily(fontFamily) {
    const html = document.documentElement;

    if (!fontFamily || fontFamily === 'inclusive') {
        html.removeAttribute('data-font-family');
    } else {
        html.setAttribute('data-font-family', fontFamily);
    }
}

// Apply text direction
function applyDirection(direction) {
    const html = document.documentElement;

    if (!direction || direction === 'ltr') {
        html.removeAttribute('dir');
    } else {
        html.setAttribute('dir', direction);
    }
}

// Mark HTML as changed (for reset button indicator)
function markChanged() {
    document.documentElement.setAttribute('data-changed', '');
}

// Initialize customization on page load
document.addEventListener('DOMContentLoaded', function() {
    const config = getConfig();

    // Apply saved configurations
    applyTheme(config.theme);
    applySidebarTheme(config.sidebarTheme || 'light');
    applyFontFamily(config.fontFamily || 'inclusive');
    applyDirection(config.direction || 'ltr');

    // Theme controls
    const themeControls = document.querySelectorAll('[data-theme-control]');
    themeControls.forEach(control => {
        control.addEventListener('click', function() {
            const theme = this.getAttribute('data-theme-control');
            const config = getConfig();

            if (theme === 'toggle') {
                // Handle toggle button in topbar
                return;
            }

            if (theme === 'system') {
                config.theme = null;
            } else {
                config.theme = theme;
            }

            saveConfig(config);
            applyTheme(config.theme);
            markChanged();

            console.log('Theme changed to:', theme);
        });
    });

    // Sidebar theme controls
    const sidebarControls = document.querySelectorAll('[data-sidebar-theme-control]');
    sidebarControls.forEach(control => {
        control.addEventListener('click', function() {
            const sidebarTheme = this.getAttribute('data-sidebar-theme-control');
            const config = getConfig();
            config.sidebarTheme = sidebarTheme;

            saveConfig(config);
            applySidebarTheme(sidebarTheme);
            markChanged();

            console.log('Sidebar theme changed to:', sidebarTheme);
        });
    });

    // Font family controls
    const fontControls = document.querySelectorAll('[data-font-family-control]');
    fontControls.forEach(control => {
        control.addEventListener('click', function() {
            const fontFamily = this.getAttribute('data-font-family-control');
            const config = getConfig();
            config.fontFamily = fontFamily;

            saveConfig(config);
            applyFontFamily(fontFamily);
            markChanged();

            console.log('Font family changed to:', fontFamily);
        });
    });

    // Direction controls
    const dirControls = document.querySelectorAll('[data-dir-control]');
    dirControls.forEach(control => {
        control.addEventListener('click', function() {
            const direction = this.getAttribute('data-dir-control');
            const config = getConfig();
            config.direction = direction;

            saveConfig(config);
            applyDirection(direction);
            markChanged();

            console.log('Direction changed to:', direction);
        });
    });

    // Reset control
    const resetControl = document.querySelector('[data-reset-control]');
    if (resetControl) {
        resetControl.addEventListener('click', function() {
            const defaultConfig = {
                theme: null,
                sidebarTheme: 'light',
                fontFamily: 'inclusive',
                direction: 'ltr'
            };

            saveConfig(defaultConfig);
            applyTheme(defaultConfig.theme);
            applySidebarTheme(defaultConfig.sidebarTheme);
            applyFontFamily(defaultConfig.fontFamily);
            applyDirection(defaultConfig.direction);

            document.documentElement.removeAttribute('data-changed');

            console.log('Configuration reset to defaults');
        });
    }

    // Fullscreen control
    const fullscreenControl = document.querySelector('[data-fullscreen-control]');
    if (fullscreenControl) {
        fullscreenControl.addEventListener('click', function() {
            if (document.fullscreenElement) {
                document.exitFullscreen();
                document.documentElement.removeAttribute('data-fullscreen');
            } else {
                document.documentElement.requestFullscreen();
                document.documentElement.setAttribute('data-fullscreen', '');
            }
        });
    }

    // Listen for fullscreen changes
    document.addEventListener('fullscreenchange', function() {
        if (document.fullscreenElement) {
            document.documentElement.setAttribute('data-fullscreen', '');
        } else {
            document.documentElement.removeAttribute('data-fullscreen');
        }
    });

    console.log('Customization panel initialized');
});
