(function ($) {
  /**
   * The following line of code is to fix GIZ-348.
   *
   * In Contact Summary tab, we faced an issue where if we click 2 angular based
   * tabs(For example: Activity, Case, Awards tabs) very quickly. Then thousands
   * of network requests are being sent to the backend, which is freezing the
   * webpage and sometimes crashing the server.
   *
   * This issue was found after upgrading to CiviCRM 5.51 from 5.39.
   *
   * Later we identified the issue in CiviCRM core, specifically in
   * https://github.com/civicrm/civicrm-core/commit/1ed9f5c505fc61802542e8dc05415fe4a1d99198.
   *
   * In the above commit in CiviCRM core, code was introduced to add `selectedChild`
   * query parameter whenever we click on a Contact Summary page tab. And because
   * of this, as the route was changed, AngularJS router was getting confused.
   *
   * So we are adding this fix, where we remove the `tabsactivate` listener from
   * `#mainTabContainer` so that `selectedChild` is not added to the URL.
   *
   * Note: This is not a perfect solution, but it will work fine for now.
   * In the long run, we should try to contact with CiviCRM core, and get it fixed
   * there. This issue should be reproducible(need to confirm) in a vanilla
   * CiviCRM Core site, with https://github.com/civicrm/org.civicrm.civicase installed.
   * That way it will be easier to convince Core team to fix this.
   */
  $(function ($) {
    $('#mainTabContainer').off('tabsactivate');
  });
})(CRM.$);
