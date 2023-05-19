@echo off
setlocal enabledelayedexpansion

for /D %%I in (*) do (
    set "folder=%%I"
    set "destination=..\!folder!"

    if not exist "!destination!" (
        mkdir "!destination!"
    )

    xcopy /E /I "!folder!\*" "!destination!\"
    echo "copied '!folder!' into '!destination!'"
)

endlocal

pause