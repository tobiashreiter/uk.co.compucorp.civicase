<?php

use CRM_Civicase_ExtensionUtil as ExtensionUtil;
use Civi\Angular\ChangeSet;

/**
 * Angular ChangeSet Helper Class.
 */
class CRM_Civicase_Hook_alterAngular_AngularChangeSet {

  /**
   * Returns ChangeSet for the case type category field.
   *
   * This ChangeSet is needed for the core Case Type create/Edit screen.
   *
   * @return \Civi\Angular\ChangeSet
   *   Angular ChangeSet.
   */
  public static function getForCaseTypeCategoryField() {
    $path = CRM_Core_Resources::singleton()
      ->getPath(ExtensionUtil::LONG_NAME, 'templates/CRM/Civicase/ChangeSet/CaseTypeCategory.html');
    $caseTypeCategoryContent = file_get_contents($path);

    return ChangeSet::create('case-type-category')
      ->alterHtml('~/crmCaseType/caseTypeDetails.html', function (phpQueryObject $doc) use ($caseTypeCategoryContent) {
        $doc->find("div[crm-ui-field*=name: 'caseTypeDetailForm.caseTypeName']")->after($caseTypeCategoryContent);
      });
  }

}
