//

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';

window.renderAppPagination = (pagination, changeHandler = 'changePage', itemLabel = 'records') => {
    if (!pagination || pagination.last_page <= 1) {
        return '';
    }

    const pageButton = (page, label = page, extraClass = '') => (
        `<li class="page-item ${extraClass}"><button type="button" class="page-link" onclick="${changeHandler}(${page})"><span>${label}</span></button></li>`
    );

    let items = '';

    if (pagination.current_page > 1) {
        items += pageButton(pagination.current_page - 1, '&lsaquo;');
    } else {
        items += '<li class="page-item disabled" aria-disabled="true"><span class="page-link"><span>&lsaquo;</span></span></li>';
    }

    const boundedRange = (start, end) => {
        const pages = [];
        for (let page = Math.max(1, start); page <= Math.min(pagination.last_page, end); page += 1) {
            pages.push(page);
        }
        return pages;
    };

    const pages = [...new Set([
        ...boundedRange(1, 2),
        ...boundedRange(pagination.current_page - 1, pagination.current_page + 1),
        ...boundedRange(pagination.last_page - 1, pagination.last_page),
    ])].sort((a, b) => a - b);

    let previousPage = 0;
    pages.forEach((page) => {
        if (previousPage && page > previousPage + 1) {
            items += '<li class="page-item disabled pagination-ellipsis" aria-disabled="true"><span class="page-link">...</span></li>';
        }

        if (page === pagination.current_page) {
            items += `<li class="page-item active" aria-current="page"><span class="page-link"><span>${page}</span></span></li>`;
        } else {
            items += pageButton(page);
        }

        previousPage = page;
    });

    if (pagination.current_page < pagination.last_page) {
        items += pageButton(pagination.current_page + 1, '&rsaquo;');
    } else {
        items += '<li class="page-item disabled" aria-disabled="true"><span class="page-link"><span>&rsaquo;</span></span></li>';
    }

    return `
        <div class="pagination-wrapper">
            <div class="pagination-info">
                Showing <span>${pagination.from}</span> to <span>${pagination.to}</span> of <span>${pagination.total}</span> ${itemLabel}
            </div>
            <nav role="navigation" aria-label="Pagination Navigation" class="pagination">
                <ul class="pagination-items">${items}</ul>
            </nav>
        </div>
    `;
};

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
