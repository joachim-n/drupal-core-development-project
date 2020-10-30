This is an experiment to try to find a better way to develop Drupal core with Composer.

The ideal would be:

- a clean git clone of Drupal core
- Composer dependencies of Drupal core are installed, so it runs
- other Composer packages you might want, such as Drush, can be installed too
- the Drupal core git clone doesn't have its composer.lock file changed by all this, because that's a pain for making PRs/patches

Instructions:

Clone this repo into, say, 'drupal-dev'.

```
$ cd drupal-dev
$ mkdir repos
$ cd repos
$ git clone --branch 9.2.x https://git.drupalcode.org/project/drupal.git
$ cd ..
$ composer install
$ ln -s PATH TO ROOT FOLDER/vendor repos/drupal/vendor
```

Current status:

- composer install works! yay!
- the autoload in repos/drupal/autoload.php crashes the site. boo! :(
