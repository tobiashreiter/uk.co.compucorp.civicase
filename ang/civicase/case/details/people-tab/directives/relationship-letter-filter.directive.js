(function (angular) {
  var module = angular.module('civicase');

  module.directive('civicaseRelationshipLetterFilter', function () {
    return {
      restrict: 'E',
      templateUrl: '~/civicase/case/details/people-tab/directives/relationship-letter-filter.directive.html',
      controller: civicaseRelationshipLetterFilterController,
      require: { ngModelCtrl: 'ngModel' },
      controllerAs: '$ctrl',
      bindToController: true,
      scope: {
        ngModel: '<'
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
      var letterValue = $scope.$ctrl.ngModel.alpha === letter ? '' : letter;

      $scope.$ctrl.ngModel.alpha = letterValue;
      // according to angular docs, making a copy is necessary for objects,
      // otherwise changes wont be reflected
      $scope.$ctrl.ngModelCtrl.$setViewValue(angular.copy($scope.$ctrl.ngModel));
    }
  }
})(angular);
