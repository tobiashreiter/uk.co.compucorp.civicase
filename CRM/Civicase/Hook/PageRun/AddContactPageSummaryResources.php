<?php

/**
 * Handles Contact Record Summary Page display.
 */
class CRM_Civicase_Hook_PageRun_AddContactPageSummaryResources {

  /**
   * Add resources (CSS and JS)for this Page.
   *
   * @param object $page
   *   Page Object.
   */
  public function run(&$page) {
    if (!$this->shouldRun($page)) {
      return;
    }

    $this->addResources();
  }

  /**
   * Add resources (CSS and JS) for this Page.
   */
  private function addResources() {
    Civi::resources()
      ->addScriptFile('uk.co.compucorp.civicase', 'js/disable-contact-summary-tab-activate.js', 2, 'html-header');
    CRM_Core_Resources::singleton()
      ->addScriptFile('uk.co.compucorp.civicase', 'packages/moment-with-locales.min.js');
  }

  /**
   * Determines if the hook will run.
   *
   * @param object $page
   *   Page Object.
   *
   * @return bool
   *   returns a boolean to determine if hook will run or not.
   */
  private function shouldRun($page) {
    return $page instanceof CRM_Contact_Page_View_Summary;
  }

}
