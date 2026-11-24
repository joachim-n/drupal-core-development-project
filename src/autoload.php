<?php

/**
 * Autoloaded file which switches the Drupal runtime.
 */

use DrupalGitCloneProject\DrupalDevRuntime;

// Switch the Drupal runtime on the CLI so we can switch the class for the 'dr'
// console command.
if (php_sapi_name() == "cli") {
  $_SERVER['APP_RUNTIME'] = DrupalDevRuntime::class;
}
