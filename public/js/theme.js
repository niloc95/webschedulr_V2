document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    
    // Check for saved theme preference or respect OS preference
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    // Apply theme
    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
        document.body.classList.add('dark-theme');
        themeIcon.classList.replace('bi-moon', 'bi-sun');
    }
    
    // Theme toggle functionality
    themeToggle.addEventListener('click', function() {
        if (document.body.classList.contains('dark-theme')) {
            // Switch to light theme
            document.body.classList.remove('dark-theme');
            themeIcon.classList.replace('bi-sun', 'bi-moon');
            localStorage.setItem('theme', 'light');
        } else {
            // Switch to dark theme
            document.body.classList.add('dark-theme');
            themeIcon.classList.replace('bi-moon', 'bi-sun');
            localStorage.setItem('theme', 'dark');
        }
    });
});