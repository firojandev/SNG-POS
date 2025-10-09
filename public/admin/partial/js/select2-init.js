/**
 * Global Select2 Initialization
 * This script initializes Select2 for all pages
 */

// Global Select2 configuration
window.Select2Config = {
    // Default configuration for all Select2 instances
    default: {
        width: '100%',
        allowClear: false, // Remove clear button
        placeholder: 'Select an option...',
        language: {
            noResults: function() {
                return "No results found";
            },
            searching: function() {
                return "Searching...";
            }
        }
    },
    
    // Small size configuration for filters
    small: {
        width: '100%',
        allowClear: false, // Remove clear button for filter dropdowns
        placeholder: 'Select...',
        minimumResultsForSearch: 0, // Always show search box
        language: {
            noResults: function() {
                return "No results found";
            },
            searching: function() {
                return "Searching...";
            }
        }
    }
};

// Global Select2 initialization function
window.initializeSelect2 = function() {
    // Wait for jQuery and Select2 to be available
    if (typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') {
        setTimeout(window.initializeSelect2, 100);
        return;
    }

    // Initialize all select elements with select2-dropdown class
    $('.select2-dropdown').each(function() {
        const $select = $(this);
        
        // Skip if already initialized
        if ($select.hasClass('select2-hidden-accessible')) {
            return;
        }

        // Basic configuration with search enabled
        let config = {
            width: '100%',
            allowClear: false,
            minimumResultsForSearch: 0, // Always show search box
            placeholder: 'Select an option...',
            language: {
                noResults: function() {
                    return "No results found";
                },
                searching: function() {
                    return "Searching...";
                }
            }
        };

        // If select is inside a modal, attach dropdown to modal
        const modal = $select.closest('.modal');
        if (modal.length > 0) {
            config.dropdownParent = modal;
        }

        // Get placeholder from the first empty option
        const emptyOption = $select.find('option[value=""]').first();
        if (emptyOption.length > 0) {
            config.placeholder = emptyOption.text();
        }

        // Initialize Select2
        try {
            $select.select2(config);
        } catch (error) {
            console.error('✗ Error initializing Select2:', error, $select);
        }
    });

    // Also initialize regular select elements in forms
    $('select.form-select:not(.select2-hidden-accessible):not(.no-select2)').each(function() {
        const $select = $(this);
        
        // Add select2-dropdown class
        $select.addClass('select2-dropdown');
        
        let config = {
            width: '100%',
            allowClear: false,
            minimumResultsForSearch: 0, // Always show search box
            placeholder: 'Select an option...',
            language: {
                noResults: function() {
                    return "No results found";
                },
                searching: function() {
                    return "Searching...";
                }
            }
        };

        const emptyOption = $select.find('option[value=""]').first();
        if (emptyOption.length > 0) {
            config.placeholder = emptyOption.text();
        }

        try {
            $select.select2(config);
        } catch (error) {
            console.error('✗ Error auto-initializing Select2:', error);
        }
    });
};

// Reinitialize Select2 for dynamically added content
window.reinitializeSelect2 = function(container) {
    const $container = container ? $(container) : $(document);
    
    $container.find('.select2-dropdown:not(.select2-hidden-accessible)').each(function() {
        const $select = $(this);
        
        let config = $.extend({}, window.Select2Config.default);
        if ($select.hasClass('form-select-sm') || $select.closest('.simple-filter-section').length > 0) {
            config = $.extend({}, window.Select2Config.small);
        }

        const placeholder = $select.data('placeholder') || $select.find('option[value=""]').text() || config.placeholder;
        config.placeholder = placeholder;

        // If select is inside a modal, attach dropdown to modal
        const modal = $select.closest('.modal');
        if (modal.length > 0) {
            config.dropdownParent = modal;
        }

        try {
            $select.select2(config);
        } catch (error) {
            console.error('Error reinitializing Select2:', error);
        }
    });
};

// Destroy Select2 instances
window.destroySelect2 = function(selector) {
    $(selector).each(function() {
        if ($(this).hasClass('select2-hidden-accessible')) {
            $(this).select2('destroy');
        }
    });
};

// Initialize when DOM is ready
$(document).ready(function() {
    window.initializeSelect2();
    
    // Double-check after 1 second
    setTimeout(() => {
        // Force reinitialize all select2 dropdowns to ensure search works
        $('.select2-dropdown').each(function() {
            const $select = $(this);
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
        });
        
        // Reinitialize with search enabled
        window.initializeSelect2();
    }, 1000);
});

// Reinitialize on AJAX content load (if needed)
$(document).ajaxComplete(function() {
    setTimeout(function() {
        window.initializeSelect2();
    }, 100);
});

// Handle dynamic content (MutationObserver for modern browsers)
if (typeof MutationObserver !== 'undefined') {
    const observer = new MutationObserver(function(mutations) {
        let shouldReinitialize = false;
        
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                // Check if any added nodes contain select elements
                for (let i = 0; i < mutation.addedNodes.length; i++) {
                    const node = mutation.addedNodes[i];
                    if (node.nodeType === 1) { // Element node
                        if (node.tagName === 'SELECT' || $(node).find('select').length > 0) {
                            shouldReinitialize = true;
                            break;
                        }
                    }
                }
            }
        });
        
        if (shouldReinitialize) {
            setTimeout(function() {
                window.initializeSelect2();
            }, 50);
        }
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}
