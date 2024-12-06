# Drupal Core Development Composer Project

This is a Composer project template for developing Drupal core.

It allows the following:

- A clean git clone of Drupal core.
- Composer dependencies of Drupal core are installed, so Drupal can be installed
  and run as normal.
- Other Composer packages you might want, such as Drush, Devel module, Admin
  Toolbar module, and Devel Accessibility can be installed too, but don't affect
  the composer files that are part of Drupal core.
- Contrib modules can be installed with Composer (normally Composer would refuse
  to install them because their info.yml file does not declare compatibility
  with core 11.x).
- Other packages, including contrib modules, can be installed as git clones to
  develop them in tandem with Drupal core.

## Roadmap

Get this into Drupal core! See
https://www.drupal.org/project/drupal/issues/1792310.

## Installation

### Basic installation

To install a Drupal project for working on Drupal core:

```bash
composer create-project joachim-n/drupal-core-development-project
```

Composer will clone Drupal core into a 'repos/drupal' directory within the
project, and then symlink that into the project when it installs Drupal core.

Drupal core is checked out to its default branch, which is currently 11.x. To
start on a different branch without first checking out 11.x, you can use the
`--no-install` option with the `composer create-project` command, then change
the branch of the Drupal core clone, then do `composer install`.

Once the Composer installation is complete, you can install Drupal as normal,
either with `drush si` or with the web UI.

### Installation on DDEV

First, make sure your DDEV version is at least 1.23.0. Next, create a new folder
for your project and `cd` into it. Then:

``` bash
ddev config --project-type=drupal --php-version=8.3
ddev start
ddev composer create joachim-n/drupal-core-development-project
ddev config --update
ddev restart
```

### Installation on DDEV with the justafish/ddev-drupal-core-dev DDEV addon

To use the justafish/ddev-drupal-core-dev DDEV addon, you need to make the
following changes to the installation instructions for that addon:

