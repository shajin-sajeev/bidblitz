// Global compact response modal system.
window.modalSystem = {
    autoHideTimers: {},
    actionCallbacks: {},
    modalState: {
        isModalOpen: false,
        lastClosedType: null,
        lastClosedTime: null
    },

    show: function(type, message, title = null, options = {}) {
        const modal = document.getElementById(type + '-modal');
        if (!modal) {
            console.warn('Modal not found:', type + '-modal');
            return;
        }

        const messageEl = document.getElementById(type + '-message');
        const titleEl = modal.querySelector('.modal-title');
        const actionBtn = modal.querySelector('.modal-action');
        const cancelBtn = modal.querySelector('.modal-cancel');

        if (messageEl) {
            if (options.html) {
                messageEl.innerHTML = message;
            } else {
                messageEl.textContent = message;
            }
        }

        if (titleEl && title) {
            titleEl.textContent = title;
        }

        if (actionBtn) {
            if (typeof options.actionCallback === 'function') {
                actionBtn.textContent = options.actionText || 'Continue';
                actionBtn.classList.remove('hidden');
                this.actionCallbacks[type] = options.actionCallback;
                actionBtn.onclick = () => {
                    this.actionCallbacks[type]();
                    this.hide(type + '-modal');
                };
                if (cancelBtn) cancelBtn.textContent = 'Cancel';
            } else {
                actionBtn.classList.add('hidden');
                actionBtn.onclick = null;
                delete this.actionCallbacks[type];
                if (cancelBtn) cancelBtn.textContent = 'Close';
            }
        }

        document.querySelectorAll('.modal.modal-show').forEach(openModal => {
            if (openModal.id !== modal.id) {
                this.hide(openModal.id);
            }
        });

        modal.style.display = 'block';
        modal.style.visibility = 'visible';
        modal.style.opacity = '1';
        modal.setAttribute('aria-hidden', 'false');
        modal.classList.add('modal-show');
        document.body.style.overflow = 'hidden';

        this.modalState.isModalOpen = true;
        this.modalState.lastClosedType = type;
        this.modalState.lastClosedTime = Date.now();

        this.clearAutoHideTimer(type);
        if (type === 'success' && !options.actionCallback) {
            this.setAutoHideTimer(type, 3500);
        }
    },

    hide: function(typeOrId) {
        const modalId = typeOrId.endsWith('-modal') ? typeOrId : typeOrId + '-modal';
        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.style.display = 'none';
        modal.style.visibility = 'hidden';
        modal.style.opacity = '0';
        modal.setAttribute('aria-hidden', 'true');
        modal.classList.remove('modal-show');

        if (!document.querySelector('.modal.modal-show')) {
            document.body.style.overflow = '';
        }

        const type = modalId.replace(/-modal$/, '');
        this.modalState.isModalOpen = false;
        this.modalState.lastClosedType = type;
        this.modalState.lastClosedTime = Date.now();
        this.clearAutoHideTimer(type);
        delete this.actionCallbacks[type];

        // Dispatch a custom event to notify modal close
        const modalClosedEvent = new Event('modalClosed');
        document.dispatchEvent(modalClosedEvent);
    },

    setAutoHideTimer: function(type, delay) {
        this.clearAutoHideTimer(type);
        this.autoHideTimers[type] = setTimeout(() => {
            this.hide(type);
        }, delay);
    },

    clearAutoHideTimer: function(type) {
        if (this.autoHideTimers[type]) {
            clearTimeout(this.autoHideTimers[type]);
            delete this.autoHideTimers[type];
        }
    },

    success: function(message, title = null, options = {}) {
        this.show('success', message, title, options);
    },

    error: function(message, title = null, options = {}) {
        this.show('error', message, title, options);
    },

    info: function(message, title = null, options = {}) {
        this.show('info', message, title, options);
    },

    checkForModalOnLoad: function() {
        const urlParams = new URLSearchParams(window.location.search);
        const modalType = urlParams.get('modal');
        const message = urlParams.get('message');

        if (modalType && message) {
            this.show(modalType, message);
            urlParams.delete('modal');
            urlParams.delete('message');
            const query = urlParams.toString();
            window.history.replaceState({}, '', window.location.pathname + (query ? '?' + query : ''));
        }
    }
};

window.showAlert = function(type, message, title = null, options = {}) {
    const method = window.modalSystem[type] ? type : 'info';
    window.modalSystem[method](message, title, options);
};

window.showNotification = function(message, type = 'info', title = null, actionCallback = null, actionText = 'Action') {
    const method = window.modalSystem[type] ? type : 'info';
    const options = {
        html: /<\/?[a-z][\s\S]*>/i.test(String(message)),
        actionCallback,
        actionText
    };
    window.modalSystem[method](message, title, options);
};

window.alert = function(message) {
    const lowerMessage = String(message).toLowerCase();

    if (lowerMessage.includes('success') || lowerMessage.includes('created') || lowerMessage.includes('updated') || lowerMessage.includes('saved')) {
        window.modalSystem.success(message);
    } else if (lowerMessage.includes('error') || lowerMessage.includes('failed') || lowerMessage.includes('invalid')) {
        window.modalSystem.error(message);
    } else {
        window.modalSystem.info(message);
    }
};

window.closeModal = function(type) {
    window.modalSystem.hide(type);
};

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal.modal-show').forEach(modal => {
            window.modalSystem.hide(modal.id);
        });
    }
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay') || e.target.classList.contains('modal-close')) {
        e.preventDefault();
        const modal = e.target.closest('.modal');
        if (modal) {
            window.modalSystem.hide(modal.id);
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    window.modalSystem.checkForModalOnLoad();

    const flash = window.appFlashMessage;
    if (flash && flash.message && window.modalSystem[flash.type]) {
        window.modalSystem[flash.type](flash.message, flash.title || null, { html: Boolean(flash.html) });
    }
});
