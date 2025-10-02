@echo off
php -d auto_prepend_file="%APPDATA%\Composer\vendor\laradumps\global-laradumps\src\scripts\global-laradumps-loader.php" %*
