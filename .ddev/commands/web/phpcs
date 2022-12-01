#!/bin/bash

## Description: Run phpcs
## Usage: phpcs [flags] [args]
## Example: "ddev phpcs web/modules/contrib"

/var/www/html/vendor/bin/phpcs --standard=Drupal,DrupalPractice $@ || \
echo "Return code ignored"
