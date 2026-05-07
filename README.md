# PDF Screenshot Protection Pro

A comprehensive WordPress plugin that provides **advanced screenshot protection** for your content across **Windows, Mac, and Mobile devices**.

## 🎯 Features

### 🪟 **Windows Protection**
- ✅ Block Print Screen (PrtScn)
- ✅ Block Alt + Print Screen
- ✅ Block Win + Shift + S (Snip Tool)
- ✅ Block Developer Tools (F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+Shift+C)
- ✅ Disable Copy (Ctrl+C)
- ✅ Disable Right-Click Context Menu

### 🍎 **Mac Protection**
- ✅ Block Cmd + Shift + 3 (Full Screenshot)
- ✅ Block Cmd + Shift + 4 (Selection Screenshot)
- ✅ Block Cmd + Shift + 5 (Screenshot App)
- ✅ Block Developer Tools (Cmd+Option+I, Cmd+Option+J, Cmd+Option+U)
- ✅ Disable Copy (Cmd+C)
- ✅ Disable Right-Click Context Menu

### 📱 **Mobile Protection**
- ✅ Detect Volume Button Screenshots (iOS & Android)
- ✅ Monitor Screen Visibility Changes
- ✅ Show Alert Notifications
- ✅ Disable Text Selection
- ✅ Disable Right-Click (Long Press)
- ✅ Prevent Context Menu

### 🔧 **General Features**
- ✅ Automatic Device Detection
- ✅ Customizable Alert Messages
- ✅ Per-Device Configuration
- ✅ Easy Admin Dashboard
- ✅ No Technical Knowledge Required
- ✅ Translation Ready

