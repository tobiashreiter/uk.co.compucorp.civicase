<?php

use CRM_Civicase_Factory_CaseTypeCategoryEventHandler as CaseTypeCategoryEventHandlerFactory;
use CRM_Civicase_Helper_CaseCategory as CaseTypeCategoryHelper;
use Civi\API\Event\RespondEvent;

/**
 * Class CRM_Civicase_Event_Listener_CaseTypeCategoryIsActiveToggler.
 *
 * Contains extra processing for case type category when it is
 * enabled / disabled, e.g. show / hide related menu item.
 */
class CRM_Civicase_Event_Listener_CaseTypeCategoryIsActiveToggler {

  /**
   * Update case type category related objects on category update.
   *
   * @param \Civi\API\Event\RespondEvent $e
   *   Event data.
   */
  public static function onRespond(RespondEvent $e) {
    $apiRequest = $e->getApiRequest();

    if ($apiRequest['version'] != 3) {
      return;
    }

    if (!self::shouldRun($apiRequest)) {
      return;
    }

    $caseCategoryValue = self::getCaseCategoryValue($apiRequest['params']['id']);
    $caseCategoryInstance = CaseTypeCategoryHelper::getInstanceObject($caseCategoryValue);
    $handler = CaseTypeCategoryEventHandlerFactory::create();
    $caseCategory = [
      'id' => $apiRequest['params']['id'],
      'is_active' => isset($apiRequest['params']['is_active']) ?? NULL,
      'icon' => isset($apiRequest['params']['icon']) ?? NULL,
    ];

    $handler->onUpdate($caseCategoryInstance, $caseCategory);
  }

  /**
   * Determines if the processing will run.
   *
   * @param array $apiRequest
   *   Api request data.
   *
   * @return bool
   *   TRUE if processing should run, FALSE otherwise.
   */
  protected static function shouldRun(array $apiRequest) {
    if ($apiRequest['entity'] != 'OptionValue' || $apiRequest['action'] != 'create') {
      return FALSE;
    }
    if (!self::isFromOptionGroupPage() || !self::isOfCaseTypeCategoryGroup($apiRequest['params']['id'])) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Checks the referer page for this request.
   *
   * This check is used for the sake of performance. As we have to listen
   * for events of all OptionValues to catch ones related to case type
   * categories, we may check first if request was sent by user from civicrm
   * admin page and not is triggered by some code, e.g. civicrm_api3().
   *
   * @return bool
   *   TRUE if request is sent from civicrm Option Groups page, FALSE otherwise.
   */
  protected static function isFromOptionGroupPage() {
    return isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '/civicrm/admin/options') !== FALSE;
  }

  /**
   * Checks if OptionValue belongs to Case Type Categories option group.
   *
   * @param int $categoryId
   *   Case type category id.
   *
   * @return bool
   *   TRUE if option value belongs to Case Type Categories option group,
   *   FALSE otherwise.
   */
  protected static function isOfCaseTypeCategoryGroup($categoryId) {
    $res = FALSE;
    if (!$categoryId) {
      return $res;
    }

    try {
      // Get related option group name.
      $result = civicrm_api3('OptionValue', 'getsingle', [
        'return' => ['option_group_id.name'],
        'id' => $categoryId,
      ]);

      // We're only interested in 'Case Type Categories' option group.
      if ($result['option_group_id.name'] == 'case_type_categories') {
        $res = TRUE;
      }
    }
    catch (Exception $e) {
    }

    return $res;
  }

  /**
   * Returns the Case type category Value.
   *
   * @param int $categoryId
   *   Category Id.
   *
   * @return mixed
   *   Case category Value
   */
  private static function getCaseCategoryValue($categoryId) {
    $categoryOptions = CRM_Core_OptionGroup::values('case_type_categories', FALSE, FALSE, TRUE, NULL, 'value', TRUE, FALSE, 'id');

    return $categoryOptions[$categoryId];
  }

}
