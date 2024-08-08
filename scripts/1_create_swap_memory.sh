#!/bin/sh

# Create swap memory for the Server does not crash while installing libraries

if free | awk '/^Swap:/ {exit !$2}'; then
    echo "Have swap"
else
    sudo fallocate -l 1G /swapfile
    ls -lh /swapfile
    sudo chmod 600 /swapfile
    ls -lh /swapfile
    sudo mkswap /swapfile
    sudo swapon /swapfile
    sudo cp /etc/fstab /etc/fstab.bak
    echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
fi
