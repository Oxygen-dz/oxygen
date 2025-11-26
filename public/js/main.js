/**
 * OxygenFramework - Main JavaScript
 * 
 * Professional JavaScript for your application
 * 
 * @author Redwan Aouni
 * @version 2.0.0
 */

(function() {
    'use strict';

    /**
     * OxygenFramework Main App
     */
    const Oxygen = {
        
        /**
         * Initialize the application
         */
        init: function() {
            console.log('OxygenFramework initialized');
            this.setupEventListeners();
            this.setupForms();
            this.setupAlerts();
        },

        /**
         * Setup global event listeners
         */
        setupEventListeners: function() {
            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Mobile menu toggle
            const menuToggle = document.querySelector('.menu-toggle');
            const navMenu = document.querySelector('.navbar-nav');
            
            if (menuToggle && navMenu) {
                menuToggle.addEventListener('click', function() {
                    navMenu.classList.toggle('active');
                });
            }
        },

        /**
         * Setup form handling
         */
        setupForms: function() {
            // Add loading state to forms on submit
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.textContent = 'Loading...';
                    }
                });
            });

            // Auto-resize textareas
            document.querySelectorAll('textarea').forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });
            });
        },

        /**
         * Setup alert auto-dismiss
         */
        setupAlerts: function() {
            document.querySelectorAll('.alert').forEach(alert => {
                // Auto dismiss after 5 seconds
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);

                // Add close button
                const closeBtn = document.createElement('span');
                closeBtn.innerHTML = '&times;';
                closeBtn.style.cssText = 'float: right; cursor: pointer; font-size: 20px;';
                closeBtn.addEventListener('click', () => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                });
                alert.insertBefore(closeBtn, alert.firstChild);
            });
        },

        /**
         * Make AJAX request
         * 
         * @param {string} url - Request URL
         * @param {object} options - Request options
         * @returns {Promise}
         */
        ajax: function(url, options = {}) {
            const defaults = {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            };

            const config = { ...defaults, ...options };

            return fetch(url, config)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .catch(error => {
                    console.error('AJAX Error:', error);
                    throw error;
                });
        },

        /**
         * Show notification
         * 
         * @param {string} message - Notification message
         * @param {string} type - Notification type (success, danger, warning, info)
         */
        notify: function(message, type = 'info') {
            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.textContent = message;
            alert.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            
            document.body.appendChild(alert);
            
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 3000);
        },

        /**
         * Confirm dialog
         * 
         * @param {string} message - Confirmation message
         * @returns {Promise<boolean>}
         */
        confirm: function(message) {
            return new Promise((resolve) => {
                const result = window.confirm(message);
                resolve(result);
            });
        },

        /**
         * Format date
         * 
         * @param {Date|string} date - Date to format
         * @returns {string}
         */
        formatDate: function(date) {
            const d = new Date(date);
            return d.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },

        /**
         * Debounce function
         * 
         * @param {Function} func - Function to debounce
         * @param {number} wait - Wait time in milliseconds
         * @returns {Function}
         */
        debounce: function(func, wait = 300) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    };

    /**
     * Initialize when DOM is ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => Oxygen.init());
    } else {
        Oxygen.init();
    }

    /**
     * Expose Oxygen to global scope
     */
    window.Oxygen = Oxygen;

})();

/**
 * Example usage:
 * 
 * // Make AJAX request
 * Oxygen.ajax('/api/posts')
 *     .then(data => console.log(data))
 *     .catch(error => console.error(error));
 * 
 * // Show notification
 * Oxygen.notify('Post created successfully!', 'success');
 * 
 * // Confirm action
 * Oxygen.confirm('Are you sure?').then(result => {
 *     if (result) {
 *         // User confirmed
 *     }
 * });
 */
