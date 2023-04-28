@echo off

echo deleting .git
rd /s /q ".git"


set folder=frontend
echo folder=%folder%

echo deleting .vercel
rd /s /q "%folder%\.vercel"

echo deleting .svelte-kit
rd /s /q "%folder%\.svelte-kit"

echo deleting node_modules
rd /s /q "%folder%\node_modules"


set folder=backend
echo folder=%folder%

echo deleting vendor
rd /s /q "%folder%\vendor"

pause