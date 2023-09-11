<?php

namespace DrupalGitCloneProject;

use Composer\Package\Link;
use Composer\Package\CompletePackage;
use Composer\Plugin\PrePoolCreateEvent;
use Composer\Semver\VersionParser;

// Development: this makes symfony var-dumper work.
// See https://github.com/composer/composer/issues/7911
// include_once './vendor/symfony/var-dumper/Resources/functions/dump.php';

/**
 * Provides Composer scripts to allow installation on core 11.x.
 *
 * Inspired by mglaman/composer-drupal-lenient.
 */
class ComposerCoreVersionsLeniency {

  /**
   * Act on pre-pool-create event.
   *
   * This tweaks the core requirement of contrib modules, so that Composer will
   * install them when the Drupal core git clone is checked out to the 11.x
   * branch.
   *
   * @param \Composer\Plugin\PrePoolCreateEvent $event
   *   The event.
   */
  public static function prePoolCreate(PrePoolCreateEvent $event) {
    // @todo Only act if the current branch of drupal/core is 11.x

    $version_parser = new VersionParser();

    $packages = $event->getPackages();
    foreach ($packages as $package) {
      $type = $package->getType();
      if ($type === 'drupal-core') {
        continue;
      }

      if ($type != 'drupal-module') {
        continue;
      }

      $requires = $package->getRequires();

      if (!isset($requires['drupal/core'])) {
        continue;
      }

      $drupal_core_require = $requires['drupal/core'];

      $pretty_constraint = $drupal_core_require->getPrettyConstraint();
      if (str_contains($pretty_constraint, '^11')) {
        continue;
      }

      // Make a new constraint with the 11 requirement appended.
      $pretty_constraint_with_11 = $pretty_constraint . ' || ^11';
      $constraint_with_11 = $version_parser->parseConstraints($pretty_constraint_with_11);

      // Replace the existing core requirement with the new one.
      $requires['drupal/core'] = new Link(
        $drupal_core_require->getSource(),
        $drupal_core_require->getTarget(),
        $constraint_with_11,
        $drupal_core_require->getDescription(),
        $constraint_with_11->getPrettyString(),
      );

      // No idea why we need to check CompletePackage, just aping
      // mglaman/composer-drupal-lenient.
      if ($package instanceof CompletePackage) {
        // @note `setRequires` is on Package but not PackageInterface.
        $package->setRequires($requires);
      }
    }
  }

}
