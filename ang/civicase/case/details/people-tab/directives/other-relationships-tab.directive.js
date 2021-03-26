(function (angular, $, _) {
  var module = angular.module('civicase');

  module.directive('civicaseOtherRelationshipsTab', function () {
    return {
      restrict: 'E',
      templateUrl: '~/civicase/case/details/people-tab/directives/other-relationships-tab.directive.html',
      controller: civicaseOtherRelationshipsTabController
    };
  });

  module.controller('civicaseOtherRelationshipsTabController', civicaseOtherRelationshipsTabController);

  /**
   * @param {object} $scope $scope
   * @param {object} civicaseCrmApi service to interact with civicrm api
   * @param {object} RelationshipType RelationshipType
   */
  function civicaseOtherRelationshipsTabController ($scope, civicaseCrmApi,
    RelationshipType) {
    var clients = _.indexBy($scope.item.client, 'contact_id');
    var item = $scope.item;
    var relTypes = RelationshipType.getAll();

    $scope.relations = [];
    $scope.relationsPageObj = { total: 0, pageSize: 25, page: 1 };
    $scope.relationsFilter = { alpha: '' };
    $scope.relationsSelectionMode = '';
    $scope.relationsSelectedTask = '';
    $scope.isRelationshipLoading = true;
    $scope.getRelations = getRelations;
    $scope.goToPage = goToPage;
    $scope.setLetterFilter = setLetterFilter;

    (function init () {
      $scope.getRelations($scope.relationsFilter);
    }());

    /**
     * @param {string} pageNumber the page number to navigate to.
     */
    function goToPage (pageNumber) {
      $scope.relationsPageObj.page = pageNumber;

      getRelations($scope.relationsFilter);
    }

    /**
     * @param {object} filter filter object
     */
    function setLetterFilter (filter) {
      getRelations(filter)
        .then(function () {
          goToPage(1);
        });
    }

    /**
     * Updates the case relationship list
     *
     * @param {object} filter filter object
     * @returns {Promise} Promise
     */
    function getRelations (filter) {
      var params = {
        case_id: item.id,
        sequential: 1,
        return: ['display_name', 'phone', 'email']
      };
      if (filter.alpha) {
        params.display_name = filter.alpha;
      }
      return civicaseCrmApi([
        ['Case', 'getrelations', _.extend(params, {
          options: {
            limit: 25,
            offset: $scope.relationsPageObj.pageSize * ($scope.relationsPageObj.page - 1)
          }
        })],
        ['Case', 'getrelationscount', params]
      ]).then(function (results) {
        $scope.relations = _.each(results[0].values, function (rel) {
          var relType = relTypes[rel.relationship_type_id];
          rel.relation = relType['label_' + rel.relationship_direction];
          rel.client = clients[rel.client_contact_id].display_name;
        });
        $scope.relationsPageObj.total = results[1].count;
        $scope.isRelationshipLoading = false;
      });
    }
  }
})(angular, CRM.$, CRM._);
