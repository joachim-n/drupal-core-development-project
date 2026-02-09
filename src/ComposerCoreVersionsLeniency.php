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
 * Provides Composer scripts to allow installation on core main branch.
 *
 * Inspired by mglaman/composer-drupal-lenient.
 */
class ComposerCoreVersionsLeniency {

  /**
   * Act on pre-pool-create event.
   *
   * This tweaks the core requirement of contrib modules, so that Composer will
   * install them when the Drupal core git clone is checked out to the 'main'
   * branch.
   *
   * @param \Composer\Plugin\PrePoolCreateEvent $event
   *   The event.
   */
  public static function prePoolCreate(PrePoolCreateEvent $event) {
    $packages = $event->getPackages();

    // Only act if the current branch of drupal/core is 'main'.
    /** @var \Composer\Package\BasePackage $package */
    foreach ($packages as $package) {
      if ($package->getName() == 'drupal/core') {
        // Apparently sometimes we get one version, sometimes the other. WTF?
        if (!in_array($package->getPrettyVersion(), ['dev-main', '12.x-dev'])) {
          return;
        }
      }
    }

    $version_parser = new VersionParser();

    /** @var \Composer\Package\BasePackage $package */
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

      // Make a new constraint with the dev-main requirement appended.
      $pretty_constraint_with_main = $pretty_constraint . ' || dev-main';
      $constraint_with_main = $version_parser->parseConstraints($pretty_constraint_with_main);

      // Replace the existing core requirement with the new one.
      $requires['drupal/core'] = new Link(
        $drupal_core_require->getSource(),
        $drupal_core_require->getTarget(),
        $constraint_with_main,
        $drupal_core_require->getDescription(),
        $constraint_with_main->getPrettyString(),
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
