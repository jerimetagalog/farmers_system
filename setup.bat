@echo off
setlocal enabledelayedexpansion

echo =====================================================================
echo  IT 107 IAS 1: Secure Farmers Registration, Assistance & Monitoring
echo  Assigned Student ID: 2020-1050
echo  One-Click Automated Setup and Database Import Script
echo =====================================================================
echo.

:: 1. Detect MySQL executable
set "MYSQL_PATH="
if exist "C:\xampp\mysql\bin\mysql.exe" (
    set "MYSQL_PATH=C:\xampp\mysql\bin\mysql.exe"
) else (
    where mysql >nul 2>&1
    if !errorlevel! equ 0 (
        set "MYSQL_PATH=mysql"
    )
)

if "%MYSQL_PATH%"=="" (
    echo [ERROR] MySQL was not found at C:\xampp\mysql\bin\mysql.exe or in system PATH.
    echo Please make sure XAMPP is installed and MySQL is running.
    echo.
    pause
    exit /b 1
)

:: 2. Ensure config.php exists
echo [1/3] Checking configuration file...
if not exist "config.php" (
    if exist "config.sample.php" (
        copy "config.sample.php" "config.php" >nul
        echo [INFO] Created 'config.php' from 'config.sample.php'.
    )
) else (
    echo [OK] config.php is ready.
)

:: 3. Create logs directory if missing
echo [2/3] Checking runtime logs directory...
if not exist "logs" (
    mkdir "logs"
    echo [INFO] Created 'logs/' directory.
) else (
    echo [OK] logs directory is ready.
)

:: 4. Import database
echo [3/3] Importing database into MySQL...
set "SQL_FILE=database\full_database_dump.sql"
if not exist "!SQL_FILE!" (
    set "SQL_FILE=database\schema.sql"
)

"%MYSQL_PATH%" -u root -e "CREATE DATABASE IF NOT EXISTS farmers_system_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
if !errorlevel! neq 0 (
    echo [WARNING] Direct login with empty password failed. If root has a password, enter when prompted:
    "%MYSQL_PATH%" -u root -p -e "CREATE DATABASE IF NOT EXISTS farmers_system_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    "%MYSQL_PATH%" -u root -p farmers_system_db < "!SQL_FILE!"
) else (
    "%MYSQL_PATH%" -u root farmers_system_db < "!SQL_FILE!"
)

if !errorlevel! equ 0 (
    echo.
    echo =====================================================================
    echo [SUCCESS] Farmers System Setup completed successfully!
    echo.
    echo Available Pre-Seeded Accounts:
    echo   1. Administrator:  admin_officer  /  Admin@2026!  (ID: 2020-1050)
    echo   2. Farmer Juan:    farmer_juan    /  Farmer@2026! (ID: 2020-1051)
    echo   3. Farmer Maria:   farmer_maria   /  Farmer@2026! (ID: 2020-1052)
    echo.
    echo Open in your browser:
    echo   http://localhost/farmers_system
    echo =====================================================================
) else (
    echo.
    echo [ERROR] Database import encountered an issue. Please verify MySQL service is running in XAMPP.
)

echo.
pause
