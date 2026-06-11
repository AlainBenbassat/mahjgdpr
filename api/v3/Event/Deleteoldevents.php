<?php
use CRM_Mahjgdpr_ExtensionUtil as E;

function _civicrm_api3_event_Deleteoldevents_spec(&$spec) {
  $spec['days']['api.required'] = 1;
}

function civicrm_api3_event_Deleteoldevents($params) {
  if (empty($params['days'])) {
    throw new CRM_Core_Exception('You must specify the number of days to keep events for.', 'days_missing');
  }

  if (!is_numeric($params['days'])) {
    throw new CRM_Core_Exception('The number of days to keep events for must be a number.', 'days_not_numeric');
  }

  $days = $params['days'];
  if ($days < 365 || $days > 9999) {
    throw new CRM_Core_Exception('The number of days to keep events for must be between 365 and 9999.', 'days_out_of_range');
  }

  $sql = "DELETE FROM civicrm_event WHERE end_date < DATE_SUB(NOW(), INTERVAL $days DAY)";
  CRM_Core_DAO::executeQuery($sql);
  CRM_Core_DAO::executeQuery('OPTIMIZE TABLE civicrm_event');
  CRM_Core_DAO::executeQuery('OPTIMIZE TABLE civicrm_participant');

  return civicrm_api3_create_success($returnValues, $params, 'Event', 'Deleteoldevents');
}
