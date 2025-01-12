<?php

require_once 'mahjgdpr.civix.php';

use CRM_Mahjgdpr_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function mahjgdpr_civicrm_config(&$config): void {
  _mahjgdpr_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function mahjgdpr_civicrm_install(): void {
  _mahjgdpr_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function mahjgdpr_civicrm_enable(): void {
  _mahjgdpr_civix_civicrm_enable();
}
