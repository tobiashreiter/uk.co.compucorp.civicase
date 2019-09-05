<?php

use CRM_Civicase_Helper_CaseCategory as CaseCategoryHelper;

/**
 * Class CRM_Civicase_Service_CaseCategoryPermission.
 */
class CRM_Civicase_Service_CaseCategoryPermission {

  /**
   * Case category name.
   *
   * @var string
   *   Case Category Name.
   */
  private $caseCategoryName;

  /**
   * CRM_Civicase_Service_CaseCategoryPermission constructor.
   *
   * @param string $caseCategoryName
   *   Case category name.
   */
  public function __construct($caseCategoryName) {
    $this->caseCategoryName = $caseCategoryName;
  }

  /**
   * Returns the permission set for a Civcase extension.
   *
   * This permission array is the original set of permissions defined in
   * the Case Core extension. Each Civicase extension variant will use
   * this same set of permissions but with proper word replacements
   * depending on the Case category.
   *
   * @return array
   *   Array of Permissions.
   */
  public function get() {
    return [
      'DELETE_IN_CASE_CATEGORY' => [
        'name' => $this->replaceWords('delete in CiviCase'),
        'label' => $this->replaceWords('CiviCase: delete in CiviCase'),
        'description' => $this->replaceWords('Delete cases'),
      ],
      'ADD_CASE_CATEGORY' => [
        'name' => $this->replaceWords('add cases'),
        'label' => $this->replaceWords('CiviCase: add cases'),
        'description' => $this->replaceWords('Open a new case'),
      ],
      'ADMINISTER_CASE_CATEGORY' => [
        'name' => $this->replaceWords('administer CiviCase'),
        'label' => $this->replaceWords('CiviCase: administer CiviCase'),
        'description' => $this->replaceWords('Define case types, access deleted cases'),
      ],
      'ACCESS_CASE_CATEGORY_AND_ACTIVITIES' => [
        'name' => $this->replaceWords('access all cases and activities'),
        'label' => $this->replaceWords('CiviCase: access all cases and activities'),
        'description' => $this->replaceWords('View and edit all cases (for visible contacts)'),
      ],
      'ACCESS_MY_CASE_CATEGORY_AND_ACTIVITIES' => [
        'name' => $this->replaceWords('access my cases and activities'),
        'label' => $this->replaceWords('CiviCase: access my cases and activities'),
        'description' => $this->replaceWords('View and edit only those cases managed by this user'),
      ],
      'BASIC_CASE_CATEGORY_INFO' => [
        'name' => $this->replaceWords('basic case information'),
        'label' => $this->replaceWords('Civicase: basic case information'),
        'description' => $this->replaceWords('Allows a user to view only basic information of cases'),
      ],
    ];
  }

  /**
   * Returns the appropriate string after proper word replacements.
   *
   * The word replacements is based on the value of the case category name.
   *
   * @param string $string
   *   String for word replacements.
   *
   * @return string
   *   Word replaced strings.
   */
  private function replaceWords($string) {
    if ($this->caseCategoryName == CaseCategoryHelper::CASE_TYPE_CATEGORY_NAME) {
      return $string;
    }

    return str_replace(
      ['CiviCase', 'cases', 'case'],
      [
        "Civi{$this->caseCategoryName}",
        $this->caseCategoryName,
        $this->caseCategoryName,
      ],
      $string
    );
  }

}
