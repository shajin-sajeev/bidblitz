// Theme Switching Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Apply saved theme on page load
    const savedTheme = sessionStorage.getItem('theme') || 'dark';
    applyTheme(savedTheme);
    
    // Listen for theme changes in forms
    const themeRadios = document.querySelectorAll('input[name="theme"]');
    themeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                applyTheme(this.value);
                sessionStorage.setItem('theme', this.value);
            }
        });
    });
    
    // Add theme toggle to navbar if needed
    addThemeToggle();
});

function applyTheme(theme) {
    const body = document.body;
    
    if (theme === 'light') {
        body.classList.add('light-theme');
        body.classList.remove('dark-theme');
    } else {
        body.classList.add('dark-theme');
        body.classList.remove('light-theme');
    }
    
    // Update radio buttons if they exist
    const themeRadios = document.querySelectorAll('input[name="theme"]');
    themeRadios.forEach(radio => {
        radio.checked = radio.value === theme;
    });
    
    // Update theme option cards
    const themeOptions = document.querySelectorAll('.theme-option');
    themeOptions.forEach(option => {
        option.classList.remove('selected');
        const radio = option.querySelector('input[name="theme"]');
        if (radio && radio.value === theme) {
            option.classList.add('selected');
        }
    });
}

function addThemeToggle() {
    const navbar = document.querySelector('.navbar .container');
    if (!navbar) return;
    
    // Check if theme toggle already exists
    if (navbar.querySelector('.theme-toggle')) return;
    
    // Create theme toggle button
    const themeToggle = document.createElement('button');
    themeToggle.className = 'theme-toggle btn btn-outline';
    themeToggle.innerHTML = `
        <span class="theme-icon">🌙</span>
    `;
    themeToggle.style.cssText = `
        background: transparent;
        border: 1px solid var(--border-color);
        color: var(--text-main);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1.2rem;
        transition: all 0.3s ease;
    `;
    
    themeToggle.addEventListener('click', function() {
        const currentTheme = document.body.classList.contains('light-theme') ? 'light' : 'dark';
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        
        applyTheme(newTheme);
        sessionStorage.setItem('theme', newTheme);
        
        // Update icon
        this.querySelector('.theme-icon').textContent = newTheme === 'light' ? '☀️' : '🌙';
    });
    
    // Add to navbar
    const navLinks = navbar.querySelector('.nav-links');
    if (navLinks) {
        navLinks.appendChild(themeToggle);
    } else {
        navbar.appendChild(themeToggle);
    }
    
    // Update icon based on current theme
    const currentTheme = document.body.classList.contains('light-theme') ? 'light' : 'dark';
    themeToggle.querySelector('.theme-icon').textContent = currentTheme === 'light' ? '☀️' : '🌙';
}

// Export for use in other files
window.themeManager = {
    applyTheme,
    addThemeToggle
};
