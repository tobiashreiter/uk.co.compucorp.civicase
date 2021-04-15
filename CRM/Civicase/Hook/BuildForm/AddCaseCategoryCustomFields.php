<?php

/**
 * AddCaseCategoryCustomFields BuildForm Hook Class.
 */
class CRM_Civicase_Hook_BuildForm_AddCaseCategoryCustomFields extends CRM_Civicase_Hook_CaseCategoryInstanceBase {

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
      'case_category_singular_label',
      ts('Singular Label'),
      [
        'size' => 45,
      ],
    );
  }

}
