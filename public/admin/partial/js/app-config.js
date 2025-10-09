/**
 * Application Configuration
 * Global configuration variables available throughout the admin panel
 */

// Global application configuration
window.appConfig = {
    currency: null, // Will be set dynamically
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
    baseUrl: window.location.origin
};

/**
 * Initialize application configuration
 * This function should be called after the DOM is ready
 */
function initializeAppConfig() {
    // Set currency from data attribute or default
    const currencyElement = document.querySelector('[data-app-currency]');
    if (currencyElement) {
        window.appConfig.currency = currencyElement.getAttribute('data-app-currency');
    } else {
        window.appConfig.currency = '$'; // Default fallback
    }
    
    // Make currency available globally for backward compatibility
    window.appCurrency = window.appConfig.currency;
}

// Auto-initialize when DOM is ready
$(document).ready(function() {
    initializeAppConfig();
});
