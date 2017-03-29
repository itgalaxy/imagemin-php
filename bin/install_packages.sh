#!/bin/sh

if [[ "${TRAVIS_OS_NAME}" == "osx" ]]; then
    brew update
    brew install nasm libpng
elif [ "${TRAVIS_OS_NAME}" == "linux" ]; then
    sudo apt-get -qq update
    sudo apt-get install -y autoconf automake nasm zlib1g-dev libpng12-dev libpng16-16
    wget http://ftp.us.debian.org/debian/pool/main/libp/libpng1.6/libpng16-16_1.6.28-1_amd64.deb
    sudo dpkg -i libpng16-16_1.6.28-1_amd64.deb
fi
