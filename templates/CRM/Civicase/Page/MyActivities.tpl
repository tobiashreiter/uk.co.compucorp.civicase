<div id="civicaseMyActivitiesTab">
  <div class="container" ng-view></div>
</div>
{literal}
<script type="text/javascript">
  CRM.$(document).one('crmLoad', function(){
    angular.bootstrap(document.getElementById('civicaseMyActivitiesTab'), ['my-activities']);
  });
</script>
{/literal}
