#!/bin/sh
# Builds assets for each nova component.
# Executed by `npm run build-components`

for d in ./nova-components/*/ ;
    do (
        # Enter each component folder.
        cd "$d"

        # Message
        echo "Building $d..."

        # Compille component assets.
        if [ -f "package.json" ]; then
            npm run prod
        fi
    );
done
