// Global Modal System
window.modalSystem = {
    show: function(type, message, title = null) {
        const modal = document.getElementById(type + '-modal');
        if (!modal) {
            console.warn('Modal not found:', type + '-modal');
            return;
        }
        
        const messageEl = document.getElementById(type + '-message');
        const titleEl = modal.querySelector('.modal-title');
        
        if (messageEl) messageEl.textContent = message;
        if (titleEl && title) titleEl.textContent = title;
        
        // Check if modal is already open to prevent duplicates
        if (modal.classList.contains('modal-show')) {
            console.log('Modal already open:', type);
            return;
        }
        
        // Force display and visibility
        modal.style.display = 'block';
        modal.style.visibility = 'visible';
        modal.style.opacity = '1';
        modal.classList.add('modal-show');
        document.body.style.overflow = 'hidden';
        
        // Store modal state
        window.modalSystem.modalState = window.modalSystem.modalState || {};
        window.modalSystem.modalState.isModalOpen = true;
        window.modalSystem.modalState.lastClosedType = type;
        window.modalSystem.modalState.lastClosedTime = Date.now();
        
        // Reset any auto-hide timers
        this.clearAutoHideTimer(type);
        
        // Auto-hide after 5 seconds for success messages
        if (type === 'success') {
            this.setAutoHideTimer(type, 5000);
        }
    },
    
    hide: function(type) {
        const modal = document.getElementById(type + '-modal');
        if (!modal) return;
        
        // Check if modal is already closed to prevent flickering
        if (!modal.classList.contains('modal-show')) {
            console.log('Modal already closed:', type);
            return;
        }
        
        // Force hide
        modal.style.display = 'none';
        modal.style.visibility = 'hidden';
        modal.style.opacity = '0';
        modal.classList.remove('modal-show');
        document.body.style.overflow = '';
        
        // Store modal state
        window.modalSystem.modalState = window.modalSystem.modalState || {};
        window.modalSystem.modalState.isModalOpen = false;
        window.modalSystem.modalState.lastClosedType = type;
        window.modalSystem.modalState.lastClosedTime = Date.now();
        
        // Clear any auto-hide timers
        this.clearAutoHideTimer(type);
    },
    
    setAutoHideTimer: function(type, delay) {
        this.clearAutoHideTimer(type);
        this.autoHideTimers = this.autoHideTimers || {};
        this.autoHideTimers[type] = setTimeout(() => {
            this.hide(type);
        }, delay);
    },
    
    clearAutoHideTimer: function(type) {
        if (this.autoHideTimers && this.autoHideTimers[type]) {
            clearTimeout(this.autoHideTimers[type]);
            delete this.autoHideTimers[type];
        }
    },
    
    success: function(message, title = null) {
        this.show('success', message, title);
    },
    
    error: function(message, title = null) {
        this.show('error', message, title);
    },
    
    info: function(message, title = null) {
        this.show('info', message, title);
    },
    
    // Check if modal should be shown on page load
    checkForModalOnLoad: function() {
        const urlParams = new URLSearchParams(window.location.search);
        const modalType = urlParams.get('modal');
        const message = urlParams.get('message');
        
        if (modalType && message) {
            // Check if modal is already open to prevent duplicates
            const modal = document.getElementById(modalType + '-modal');
            if (modal && modal.classList.contains('modal-show')) {
                console.log('Modal already open:', modalType);
                return;
            }
            
            // Show modal and clean URL
            this.show(modalType, message);
            
            // Clean URL parameters without page reload
            const cleanUrl = window.location.pathname + window.location.search;
            const url = new URL(cleanUrl);
            url.searchParams.delete('modal');
            url.searchParams.delete('message');
            const newUrl = url.pathname + url.search;
            
            // Update URL without page reload
            window.history.replaceState({}, '', newUrl);
            
            // Store modal state
            window.modalSystem.modalState = window.modalSystem.modalState || {};
            window.modalSystem.modalState.isModalOpen = true;
            window.modalSystem.modalState.lastClosedType = modalType;
            window.modalSystem.modalState.lastClosedTime = Date.now();
            
            // Reset any auto-hide timers
            this.clearAutoHideTimer(modalType);
            
            // Auto-hide after 5 seconds for success messages
            if (modalType === 'success') {
                this.setAutoHideTimer(modalType, 5000);
            }
        }
    },
    
    autoHideTimers: {}
};

// Global function to replace alerts
window.showAlert = function(type, message) {
    window.modalSystem[type](message);
};

// Replace default alert with modal system
const originalAlert = window.alert;
window.alert = function(message) {
    // Try to determine if this is an error or success message
    const lowerMessage = message.toLowerCase();
    
    if (lowerMessage.includes('success') || lowerMessage.includes('created') || lowerMessage.includes('updated') || lowerMessage.includes('saved')) {
        window.modalSystem.success(message);
    } else if (lowerMessage.includes('error') || lowerMessage.includes('failed') || lowerMessage.includes('invalid')) {
        window.modalSystem.error(message);
    } else if (lowerMessage.includes('warning') || lowerMessage.includes('notice')) {
        window.modalSystem.info(message);
    } else {
        // Default to info for unknown message types
        window.modalSystem.info(message);
    }
};

// Close modal function for global access
window.closeModal = function(type) {
    window.modalSystem.hide(type);
};

// Handle escape key to close modals
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('.modal[style*="block"]');
        modals.forEach(modal => {
            modal.style.display = 'none';
            modal.style.visibility = 'hidden';
            modal.style.opacity = '0';
            modal.classList.remove('modal-show');
        });
        document.body.style.overflow = '';
    }
});

// Handle click events (overlay and close button)
document.addEventListener('click', function(e) {
    // Handle overlay clicks
    if (e.target.classList.contains('modal-overlay')) {
        e.preventDefault();
        const modals = document.querySelectorAll('.modal[style*="block"]');
        modals.forEach(modal => {
            modal.style.display = 'none';
            modal.style.visibility = 'hidden';
            modal.style.opacity = '0';
            modal.classList.remove('modal-show');
        });
        document.body.style.overflow = '';
    }
    
    // Handle close button clicks
    if (e.target.classList.contains('modal-close')) {
        e.preventDefault();
        e.stopPropagation();
        
        // Find the parent modal
        const modalElement = e.target.closest('.modal');
        while (modalElement && !modalElement.classList.contains('modal')) {
            modalElement = modalElement.parentElement;
        }
        
        if (modalElement) {
            console.log('Closing modal via close button:', modalElement.id);
            modalElement.style.display = 'none';
            modalElement.style.visibility = 'hidden';
            modalElement.style.opacity = '0';
            modalElement.classList.remove('modal-show');
            document.body.style.overflow = '';
        }
    }
});

// Check for modal on page load
document.addEventListener('DOMContentLoaded', function() {
    // Small delay to ensure DOM is ready
    setTimeout(() => {
        window.modalSystem.checkForModalOnLoad();
    }, 100);
});

// Store modal state to prevent auto-reopening
window.modalSystem.modalState = {
    isModalOpen: false,
    lastClosedType: null,
    lastClosedTime: null
};
