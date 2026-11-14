#!/bin/sh
# Builds WordPress-installable zips into ./dist
set -e
mkdir -p dist
rm -f dist/*.zip
(cd wp-content/themes && zip -qr ../../dist/pickora.zip pickora)
(cd wp-content/plugins && zip -qr ../../dist/pickora-core.zip pickora-core)
echo "Built dist/pickora.zip and dist/pickora-core.zip"
