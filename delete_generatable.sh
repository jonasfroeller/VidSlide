#!/bin/bash

echo deleting .git
rm -rf .git

folder="frontend"
echo folder=$folder

echo deleting .vercel
rm -rf "$folder/.vercel"

echo deleting .svelte-kit
rm -rf "$folder/.svelte-kit"

echo deleting node_modules
rm -rf "$folder/node_modules"

folder="backend"
echo folder=$folder

echo deleting vendor
rm -rf "$folder/vendor"

read -p "Press Enter to exit..."
