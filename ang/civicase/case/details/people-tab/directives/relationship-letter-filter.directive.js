(function (angular) {
  var module = angular.module('civicase');

  module.directive('civicaseRelationshipLetterFilter', function () {
    return {
      restrict: 'E',
      templateUrl: '~/civicase/case/details/people-tab/directives/relationship-letter-filter.directive.html',
      controller: civicaseRelationshipLetterFilterController,
      scope: {
        model: '=',
        clickListener: '&'
      }
    };
  });

  module.controller(
    'civicaseRelationshipLetterFilterController',
    civicaseRelationshipLetterFilterController
  );

  /**
   * civicaseRelationshipLetterFilter Controller
   *
   * @param {object} $scope $scope
   */
  function civicaseRelationshipLetterFilterController ($scope) {
    $scope.letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
    $scope.setLetterFilter = setLetterFilter;

    /**
     * Filters result on the basis of letter clicked
     *
     * @param {string} letter letter
     */
    function setLetterFilter (letter) {
      $scope.model.alpha = $scope.model.alpha === letter ? '' : letter;

      $scope.clickListener({ filter: $scope.model });
    }
  }
})(angular);
