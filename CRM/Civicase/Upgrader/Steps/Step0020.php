<?php

use CRM_Civicase_Setup_Manage_QuotationTemplateManager as QuotationTemplateManager;

/**
 * Update sales order invoice message template.
 */
class CRM_Civicase_Upgrader_Steps_Step0020 {

  /**
   * Runs the upgrader changes.
   *
   * @return bool
   *   Return value in boolean.
   */
  public function apply(): bool {
    try {
      (new QuotationTemplateManager())->create();
    }
    catch (\Throwable $th) {
      \Civi::log()->error('Error upgrading Civicase', [
        'context' => [
          'backtrace' => $th->getTraceAsString(),
          'message' => $th->getMessage(),
        ],
      ]);
    }

    return TRUE;
  }

}
