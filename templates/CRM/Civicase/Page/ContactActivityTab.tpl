<div id="civicaseActivitiesTab" >
  <div class="container" ng-view></div>
</div>
{literal}
<script type="text/javascript">
  (function(angular, $, _) {
    angular.module('civicaseActivitiesTab', ['civicase']);
    angular.module('civicaseActivitiesTab').config(function($routeProvider) {
      $routeProvider.when('/', {
        reloadOnSearch: false,
        template: '<civicase-contact-activity-tab></civicase-contact-activity-tab>'
      });
    });
  })(angular, CRM.$, CRM._);

  CRM.$(document).one('crmLoad', function(){
    angular.bootstrap(document.getElementById('civicaseActivitiesTab'), ['civicaseActivitiesTab']);
  });
</script>
{/literal}
