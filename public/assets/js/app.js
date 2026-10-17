/**
 * Kesari Logistics SaaS - Global UI/UX Application Engine
 * High-performance Vanilla ES6+ interactions, toast notifications, keyboard shortcuts,
 * drawer navigation, clipboard utilities, and table batch selection.
 */

(function () {
    'use strict';

    // ==========================================
    // 1. Toast Notification System
    // ==========================================
    class ToastManager {
        constructor() {
            this.container = document.getElementById('toast-container');
            if (!this.container) {
                this.container = document.createElement('div');
                this.container.id = 'toast-container';
                this.container.className = 'toast-container';
                document.body.appendChild(this.container);
            }
        }

        /**
         * Show toast message
         * @param {string} message
         * @param {'success'|'error'|'warning'|'info'} type
         * @param {number} duration
         */
        show(message, type = 'info', duration = 3500) {
            const toast = document.createElement('div');
            toast.className = `toast-item toast-${type}`;

            const icons = {
                success: '✓',
                error: '✕',
                warning: '⚠',
                info: 'ℹ'
            };

            toast.innerHTML = `
                <div class="toast-icon">${icons[type] || 'ℹ'}</div>
                <div class="toast-content">
                    <div class="toast-message">${message}</div>
                </div>
                <button type="button" class="toast-close" aria-label="Close">&times;</button>
                <div class="toast-progress" style="animation-duration: ${duration}ms"></div>
            `;

            const closeBtn = toast.querySelector('.toast-close');
            const removeToast = () => {
                toast.classList.add('toast-hiding');
                toast.addEventListener('animationend', () => {
                    if (toast.parentElement) toast.parentElement.removeChild(toast);
                }, { once: true });
            };

            closeBtn.addEventListener('click', removeToast);

            // Auto dismiss timer
            const timer = setTimeout(removeToast, duration);
            toast.addEventListener('mouseenter', () => clearTimeout(timer));

            this.container.appendChild(toast);
            // Trigger enter animation
            requestAnimationFrame(() => toast.classList.add('toast-visible'));
        }
    }

    const toast = new ToastManager();
    window.showToast = (msg, type, dur) => toast.show(msg, type, dur);

    // ==========================================
    // 2. Mobile Sidebar Drawer & Backdrop
    // ==========================================
    function setupMobileNavigation() {
        const toggleBtn = document.getElementById('sidebar-toggle');
        const sidebar = document.querySelector('.sidebar');
        let backdrop = document.querySelector('.sidebar-backdrop');

        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.className = 'sidebar-backdrop';
            document.body.appendChild(backdrop);
        }

        const openSidebar = () => {
            if (sidebar) sidebar.classList.add('open');
            backdrop.classList.add('visible');
            document.body.classList.add('sidebar-locked');
        };

        const closeSidebar = () => {
            if (sidebar) sidebar.classList.remove('open');
            backdrop.classList.remove('visible');
            document.body.classList.remove('sidebar-locked');
        };

        if (toggleBtn) {
            toggleBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (sidebar && sidebar.classList.contains('open')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });
        }

        backdrop.addEventListener('click', closeSidebar);

        // Close on ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && sidebar && sidebar.classList.contains('open')) {
                closeSidebar();
            }
        });
    }

    // ==========================================
    // 3. Global Keyboard Shortcuts (Logistics Pro)
    // ==========================================
    function setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ignore when typing inside input / textarea / select
            const tag = (e.target.tagName || '').toLowerCase();
            const isEditing = tag === 'input' || tag === 'textarea' || tag === 'select' || e.target.isContentEditable;

            // Ctrl+K or Cmd+K: Focus Global Search
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                const search = document.querySelector('.search-input');
                if (search) {
                    search.focus();
                    search.select();
                }
                return;
            }

            // Quick Hotkeys when NOT typing:
            if (!isEditing) {
                // Key 'B' or 'b' -> New Booking
                if (e.key.toLowerCase() === 'b' && !e.ctrlKey && !e.metaKey && !e.altKey) {
                    const bookingBtn = document.querySelector('a[href="/bookings/new"]');
                    if (bookingBtn) {
                        e.preventDefault();
                        window.location.href = '/bookings/new';
                    }
                }
                // Key 'P' or 'p' -> Print Hub
                if (e.key.toLowerCase() === 'p' && !e.ctrlKey && !e.metaKey && !e.altKey) {
                    const printBtn = document.querySelector('a[href="/print-labels"]');
                    if (printBtn) {
                        e.preventDefault();
                        window.location.href = '/print-labels';
                    }
                }
                // Key 'I' or 'i' -> Invoices
                if (e.key.toLowerCase() === 'i' && !e.ctrlKey && !e.metaKey && !e.altKey) {
                    const invBtn = document.querySelector('a[href="/invoices"]');
                    if (invBtn) {
                        e.preventDefault();
                        window.location.href = '/invoices';
                    }
                }
            }
        });
    }

    // ==========================================
    // 4. Copy to Clipboard Utility
    // ==========================================
    function setupCopyableElements() {
        document.addEventListener('click', (e) => {
            const target = e.target.closest('[data-copy], .copyable');
            if (!target) return;

            const textToCopy = target.getAttribute('data-copy') || target.innerText.trim();
            if (!textToCopy) return;

            navigator.clipboard.writeText(textToCopy).then(() => {
                target.classList.add('copied-feedback');
                toast.show(`Copied "${textToCopy}" to clipboard`, 'success', 2200);

                setTimeout(() => {
                    target.classList.remove('copied-feedback');
                }, 1200);
            }).catch(err => {
                toast.show('Failed to copy to clipboard', 'error');
            });
        });
    }

    // ==========================================
    // 5. Form Submissions Double-Click Prevention
    // ==========================================
    function setupButtonLoadingStates() {
        document.addEventListener('submit', (e) => {
            const form = e.target;
            // Allow forms with target="_blank" to submit without locking button
            if (form.getAttribute('target') === '_blank') return;

            const submitBtn = form.querySelector('button[type="submit"]:not([data-no-loader])');
            if (submitBtn && !submitBtn.disabled) {
                // Delay disabling slightly so browser handles native form validation & submit data
                setTimeout(() => {
                    submitBtn.classList.add('btn-loading');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.setAttribute('data-original-text', originalText);
                    submitBtn.innerHTML = `
                        <span class="spinner-sm"></span> Processing...
                    `;
                }, 50);
            }
        });
    }

    // ==========================================
    // 6. Flash Message Auto-Toast Converter
    // ==========================================
    function convertFlashMessagesToToasts() {
        // Collect existing inline alerts and enhance them or convert to toasts
        const flashSuccess = document.querySelectorAll('.alert-flash-success, [data-flash-success]');
        flashSuccess.forEach(el => {
            const msg = el.innerText.trim();
            if (msg) toast.show(msg, 'success', 4000);
        });

        const flashError = document.querySelectorAll('.alert-flash-error, [data-flash-error]');
        flashError.forEach(el => {
            const msg = el.innerText.trim();
            if (msg) toast.show(msg, 'error', 5000);
        });
    }

    // ==========================================
    // 7. Interactive Table Batch Selection Helper
    // ==========================================
    function setupBatchTables() {
        const selectAllInputs = document.querySelectorAll('[data-select-all]');
        selectAllInputs.forEach(masterCheckbox => {
            const table = masterCheckbox.closest('table');
            if (!table) return;

            const targetName = masterCheckbox.getAttribute('data-select-all');
            const rowCheckboxes = table.querySelectorAll(`input[type="checkbox"][name="${targetName}"]`);
            const counterBadge = document.querySelector(`[data-selected-count="${targetName}"]`);

            const updateCount = () => {
                const selected = table.querySelectorAll(`input[type="checkbox"][name="${targetName}"]:checked`).length;
                if (counterBadge) {
                    counterBadge.innerText = selected;
                    counterBadge.style.display = selected > 0 ? 'inline-flex' : 'none';
                }
            };

            masterCheckbox.addEventListener('change', () => {
                rowCheckboxes.forEach(cb => {
                    cb.checked = masterCheckbox.checked;
                    const tr = cb.closest('tr');
                    if (tr) {
                        tr.classList.toggle('row-selected', masterCheckbox.checked);
                    }
                });
                updateCount();
            });

            rowCheckboxes.forEach(cb => {
                cb.addEventListener('change', () => {
                    const tr = cb.closest('tr');
                    if (tr) tr.classList.toggle('row-selected', cb.checked);
                    const allChecked = Array.from(rowCheckboxes).every(c => c.checked);
                    masterCheckbox.checked = allChecked;
                    updateCount();
                });
            });
        });
    }

    // ==========================================
    // 8. Global Search Debounce & Quick Navigate
    // ==========================================
    function setupGlobalSearch() {
        const searchInput = document.querySelector('.search-input');
        if (!searchInput) return;

        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                const query = searchInput.value.trim();
                if (query) {
                    // Navigate to consignments filter by default with query
                    window.location.href = '/bookings?q=' + encodeURIComponent(query);
                }
            }
        });
    }

    // ==========================================
    // Initialize on DOM Ready
    // ==========================================
    document.addEventListener('DOMContentLoaded', () => {
        setupMobileNavigation();
        setupKeyboardShortcuts();
        setupCopyableElements();
        setupButtonLoadingStates();
        convertFlashMessagesToToasts();
        setupBatchTables();
        setupGlobalSearch();
    });

})();
