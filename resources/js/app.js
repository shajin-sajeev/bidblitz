//

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';

document.addEventListener('DOMContentLoaded', () => {
    const menuButton = document.querySelector('.mobile-menu-toggle');
    const drawer = document.getElementById('mobile-app-drawer');
    const closeTargets = document.querySelectorAll('[data-mobile-drawer-close]');

    const setDrawerState = (isOpen) => {
        if (!drawer || !menuButton) {
            return;
        }

        document.body.classList.toggle('mobile-drawer-open', isOpen);
        drawer.setAttribute('aria-hidden', String(!isOpen));
        menuButton.setAttribute('aria-expanded', String(isOpen));
    };

    menuButton?.addEventListener('click', () => {
        setDrawerState(!document.body.classList.contains('mobile-drawer-open'));
    });

    closeTargets.forEach((target) => {
        target.addEventListener('click', () => setDrawerState(false));
    });

    drawer?.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => setDrawerState(false));
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setDrawerState(false);
        }
    });

    const activateDashboardTab = (tab, shouldUpdateUrl = false) => {
        if (!tab) {
            return;
        }

        document.querySelectorAll('[data-dashboard-tab]').forEach((item) => {
            item.classList.toggle('active', item.getAttribute('data-dashboard-tab') === tab);
        });

        document.querySelectorAll('[data-dashboard-panel]').forEach((panel) => {
            panel.classList.toggle('active', panel.getAttribute('data-dashboard-panel') === tab);
        });

        if (shouldUpdateUrl && window.history?.replaceState) {
            const url = new URL(window.location.href);
            url.searchParams.set('dashboard_tab', tab);
            window.history.replaceState({}, '', url);
        }
    };

    const dashboardTabs = document.querySelectorAll('[data-dashboard-tab]');
    if (dashboardTabs.length) {
        const url = new URL(window.location.href);
        const requestedTab = url.searchParams.get('dashboard_tab');
        const hasAuctionPage = url.searchParams.has('page');
        activateDashboardTab(requestedTab || (hasAuctionPage ? 'auctions' : 'overview'));

        document.querySelectorAll('[data-dashboard-panel="auctions"] .pagination a').forEach((link) => {
            const href = new URL(link.href);
            href.searchParams.set('dashboard_tab', 'auctions');
            link.href = href.toString();
        });
    }

    dashboardTabs.forEach((button) => {
        button.addEventListener('click', () => {
            const tab = button.getAttribute('data-dashboard-tab');
            activateDashboardTab(tab, true);
        });
    });
});
