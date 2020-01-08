<div id="bootstrap-theme" class="civicase__crm-dashboard">
  <ul class="nav nav-pills nav-pills-horizontal civicase__tabs--dark">
    <li class="active"><a href="#dashboard" data-toggle="tab">Dashboard</a></li>
    <li class=""><a href="#myactivities" data-toggle="tab">My Activities</a></li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane active" id="dashboard">
      {include file="CRM/Contact/Page/DashBoardDashlet.tpl"}
    </div>
    <div class="tab-pane" id="myactivities">
      My Activities.
    </div>
  </div>
</div>
{literal}
<script type="text/javascript">
  CRM.$(function(){
    var hash = window.location.hash;
    hash && CRM.$('ul.nav.nav-pills a[href="' + hash + '"]').tab('show');
    CRM.$('ul.nav.nav-pills a').click(function (e) {
      CRM.$(this).tab('show');
      window.location.hash = this.hash;
    });
  });
</script>
{/literal}
