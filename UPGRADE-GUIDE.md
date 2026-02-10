# Nova TipTap Upgrade Guide

## Upgrading to v6.0

Version 6.0 is a major update that upgrades TipTap from v2 beta to v3 stable and removes the Font Awesome dependency.

### Pre-Upgrade Checklist

- [ ] **Backup your application** before updating
- [ ] **Check for custom TipTap extensions** that may need updating
- [ ] **Review any icon customizations** (Font Awesome is removed)
- [ ] **Test in a staging environment** first if possible

### Step-by-Step Upgrade

#### 1. Update the Package

```bash
composer update marshmallow/nova-tiptap
```

#### 2. Clear Nova Assets

```bash
php artisan nova:publish
php artisan view:clear
php artisan cache:clear
```

#### 3. Clear Browser Cache

Clear your browser cache or do a hard refresh (Ctrl+Shift+R / Cmd+Shift+R) to load the new assets.

### Breaking Changes

#### TipTap v3 Migration

- **History extension renamed**: `History` is now `UndoRedo`
- **Package consolidation**: Many extension packages are now consolidated:
  - Lists: `@tiptap/extension-list` (BulletList, OrderedList, ListItem)
  - Tables: `@tiptap/extension-table` (Table, TableRow, TableCell, TableHeader)
  - Core: `@tiptap/extensions` (UndoRedo, Placeholder, Dropcursor, Gapcursor)

