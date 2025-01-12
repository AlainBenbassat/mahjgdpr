<?php

use CRM_Mahjgdpr_ExtensionUtil as E;

class CRM_Mahjgdpr_Form_ContactSearch extends CRM_Core_Form {
  public function buildQuickForm(): void {
    $this->setTitle('MahJ RGPD');

    $this->addFormElements();
    $this->addFormButtons();

    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function postProcess(): void {
    $values = $this->exportValues();

    if ($values['are_you_sure'] == 1) {
      CRM_Mahjgdpr_Queue::run();
    }

    parent::postProcess();
  }

  private function addFormElements() {
    $group = new CRM_Mahjgdpr_Group();
    $this->addYesNo('are_you_sure', 'Voulez-vous rechercher les contacts newsletter à relancer et les mettre dans le groupe ' . $group::targetGroupTitle . ' ?', FALSE, TRUE);
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
