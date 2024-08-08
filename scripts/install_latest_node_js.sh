#!/bin/sh

# Install Latest Node

# Some Laravel apps need Node & NPM for the frontend assets.
# This script installs the latest Node 12.x alongside
# with the paired NPM release.

sudo apt remove -y nodejs npm

sudo rm -fr /var/cache/apt/*

sudo apt clean all

curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -

sudo apt-get install nsolid -y
