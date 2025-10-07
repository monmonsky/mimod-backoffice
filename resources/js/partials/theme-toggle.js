/**
 * Theme Toggle Handler
 * Handles theme switching between light, dark, and system
 */

const STORAGE_KEY = "__NEXUS_CONFIG_v3.0__";

// Theme cycle: light -> dark (skip system for direct toggle)
const themeOrder = ['light', 'dark'];

function getStoredTheme() {
    try {
        const stored = localStorage.getItem(STORAGE_KEY);
        if (stored) {
            return JSON.parse(stored).theme || 'system';
        }
    } catch (err) {
        console.error('Error reading theme from localStorage:', err);
    }
    return 'system';
}

function saveTheme(theme) {
    try {
        const config = {
            theme: theme
        };
        localStorage.setItem(STORAGE_KEY, JSON.stringify(config));
    } catch (err) {
        console.error('Error saving theme to localStorage:', err);
    }
}

function applyTheme(theme) {
    if (theme === 'system') {
        // Check system preference and apply actual theme
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const actualTheme = prefersDark ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', actualTheme);
    } else {
        document.documentElement.setAttribute('data-theme', theme);
    }

    // Update icon visibility
    updateThemeIcon(theme);
}

function updateThemeIcon(theme) {
    const sunIcon = document.querySelector('[data-icon="sun"]');
    const moonIcon = document.querySelector('[data-icon="moon"]');
    const monitorIcon = document.querySelector('[data-icon="monitor"]');

    if (!sunIcon || !moonIcon || !monitorIcon) return;

    // Reset all icons
    sunIcon.style.opacity = '0';
    sunIcon.style.transform = 'translateY(-1rem)';
    moonIcon.style.opacity = '0';
    moonIcon.style.transform = 'translateY(1rem)';
    monitorIcon.style.opacity = '0';

    // Show appropriate icon
    if (theme === 'system') {
        monitorIcon.style.opacity = '1';
        monitorIcon.style.transform = 'translateY(0)';
    } else if (theme === 'light') {
        sunIcon.style.opacity = '1';
        sunIcon.style.transform = 'translateY(0)';
    } else if (theme === 'dark') {
        moonIcon.style.opacity = '1';
        moonIcon.style.transform = 'translateY(0)';
    }
}

function toggleTheme() {
    const currentTheme = getStoredTheme();
    const currentIndex = themeOrder.indexOf(currentTheme);
    const nextIndex = (currentIndex + 1) % themeOrder.length;
    const nextTheme = themeOrder[nextIndex];

    saveTheme(nextTheme);
    applyTheme(nextTheme);

    console.log(`Theme switched: ${currentTheme} -> ${nextTheme}`);
}

// Initialize theme on load
document.addEventListener('DOMContentLoaded', function() {
    const currentTheme = getStoredTheme();
    applyTheme(currentTheme);

    // Attach event listener to theme toggle button
    const toggleButton = document.querySelector('[data-theme-control="toggle"]');
    if (toggleButton) {
        toggleButton.addEventListener('click', toggleTheme);
        console.log('Theme toggle button initialized');
    }
});

// Listen for system theme changes
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    const currentTheme = getStoredTheme();
    if (currentTheme === 'system') {
        applyTheme('system');
    }
});