- For `ddev config`, specify --project-type=drupal
- Do `composer install` before doing `ddev get justafish/ddev-drupal-core-dev`
- Do `ln -s web/autoload.php .` so the addon's `ddev drupal` command find the
  autoloader. (There is a merge request to remove the need for this:
  https://github.com/justafish/ddev-drupal-core-dev/pull/35)
- DO NOT do `drupal install`. Instead, do `ddev drush si --db-url=sqlite://sites/default/files/.ht.sqlite?module=sqlite -y`
  (You might need to manually create web/sites/default/files first)
  The `drupal install` command does not work when the drupal package is
  symlinked in by Composer.

To run PHPUnit tests, you will need to tweak the DDEV phpunit command until
https://github.com/justafish/ddev-drupal-core-dev/pull/37 is fixed.

## Installing other packages

You can install any Composer package as you would with a normal project. This
will not affect Drupal core.

To work with Composer, you need to be in the root directory of the project, not
in the Drupal core folders.

If Drupal core is checked out at a feature branch, Composer may complain that
dependencies are not met, because it does not see the feature branch as
satisfying the dependency. You can either:

- Temporarily switch Drupal code back to the main branch, do Composer tasks,
  then switch it back.
- Define the version in the `repositories` section of the project composer.json.
- Define a branch alias in the project composer.json.

### Installing other packages from path repositories

You can install additional packages from a path repository, in the same way that
Drupal core is installed (although other packages will no require all the tweaks
that Drupal core does!). This can be useful to develop packages and modules in
tandem with core.

1. Create a git clone of the module or package. The `repos/` folder can be used
   for this. It's simplest to start off from a main branch so that Composer sees
   this as the installed version, and dependencies work properly.
2. Define a path respository for the package. See
   https://getcomposer.org/doc/05-repositories.md#path for details.
3. Do `composer require` for the package.

You can now switch the package to a feature branch, such as one from a merge
request, in order to work on a feature or bug.

If you need to perform Composer operations, Composer may complain that the
feature branch does not satisfy requirements. You can do one of:

* Check out the main branch with git, perform the Composer operations, then
  return to the feature branch.
* Define the version that Composer sees for this package by specifying the
  "versions" option in the declaration of the path repository in
  `composer.json`.

## Limitations

### Contrib and custom tests

Contrib and custom module tests can't be run. For details, see
https://github.com/joachim-n/drupal-core-development-project/issues/14.

### 'Could not scan for classes' error messages

During some Composer commands you may see multiple copies of this error message:

> Could not scan for classes inside [Drupal class filename].

These are harmless and can be ignored.

## Developing Drupal core

You can use the Drupal core git clone at `repos/drupal/` in any way you like:
create feature branches, clone from drupal.org issue forks, and so on. Changes
you make to files in the git clone affect the project, since the git clone is
symlinked into it.

### Managing the Composer project

Changes to the Drupal core git clone's composer.json will be taken into account
by Composer. So for example, if pulling from the main branch of Drupal core
changes Composer dependencies, and in particular if you change to a different
core major or minor branch, you should run `composer update` on the project to
install these.

### Running tests

The following are required to run tests.

#### PHPUnit configuration

The simplest way to run tests with this setup is to put the phpunit.xml file in
the project root and then run tests from there:

``` bash
vendor/bin/phpunit web/core/PATH-TO-TEST-FILE/TestFile.php
```

##### On DDEV

1. Copy the `phpunit-ddev.xml` file that this template provides and rename it to
   `phpunit.xml`:

``` bash
cp phpunit-ddev.xml phpunit.xml
```

2. Change the BROWSERTEST_OUTPUT_BASE_URL value to the host URL of the project.

##### On other platforms

1. Copy Drupal core's sample `phpunit.xml.dist`` file to the project root and
rename it to `phpunit.xml`:

``` bash
cp web/core/phpunit.xml.dist phpunit.xml
```

2. Change the `bootstrap` attribute so the path is correct:

```
bootstrap="web/core/tests/bootstrap.php"
```

### Debugging

You can set up debugging in an IDE that's open at the `repos/drupal` folder, so
that it recognises the process being run from the project root.

For example, in VSCode, this is done as follows in the debugger configuration:

``` json
"pathMappings": {
  // Make this work with the root project.
  "/ABSOLUTE/PATH/TO/PROJECT/repos/drupal": "${workspaceRoot}"
}
```

## Technical details

The rest of this document is gory technical details you only need to know if
you're working on this project template or debugging it.

### How it works

The composer.json at the project root uses a Composer path repository so that
when the drupal/drupal package is installed, it's symlinked in from the Drupal
core git clone, at the branch that the clone has checked out.

Drupal core itself defines path repositories in its top-level composer.json.
These need to be overridden in the project root composer.json so they point to
inside the Drupal core git clone.

Additionally, the paths to the drupal/core-recommended and drupal/core-dev
packages are defined as path repositories, so that the package versions which
are fixed in those metapackages are respected in the project. This means the
same versions are installed as if installing Composer packages on a plain git
clone of Drupal core.

Contrib modules are made installable with the ComposerCoreVersionsLeniency
Composer script.

### Manual Installation

Clone the repository for this template into, say, 'drupal-dev'.

``` bash
cd drupal-dev

# Create a folder in which to store git clones, which Composer will symlink in.
mkdir repos
cd repos

# Clone Drupal core, to whatever branch you like.
git clone --branch 11.x https://git.drupalcode.org/project/drupal.git

# Go back to the project root.
cd ..

# Install packages with Composer.
composer install
```

The Drupal core git clone will be clean apart from:

``` bash
 sites/default/settings.php
 vendor
```

Since it doesn't have a .gitignore at the top level, you can add one to ignore
those files if you like.

### Project template development installation

To test how Composer creates a new project from the template, you need a git
clone of the template repository.

In a separate location, do:

``` bash
composer create-project joachim-n/drupal-core-development-project NEW_PROJECT_DIRECTORY --stability=dev --repository='{"url":"/path/to/git/clone/of/project/template/","type":"vcs"}'
```

### Workarounds

Several workarounds are necessary to make Drupal core work correctly when
symlinked into the project. These are all taken care of by Composer scripts
during installation. Details are below.

Most if not all of these will no longer be needed once
https://www.drupal.org/project/drupal/issues/1792310 is fixed.

#### Vendor folder

The vendor folder has to be symlinked into the Drupal core repository, because
otherwise code in core that expects to find a Composer autoloader fails.

This is done by a Composer script after initial installation. The manual command
is:

``` bash
ln -s ../../vendor ./repos/drupal/vendor
```

#### App root files patches

The index.php and update.php scaffold files have to be patched after they have
been copied to web/index.php, because otherwise DrupalKernel guesses the Drupal
app root as incorrectly being inside the Drupal core git clone, which means it
can't find the settings.php file.

This is done by a Composer script after initial installation. The manual
commands are:

``` bash
cd web && patch -p1 <../scaffold/scaffold-patch-index-php.patch
cd web && patch -p1 <../scaffold/scaffold-patch-update-php.patch
```

See https://www.drupal.org/project/drupal/issues/3188703 for more detail.

#### Drush rebuild command

The Drush cache:rebuild command does not work correctly if contrib modules are
present, because it calls drupal_rebuild() which lets DrupalKernel guess the
app root incorrectly.

This project template contains a /drush folder which has a command class which
replaces that command with custom code to correctly handle the app root.

#### Simpletest folder

When running browser tests, the initial setup of Drupal in
FunctionalTestSetupTrait::prepareEnvironment() creates a site folder using the
real file locations with symlinks resolved, thus
`repos/drupal/sites/simpletest`, but during the request to the test site, Drupal
looks in `/web/sites/simpletest`.

Additionally, the HTML files output from Browser tests are written into the
Drupal core git clone, and so the URLs shown in PHPUnit output are incorrect.

The fix for both of these is to create the simpletest site folder in the web
root and symlink it into the Drupal core git clone.

This is done by a Composer script after initial installation. The manual command
is:

``` bash
mkdir -p web/sites/simpletest
ln -s ../../../web/sites/simpletest repos/drupal/sites
```

#### Autoload of Drupal composer testing classes

Drupal's /composer folder is not symlinked and therefore isn't visible to
Composer. It's needed for some tests, and so is declared as an autoload
location.

## Core Development Using DDEV

1. Clone this repository by using `git clone --branch=master https://github.com/bhanu951/drupal-core-development-project.git`
2. cd repos
3. git clone --branch=11.x https://git.drupalcode.org/project/drupal.git drupal
4. ddev get drud/ddev-selenium-standalone-chrome (downloads latest chrome driver)
5. ddev composer install (from project root)
6. ddev composer require drupal/admin_toolbar drupal/devel
7. cd repos/drupal ; `git status` to track core changes

### DDEV Commands Usage

1. ddev phpcs core/modules/user/src/RegisterForm.php (from repos/drupal directory)
2. ddev phpcbf core/modules/user/src/RegisterForm.php (from repos/drupal directory)
3. ddev phpunit core/modules/user/tests/src/Functional/UserAdminTest.php (from repos/drupal directory)
4. ddev code-check (ddev equivalent of running `sh core/scripts/dev/commit-code-check.sh`)
5. ddev cspell-check (Checks for forbidden and new words which are not present in dictonary)
6. ddev install (Installs new site)
7. ddev drush [arguments] (from project root)

### TODO

1. Add DDEV Rector Commands
2. Find workaround for ddev phpstan command failures

### Tips

1. ddev drush si --site-name=drupal-145353

### Known Issues

There is a known issue where PHPSTAN doesnt work when the
`ComponentTestDoesNotExtendCoreTest` is enabled in `core/phpstan.neon.dist`
hence it advised to comment the rule for local analysis.

```yaml
rules:
# - Drupal\PHPStan\Rules\ComponentTestDoesNotExtendCoreTest
- PHPStan\Rules\Functions\MissingFunctionReturnTypehintRule
- PHPStan\Rules\Methods\MissingMethodReturnTypehintRule
```
