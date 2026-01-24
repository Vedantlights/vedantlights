# Remove react-app\.git so the parent repo can track react-app (fix "empty on GitHub")
#
# IMPORTANT: Close Cursor/VS Code and any Git GUIs first. Git locks .git files.
# Then run: powershell -ExecutionPolicy Bypass -File "d:\public_html\remove-react-app-git.ps1"

$target = Join-Path $PSScriptRoot "react-app\.git"
if (-not (Test-Path $target)) {
    Write-Host "react-app\.git already removed. Done."
    exit 0
}
Write-Host "Removing react-app\.git ..."
Remove-Item -Recurse -Force $target
if (Test-Path $target) {
    Write-Host "ERROR: Could not remove .git (files may be locked). Close Cursor/Git, then run this script again."
    exit 1
}
Write-Host "Done. Now run from your repo root: git add react-app && git commit -m \"Add react-app\" && git push"
