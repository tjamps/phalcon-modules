#!/bin/bash

echo -e "+ Fixing tabs for all PHP files..."
for file in $(find . -name "*.php");
do
    echo -e "+ $file"
    expand -t 4 "$file" > "${file}.tmp" && mv "${file}.tmp" "$file"
done
echo -e "+ Done."
