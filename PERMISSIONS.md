# Ximdex CMS File Permissions Guide

## Overview

Ximdex CMS requires specific file and directory permissions to function correctly. The web server (nginx + PHP-FPM) runs as `www-data` and needs write access to certain files and directories.

## Standard Permissions

### Directory Permissions
- **Main directories**: `2775` (drwxrwsr-x) - setgid bit set, owner and group writable
  - `/conf/` - Configuration files
  - `/data/` - Runtime data
  - `/logs/` - Application logs

- **Subdirectories**: `775` (drwxrwxr-x) - owner and group writable
  - All subdirectories under `data/`, `logs/`
  - Cache directories in `vendor/`

### File Permissions

#### Runtime/State Files (Web Server Writable)
- **_STATUSFILE**: `666` (rw-rw-rw-) - owner and group can write
- **install-modules.php**: `775` (rwxrwxr-x) - owner and group can write
- **install-params.conf.php**: `660` (rw-rw----) - owner and group can write

#### Sensitive Configuration Files (Read-Only for Web Server)
- **Config files** (browser.php, diskspace.php, etc.): `640` (rw-r-----)
  - Owner (deployment user) can read/write
  - Group (www-data) can read only
  - Others: no access

#### Log Files
- **Log files**: `664` (rw-rw-r--)
  - Owner and group can write
  - Others can read

## File Ownership

- **Owner**: `juanpri` (deployment user)
- **Group**: `www-data` (web server group)
- **Setgid bit**: Set on main directories so new files inherit `www-data` group

## Setup Script

To apply correct permissions after deployment:

```bash
#!/bin/bash
# Ximdex CMS Permission Setup Script

XIMDEX_ROOT="/mnt/services/CLAUDING/xcms"

# Main directories - setgid + group writable
echo "Setting main directory permissions..."
chmod 2775 "$XIMDEX_ROOT/conf/"
chmod 2775 "$XIMDEX_ROOT/data/"
chmod 2775 "$XIMDEX_ROOT/logs/"

# Subdirectories
echo "Setting subdirectory permissions..."
find "$XIMDEX_ROOT/data" -type d -exec chmod 775 {} \;
find "$XIMDEX_ROOT/logs" -type d -exec chmod 775 {} \;
find "$XIMDEX_ROOT/vendor" -type d -name "cache" -exec chmod 775 {} \;
find "$XIMDEX_ROOT/public_xmd" -type d \( -name "cache" -o -name "tmp" \) -exec chmod 775 {} \;

# Runtime files - web server writable
echo "Setting runtime file permissions..."
chmod 666 "$XIMDEX_ROOT/conf/_STATUSFILE"
chmod 660 "$XIMDEX_ROOT/conf/install-params.conf.php"

# Log files
echo "Setting log file permissions..."
find "$XIMDEX_ROOT/logs" -type f -name "*.log" -exec chmod 664 {} \;

# Config files - read-only for web server
echo "Setting config file permissions..."
find "$XIMDEX_ROOT/conf" -type f -name "*.php" ! -name "install-params.conf.php" -exec chmod 640 {} \;

echo "Permissions updated successfully!"
```

## Verification

Test that permissions are correct by running the installer or accessing the web interface:

1. **Installer test**:
   - Can proceed through all steps without permission errors
   - Settings are saved successfully
   - Status file is updated

2. **Runtime test**:
   - Logs are being written
   - Cache directories are writable
   - Upload files can be created

## Troubleshooting

### Permission Denied Errors

If you see "Permission denied" in logs:

```bash
# Check current permissions
ls -la $XIMDEX_ROOT/conf/
ls -la $XDEX_ROOT/data/
ls -la $XIMDEX_ROOT/logs/

# Reset to recommended values
sudo bash setup-permissions.sh
```

### Wrong File Ownership

If files are created with wrong owner/group:

```bash
# Fix ownership
sudo chown -R juanpri:www-data $XIMDEX_ROOT/
sudo chmod 2775 $XIMDEX_ROOT/conf/
sudo chmod 2775 $XIMDEX_ROOT/data/
sudo chmod 2775 $XIMDEX_ROOT/logs/
```

### Cache Issues

Clear cache if experiencing weird behavior:

```bash
rm -rf $XIMDEX_ROOT/data/cache/*
rm -rf $XIMDEX_ROOT/data/tmp/*
find $XIMDEX_ROOT/vendor -type d -name "cache" -exec rm -rf {}/* \;
```

## Security Notes

- **Public root** (`/public_xmd/`) should be owned by deployment user, not web server
- **Sensitive files** (install-params.conf.php) should be readable by web server but not world-readable
- **Log files** may contain sensitive info - keep them 664 (owner/group writable, others read-only)
- **Setgid bit** ensures new files in shared directories are accessible by web server group

## References

- Web server user: `www-data`
- Deployment user: `juanpri`
- PHP-FPM pool: `www`
- Configuration: `/etc/php/7.2/fpm/pool.d/www.conf`
