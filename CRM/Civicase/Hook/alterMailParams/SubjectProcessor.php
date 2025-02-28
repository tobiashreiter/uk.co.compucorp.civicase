<?php

/**
 * Remove word 'case' in email subject.
 */
class CRM_Civicase_Hook_alterMailParams_SubjectProcessor {

  /**
   * A substring of email subject that should be removed.
   *
   * @var string
   *   This substring will be removed.
   */
  private $toRemove = '[case ';

  /**
   * Email subject processor.
   *
   * Remove word 'case' in email subject.
   *
   * @param array $params
   *   Mail parameters.
   * @param string $context
   *   Mail context.
   */
  public function run(array &$params, $context) {
    $caseId = CRM_Utils_Request::retrieve('caseid', 'Integer');
    if (!$this->shouldRun($params, $context, $caseId)) {
      return;
    }

    // Make sure we make just 1 replacement.
    $subject = explode($this->toRemove, $params['subject'], 2);
    $params['subject'] = '[' . $subject[1];
  }

  /**
   * Determines if the hook will run.
   *
   * @param array $params
   *   Mail parameters.
   * @param string $context
   *   Mail context.
   * @param int $caseId
   *   Case id.
   *
   * @return bool
   *   returns TRUE if hook should run, FALSE otherwise.
   */
  private function shouldRun(array $params, $context, $caseId) {
    if (empty($params['subject'])) {
      return FALSE;
    }
    // If case id is set and email subject starts with '[case '.
    return $caseId && strpos($params['subject'], $this->toRemove) === 0;
  }

}
