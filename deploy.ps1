# Deployment script for vedantlights
# This script handles git pull with cleanup of untracked files that might conflict

param(
    [string]$RemoteUrl = "https://github.com/Vedantlights/vedantlights.git",
    [switch]$ForceClean = $false
)

Write-Host "Starting deployment..." -ForegroundColor Green

# Change to script directory
$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $ScriptDir

# Check if this is a git repository
if (-not (Test-Path ".git")) {
    Write-Host "ERROR: Not a git repository" -ForegroundColor Red
    exit 1
}

# Get current branch
$currentBranch = git rev-parse --abbrev-ref HEAD
Write-Host "Current branch: $currentBranch" -ForegroundColor Cyan

# Check for uncommitted changes
$status = git status --porcelain
if ($status -and -not $ForceClean) {
    Write-Host "WARNING: You have uncommitted changes:" -ForegroundColor Yellow
    Write-Host $status
    $response = Read-Host "Continue anyway? (y/N)"
    if ($response -ne "y" -and $response -ne "Y") {
        Write-Host "Deployment cancelled" -ForegroundColor Yellow
        exit 0
    }
}

# Clean up ignored files that might conflict with merge
# git clean respects .gitignore, but merge still complains about untracked files
Write-Host "Cleaning ignored files that might conflict with merge..." -ForegroundColor Cyan
try {
    # Remove ignored files in writable directories (logs, cache, debugbar, etc.)
    $ignoredPatterns = @(
        "writable\logs\*.log",
        "writable\debugbar\*.json",
        "writable\cache\*",
        "writable\session\*"
    )
    
    $filesToRemove = @()
    foreach ($pattern in $ignoredPatterns) {
        $files = Get-ChildItem -Path $pattern -ErrorAction SilentlyContinue
        if ($files) {
            $filesToRemove += $files
        }
    }
    
    if ($filesToRemove.Count -gt 0) {
        Write-Host "Found $($filesToRemove.Count) ignored file(s) that might conflict:" -ForegroundColor Yellow
        foreach ($file in $filesToRemove) {
            Write-Host "  - $($file.FullName)" -ForegroundColor Yellow
        }
        if ($ForceClean) {
            foreach ($file in $filesToRemove) {
                Remove-Item -Path $file.FullName -Force -ErrorAction SilentlyContinue
            }
            Write-Host "Ignored files removed" -ForegroundColor Green
        } else {
            $response = Read-Host "Remove these ignored files? (y/N)"
            if ($response -eq "y" -or $response -eq "Y") {
                foreach ($file in $filesToRemove) {
                    Remove-Item -Path $file.FullName -Force -ErrorAction SilentlyContinue
                }
                Write-Host "Ignored files removed" -ForegroundColor Green
            }
        }
    } else {
        Write-Host "No ignored files to clean" -ForegroundColor Green
    }
} catch {
    Write-Host "Error cleaning ignored files: $_" -ForegroundColor Red
}

# Fetch latest changes
Write-Host "Fetching latest changes from remote..." -ForegroundColor Cyan
try {
    git fetch origin
    if ($LASTEXITCODE -ne 0) {
        throw "Git fetch failed"
    }
} catch {
    Write-Host "ERROR: Failed to fetch from remote: $_" -ForegroundColor Red
    exit 1
}

# Clean up untracked files that match .gitignore patterns
Write-Host "Cleaning untracked files that match .gitignore..." -ForegroundColor Cyan
try {
    # Remove untracked files and directories (dry-run first to show what will be removed)
    $untracked = git clean -fdn
    if ($untracked) {
        Write-Host "Files to be removed:" -ForegroundColor Yellow
        Write-Host $untracked
        if ($ForceClean) {
            git clean -fd
            Write-Host "Untracked files removed" -ForegroundColor Green
        } else {
            $response = Read-Host "Remove these untracked files? (y/N)"
            if ($response -eq "y" -or $response -eq "Y") {
                git clean -fd
                Write-Host "Untracked files removed" -ForegroundColor Green
            }
        }
    } else {
        Write-Host "No untracked files to clean" -ForegroundColor Green
    }
} catch {
    Write-Host "Error cleaning untracked files: $_" -ForegroundColor Red
}

# Pull latest changes
Write-Host "Pulling latest changes..." -ForegroundColor Cyan
try {
    $pullOutput = git pull origin $currentBranch 2>&1 | Out-String
    if ($LASTEXITCODE -ne 0) {
        # Check if error is about untracked files that would be overwritten
        if ($pullOutput -match "untracked working tree files would be overwritten") {
            Write-Host "ERROR: Untracked files would be overwritten by merge" -ForegroundColor Red
            Write-Host "Attempting to remove conflicting files..." -ForegroundColor Yellow
            
            # Extract file paths from error message
            $conflictFiles = $pullOutput | Select-String -Pattern "^\s+(.+)$" | ForEach-Object { 
                $line = $_.Line.Trim()
                if ($line -and -not $line.StartsWith("error:") -and -not $line.StartsWith("Please")) {
                    $line
                }
            }
            
            foreach ($file in $conflictFiles) {
                if ($file -and (Test-Path $file)) {
                    Write-Host "  Removing: $file" -ForegroundColor Yellow
                    Remove-Item -Path $file -Force -ErrorAction SilentlyContinue
                }
            }
            
            # Retry pull after removing conflicting files
            Write-Host "Retrying pull..." -ForegroundColor Cyan
            git pull origin $currentBranch
            if ($LASTEXITCODE -ne 0) {
                throw "Git pull failed after cleanup"
            }
            Write-Host "Successfully pulled latest changes after cleanup" -ForegroundColor Green
        } else {
            throw "Git pull failed: $pullOutput"
        }
    } else {
        Write-Host "Successfully pulled latest changes" -ForegroundColor Green
    }
} catch {
    Write-Host "ERROR: Failed to pull: $_" -ForegroundColor Red
    Write-Host "You may need to resolve conflicts manually" -ForegroundColor Yellow
    exit 1
}

Write-Host "Deployment completed successfully!" -ForegroundColor Green
