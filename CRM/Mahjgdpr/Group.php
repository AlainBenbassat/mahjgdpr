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
    $newsletterGroups = \Civi::settings()->get('mahjgdpr_newsletter_group');
    $cutoffDate = \Civi::settings()->get('mahjgdpr_cutoff_creation_date');

    $this->fillEmailDomains();

    $groupContacts = \Civi\Api4\GroupContact::get(FALSE)
      ->addSelect('contact_id', 'email.email')
      ->addJoin('Email AS email', 'INNER', ['contact_id', '=', 'email.contact_id'], ['email.is_primary', '=', 1])
      ->addWhere('group_id', 'IN', $newsletterGroups)
      ->addWhere('contact_id.contact_type', '=', 'Individual')
      ->addWhere('contact_id.created_date', '<=', $cutoffDate)
      ->setLimit(500)
      ->execute();
    foreach ($groupContacts as $groupContact) {
      if ($this->isGdprDomain($groupContact['email.email']) && $this->isIsolatedContact($groupContact['contact_id'])) {
        $this->addContact($groupContact['contact_id']);
      }
    }
  }

  public function removeContact($contactId) {
    \Civi\Api4\GroupContact::delete(FALSE)
      ->addWhere('group_id', $this->targetGroupId)
      ->addWhere('contact_id', $contactId)
      ->execute();
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
      if (str_contains($email, $domain)) {
        return TRUE;
      }
    }

    return FALSE;
  }

  private function addContact($contactId): void {
    try {
      \Civi\Api4\GroupContact::create(FALSE)
        ->addValue('group_id', $this->targetGroupId)
        ->addValue('contact_id', $contactId)
        ->addValue('status', 'Added')
        ->execute();
    }
    catch (\Exception $e) {
      // don't do anything
    }
  }

  public function isIsolatedContact($contactId): bool {
    $contact = new CRM_Mahjgdpr_Contact($contactId);

    if ($contact->hasEventRegistrations()) {
      return FALSE;
    }

    if ($contact->hasContributions()) {
      return FALSE;
    }

    if ($contact->hasMemberships()) {
      return FALSE;
    }

    if ($contact->hasContributions()) {
      return FALSE;
    }

    if ($contact->hasOpenedMailings()) {
      return FALSE;
    }

    if ($contact->hasClicks()) {
      return FALSE;
    }

    if ($contact->wantsToBeKeptInDatebase()) {
      return FALSE;
    }

    return TRUE;
  }

}