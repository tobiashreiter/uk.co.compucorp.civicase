(function (angular, $, _, CRM) {
  var module = angular.module('civicase');

  module.factory('formatCase', function ($sce, formatActivity, CasesUtils,
    CaseStatus, CaseType, isTruthy) {
    var caseStatuses = CaseStatus.getAll(true);

    return function (item) {
      item.client = [];
      item.subject = getFormattedCaseSubject(item.subject);
      item.status = caseStatuses[item.status_id].label;
      item.color = caseStatuses[item.status_id].color;
      item.case_type = CaseType.getById(item.case_type_id).title;
      item.selected = false;
      item.is_deleted = isTruthy(item.is_deleted);

      countIncompleteOtherTasks(item);
      formatCaseCustomData(item);

      _.each(item.activity_summary, function (activities) {
        _.each(activities, function (act) {
          formatActivity(act, item.id);
        });
      });

      _.each(item, function (field) {
        if (field && typeof field.activity_date_time !== 'undefined') {
          formatActivity(field, item.id);
        }
      });

      _.each(item.contacts, function (contact) {
        if (CasesUtils.isClientRole(contact)) {
          item.client.push(contact);
        }

        if (isTruthy(contact.manager)) {
          item.manager = contact;
        }
      });

      return item;
    };

    /**
     * Returns a formatted subject for the case.
     *
     * If the subject is not defined will return an empty string. Otherwise it
     * will return an escaped subject string free of HTML entities.
     *
     * We unwrap (valueOf) and then wrap (trustAsHtml) the subject as a trusted
     * HTML value because the value could have already been wrapped and trying
     * to wrap the value again would throw an error.
     *
     * @param {string} subject the case subject before being formatted.
     * @returns {string} The formatted subject.
     */
    function getFormattedCaseSubject (subject) {
      if (typeof subject === 'undefined') {
        return '';
      }

      return $sce.trustAsHtml($sce.valueOf(subject));
    }

    /**
     * Accumulates non communication and task counts as
     * other count for incomplete tasks
     *
     * @param {object} item case
     */
    function countIncompleteOtherTasks (item) {
      item.category_count.other = {};
      item.category_count.other.incomplete = 0;
      item.category_count.other.overdue = 0;

      var excludedCategories = ['communication', 'task', 'other'];

      _.each(_.keys(item.category_count), function (category) {
        if (!_.contains(excludedCategories, category)) {
          item.category_count.other.incomplete += item.category_count[category].incomplete || 0;
          item.category_count.other.overdue += item.category_count[category].overdue || 0;
        }
      });
    }

    /**
     * Groups the the given case's custom data by its style field (Inline or Tab)
     * and parses their wight field into numbers so they can be properly sorted.
     *
     * @param {object} item case
     */
    function formatCaseCustomData (item) {
      if (!item['api.CustomValue.getalltreevalues']) {
        item.customData = {};

        return;
      }

      var customData = item['api.CustomValue.getalltreevalues'].values;
      delete item['api.CustomValue.getalltreevalues'];

      item.customData = _.chain(customData)
        .map(function (fieldSet) {
          return _.extend({}, fieldSet, {
            weight: parseInt(fieldSet.weight, 10)
          });
        })
        .groupBy('style')
        .value();
    }
  });
})(angular, CRM.$, CRM._, CRM);