If you have custom TipTap extensions, you may need to update imports and APIs. See the [TipTap v3 migration guide](https://tiptap.dev/docs/editor/resources/upgrade) for details.

#### Image Extension Change

- **Before**: Separate `@tiptap/extension-image` and `tiptap-extension-resize-image`
- **After**: Only `tiptap-extension-resize-image` (extended with custom attributes)

The image node type is now `imageResize` instead of `image`. If you have custom code that checks for active images or manipulates image attributes, update accordingly:

```javascript
// Before
editor.isActive('image')
editor.getAttributes('image')
editor.commands.updateAttributes('image', { ... })

// After
editor.isActive('imageResize')
editor.getAttributes('imageResize')
editor.commands.updateAttributes('imageResize', { ... })
```

#### Icon Library Change

- **Before**: Font Awesome (free + pro packages)
- **After**: Custom SVG icons

All toolbar icons have been replaced with custom SVG components. If you customized icons, you'll need to update your customizations to use the new icon system.

#### lowlight API Change

- **Before**: `import { lowlight } from 'lowlight'`
- **After**: `import { common, createLowlight } from 'lowlight'; const lowlight = createLowlight(common);`

### Dependencies Removed

The following packages are no longer needed and have been removed:

- `@fortawesome/fontawesome-free`
- `@fortawesome/fontawesome-pro`
- `@fortawesome/fontawesome-svg-core`
- `@fortawesome/free-solid-svg-icons`
- `@fortawesome/pro-regular-svg-icons`
- `@fortawesome/pro-solid-svg-icons`
- `@fortawesome/vue-fontawesome`
- `vue-trix`

### Dependencies Added

- `@floating-ui/dom` (required for TipTap v3 menus)
- `@heroicons/vue` (optional, available for future use)

### Troubleshooting

#### Icons not appearing

Clear browser cache and do a hard refresh. The new SVG icons are bundled differently than Font Awesome.

#### TipTap editor not loading

1. Check browser console for JavaScript errors
2. Ensure you've run `npm run production` or `npm run dev` to rebuild assets
3. Clear all caches: `php artisan cache:clear && php artisan view:clear`

#### Code syntax highlighting broken

The lowlight API changed in v3. If you have custom syntax highlighting configuration, update to use the new `createLowlight()` API.

#### Image resize handles not working

Image resizing is now handled by the `tiptap-extension-resize-image` package exclusively. If you have existing images that don't show resize handles:

1. Click on the image to select it
2. The resize handles should appear at the corners
3. If editing image properties (title, alt, class), click the image button in the toolbar after selecting the image

---

# Nova TipTap Security Upgrade Guide

## 🚨 Critical Security Patch - Immediate Action Required

This guide will help you safely upgrade nova-tiptap to address a **Critical Remote Code Execution vulnerability**.

## Pre-Upgrade Checklist

-   [ ] **Backup your application** before updating
-   [ ] **Review current file upload usage** in your Nova resources
-   [ ] **Check which disks you're using** for file storage
-   [ ] **Note any custom file types** you need to support
-   [ ] **Test in a staging environment** first if possible

## Step-by-Step Upgrade

### 1. Update the Package

```bash
composer update marshmallow/nova-tiptap
```

### 2. Publish Updated Configuration

```bash
php artisan vendor:publish --tag=nova-tiptap-config --force
```

**⚠️ Note**: This will overwrite your existing `config/nova-tiptap.php`. If you had custom settings, you'll need to reapply them.

### 3. Review Upload Configuration

Edit `config/nova-tiptap.php` and customize the upload settings:

```php
<?php

return [
    // ... existing config ...

    'upload' => [
        // File size limits (in bytes)
        'max_image_size' => 5242880,  // 5MB
        'max_file_size' => 10485760,  // 10MB

        // Allowed file extensions
        'allowed_image_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
        'allowed_file_extensions' => [
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
            'txt', 'rtf', 'csv', 'zip', 'rar', '7z',
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
            'mp3', 'wav', 'mp4', 'avi', 'mov', 'wmv'
        ],

        // Allowed storage disks (SECURITY CRITICAL)
        'allowed_disks' => ['public', 'local'],

        // Enable strict MIME type checking
        'strict_mime_checking' => true,
    ],
];
```

### 4. Update Disk Configuration (if needed)

If you were using disks other than `public` or `local`, add them to `allowed_disks`:

```php
'allowed_disks' => ['public', 'local', 's3'], // Add your disks here
```

**⚠️ Security Warning**: Only add disks that should be accessible for uploads. Never add sensitive disks.

### 5. Update File Type Lists (if needed)

If you need additional file types, add them to the allowed extensions:

```php
'allowed_file_extensions' => [
    // ... default types ...
    'ai', 'psd', 'sketch', // Add custom types here
],
```

**⚠️ Security Warning**: Never add executable extensions like `php`, `exe`, `sh`, `bat`, etc.

### 6. Update Your Nova Resources (if needed)

If you were programmatically setting disks in your Nova resources, ensure they're in the allowed list:

```php
// In your Nova Resource
Tiptap::make('Content')
    ->disk('public') // Make sure this disk is in allowed_disks
    ->path('articles');
```

## Breaking Changes

### Authentication Required

-   **Before**: Anyone could upload files
-   **After**: Must be authenticated Nova user

### File Type Restrictions

-   **Before**: Any file type allowed
-   **After**: Only whitelisted extensions allowed

### Disk Restrictions

-   **Before**: Could upload to any configured disk
-   **After**: Only allowed disks accepted

## Troubleshooting

### "File type not allowed" errors

**Solution**: Add the file extension to `allowed_file_extensions` or `allowed_image_extensions` in config.

### "Unauthorized" errors on uploads

**Solution**: Ensure users are properly authenticated in Nova before uploading.

### Files not appearing

**Solution**: Check that the disk is in `allowed_disks` and properly configured.

### "Disk not allowed" errors

**Solution**: Add the disk to `allowed_disks` in the security configuration.

## Security Validation Checklist

After upgrading, verify:

-   [ ] Unauthenticated requests to upload endpoints return 401/403
-   [ ] PHP/executable files are rejected
-   [ ] Only configured disks are accessible
-   [ ] File size limits are enforced
-   [ ] Path traversal attempts are blocked
-   [ ] Existing functionality still works for authenticated users

## Rollback Plan (Emergency Only)

If you encounter critical issues and need to rollback:

```bash
# 1. Revert to previous version
composer require marshmallow/nova-tiptap:5.6.0

# 2. Restore your backup configuration
cp config/nova-tiptap.php.backup config/nova-tiptap.php

# 3. Clear caches
php artisan config:clear
php artisan route:clear
```

**⚠️ WARNING**: Rollback leaves you vulnerable. Only use as a temporary measure while fixing issues.

## Support

If you need help with the upgrade:

1. **Check the logs**: Look for specific error messages in `storage/logs/laravel.log`
2. **Review configuration**: Ensure your security settings match your needs
3. **Test systematically**: Validate each security feature is working
4. **Open an issue**: If you find bugs or need assistance

## Version History

-   **Before patch**: Vulnerable to RCE
-   **After patch**: Secure with authentication and file validation

---

**Remember**: This security patch is critical. Do not delay the upgrade.
