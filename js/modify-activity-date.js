CRM.$(function ($) {
  (function () {
    const date = CRM.vars.civicase.formatted_date;

    CRM.$('.crm-activity-form-block-activity_date_time > td.view-value').text(date);
  })();
});
