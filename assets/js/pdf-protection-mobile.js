/**
 * PDF Screenshot Protection - Mobile Protection
 */

(function($) {
    'use strict';

    var MobileProtection = {
        init: function() {
            if (typeof pdfProtectionSettings === 'undefined') {
                return;
            }
            
            if (pdfProtectionSettings.mobile_enabled) {
                this.blockScreenshot();
                this.disableContextMenu();
                this.disableTextSelection();
                this.addProtectionOverlay();
            }
        },

        blockScreenshot: function() {
            var self = this;

            // Detect volume button press (iOS and Android)
            $(document).on('keydown', function(e) {
                // Volume down key (typically keyCode 177)
                // Volume up key (typically keyCode 176)
                if (e.keyCode === 176 || e.keyCode === 177) {
                    // Prevent the event
                    e.preventDefault();
                    self.showAlert();
                    return false;
                }
            });

            // Monitor for visual changes (screenshot detection)
            this.monitorScreenVisibility();
        },

        monitorScreenVisibility: function() {
            var self = this;

            // Detect when app enters background (possible screenshot)
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    // App went to background, likely screenshot was taken
                    self.showAlert();
                }
            });

            // iOS specific detection
            $(window).on('beforeunload', function() {
                // Screenshot was taken on iOS
            });
        },

        disableContextMenu: function() {
            $(document).on('contextmenu', function(e) {
                e.preventDefault();
                return false;
            });
        },

        disableTextSelection: function() {
            $('body').css({
                'user-select': 'none',
                '-webkit-user-select': 'none',
                '-moz-user-select': 'none',
                '-ms-user-select': 'none'
            });

            $(document).on('selectstart', function(e) {
                e.preventDefault();
                return false;
            });
        },

        addProtectionOverlay: function() {
            var overlay = $('<div id="mobile-protection-overlay" style="' +
                'position: fixed; ' +
                'top: 0; ' +
                'left: 0; ' +
                'width: 100%; ' +
                'height: 100%; ' +
                'z-index: 1; ' +
                'pointer-events: none; ' +
                'background: transparent;" />');
            
            $('body').prepend(overlay);
        },

        showAlert: function() {
            if (pdfProtectionSettings.alert_message) {
                console.warn(pdfProtectionSettings.alert_message);
                
                // Show a subtle notification without blocking the UI
                var notification = $('<div style="' +
                    'position: fixed; ' +
                    'top: 20px; ' +
                    'left: 20px; ' +
                    'right: 20px; ' +
                    'background: rgba(0, 0, 0, 0.8); ' +
                    'color: white; ' +
                    'padding: 15px; ' +
                    'border-radius: 5px; ' +
                    'z-index: 10000; ' +
                    'text-align: center; ' +
                    'font-size: 14px;" />')
                    .text(pdfProtectionSettings.alert_message);
                
                $('body').append(notification);
                
                setTimeout(function() {
                    notification.fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 3000);
            }
        }
    };

    $(function() {
        MobileProtection.init();
    });

})(jQuery);
