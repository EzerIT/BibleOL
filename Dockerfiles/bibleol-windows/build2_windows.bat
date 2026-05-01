@echo off
setlocal enabledelayedexpansion

:: Get the absolute path of the grandparent directory
:: %~dp0 is the directory of the script (Dockerfiles/bibleol-windows/)
pushd "%~dp0..\.."
set "PROJECT_ROOT=%CD%"
popd

:: Check if the drive is a network/mapped drive
set "DRIVE_LETTER=%PROJECT_ROOT:~0,2%"
for /f "tokens=2 delims= " %%a in ('net use %DRIVE_LETTER% 2^>nul ^| findstr /C:"Remote name"') do set "REMOTE_PATH=%%a"

if defined REMOTE_PATH (
    echo [WARNING] You are running from a mapped network drive (%DRIVE_LETTER% - %REMOTE_PATH%^).
    echo Docker for Windows often fails to mount mapped drives with the error:
    echo "The volume does not contain a recognized file system"
    echo.
    echo It is HIGHLY recommended to move the BibleOL folder to a local drive (e.g., C:\BibleOL^).
    echo.
    set /p "CONTINUE=Do you want to try anyway? (y/n): "
    if /i "!CONTINUE!" neq "y" exit /b 1
)

echo Building image...
docker image build -t tmccormack14/bibleol-windows:2026_04_27 .

echo Starting container with volume mapping...
echo Project root: %PROJECT_ROOT%

:: Try to use forward slashes for better compatibility in some Docker versions
set "FORWARD_ROOT=%PROJECT_ROOT:\=/%"

docker container run -dt -p 8000:80 --env-file environment.txt --volume "%FORWARD_ROOT%:/var/www/html/BibleOL" --name bibleol-windows-container tmccormack14/bibleol-windows:2026_04_27

if %errorLevel% neq 0 (
    echo.
    echo [ERROR] Docker failed to start the container.
    if defined REMOTE_PATH (
        echo This is likely because you are on a network drive. 
        echo Please move the project to a local drive and try again.
    )
    exit /b %errorLevel%
)

docker container exec bibleol-windows-container bash start.sh
