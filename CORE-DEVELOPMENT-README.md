# CORE DEVELOPMENT GUIDE

1. Clone this repository by using git clone https://github.com/bhanu951/drupal-core-development-project.git
2. cd repos
3. git clone --branch=10.1.x https://git.drupalcode.org/project/drupal.git drupal
4. ddev composer install
5. ddev composer require drupal/admin_toolbar drupal/devel
6. ddev phpcs repo/drupal/core/<PATH_TO_FILE> (from project root)
7. cd repos/drupal ; git status to track core changes

## Commands Usage

ddev phpunit repos/drupal/core/modules/action/tests/src/Functional
ddev phpcs repos/drupal/core/modules/action/

## TODO
1. Add PHPStan,Rector commands
