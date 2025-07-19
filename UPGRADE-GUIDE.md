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
