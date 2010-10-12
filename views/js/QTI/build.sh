#!/bin/bash

cat src/Widget.js > qti.js
cat src/ResultCollector.js >> qti.js
cat src/init.js >> qti.js
jsmin <  qti.js > qti.min.js 
rm qti.js

exit 0
