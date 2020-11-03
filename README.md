This is a Composer template for developing Drupal core.

It allows:

- a clean git clone of Drupal core.
- Composer dependencies of Drupal core are installed, so Drupal can be installed
  and run as normal.
- other Composer packages you might want, such as Drush, can be installed too,
  but don't affect the composer files that are part of Drupal core.

## Instructions:

Clone this repo into, say, 'drupal-dev'.

```
$ cd drupal-dev
# Create a folder in which to store git clones, which Composer will symlink in.
$ mkdir repos
$ cd repos
# Clone Drupal core, to whatever branch you like.
$ git clone --branch 9.2.x https://git.drupalcode.org/project/drupal.git
# Go back to the project root.
$ cd ..
$ composer install
$ ln -s PATH-TO-PROJECT-FOLDER/vendor repos/drupal/vendor
```

The Drupal core git clone will be clean apart from:

```
	sites/default/settings.php
	vendor
```

Since it doesn't have a .gitignore at the top level, you can add one to ignore
those files if you like.

## Known problems

### Modules

Drupal core can't see third-party modules because it detects the app root as
being repos/drupal rather than web/. A workaround for this is to change the
line in web/index.html that creates the kernel:

```
$kernel = new DrupalKernel('prod', $autoloader, TRUE, __DIR__);
```

With this setup, you can use the site folder at web/sites/default (rather than
repos/drupal/sites/default).

Drush doesn't detect modules either because of this bug: https://github.com/drush-ops/drush/issues/4584

## How it works

The composer.json at the project root uses a Composer path repository so that when the drupal/drupal package is installed, it's symlinked in from the Drupal core git clone, at the branch that the clone has checked out.

Drupal core itself defines path repositories in its top-level composer.json. These need to be overridden in the project root composer.json so they point to inside the Drupal core git clone.
