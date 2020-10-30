This is a Composer template for developing Drupal core.

It allows:

- a clean git clone of Drupal core.
- Composer dependencies of Drupal core are installed, so Drupal can be installed
  and run as normal.
- other Composer packages you might want, such as Drush, can be installed too,
  but don't affect the composer files that are part of Drupal core.

Instructions:

Clone this repo into, say, 'drupal-dev'.

```
# Create a project folder.
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
