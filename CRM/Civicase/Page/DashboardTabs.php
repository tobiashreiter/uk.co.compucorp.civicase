<?php

/**
 * Dashboard Tabs Page.
 */
class CRM_Civicase_Page_DashboardTabs extends CRM_Contact_Page_DashBoard {

  /**
   * Adds the CRM dashboard's CSS file and Dashboard page content.
   */
  public function run() {
    CRM_Core_Resources::singleton()
      ->addStyleFile('uk.co.compucorp.civicase', 'css/civicase.min.css');

    return parent::run();
  }

}
