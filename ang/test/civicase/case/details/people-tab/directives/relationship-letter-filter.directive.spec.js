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
    var clickListener;

    describe('when the letter is clicked for the first time', () => {
      beforeEach(() => {
        clickListener = jasmine.createSpy('clickListener');
        $scope.model = {};
        $scope.clickListener = clickListener;

        initController({ $scope: $scope });

        $scope.setLetterFilter('Y');
      });

      it('filters with clicked letter', () => {
        expect($scope.model.alpha).toBe('Y');
        expect(clickListener).toHaveBeenCalledWith({ filter: { alpha: 'Y' } });
      });
    });

    describe('when the letter is clicked for the second time', () => {
      beforeEach(() => {
        clickListener = jasmine.createSpy('clickListener');
        $scope.model = { alpha: 'Y' };
        $scope.clickListener = clickListener;

        initController({ $scope: $scope });

        $scope.setLetterFilter('Y');
      });

      it('resets the filter', () => {
        expect($scope.model.alpha).toBe('');
        expect(clickListener).toHaveBeenCalledWith({ filter: { alpha: '' } });
      });
    });
  });

  /**
   * Initializes the controller.
   *
   * @param {object} dependencies the list of dependencies to pass to the controller.
   */
  function initController (dependencies) {
    $controller('civicaseRelationshipLetterFilterController', dependencies);
  }
});
