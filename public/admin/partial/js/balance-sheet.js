"use strict";

/**
 * Balance Sheet Manager
 *
 * @package    SNG-POS
 * @subpackage Balance Sheet Module
 * @version    1.0.0
 */

class BalanceSheetManager {
    constructor() {
        // Configuration
        this.config = window.balanceSheetConfig || {};
        this.currency = this.config.currency || '$';

        // Initialize
        this.init();
    }

    /**
     * Initialize the page
     */
    init() {
        console.log('BalanceSheetManager: Initializing...');

        // Initialize jQuery UI datepickers
        this.initializeDatePickers();

        // Bind events
        this.bindEvents();
    }

    /**
     * Initialize jQuery UI datepickers
     */
    initializeDatePickers() {
        if (typeof $.fn.datepicker !== 'undefined') {
            const phpFmt = this.config.dateFormatPhp || 'Y-m-d';
            const jqFmt = this.phpDateFormatToJqueryUI(phpFmt);

            $('#as_of_date').datepicker({
                dateFormat: jqFmt,
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                maxDate: 0 // Cannot select future dates
            });
        }
    }

    /**
     * Map PHP date format to jQuery UI datepicker format
     */
    phpDateFormatToJqueryUI(phpFormat) {
        const map = {
            'Y': 'yy',
            'y': 'y',
            'm': 'mm',
            'n': 'm',
            'd': 'dd',
            'j': 'd',
            'F': 'MM',
            'M': 'M',
            'l': 'DD',
            'D': 'D'
        };

        let jqFormat = phpFormat;
        for (const [phpChar, jqChar] of Object.entries(map)) {
            jqFormat = jqFormat.replace(new RegExp(phpChar, 'g'), jqChar);
        }

        return jqFormat;
    }

    /**
     * Bind UI events
     */
    bindEvents() {
        // Additional event bindings can be added here
    }
}

// Initialize when DOM is ready
$(document).ready(function() {
    window.balanceSheetManager = new BalanceSheetManager();
});
