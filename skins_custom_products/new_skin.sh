#!/bin/bash

skin=$1

mkdir $skin
ln -s ../../upgrade/custom_magazineexchange/skins_magazineexchange/customer_altskin.css $skin/customer_altskin.css
ln -s ../../upgrade/custom_magazineexchange/skins_magazineexchange/images $skin/images
