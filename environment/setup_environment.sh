#!/bin/bash

for folder in */; do
    folder_name=$(basename "$folder")
    destination="../$folder_name"

    if [ ! -d "$destination" ]; then
        mkdir -p "../$destination"
    fi

    cp -R "$folder" "$destination"
    echo "copied '$folder/' into '$destination/'"
done

read -p "Press Enter to exit..."
