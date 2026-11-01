#!/bin/sh
set -eu

root=$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)
analysis="$root/build/psalm-php56"

# Use the same manifest and PHP 5.6 lock for the dependencies being analyzed.
# Psalm itself runs from the root vendor directory on the development PHP version.
mkdir -p "$analysis"
cp "$root/composer.json" "$analysis/composer.json"
cp "$root/composer-5.6.lock" "$analysis/composer.lock"
cp "$root/psalm.xml" "$analysis/psalm.xml"
if [ ! -e "$analysis/src" ]; then
    ln -s ../../src "$analysis/src"
fi
if [ ! -e "$analysis/stubs" ]; then
    ln -s ../../stubs "$analysis/stubs"
fi
composer install --no-dev --no-interaction --working-dir="$analysis"
