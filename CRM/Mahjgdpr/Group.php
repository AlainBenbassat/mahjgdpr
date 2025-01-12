<?php

class CRM_Mahjgdpr_Group {
  private const targetGroupName = 'gdpr_target_contacts';
  public const targetGroupTitle = 'RGPD Contacts à relancer';
  public $targetGroupId = 0;

  public function __construct() {
    $this->fillTargetGroupId();
  }

  private function fillTargetGroupId() {
    $this->targetGroupId = $this->getTargetGroupId() || $this->createTargetGroup();
  }

  private function getTargetGroupId() {
    $group = \Civi\Api4\Group::get(FALSE)
      ->addSelect('id')
      ->addWhere('name', '=', self::targetGroupName)
      ->execute()
      ->first();

    return $group['id'] ?? FALSE;
  }

  private function createTargetGroup() {
    $results = \Civi\Api4\Group::create(FALSE)
      ->addValue('name', self::targetGroupName)
      ->addValue('title', self::targetGroupTitle)
      ->addValue('group_type', [2]) // mailing group
      ->execute();

    return $results[0]['id'];
  }


}