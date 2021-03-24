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
    // The ts() and hs() functions help load strings for this module.
    var clients = _.indexBy($scope.item.client, 'contact_id');
    var item = $scope.item;
    var relTypes = RelationshipType.getAll();

    $scope.relations = [];
    $scope.relationsPageObj = { total: 0, pageSize: 25, page: 1 };
    $scope.relationsAlphaFilter = '';
    $scope.relationsSelectionMode = '';
    $scope.relationsSelectedTask = '';
    $scope.isRelationshipLoading = true;
    $scope.getRelations = getRelations;

    (function init () {
      $scope.$watch('relationsPageObj.page', function () {
        $scope.getRelations();
      });
    }());

    /**
     * Updates the case relationship list
     */
    function getRelations () {
      var params = {
        case_id: item.id,
        sequential: 1,
        return: ['display_name', 'phone', 'email']
      };
      if ($scope.relationsAlphaFilter) {
        params.display_name = $scope.relationsAlphaFilter;
      }
      civicaseCrmApi([
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
