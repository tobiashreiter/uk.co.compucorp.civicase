(function (_) {
  describe('civicaseFileFilter', function () {
    var $controller, $rootScope, $scope;

    beforeEach(module('civicase'));

    beforeEach(inject(function (_$controller_, _$rootScope_) {
      $controller = _$controller_;
      $rootScope = _$rootScope_;
    }));

    describe('when filtering by tags', function () {
      beforeEach(function () {
        initController();
      });

      describe('when a single tag is selected', () => {
        beforeEach(function () {
          $scope.customFilters.tag_id = ['1'];
          $scope.$digest();
        });

        it('filters by the selected tags', () => {
          expect($scope.fileFilterParams.tag_id).toEqual('1');
          expect($scope.refresh).toHaveBeenCalled();
        });
      });

      describe('when multiple tags are selected', () => {
        beforeEach(function () {
          $scope.customFilters.tag_id = ['1', '2'];
          $scope.$digest();
        });

        it('filters by all the selected tags', () => {
          expect($scope.fileFilterParams.tag_id).toEqual({ IN: ['1', '2'] });
          expect($scope.refresh).toHaveBeenCalled();
        });
      });

      describe('when no tags are selected', () => {
        beforeEach(function () {
          $scope.customFilters.tag_id = [];
          $scope.$digest();
        });

        it('does not filter by tags', () => {
          expect($scope.fileFilterParams.tag_id).toBeUndefined();
          expect($scope.refresh).toHaveBeenCalled();
        });
      });
    });

    /**
     * Initialise controller
     */
    function initController () {
      $scope = $rootScope.$new();
      $scope.fileFilterParams = {};
      $scope.refresh = jasmine.createSpy('refresh');

      $controller('civicaseFileFilterController', {
        $scope: $scope
      });
    }
  });
})(CRM._);
