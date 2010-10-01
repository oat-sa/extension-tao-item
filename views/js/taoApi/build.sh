#!/bin/bash

cat src/constant.js > taoApi.js
cat src/core.js >> taoApi.js
cat src/api.js >> taoApi.js
jsmin < taoApi.js > taoApi.min.js 
rm taoApi.js

exit 0
