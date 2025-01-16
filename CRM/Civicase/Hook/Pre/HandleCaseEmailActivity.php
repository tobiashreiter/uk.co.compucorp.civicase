<?php

/**
 * Handles Precessing of Case email activities.
 */
class CRM_Civicase_Hook_Pre_HandleCaseEmailActivity {

  /**
   * Case email utils.
   *
   * @var CRM_Utils_Mail_CaseMail
   *   This Case email utils object.
   */
  private CRM_Utils_Mail_CaseMail $caseMailUtil;

  public function __construct() {
    $this->caseMailUtil = new CRM_Utils_Mail_CaseMail();
  }

  /**
   * Add case id to email activity creation params if it belongs to a case.
   *
   * @param string $op
   *   The operation being performed.
   * @param string $objectName
   *   Object name.
   * @param mixed $objectId
   *   Object ID.
   * @param array &$params
   *   Array of an entity.
   */
  public function run($op, $objectName, $objectId, &$params) {
    if (!$this->shouldRun($op, $objectName, $params)) {
      return;
    }

    $subject = $params['subject'] ?? '';
    $subjectPatterns = $this->caseMailUtil->getSubjectPatterns();
    $caseId = NULL;
    $matches = [];
    $key = CRM_Core_DAO::escapeString(CIVICRM_SITE_KEY);

    for ($i = 0; $caseId === NULL && $i < count($subjectPatterns); $i++) {
      if (preg_match($subjectPatterns[$i], $subject, $matches)) {
        $query = "SELECT id FROM civicrm_case WHERE SUBSTR(SHA1(CONCAT('$key', id)), 1, 7) = %1";
        $caseId = CRM_Core_DAO::singleValueQuery($query, [1 => [$matches[1], 'String']]) ?: NULL;
        if (empty($caseId)) {
          $query = "SELECT id FROM civicrm_case WHERE id = %1";
          $caseId = CRM_Core_DAO::singleValueQuery($query, [1 => [$matches[1], 'String']]) ?: NULL;
        }
      }

      $matches = [];
    }

    if ($caseId !== NULL) {
      $params['case_id'] = $caseId;
    }
  }

  /**
   * Determines if the hook should run or not.
   *
   * @param string $op
   *   The operation being performed.
   * @param string $objectName
   *   Object name.
   * @param array $params
   *   Params array.
   *
   * @return bool
   *   returns a boolean to determine if hook will run or not.
   */
  private function shouldRun($op, $objectName, $params) {
    return strtolower($objectName) == 'activity' && $op == 'create' && empty($params['case_id'] ?? NULL)
      && $this->caseMailUtil->isCaseEmail($params['subject'] ?? '');
  }

}
