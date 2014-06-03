#!/bin/sh

autoload () {
    vendor/autoload/recipe.sh --configuration=configuration/autoload.php --destination=dest/autoload.php --include_dir=public_html
}

eval $1