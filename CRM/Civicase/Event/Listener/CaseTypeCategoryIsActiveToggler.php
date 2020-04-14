<?php

use CRM_Civicase_Factory_CaseTypeCategoryEventHandler as CaseTypeCategoryEventHandlerFactory;
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
    if ($apiRequest['entity'] != 'OptionValue' || $apiRequest['action'] != 'create') {
      return;
    }
    if (!self::checkReferrer() || !self::checkOptionGroup($apiRequest['params']['option_group_id'])) {
      return;
    }

    $handler = CaseTypeCategoryEventHandlerFactory::create();
    $handler->onUpdate(
      $apiRequest['params']['id'],
      isset($apiRequest['params']['is_active']) ? $apiRequest['params']['is_active'] : NULL,
      isset($apiRequest['params']['icon']) ? $apiRequest['params']['icon'] : NULL
    );
  }

  /**
   * Checks the referer page for this request.
   *
   * This check may be used for the sake of performance. As we have to listen
   * for events of all OptionValues to catch ones related to case type
   * categories, we may check first if request was sent by user from civicrm
   * admin page and not is triggered by some code, e.g. civicrm_api3().
   *
   * @return bool
   *   TRUE if request is sent from civicrm Option Groups page, FALSE otherwise.
   */
  protected static function checkReferrer() {
    return strpos($_SERVER['HTTP_REFERER'], '/civicrm/admin/options') != FALSE;
  }

  /**
   * Checks if OptionValue belongs to Case Type Categories option group.
   *
   * @param int $optionGroupId
   *   Case type category option group id or name. If is id then name would be
   *   fetched from DB.
   *
   * @return bool
   *   TRUE if option value belongs to Case Type Categories option group,
   *   FALSE otherwise.
   */
  protected static function checkOptionGroup($optionGroupId) {
    $res = FALSE;
    if (!$optionGroupId) {
      return $res;
    }

    try {
      // Load option group.
      $optionGroup = civicrm_api3('OptionGroup', 'getsingle', [
        'id' => $optionGroupId,
      ]);

      // We're only interested in Case Type Categories option group.
      if (!empty($optionGroup['name']) && $optionGroup['name'] == 'case_type_categories') {
        $res = TRUE;
      }
    }
    catch (Exception $e) {
    }

    return $res;
  }
}
