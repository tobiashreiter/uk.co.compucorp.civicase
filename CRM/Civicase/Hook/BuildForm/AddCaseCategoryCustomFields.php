<?php

use CRM_Civicase_Service_CaseCategoryCustomFieldsSetting as CaseCategoryCustomFieldsSetting;

/**
 * AddCaseCategoryCustomFields BuildForm Hook Class.
 */
class CRM_Civicase_Hook_BuildForm_AddCaseCategoryCustomFields extends CRM_Civicase_Hook_CaseCategoryFormHookBase {

  /**
   * Adds the Case Category Custom Form fields.
   *
   * @param CRM_Core_Form $form
   *   Form Class object.
   * @param string $formName
   *   Form Name.
   */
  public function run(CRM_Core_Form &$form, $formName) {
    if (!$this->shouldRun($form, $formName)) {
      return;
    }

    $this->updatePrimaryLabelText($form);
    $this->addSingularLabelFormField($form);
    $this->addCaseCategoryCustomFieldsTemplate();
  }

  /**
   * Adds the template for case category custom fields.
   */
  private function addCaseCategoryCustomFieldsTemplate() {
    $templatePath = CRM_Civicase_ExtensionUtil::path() . '/templates';
    CRM_Core_Region::instance('page-body')->add(
      [
        'template' => "{$templatePath}/CRM/Civicase/Form/CaseCategoryCustomFields.tpl",
      ]
    );
  }

  /**
   * Adds the Case Category Singular Label Form field.
   *
   * @param CRM_Core_Form $form
   *   Form Class object.
   */
  private function addSingularLabelFormField(CRM_Core_Form $form) {
    $singularLabel = $form->add(
      'text',
      'singular_label',
      ts('Secondary Label'),
      ['size' => 45, 'maxLength' => 45],
      TRUE
    );

    if ($form->getVar('_id')) {
      $customFields = $this->getCustomFieldValues($form);

      $singularLabel->setValue($customFields['singular_label']);
    }
  }

  /**
   * Returns case category custom field values for the given form.
   *
   * @param CRM_Core_Form $form
   *   Form Class object.
   *
   * @return array
   *   Case Category custom field values.
   */
  private function getCustomFieldValues(CRM_Core_Form $form) {
    $caseCategoryCustomFields = new CaseCategoryCustomFieldsSetting();
    $formValues = $form->getVar('_values');
    $caseCategoryId = $formValues['value'];

    return $caseCategoryCustomFields->get($caseCategoryId);
  }

  /**
   * Updates the label text for the primary label field.
   *
   * @param CRM_Core_Form $form
   *   Form Class object.
   */
  private function updatePrimaryLabelText(CRM_Core_Form $form) {
    $labelField = $form->getElement('label');
    $labelField->setLabel('Primary Label');
    $labelField->setAttribute('maxlength', 45);
  }

}
