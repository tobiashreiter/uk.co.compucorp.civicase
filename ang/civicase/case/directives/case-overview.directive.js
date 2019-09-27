(function (angular, $, _) {
  var module = angular.module('civicase');

  module.directive('civicaseCaseOverview', function () {
    return {
      restrict: 'EA',
      replace: true,
      templateUrl: '~/civicase/case/directives/case-overview.directive.html',
      controller: civicaseCaseOverviewController,
      scope: {
        caseFilter: '<'
      },
      link: civicaseCaseOverviewLink
    };

    /**
     * Link function for civicaseCaseOverview
     *
     * @param {Object} $scope
     * @param {jQuery} element
     * @param {Object} attrs
     */
    function civicaseCaseOverviewLink ($scope, element, attrs) {
      (function init () {
        $scope.$watch('showBreakdown', recalculateScrollbarPosition);
      }());

      /**
       * Watchers for showBreakdown variable
       */
      function recalculateScrollbarPosition () {
        $scope.$emit('civicase::custom-scrollbar::recalculate');
      }
    }
  });

  module.controller('civicaseCaseOverviewController', civicaseCaseOverviewController);

  /**
   * Controller for civicaseCaseOverview
   *
   * @param {Object} $scope
   * @param {crmApi} Object
   */
  function civicaseCaseOverviewController ($scope, crmApi, BrowserCache) {
    var BROWSER_CACHE_IDENTIFIER = 'civicase.CaseOverview.hiddenCaseStatuses';
    var caseTypes = CRM.civicase.caseTypes;
    var caseTypeCategories = CRM.civicase.caseTypeCategories;

    $scope.summaryData = [];
    $scope.caseStatuses = _.chain(CRM.civicase.caseStatuses)
      .sortBy(function (status) { return status.weight; })
      .indexBy('weight')
      .value();

    (function init () {
      setCaseTypesBasedOnCategory();
      // We hide the breakdown when there's only one case type
      if ($scope.caseTypesLength < 2) {
        $scope.showBreakdown = false;
      }

      $scope.$watch('caseFilter', caseFilterWatcher, true);
      loadHiddenCaseStatuses();
    }());

    /**
     * Checks if all statuses are hidden
     *
     * @return {Boolean}
     */
    $scope.areAllStatusesHidden = function () {
      return _.filter($scope.caseStatuses, function (status) {
        return !status.isHidden;
      }).length === 0;
    };

    /**
     * Creates link to the filtered cases list
     *
     * @param {String} type
     * @param {String} status
     * @return {String} link to the filtered list of cases
     */
    $scope.caseListLink = function (type, status) {
      var cf = {};

      if (type) {
        cf.case_type_id = [type];
      }

      if (status) {
        cf.status_id = [status];
      }

      if ($scope.myCasesOnly) {
        cf.case_manager = [CRM.config.user_contact_id];
      }

      return '#/case/list?' + $.param({cf: JSON.stringify(cf)});
    };

    /**
     * Toggle status view
     *
     * @param {event} event object
     * @param {Number} index of the case status
     */
    $scope.toggleStatusVisibility = function ($event, index) {
      $scope.caseStatuses[index + 1].isHidden = !$scope.caseStatuses[index + 1].isHidden;
      storeHiddenCaseStatuses();
      $event.stopPropagation();
    };

    /**
     * Toggles the visibility of the breakdown dropdown
     */
    $scope.toggleBrekdownVisibility = function () {
      $scope.showBreakdown = !$scope.showBreakdown;
    };

    /**
     * Watcher function for caseFilter
     */
    function caseFilterWatcher () {
      setCaseTypesBasedOnCategory();
      loadStatsData();
    }

    /**
     * Loads from the browser cache the ids of the case status that have been
     * previously hidden and marks them as such.
     */
    function loadHiddenCaseStatuses () {
      var hiddenCaseStatuses = BrowserCache.get(BROWSER_CACHE_IDENTIFIER, []);

      hiddenCaseStatuses.forEach(function (caseStatusId) {
        $scope.caseStatuses[caseStatusId].isHidden = true;
      });
    }

    /**
     * Loads Stats data
     */
    function loadStatsData () {
      var apiCalls = [];

      apiCalls.push(['Case', 'getstats', $scope.caseFilter || {}]);
      crmApi(apiCalls).then(function (response) {
        $scope.summaryData = response[0].values;
      });
    }

    /**
     * Sets the Case Types Based on Case Type Category
     */
    function setCaseTypesBasedOnCategory () {
      var filteredCaseTypes = $scope.caseFilter['case_type_id.case_type_category']
        ? getCaseTypesFilteredByCategory($scope.caseFilter['case_type_id.case_type_category'])
        : caseTypes;

      $scope.caseTypes = filteredCaseTypes;
      $scope.caseTypesLength = _.size($scope.caseTypes);
    }

    /**
     * Returns case types filtered by given category
     *
     * @param {String} categoryName
     * @return {Array}
     */
    function getCaseTypesFilteredByCategory (categoryName) {
      var caseTypeCategory = _.find(caseTypeCategories, function (category) {
        return category.name.toLowerCase() === categoryName.toLowerCase();
      });

      if (!caseTypeCategory) {
        return [];
      }

      return _.pick(caseTypes, function (caseType) {
        return caseType.case_type_category === caseTypeCategory.value;
      });
    }

    /**
     * Stores in the browser cache the id values of the case statuses that have been
     * hidden.
     */
    function storeHiddenCaseStatuses () {
      var hiddenCaseStatusesIds = _.chain($scope.caseStatuses)
        .pick(function (caseStatus, key) {
          return caseStatus.isHidden;
        })
        .keys()
        .value();

      BrowserCache.set(BROWSER_CACHE_IDENTIFIER, hiddenCaseStatusesIds);
    }
  }
})(angular, CRM.$, CRM._);
