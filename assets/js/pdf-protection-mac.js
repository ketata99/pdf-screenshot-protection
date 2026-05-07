/**
 * PDF Screenshot Protection - Mac Protection
 */

(function($) {
    'use strict';

    var MacProtection = {
        init: function() {
            if (typeof pdfProtectionSettings === 'undefined') {
                return;
            }
            
            if (pdfProtectionSettings.mac_enabled) {
                this.blockScreenshot();
                this.blockDevTools();
            }
        },

        blockScreenshot: function() {
            $(document).keydown(function(e) {
                // Check if Cmd key is pressed
                if (!e.metaKey) return;

                // Block Cmd + Shift + 3 (Full Screenshot)
                if (e.shiftKey && e.keyCode === 51) {
                    e.preventDefault();
                    alert(pdfProtectionSettings.alert_message);
                    return false;
                }

                // Block Cmd + Shift + 4 (Selection Screenshot)
                if (e.shiftKey && e.keyCode === 52) {
                    e.preventDefault();
                    alert(pdfProtectionSettings.alert_message);
                    return false;
                }

                // Block Cmd + Shift + 5 (Screenshot App)
                if (e.shiftKey && e.keyCode === 53) {
                    e.preventDefault();
                    alert(pdfProtectionSettings.alert_message);
                    return false;
                }
            });
        },

        blockDevTools: function() {
            $(document).keydown(function(e) {
                // Check if Cmd key is pressed
                if (!e.metaKey) return;

                // Block Cmd + Option + I (Inspector)
                if (e.altKey && e.keyCode === 73) {
                    e.preventDefault();
                    alert('Developer Tools are disabled.');
                    return false;
                }

                // Block Cmd + Option + J (Console)
                if (e.altKey && e.keyCode === 74) {
                    e.preventDefault();
                    alert('Developer Tools are disabled.');
                    return false;
                }

                // Block Cmd + Option + U (View Source)
                if (e.altKey && e.keyCode === 85) {
                    e.preventDefault();
                    alert('Developer Tools are disabled.');
                    return false;
                }
            });
        }
    };

    $(function() {
        MacProtection.init();
    });

})(jQuery);
