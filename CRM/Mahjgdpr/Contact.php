<?php

class CRM_Mahjgdpr_Contact {
  public function __construct(private int $contactId) {
  }

  public function hasEventRegistrations() {
    $sql = "
      select
        count(*)
      FROM
        civicrm_participant p
      where 
        p.contact_id = {$this->contactId}
    ";
    $num = CRM_Core_DAO::singleValueQuery($sql);
    if ($num > 0) {
      return TRUE;
    }

    return FALSE;
  }

  public function hasContributions() {
    $sql = "
      select
        count(*)
      FROM
        civicrm_contribution c
      where 
        c.contact_id = {$this->contactId}
    ";
    $num = CRM_Core_DAO::singleValueQuery($sql);
    if ($num > 0) {
      return TRUE;
    }

    return FALSE;
  }

  public function hasMemberships() {
    $sql = "
      select
        count(*)
      FROM
        civicrm_membership m
      where 
        m.contact_id = {$this->contactId}
    ";
    $num = CRM_Core_DAO::singleValueQuery($sql);
    if ($num > 0) {
      return TRUE;
    }

    return FALSE;
  }

  public function hasOpenedMailings() {
    $sql = "
      select
        count(*)
      FROM
        civicrm_mailing_event_queue q
      inner join
        civicrm_mailing_event_opened o on o.event_queue_id = q.id
      where 
        q.contact_id = {$this->contactId}
      and
         o.time_stamp >= date_sub(now(), INTERVAL 3 YEAR)
    ";
    $num = CRM_Core_DAO::singleValueQuery($sql);
    if ($num > 0) {
      return TRUE;
    }

    return FALSE;
  }

  public function hasClicks() {
    $sql = "
      select
        count(*)
      FROM
        civicrm_mailing_event_queue q
      inner join
        civicrm_mailing_event_trackable_url_open o on o.event_queue_id = q.id
      where 
        q.contact_id = {$this->contactId}
      and
        o.time_stamp >= date_sub(now(), INTERVAL 3 YEAR)
    ";
    $num = CRM_Core_DAO::singleValueQuery($sql);
    if ($num > 0) {
      return TRUE;
    }

    return FALSE;
  }

  public function wantsToBeKeptInDatebase() {
    $sql = "
      select
        id
      from
        civicrm_value_rgpd_23
      where
        accord_rgpd_141 = 1
      and
        entity_id = {$this->contactId}
    ";
    $num = CRM_Core_DAO::singleValueQuery($sql);
    if ($num > 0) {
      return TRUE;
    }

    return FALSE;
  }
}
