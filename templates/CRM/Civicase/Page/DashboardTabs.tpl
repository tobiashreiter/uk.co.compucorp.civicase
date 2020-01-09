<div id="bootstrap-theme" class="civicase__crm-dashboard">
  <ul
    civicase-crm-dashboard-tabset-affix
    class="civicase__affix__activity-filters nav nav-pills nav-pills-horizontal nav-pills-horizontal-default civicase__crm-dashboard__tabs">
    <li class="active"><a href="#dashboard" data-toggle="tab">Dashboard</a></li>
    <li class=""><a href="#myactivities" data-toggle="tab">My Activities</a></li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane active" id="dashboard">
      {include file="CRM/Contact/Page/DashBoardDashlet.tpl"}
    </div>
    <div class="tab-pane civicase__crm-dashboard__myactivities-tab" id="myactivities">
      <div ng-view></div>
    </div>
  </div>
</div>
{literal}
<script type="text/javascript">
  CRM.$(function() {
    var hashWithoutQueryParams = window.location.hash.split('?')[0];
    var hash = hashWithoutQueryParams.substr(2);

    hash && CRM.$('ul.nav.nav-pills a[href="#' + hash + '"]').tab('show');

    CRM.$('ul.nav.nav-pills a').click(function (e) {
      CRM.$(this).tab('show');
      window.location.hash = this.hash;
      CRM.$(window).scrollTop(0);
    });
  });

  (function(angular, $, _) {
    angular.module('civicaseCrmDashboard', ['civicase']);
    angular.module('civicaseCrmDashboard').config(function($routeProvider) {
      $routeProvider.when('/myactivities', {
        reloadOnSearch: false,
        template: '<civicase-my-activities-tab></civicase-my-activities-tab>'
      });
    });
  })(angular, CRM.$, CRM._);

  CRM.$(document).one('crmLoad', function(){
    angular.bootstrap(document.getElementsByClassName('civicase__crm-dashboard')[0], ['civicaseCrmDashboard']);
  });
</script>
{/literal}
