#!/bin/bash

echo -e "\e[32mFixing tabs for all PHP files...\e[0m"
for file in $(find . -name "*.php");
do
    echo -e "\e[33m+ $file\e[0m"
    expand -t 4 "$file" > "${file}.tmp" && mv "${file}.tmp" "$file"
done
echo -e "\e[32mDone.\e[0m"
