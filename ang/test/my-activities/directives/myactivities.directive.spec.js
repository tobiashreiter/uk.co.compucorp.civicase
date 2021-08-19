(function ($, _) {
  describe('civicaseMyActivities', () => {
    var $controller, $rootScope, $scope, permissions;

    beforeEach(module('civicase.data', 'my-activities', 'civicase.templates'));

    beforeEach(inject((_$controller_, _$rootScope_) => {
      $controller = _$controller_;
      $rootScope = _$rootScope_;
      $scope = $rootScope.$new();
      $scope.filters = {};

      CRM.checkPerm.and.callFake(checkPermMock);

      permissions = {
        'access my cases and activities': true,
        'access all cases and activities': true
      };

      initController();
    }));

    describe('on init', () => {
      describe('visibility of cases activities', () => {
        describe('when both "my cases" and "all cases" permissions are not available', () => {
          beforeEach(() => {
            permissions = {
              'access my cases and activities': false,
              'access all cases and activities': false
            };

            initController();
          });

          it('hides the case activites', () => {
            expect($scope.displayOptions).toEqual({ include_case: false });
          });
        });

        describe('when only "my cases" permission is available', () => {
          beforeEach(() => {
            permissions = {
              'access my cases and activities': true,
              'access all cases and activities': false
            };

            initController();
          });

          it('shows the case activites', () => {
            expect($scope.displayOptions).toEqual({ include_case: true });
          });
        });

        describe('when only "all cases" permission is available', () => {
          beforeEach(() => {
            permissions = {
              'access my cases and activities': false,
              'access all cases and activities': true
            };

            initController();
          });

          it('shows the case activites', () => {
            expect($scope.displayOptions).toEqual({ include_case: true });
          });
        });

        describe('when both "my cases" and "all cases" permissions are available', () => {
          beforeEach(() => {
            permissions = {
              'access my cases and activities': true,
              'access all cases and activities': true
            };

            initController();
          });

          it('shows the case activites', () => {
            expect($scope.displayOptions).toEqual({ include_case: true });
          });
        });
      });

      it('shows the incomplete activites for current logged in user', () => {
        expect($scope.filters).toEqual({
          $contact_id: 203,
          '@involvingContact': 'myActivities',
          status_id: ['1', '4', '7', '9', '10']
        });
      });
    });

    /**
     * Initializes the my activity controller.
     */
    function initController () {
      $controller('CivicaseMyActivitiesController', {
        $scope: $scope
      });
    }

    /**
     * Mock function to determines if the user has permission
     * using the permissions global object.
     *
     * @param {string} permissionName the name of the permission.
     * @returns {boolean} true if the user has the given permission.
     */
    function checkPermMock (permissionName) {
      return permissions[permissionName];
    }
  });
})(CRM.$, CRM._);
