/**
 * Theme Management for Dark/Light Mode
 */

// Initialize theme on page load
function initializeTheme() {
    const html = document.documentElement;
    const isDark = localStorage.getItem('theme') === 'dark' || 
                   (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
    
    if (isDark) {
        html.classList.add('dark');
        updateThemeButtons('dark');
    } else {
        html.classList.remove('dark');
        updateThemeButtons('light');
    }
}

// Update button states
function updateThemeButtons(theme) {
    const lightBtn = document.getElementById('theme-light-btn');
    const darkBtn = document.getElementById('theme-dark-btn');
    
    if (theme === 'light') {
        lightBtn?.classList.add('bg-blue-500', 'text-white');
        lightBtn?.classList.remove('bg-white', 'text-gray-600');
        darkBtn?.classList.remove('bg-slate-700', 'text-white');
        darkBtn?.classList.add('bg-white', 'text-gray-600');
    } else {
        darkBtn?.classList.add('bg-slate-700', 'text-white');
        darkBtn?.classList.remove('bg-white', 'text-gray-600');
        lightBtn?.classList.remove('bg-blue-500', 'text-white');
        lightBtn?.classList.add('bg-white', 'text-gray-600');
    }
}

// Toggle light mode
window.toggleLightMode = function() {
    const html = document.documentElement;
    html.classList.remove('dark');
    localStorage.setItem('theme', 'light');
    updateThemeButtons('light');
};

// Toggle dark mode
window.toggleDarkMode = function() {
    const html = document.documentElement;
    html.classList.add('dark');
    localStorage.setItem('theme', 'dark');
    updateThemeButtons('dark');
};

// Initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeTheme);
} else {
    initializeTheme();
}
