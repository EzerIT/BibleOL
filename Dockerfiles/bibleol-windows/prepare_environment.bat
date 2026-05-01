@echo off
setlocal enabledelayedexpansion

echo ======================================================
echo Bible Online Learner - Environment Preparation Script
echo ======================================================

:: Check for Administrative Privileges
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo [ERROR] This script requires Administrative privileges.
    echo Please right-click and "Run as Administrator".
    pause
    exit /b 1
)

:: 1. Check/Install Chocolatey
echo [1/5] Checking for Chocolatey...
where choco >nul 2>&1
if %errorLevel% neq 0 (
    echo Chocolatey not found. Installing...
    powershell -NoProfile -ExecutionPolicy Bypass -Command "[System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))"
    if %errorLevel% neq 0 (
        echo [ERROR] Failed to install Chocolatey.
        pause
        exit /b 1
    )
    :: Refresh environment variables for the current session
    set "PATH=%PATH%;%ALLUSERSPROFILE%\chocolatey\bin"
    echo Chocolatey installed successfully.
) else (
    echo Chocolatey is already installed.
)

:: 2. Check/Install Git
echo [2/5] Checking for Git...
where git >nul 2>&1
if %errorLevel% neq 0 (
    echo Git not found. Installing via Chocolatey...
    choco install git -y
) else (
    echo Git is already installed.
)

:: 3. Check/Install Make
echo [3/5] Checking for Make...
where make >nul 2>&1
if %errorLevel% neq 0 (
    echo Make not found. Installing via Chocolatey...
    choco install make -y
) else (
    echo Make is already installed.
)

:: 4. Check/Install WSL
echo [4/5] Checking for WSL...
wsl --status >nul 2>&1
if %errorLevel% neq 0 (
    echo WSL not found or not responding. Attempting to install WSL...
    echo Note: This may require a system restart.
    wsl --install
    echo Please restart your computer if prompted and run this script again.
) else (
    echo WSL is already installed.
)

:: 5. Check/Install Docker Desktop
echo [5/5] Checking for Docker...
where docker >nul 2>&1
if %errorLevel% neq 0 (
    echo Docker not found. Installing Docker Desktop via Chocolatey...
    choco install docker-desktop -y
    echo [NOTICE] You will need to log out or restart for Docker group changes to take effect.
) else (
    echo Docker is already installed.
)

echo ======================================================
echo Environment Preparation Complete!
echo ======================================================
echo [NOTE] If you installed WSL or Docker for the first time, 
echo        please RESTART your computer before proceeding.
echo ======================================================
pause
