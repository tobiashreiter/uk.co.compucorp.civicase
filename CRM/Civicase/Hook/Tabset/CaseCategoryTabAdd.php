<?php

use CRM_Case_BAO_CaseType as CaseType;
use CRM_Civicase_Helper_CaseCategory as CaseCategoryHelper;

/**
 * Class CRM_Civicase_Hook_TabsetCaseCategoryTabAdd.
 */
class CRM_Civicase_Hook_Tabset_CaseCategoryTabAdd {

  /**
   * Determines what happens if the hook is handled.
   *
   * @param string $tabsetName
   *   Tabset name.
   * @param array $tabs
   *   Tabs list.
   * @param array $context
   *   Context.
   * @param bool $useAng
   *   Whether to use angular.
   */
  public function run($tabsetName, array &$tabs, array $context, &$useAng) {
    if (!$this->shouldRun($tabsetName)) {
      return;
    }

    $this->addCaseCategoryContactTabs($tabs, $context['contact_id'], $useAng);
  }

  /**
   * Add Case Category Contact Tabs.
   *
   * @param array $tabs
   *   Tabs list.
   * @param int $contactID
   *   Contact ID.
   * @param bool $useAng
   *   Whether to use angular.
   */
  private function addCaseCategoryContactTabs(array &$tabs, $contactID, &$useAng) {
    $caseTypeCategories = CaseType::buildOptions('case_type_category', 'match');
    if (empty($caseTypeCategories)) {
      return;
    }

    $caseTabWeight = $this->getCaseTabWeight($tabs);
    foreach ($caseTypeCategories as $categoryName => $categoryLabel) {
      if ($categoryName == CaseCategoryHelper::CASE_TYPE_CATEGORY_NAME) {
        continue;
      }
      $caseTabWeight++;
      $useAng = TRUE;
      $tabs[] = [
        'id' => $categoryName,
        'url' => CRM_Utils_System::url('civicrm/case/contact-case-tab', [
          'cid' => $contactID,
          'case_type_category' => $categoryName,
        ]),
        'title' => ts($categoryLabel),
        'weight' => $caseTabWeight,
        'count' => CaseCategoryHelper::getCaseCount($categoryName, $contactID),
        'class' => 'livePage',
      ];
    }
  }

  /**
   * Checks if the hook should run.
   *
   * @param string $tabsetName
   *   Tabset name.
   *
   * @return bool
   *   Return value.
   */
  private function shouldRun($tabsetName) {
    return $tabsetName === 'civicrm/contact/view';
  }

  /**
   * Return the tab weight of the case tab.
   *
   * @param array $tabs
   *   Tabs.
   *
   * @return int
   *   Weight of case tab.
   */
  private function getCaseTabWeight(array $tabs) {
    foreach ($tabs as $key => $tab) {
      if ($tab['id'] === 'case') {
        return $tab['weight'];
      }
    }

    return 0;
  }

}
