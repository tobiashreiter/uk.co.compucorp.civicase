(function (angular, $, _) {
  var module = angular.module('civicase');

  module.directive('civicaseCaseDetailsPeopleTab', function () {
    return {
      restrict: 'A',
      templateUrl: '~/civicase/case/details/people-tab/directives/case-details-people-tab.directive.html',
      controller: civicaseViewPeopleController,
      scope: {
        item: '=civicaseCaseDetailsPeopleTab',
        refresh: '=refreshCallback'
      }
    };
  });

  module.controller('civicaseViewPeopleController', civicaseViewPeopleController);

  /**
   * civicaseViewPeopleController Controller
   *
   * @param {object} $scope $scope
   * @param {object} allowMultipleCaseClients allow multiple clients configuration value
   * @param {Function} civicaseCrmUrl crm url service.
   * @param {object} ts ts
   * @param {boolean} civicaseSingleCaseRolePerType if a single case role can be assigned per type
   * @param {Function} civicaseCrmLoadForm crm load form service.
   */
  function civicaseViewPeopleController ($scope,
    allowMultipleCaseClients, civicaseCrmUrl, ts, civicaseSingleCaseRolePerType,
    civicaseCrmLoadForm) {
    $scope.ts = ts;
    $scope.allowMultipleCaseClients = allowMultipleCaseClients;
    $scope.civicaseSingleCaseRolePerType = civicaseSingleCaseRolePerType;

    $scope.letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
    $scope.contactTasks = CRM.civicase.contactTasks;
    $scope.ceil = Math.ceil;

    $scope.getSelectedContacts = getSelectedContacts;
    $scope.setSelectionMode = setSelectionMode;
    $scope.setTab = setTab;
    $scope.setLetterFilter = setLetterFilter;
    $scope.doContactTask = doContactTask;

    (function init () {
      $scope.$bindToRoute({ expr: 'tab', param: 'peopleTab', format: 'raw', default: 'roles' });
    }());

    /**
     * Get selected contacts from the selection bar
     *
     * @param {string} tab tab
     * @returns {Array} selected contact
     */
    function getSelectedContacts (tab) {
      var idField = tab === 'roles' ? 'contact_id' : 'id';
      var recordsList = tab === 'roles' ? $scope.roles.fullRolesList : $scope.relations;
      var isCheckedSelectionMode = $scope[tab + 'SelectionMode'] === 'checked';
      var isAllSelectionMode = $scope[tab + 'SelectionMode'] === 'all';

      if (isCheckedSelectionMode) {
        return _(recordsList).filter({ checked: true }).map(idField)
          .uniq().value();
      } else if (isAllSelectionMode) {
        return _(recordsList).map(idField).uniq().compact().value();
      }

      return [];
    }

    /**
     * Sets selection mode
     *
     * @param {string} mode mode
     * @param {string} tab tab
     */
    function setSelectionMode (mode, tab) {
      $scope[tab + 'SelectionMode'] = mode;
    }

    /**
     * Sets selected tab
     *
     * @param {string} tab tab
     */
    function setTab (tab) {
      $scope.tab = tab;
    }

    /**
     * Filters result on the basis of letter clicked
     *
     * @param {string} letter letter
     * @param {string} tab tab
     */
    function setLetterFilter (letter, tab) {
      if ($scope[tab + 'AlphaFilter'] === letter) {
        $scope[tab + 'AlphaFilter'] = '';
      } else {
        $scope[tab + 'AlphaFilter'] = letter;
      }

      if (tab === 'roles') {
        $scope.roles.filterRoles($scope.rolesAlphaFilter, $scope.rolesFilter);
      } else {
        $scope.getRelations();
      }
    }

    /**
     * Update the contacts with the task
     *
     * @param {string} tab tab
     */
    function doContactTask (tab) {
      var task = $scope.contactTasks[$scope[tab + 'SelectedTask']];
      $scope[tab + 'SelectedTask'] = '';
      civicaseCrmLoadForm(civicaseCrmUrl(task.url, { cids: $scope.getSelectedContacts(tab).join(',') }))
        .on('crmFormSuccess', $scope.refresh)
        .on('crmFormSuccess', function () {
          $scope.refresh();
          if (tab === 'relations') {
            $scope.getRelations();
          }
        });
    }
  }
})(angular, CRM.$, CRM._);
