#!/bin/bash

BUILDURL="https://citi-gs.tudor.lu/svn/TAO/projects/taoItems/branches/authoring/nightly_build"
TODAYBUILD="build_"`date +%Y%m%d`

svn export --username bchevrier "$BUILDURL" "$TODAYBUILD"
rm -rf /home/crp/workspace/taoItems/models/ext/itemAuthoring/waterphenix/*
cp -r "${TODAYBUILD}/*" "/home/crp/workspace/taoItems/models/ext/itemAuthoring/waterphenix/"

rm -rf "${TODAYBUILD}"

exit 0
