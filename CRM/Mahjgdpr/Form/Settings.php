<?php

use CRM_Mahjgdpr_ExtensionUtil as E;

class CRM_Mahjgdpr_Form_Settings extends CRM_Core_Form {
  public function buildQuickForm(): void {
    $this->setTitle('Configuration MahJ RGPD');

    $this->addFormElements();
    $this->addFormButtons();

    $this->assign('target_group_link', $this->getTargetGroupLink());
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function postProcess(): void {
    $values = $this->exportValues();
    \Civi::settings()->set('mahjgdpr_newsletter_group', $values['newslettergroup']);
    \Civi::settings()->set('mahjgdpr_email_domains', $values['emailsuffexes']);

    parent::postProcess();
  }

  private function addFormElements() {
    $this->add('select', 'newslettergroup', 'Groupe newsletter', CRM_Core_PseudoConstant::nestedGroup(), ['class' => 'crm-select2 crm-action-menu fa-plus huge'], TRUE);
    $this->add('textarea', 'emailsuffexes', 'Noms de domaine à prendre en compte', ['class' => 'big'], TRUE);
  }

  private function addFormButtons() {
    $this->addButtons([
      [
        'type' => 'submit',
        'name' => E::ts('Submit'),
        'isDefault' => TRUE,
      ],
      [
        'type' => 'cancel',
        'name' => E::ts('Cancel'),
      ],
    ]);
  }

  public function setDefaultValues() {
    $defaults = parent::setDefaultValues();

    $newsletterGroup = \Civi::settings()->get('mahjgdpr_newsletter_group');
    if (!empty($newsletterGroup)) {
      $defaults['newslettergroup'] = $newsletterGroup;
    }

    $emailDomains = \Civi::settings()->get('mahjgdpr_email_domains');
    if (empty($emailDomains)) {
      $defaults['emailsuffexes'] = "@yahoo.\n@gmail.\n@hotmail.";
    }
    else {
      $defaults['emailsuffexes'] = $emailDomains;
    }

    return $defaults;
  }

  private function getTargetGroupLink() {
    $gdprGroup = new CRM_Mahjgdpr_Group();

    $url = CRM_Utils_System::url('civicrm/group/search', 'reset=1&force=1&context=smog&component_mode=1&gid=' . $gdprGroup->targetGroupId);
    $groupTitle = $gdprGroup::targetGroupTitle;

    return "<a href=\"$url\">$groupTitle</a>";
  }

  private function getRenderableElementNames(): array {
    $elementNames = [];
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

}
