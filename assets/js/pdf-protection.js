/**
 * PDF Screenshot Protection - Frontend Script
 */

(function($) {
    'use strict';

    var PDFProtection = {
        init: function() {
            if (typeof pdfProtectionSettings === 'undefined') {
                return;
            }

            this.settings = pdfProtectionSettings;
            this.protectPDFs();
        },

        protectPDFs: function() {
            var self = this;
            var method = this.settings.method;

            // Apply watermark
            if (method === 'watermark' || method === 'combined') {
                this.addWatermark();
            }

            // Block copy and print
            if (method === 'disable_copy' || method === 'combined') {
                this.blockCopyPrint();
            }

            // Block right-click
            if (this.settings.method) {
                this.blockContextMenu();
            }
        },

        addWatermark: function() {
            var watermarkText = this.settings.watermark_text || 'CONFIDENTIAL';

            // Apply watermark styles
            var style = document.createElement('style');
            style.innerHTML = `
                .pdf-protected::before {
                    content: "${watermarkText}";
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    font-size: 60px;
                    font-weight: bold;
                    opacity: 0.1;
                    transform: translate(-50%, -50%) rotate(-45deg);
                    z-index: 10;
                    pointer-events: none;
                    width: 100%;
                    text-align: center;
                }
            `;
            document.head.appendChild(style);

            // Add class to PDF elements
            $('embed[type="application/pdf"], iframe[src*=".pdf"], object[data*=".pdf"]').addClass('pdf-protected');
        },

        blockCopyPrint: function() {
            // Disable print
            if (this.settings.block_print) {
                $(document).keydown(function(e) {
                    if ((e.ctrlKey || e.metaKey) && e.keyCode === 80) {
                        e.preventDefault();
                        alert('Printing is disabled for protected PDFs.');
                        return false;
                    }
                });
            }

            // Disable copy
            if (this.settings.block_copy) {
                $(document).on('copy', function(e) {
                    var selection = window.getSelection();
                    if (selection && selection.toString().length > 0) {
                        e.preventDefault();
                        alert('Copying is disabled for protected PDFs.');
                    }
                });
            }
        },

        blockContextMenu: function() {
            $(document).on('contextmenu', function(e) {
                if ($(e.target).closest('embed[type="application/pdf"], iframe[src*=".pdf"], object[data*=".pdf"]').length) {
                    e.preventDefault();
                    alert('Right-click is disabled for protected PDFs.');
                    return false;
                }
            });
        }
    };

    // Initialize on document ready
    $(function() {
        PDFProtection.init();
    });

})(jQuery);
