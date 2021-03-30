describe('Relationship Letter Filter', () => {
  let $controller, $rootScope, $scope;

  beforeEach(module('civicase'));

  beforeEach(inject(function (_$controller_, _$rootScope_) {
    $controller = _$controller_;
    $rootScope = _$rootScope_;

    $scope = $rootScope.$new();
  }));

  describe('on init', () => {
    beforeEach(() => {
      initController({
        $scope: $scope
      });
    });

    it('shows the letters as filters', () => {
      expect($scope.letters).toEqual(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z']);
    });
  });

  describe('when clicking on a letter filter', () => {
    describe('when the letter is clicked for the first time', () => {
      beforeEach(() => {
        initController({ $scope: $scope });

        $scope.setLetterFilter('Y');
      });

      it('filters with clicked letter', () => {
        expect($scope.$ctrl.ngModel.alpha).toBe('Y');
        expect($scope.$ctrl.ngModelCtrl.$setViewValue)
          .toHaveBeenCalledWith({ alpha: 'Y' });
      });
    });

    describe('when the letter is clicked for the second time', () => {
      beforeEach(() => {
        initController({ $scope: $scope });
        $scope.$ctrl.ngModel.alpha = 'Y';
        $scope.setLetterFilter('Y');
      });

      it('resets the filter', () => {
        expect($scope.$ctrl.ngModel.alpha).toBe('');
        expect($scope.$ctrl.ngModelCtrl.$setViewValue)
          .toHaveBeenCalledWith({ alpha: '' });
      });
    });
  });

  /**
   * Initializes the controller.
   *
   * @param {object} dependencies the list of dependencies to pass to the controller.
   */
  function initController (dependencies) {
    $controller('civicaseRelationshipLetterFilterController as $ctrl', dependencies);
    $scope.$ctrl = {
      ngModel: {},
      ngModelCtrl: {
        $setViewValue: jasmine.createSpy('$setViewValue')
      }
    };
  }
});
