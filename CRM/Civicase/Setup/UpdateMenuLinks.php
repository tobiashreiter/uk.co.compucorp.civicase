<?php

/**
 * Updates the Manage Cases Menu URLs.
 */
class CRM_Civicase_Setup_UpdateMenuLinks {

  /**
   * Updates the Manage Cases Menu URLs.
   */
  public function apply() {
    $this->updateManageCasesMenuLink();

    return TRUE;
  }

  /**
   * Updates the Manage Cases Menu URL.
   *
   * To filter with `cases` case type category.
   */
  private function updateManageCasesMenuLink() {
    $casesParentMenu = civicrm_api3('Navigation', 'getsingle', [
      'name' => "cases",
    ]);

    if ($casesParentMenu['id']) {
      $manageCasesMenuItem = civicrm_api3('Navigation', 'getsingle', [
        'name' => "Manage Cases",
        'parent_id' => $casesParentMenu['id'],
      ]);
    }

    if ($manageCasesMenuItem['id']) {
      civicrm_api3('Navigation', 'create', [
        'id' => $manageCasesMenuItem['id'],
        'url' => 'civicrm/case/a/#/case/list?cf=%7B"case_type_category":"cases"%7D',
      ]);
    }

  }

}
