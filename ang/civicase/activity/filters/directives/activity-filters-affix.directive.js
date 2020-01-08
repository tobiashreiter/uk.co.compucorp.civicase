(function (angular, $, _) {
  var module = angular.module('civicase');

  module.directive('civicaseActivityFiltersAffix', function ($rootScope, $timeout) {
    return {
      link: civicaseActivityFiltersAffix
    };

    /**
     * Link function for civicaseActivityFiltersAffix
     *
     * @param {object} scope scope object of the directive
     * @param {object} $el directive element
     * @param {object} attr attributes
     */
    function civicaseActivityFiltersAffix (scope, $el, attr) {
      var $filter, $feedBodyPanel, $tabs, $toolbarDrawer;

      (function init () {
        affixActivityFilters();
        scope.$on('civicase::case-search::dropdown-toggle', resetAffix);
      }());

      /**
       * Get Tabs element depending on the page
       *
       * @returns {object} tab element
       */
      function getTabsElement () {
        if ($('.civicase__dashboard').length > 0) {
          return $('.civicase__dashboard__tab-container ul.nav');
        } else if ($('.civicase__crm-dashboard > ul.nav').length > 0) {
          return $('.civicase__crm-dashboard > ul.nav');
        } else {
          return $('.civicase__case-body_tab');
        }
      }

      /**
       * Sets Activity Filters affix offsets
       */
      function affixActivityFilters () {
        $filter = $('.civicase__activity-filter');
        $feedBodyPanel = $('.civicase__activity-filter ~ .panel-body');
        $tabs = getTabsElement();
        $toolbarDrawer = $('#toolbar');
        var FEED_BODY_ORIGINAL_PADDING_TOP = 8;

        $timeout(function () {
          $filter.affix({
            offset: {
              top: $filter.offset().top - ($toolbarDrawer.height() + $tabs.height())
            }
          }).on('affixed.bs.affix', function () {
            $filter.css('top', $toolbarDrawer.height() + $tabs.height());
            $feedBodyPanel.css('padding-top', $filter.outerHeight() + FEED_BODY_ORIGINAL_PADDING_TOP);
          }).on('affixed-top.bs.affix', function () {
            $filter.css('top', 'auto');
            $feedBodyPanel.css('padding-top', FEED_BODY_ORIGINAL_PADDING_TOP + 'px');
          });
        });
      }

      /**
       * Resets Activity Filters affix offsets
       */
      function resetAffix () {
        $timeout(function () {
          // Reset right case view tab header
          if ($filter.data('bs.affix')) {
            $filter.data('bs.affix').options.offset.top = $filter.offset().top - ($toolbarDrawer.height() + $tabs.height());
          }
        });
      }
    }
  });
})(angular, CRM.$, CRM._);
