This is a Composer template for developing Drupal core.

It allows:

- a clean git clone of Drupal core.
- Composer dependencies of Drupal core are installed, so Drupal can be installed
  and run as normal.
- other Composer packages you might want, such as Drush, can be installed too,
  but don't affect the composer files that are part of Drupal core.

## Instructions

### Installation

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

# Install packages with Composer.
$ composer install

# Symlink the Composer vendor folder into the Drupal core clone. This is so that
# code in Drupal core that expects it there works correctly.
$ ln -s PATH-TO-PROJECT-FOLDER/vendor repos/drupal/vendor
```

The Drupal core git clone will be clean apart from:

```
	sites/default/settings.php
	vendor
```

Since it doesn't have a .gitignore at the top level, you can add one to ignore
those files if you like.

### Running tests

The simplest way to run tests with this setup is to put the phpunit.xml file in the project root and then run tests from there:

$ vendor/bin/phpunit web/core/PATH-TO-TEST-FILE/TestFile.php

To set this up, copy Drupal core's sample phpunit.xml file to the project root:

$ cp web/core/phpunit.xml.dist phpunit.xml

Then change the `bootstrap` attribute so the path is correct:

```
<phpunit bootstrap="web/core/tests/bootstrap.php"
```

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

## Workarounds

### Vendor folder

The vendor folder has to be symlinked into the Drupal core repository, because otherwise code in core that expects to find a Composer autoloader fails.

### App root index.php patch

The index.php scaffold file has to be patched after it has been copied to web/index.php, because otherwise DrupalKernel guesses the Drupal app root as incorrectly being inside the Drupal core git clone, which means it can't find the settings.php file.

See https://www.drupal.org/project/drupal/issues/3188703.

## How it works

The composer.json at the project root uses a Composer path repository so that when the drupal/drupal package is installed, it's symlinked in from the Drupal core git clone, at the branch that the clone has checked out.

Drupal core itself defines path repositories in its top-level composer.json. These need to be overridden in the project root composer.json so they point to inside the Drupal core git clone.
