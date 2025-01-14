<?php

class CRM_Mahjgdpr_Group {
  private const targetGroupName = 'gdpr_target_contacts';
  public const targetGroupTitle = 'RGPD Contacts à relancer';
  public $targetGroupId = 0;
  private $emailDomains = [];

  public function __construct() {
    $this->fillTargetGroupId();
  }

  public function clear() {
    $sql = "delete from civicrm_group_contact gc where gc.group_id = " . $this->targetGroupId;
    \CRM_Core_DAO::executeQuery($sql);

    $sql = "delete from civicrm_group_contact_cache gcc where gcc.group_id = " . $this->targetGroupId;
    \CRM_Core_DAO::executeQuery($sql);
  }

  public function populate() {
    $newsletterGroup = \Civi::settings()->get('mahjgdpr_newsletter_group');
    $cutoffDate = \Civi::settings()->get('mahjgdpr_cutoff_creation_date');

    $this->fillEmailDomains();

    $groupContacts = \Civi\Api4\GroupContact::get(FALSE)
      ->addSelect('contact_id', 'email.email')
      ->addJoin('Email AS email', 'INNER', ['contact_id', '=', 'email.id'], ['email.is_primary', '=', 1])
      ->addWhere('group_id', '=', $newsletterGroup)
      ->addWhere('contact_id.contact_type', '=', 'Individual')
      ->addWhere('contact_id.created_date', '<=', $cutoffDate)
      ->execute();
    foreach ($groupContacts as $groupContact) {
      if ($groupContact['email.email']) {
        \Civi\Api4\GroupContact::create(FALSE)
          ->addValue('group_id', $this->targetGroupId)
          ->addValue('contact_id', $groupContact['contact_id'])
          ->addValue('status', 'Added')
          ->execute();
      }
    }

  }

  private function fillTargetGroupId() {
    $this->targetGroupId = $this->getTargetGroupId();
    if (!$this->targetGroupId) {
      $this->targetGroupId = $this->createTargetGroup();
    }
  }

  private function getTargetGroupId() {
    $group = \Civi\Api4\Group::get(FALSE)
      ->addSelect('id')
      ->addWhere('name', '=', self::targetGroupName)
      ->execute()
      ->first();

    return !empty($group['id']) ? $group['id'] : FALSE;
  }

  private function createTargetGroup() {
    $results = \Civi\Api4\Group::create(FALSE)
      ->addValue('name', self::targetGroupName)
      ->addValue('title', self::targetGroupTitle)
      ->addValue('group_type', [2]) // mailing group
      ->execute();

    return $results[0]['id'];
  }

  private function fillEmailDomains() {
    $domains = \Civi::settings()->get('mahjgdpr_email_domains');
    $this->emailDomains = explode("\n", $domains);
  }

  private function isGdprDomain($email) {
    foreach ($this->emailDomains as $domain) {
      if (strpos($email, $domain) !== FALSE) {
        return TRUE;
      }
    }

    return FALSE;
  }

}