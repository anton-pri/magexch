#!/bin/bash

mod=$1

if [ -z "$mod" ]; then
echo -e "Use: \n $0 <addon_name>"
exit 0
fi

if [ ! -d "$mod" ]; then
    echo "Error: addon folder '$mod' is not found"
    exit 0
fi

echo "addon folder '$mod' is found..."
cd ..
echo "create symbolic link to skins"
ln -s upgrade/$mod/skins_*
echo "create symbolic link to addon scripts in core/addons"
ln -s ../../upgrade/$mod/core/addons/$mod core/addons/$mod
echo "create symbolic link to addon templates in skins/addons"
ln -s ../../upgrade/$mod/skins/addons/$mod skins/addons/$mod
