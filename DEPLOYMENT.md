# Deployment Guide

## Issue: Untracked Files Blocking Deployment

If you encounter an error like:
```
pull: error: The following untracked working tree files would be overwritten by merge:
	writable/logs/log-2026-01-28.log
Please move or remove them before you merge.
```

## Solution

### Option 1: Use the Deployment Script (Recommended)

Run the PowerShell deployment script:
```powershell
.\deploy.ps1
```

Or to automatically clean untracked files:
```powershell
.\deploy.ps1 -ForceClean
```

### Option 2: Manual Cleanup

1. **Remove the blocking file(s):**
   ```powershell
   Remove-Item "writable/logs/log-2026-01-28.log" -Force
   ```

2. **Or clean all untracked files:**
   ```powershell
   git clean -fd
   ```

3. **Then pull:**
   ```powershell
   git pull origin main
   ```

### Option 3: Stash and Pull

```powershell
git stash
git pull origin main
git stash pop
```

## Prevention

The `.gitignore` file has been updated to ignore:
- `writable/logs/*` - All log files
- `writable/debugbar/*` - Debug bar files
- `writable/cache/*` - Cache files
- `writable/session/*` - Session files
- `writable/uploads/*` - Upload files

**Important:** Commit the updated `.gitignore` file:
```powershell
git add .gitignore
git commit -m "Update .gitignore to exclude writable directory files"
git push origin main
```

## Deployment Best Practices

1. Always commit `.gitignore` changes before deployment
2. Use the deployment script for automated cleanup
3. Ensure no processes are locking files (close editors, stop services)
4. Check git status before pulling: `git status`
