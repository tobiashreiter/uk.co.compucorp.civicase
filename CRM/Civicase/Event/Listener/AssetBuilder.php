<?php

use Civi\Angular\Page\Modules;
use Civi\Core\Event\GenericHookEvent;

/**
 * Asset Builder Event Listener Class.
 */
class CRM_Civicase_Event_Listener_AssetBuilder {

  /**
   * Add word replacements to Angular asset.
   *
   * @param \Civi\Core\Event\GenericHookEvent $event
   *   AssetBuilder Event.
   */
  public static function addWordReplacements(GenericHookEvent $event) {
    if ($event->asset == 'angular-modules.json') {
      $caseCategoryName = CRM_Core_Session::singleton()->get('current_case_category');
      CRM_Civicase_Hook_Helper_CaseTypeCategory::addWordReplacements($caseCategoryName);

      // Rebuild the asset if it has been built.
      if (!empty($event->content)) {
        Modules::buildAngularModules($event);
      }
    }
  }

}
