/**
 * Theme Initialization
 * Load theme from localStorage on page load
 */

try {
    const localStorageItem = localStorage.getItem("__NEXUS_CONFIG_v3.0__");
    if (localStorageItem) {
        const theme = JSON.parse(localStorageItem).theme;
        if (theme === "system") {
            // Apply system preference
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.setAttribute("data-theme", prefersDark ? 'dark' : 'light');
        } else {
            document.documentElement.setAttribute("data-theme", theme);
        }
    } else {
        // Default to light theme if no preference saved
        document.documentElement.setAttribute("data-theme", 'light');
    }
} catch (err) {
    console.log(err);
    // Fallback to light theme on error
    document.documentElement.setAttribute("data-theme", 'light');
}
