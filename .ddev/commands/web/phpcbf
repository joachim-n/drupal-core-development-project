#!/bin/bash

## Description: Run phpcbf
## Usage: phpcbf [flags] [args]
## Example: "ddev phpcbf web/modules/contrib"

/var/www/html/vendor/bin/phpcbf --standard=Drupal,DrupalPractice $@ || \
echo "Return code ignored"
