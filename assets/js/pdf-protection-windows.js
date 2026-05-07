/**
 * PDF Screenshot Protection - Windows Protection
 */

(function($) {
    'use strict';

    var WindowsProtection = {
        init: function() {
            if (typeof pdfProtectionSettings === 'undefined') {
                return;
            }
            
            if (pdfProtectionSettings.windows_enabled) {
                this.blockScreenshot();
                this.blockDevTools();
            }
        },

        blockScreenshot: function() {
            $(document).keydown(function(e) {
                // Block Print Screen
                if (e.keyCode === 44) {
                    e.preventDefault();
                    alert(pdfProtectionSettings.alert_message);
                    return false;
                }

                // Block Alt + Print Screen
                if (e.altKey && e.keyCode === 44) {
                    e.preventDefault();
                    alert(pdfProtectionSettings.alert_message);
                    return false;
                }

                // Block Win + Shift + S (Snip Tool)
                if (e.keyCode === 83 && e.shiftKey && (e.metaKey || e.ctrlKey)) {
                    e.preventDefault();
                    alert(pdfProtectionSettings.alert_message);
                    return false;
                }
            });
        },

        blockDevTools: function() {
            $(document).keydown(function(e) {
                // Block F12 (Developer Tools)
                if (e.keyCode === 123) {
                    e.preventDefault();
                    alert('Developer Tools are disabled.');
                    return false;
                }

                // Block Ctrl + Shift + I (Inspector)
                if (e.ctrlKey && e.shiftKey && e.keyCode === 73) {
                    e.preventDefault();
                    alert('Developer Tools are disabled.');
                    return false;
                }

                // Block Ctrl + Shift + J (Console)
                if (e.ctrlKey && e.shiftKey && e.keyCode === 74) {
                    e.preventDefault();
                    alert('Developer Tools are disabled.');
                    return false;
                }

                // Block Ctrl + Shift + C (Element Selector)
                if (e.ctrlKey && e.shiftKey && e.keyCode === 67) {
                    e.preventDefault();
                    alert('Developer Tools are disabled.');
                    return false;
                }
            });
        }
    };

    $(function() {
        WindowsProtection.init();
    });

})(jQuery);
