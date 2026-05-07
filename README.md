# PDF Screenshot Protection

A powerful WordPress plugin to protect your PDF files against screenshots, copying, printing, and unauthorized access.

## Features

✅ **Multiple Protection Methods**
- Watermark overlay on PDFs
- Disable copy/print functionality
- Combined protection approach

✅ **Easy Configuration**
- Simple admin dashboard
- Customizable watermark text
- Toggle protection on/off instantly

✅ **User-Friendly**
- No technical knowledge required
- Works with embedded PDFs
- Compatible with standard WordPress PDF attachments

✅ **Security Features**
- Block right-click context menu
- Prevent text selection
- Disable copy shortcut (Ctrl+C)
- Disable print shortcut (Ctrl+P)

## Installation

1. Download the plugin or clone the repository:
   ```bash
   git clone https://github.com/ketata99/pdf-screenshot-protection.git
   ```

2. Upload to your WordPress plugins directory:
   ```
   /wp-content/plugins/pdf-screenshot-protection/
   ```

3. Activate the plugin from WordPress admin panel:
   - Go to `Plugins` → `Installed Plugins`
   - Find "PDF Screenshot Protection"
   - Click "Activate"

## Usage

### Basic Configuration

1. Navigate to `PDF Protection` in the WordPress admin menu
2. Configure your protection settings:
   - **Enable Protection**: Toggle protection on/off
   - **Protection Method**: Choose between Watermark, Disable Copy/Print, or Combined
   - **Watermark Text**: Enter custom text for watermark (default: "CONFIDENTIAL")
   - **Block Copy**: Prevent users from copying PDF text
   - **Block Print**: Prevent users from printing PDFs
   - **Block Download**: Prevent users from downloading PDFs
3. Click "Save Changes"

### Protection Methods

#### Watermark
Adds a semi-transparent overlay text across the PDF. Perfect for marking PDFs as confidential.

#### Disable Copy/Print
Prevents users from copying text or printing the PDF document.

#### Combined
Applies both watermark and copy/print blocking for maximum security.

## Configuration Options

All settings are stored in WordPress options and can be modified through the admin interface:

- `pdf_screenshot_protection_enabled` - Enable/disable the plugin
- `pdf_screenshot_protection_method` - Protection method (watermark/disable_copy/combined)
- `pdf_screenshot_protection_watermark` - Custom watermark text
- `pdf_screenshot_protection_block_copy` - Block copy functionality
- `pdf_screenshot_protection_block_print` - Block print functionality
- `pdf_screenshot_protection_block_download` - Block download functionality

## Screenshots

### Admin Settings Panel
Easy-to-use admin interface for configuring PDF protection settings.

### Protected PDF
PDFs are displayed with protection layers applied according to your settings.

## Compatibility

- **WordPress**: 5.0 and above
- **PHP**: 7.2 and above
- **Browsers**: All modern browsers (Chrome, Firefox, Safari, Edge)

## File Structure

```
pdf-screenshot-protection/
├── pdf-screenshot-protection.php       # Main plugin file
├── includes/
│   ├── class-pdf-screenshot-protection.php  # Core functionality
│   └── class-admin-settings.php             # Admin settings
├── assets/
│   ├── js/
│   │   └── pdf-protection.js           # Frontend JavaScript
│   └── css/
│       ├── pdf-protection.css          # Frontend styles
│       └── admin.css                   # Admin styles
├── languages/
│   └── pdf-screenshot-protection.pot   # Translation template
├── README.md                            # Documentation
└── LICENSE                              # GPL v2 License
```

## Development

### Code Structure

The plugin follows WordPress coding standards:
- Object-oriented programming with singleton pattern
- Proper hook usage (actions and filters)
- Security best practices (escaping, sanitizing)
- Comprehensive inline documentation

### Hooks

#### Filters
- `pdf_screenshot_protection_enabled` - Modify protection enabled status
- `pdf_screenshot_protection_method` - Modify protection method

#### Actions
- `pdf_screenshot_protection_init` - Plugin initialization
- `pdf_screenshot_protection_enqueue_scripts` - Script/style enqueue

## Security Considerations

⚠️ **Important Notes**:
- This plugin provides user-level protection, not cryptographic PDF encryption
- Advanced users may still find ways to bypass protections
- For highly sensitive documents, consider PDF encryption at the file level
- Always keep WordPress and plugins updated

## Troubleshooting

### PDFs not showing watermark
- Ensure the plugin is activated
- Check that protection is enabled in settings
- Verify the protection method is set to "Watermark" or "Combined"
- Clear browser cache

### Copy/Print still works
- Some PDF viewers may have built-in protection override
- Check if browser extensions are interfering
- Try a different browser

### Admin menu not appearing
- Ensure you have administrator privileges
- Verify the plugin is activated
- Check PHP error logs

## FAQ

**Q: Will this prevent users from taking screenshots?**
A: The watermark can be visible in screenshots, but this plugin cannot technically prevent screenshots on a user's device. For absolute screenshot prevention, consider solutions with DRM (Digital Rights Management).

**Q: Is this secure?**
A: This plugin provides reasonable protection against casual copying and printing. For highly sensitive documents, implement PDF encryption or consider specialized DRM solutions.

**Q: Can I customize the watermark text?**
A: Yes! Go to PDF Protection settings and enter your custom watermark text.

**Q: Does this work with all PDF viewers?**
A: It works best with browser-based PDF viewers. Plugin functionality may vary depending on the PDF viewer being used.

**Q: Can I translate this plugin?**
A: Yes! The plugin is fully translatable. Copy `languages/pdf-screenshot-protection.pot` and create your language files.

## Support

For issues, questions, or feature requests, please visit:
- GitHub Issues: https://github.com/ketata99/pdf-screenshot-protection/issues
- GitHub Discussions: https://github.com/ketata99/pdf-screenshot-protection/discussions

## License

This plugin is licensed under the GPL v2 or later. See `LICENSE` file for details.

## Changelog

### Version 1.0.0
- Initial release
- Core protection features
- Admin settings panel
- Watermark functionality
- Copy/Print blocking

## Credits

Developed by ketata99

## Disclaimer

This plugin is provided as-is without any warranties. Always backup your site before installing new plugins.
