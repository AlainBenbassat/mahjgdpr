<?php

require_once 'mahjgdpr.civix.php';

use CRM_Mahjgdpr_ExtensionUtil as E;

function mahjgdpr_civicrm_custom( $op, $groupID, $entityID, &$params ) {
  if ($op != 'create' && $op != 'edit') {
    return;
  }

  if ($groupID != 23) {
    return;
  }

  // update the consent date if the person answers YES and no date is filled in yet
  $sql = "select * from civicrm_value_rgpd_23 where entity_id = $entityID and ifnull(date_de_confirmation_142, '') = '' and accord_rgpd_141 = 1";
  $dao = CRM_Core_DAO::executeQuery($sql);
  if ($dao->fetch()) {
    CRM_Core_DAO::executeQuery("update civicrm_value_rgpd_23 set accord_rgpd_141 = now() where id = " . $dao->id);

    $g = new CRM_Mahjgdpr_Group();
    $g->removeContact($entityID);
  }
}


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