## 📦 Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/ketata99/pdf-screenshot-protection.git
   ```

2. **Upload to WordPress:**
   - Upload the folder to `/wp-content/plugins/`
   - Or use WordPress admin: Plugins → Add New → Upload Plugin

3. **Activate the plugin:**
   - Go to WordPress Admin → Plugins
   - Find "PDF Screenshot Protection Pro"
   - Click "Activate"

## ⚙️ Configuration

### Admin Dashboard

1. Navigate to **Screenshot Protection** in the WordPress admin menu
2. Configure your settings:

#### General Settings
- **Enable Protection** - Toggle protection on/off
- **Alert Message** - Customize the alert message shown when users attempt screenshots
- **Block Copy** - Disable copy functionality across all devices
- **Block Right-Click** - Disable right-click context menu

#### Windows Protection
- **Enable Windows Protection** - Toggle Windows-specific protection
- **Block Print Screen** - Prevent PrtScn key
- **Block Alt + Print Screen** - Prevent Alt+PrtScn combination
- **Block Win + Shift + S** - Prevent Snip Tool shortcut
- **Block Developer Tools** - Prevent F12 and DevTools shortcuts

#### Mac Protection
- **Enable Mac Protection** - Toggle Mac-specific protection
- **Block Cmd + Shift + 3** - Prevent full screenshot
- **Block Cmd + Shift + 4** - Prevent selection screenshot
- **Block Cmd + Shift + 5** - Prevent Screenshot app
- **Block Developer Tools** - Prevent DevTools shortcuts

#### Mobile Protection
- **Enable Mobile Protection** - Toggle Mobile-specific protection
- **Block Volume Button Screenshots** - Prevent screenshots via volume buttons
- **Show Alert on Screenshot Attempt** - Display notification on detection

3. Click **Save Changes**

## 🔧 Device Detection

The plugin automatically detects the user's device and applies the appropriate protection:

| Device | Protection Applied |
|--------|--------------------|
| Windows PC | Windows Protection |
| Mac | Mac Protection |
| iPhone/iPad | iOS/Mobile Protection |
| Android Phone/Tablet | Android/Mobile Protection |
| Other | General Protection |

## 📋 File Structure

```
pdf-screenshot-protection/
├── pdf-screenshot-protection.php          # Main plugin file
├── includes/
│   ├── class-device-detector.php          # Device detection logic
│   ├── class-pdf-screenshot-protection.php # Core functionality
│   └── class-admin-settings.php           # Admin panel
├── assets/
│   ├── js/
│   │   ├── pdf-protection-windows.js      # Windows protection
│   │   ├── pdf-protection-mac.js          # Mac protection
│   │   └── pdf-protection-mobile.js       # Mobile protection
│   └── css/
│       ├── pdf-protection.css             # Frontend styles
│       └── admin.css                      # Admin styles
├── languages/
│   └── pdf-screenshot-protection.pot      # Translation template
├── README.md                              # Documentation
└── LICENSE                                # GPL v2 License
```

## 🔐 Security Considerations

⚠️ **Important Notes**:

1. **Browser-Level Protection**: This plugin operates at the browser/application level and provides reasonable protection against casual screenshot attempts.

2. **Advanced Users**: Sophisticated users may find ways to bypass these protections using:
   - System-level tools
   - Different operating systems
   - Virtual machines
   - External hardware capture devices

3. **Best Practices**:
   - Use this plugin in conjunction with other security measures
   - Keep WordPress and all plugins updated
   - Implement server-side security measures
   - Use HTTPS for all connections
   - Consider PDF encryption for sensitive documents

4. **Limitations**:
   - Cannot prevent physical photographs of the screen
   - Cannot prevent all clipboard operations
   - Cannot prevent network-level interception

## 🐛 Troubleshooting

### Protection not working
1. Verify the plugin is activated
2. Check that protection is enabled in settings
3. Clear browser cache
4. Test in a different browser
5. Disable browser extensions that might interfere

### Admin page not loading
1. Verify you have administrator privileges
2. Check PHP error logs
3. Increase PHP memory limit
4. Disable other admin plugins temporarily

### Settings not saving
1. Check file permissions on `/wp-content/plugins/`
2. Verify WordPress database connectivity
3. Check PHP error logs
4. Try disabling other plugins

## ❓ FAQ

**Q: Can this completely prevent all screenshots?**
A: No. This plugin prevents keyboard shortcuts and common methods, but cannot prevent system-level capture or physical photography.

**Q: Does this work on all browsers?**
A: It works on all modern browsers (Chrome, Firefox, Safari, Edge). Some mobile browsers may have limitations.

**Q: Can I customize the alert message?**
A: Yes! Go to Screenshot Protection settings and enter your custom message in the "Alert Message" field.

**Q: What about mobile apps that embed web content?**
A: The plugin works within web browsers. In-app browsers may have different behavior.

**Q: Is this GDPR compliant?**
A: The plugin doesn't collect personal data. Ensure your overall WordPress setup is GDPR compliant.

**Q: Can I use this on WooCommerce or membership sites?**
A: Yes! The plugin works site-wide or can be combined with conditional logic for specific pages.

## 📝 Changelog

### Version 2.0.0 (Current)
- Complete rewrite with device-specific protection
- Added Windows protection suite
- Added Mac protection suite
- Added Mobile protection suite
- Improved admin dashboard UI
- Added automatic device detection
- Enhanced security features

### Version 1.0.0
- Initial release
- Basic protection features
- Simple admin panel

## 🤝 Support

For issues, questions, or feature requests:
- GitHub Issues: [https://github.com/ketata99/pdf-screenshot-protection/issues](https://github.com/ketata99/pdf-screenshot-protection/issues)
- GitHub Discussions: [https://github.com/ketata99/pdf-screenshot-protection/discussions](https://github.com/ketata99/pdf-screenshot-protection/discussions)

## 📄 License

This plugin is licensed under the GPL v2 or later. See `LICENSE` file for details.

## 👤 Credits

Developed by **ketata99**

## ⚖️ Disclaimer

This plugin is provided "as-is" without any warranties. The author is not responsible for any damages or data loss caused by using this plugin. Always backup your site before installing new plugins.

---

**Support the Developer**: If you find this plugin useful, please consider leaving a ⭐ on GitHub!
