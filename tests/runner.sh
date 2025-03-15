#!/bin/bash

cd $( dirname ${BASH_SOURCE[0]:-$0} )

BANNER=true ../node_modules/.bin/vite build --emptyOutDir
../node_modules/.bin/vite build --emptyOutDir
../node_modules/.bin/vite --config vite-no-input.config.ts &> /dev/null
../node_modules/.bin/vite build --config vite-unnamed-entries.ts
# ../node_modules/.bin/vite dev &
../vendor/bin/phpunit .
