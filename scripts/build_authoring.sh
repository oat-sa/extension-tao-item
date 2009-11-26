#!/bin/bash

BUILDURL="https://citi-gs.tudor.lu/svn/TAO/projects/taoItems/branches/authoring/nightly_build"
TODAYBUILD="build_"`date +%Y%m%d`

svn export "$BUILDURL" "$TODAYBUILD"
mv "$TODAYBUILD" "/home/crp/workspace/taoItems/models/ext/itemsAuthoring/waterphenix"

exit 0
