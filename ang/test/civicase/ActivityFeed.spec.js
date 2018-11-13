/* eslint-env jasmine */

(function (_) {
  describe('civicaseActivityFeed', function () {
    describe('Activity Feed Controller', function () {
      var $provide, $controller, $rootScope, $scope, $q, crmApi, CaseTypes;

      beforeEach(module('civicase', 'civicase.data', function (_$provide_) {
        $provide = _$provide_;
      }));

      beforeEach(inject(function () {
        $provide.factory('crmThrottle', function () {
          var crmThrottle = jasmine.createSpy('crmThrottle');

          crmThrottle.and.callFake(function (callable) {
            callable();

            return $q.resolve([{
              acts: { values: [] },
              all: { values: [] }
            }]);
          });

          return crmThrottle;
        });
      }));

      beforeEach(inject(function (_$controller_, _$rootScope_, _$q_, _CaseTypes_, _crmApi_) {
        $controller = _$controller_;
        $rootScope = _$rootScope_;
        $q = _$q_;
        CaseTypes = _CaseTypes_;

        $scope = $rootScope.$new();
        $scope.$bindToRoute = jasmine.createSpy('$bindToRoute');
        crmApi = _crmApi_;
      }));

      describe('loadActivities', function () {
        describe('when filtered by activity set and activity id', function () {
          var expectedActivityTypeIDs = [];

          beforeEach(function () {
            crmApi.and.returnValue($q.resolve({acts: {}}));
            initController();
            $scope.filters.activitySet = CaseTypes.get()['1'].definition.activitySets[0].name;
            $scope.filters.activity_type_id = '5';
            $scope.$digest();

            _.each(CaseTypes.get()['1'].definition.activitySets[0].activityTypes, function (activityTypeFromSet) {
              expectedActivityTypeIDs.push(_.findKey(CRM.civicase.activityTypes, function (activitySet) {
                return activitySet.name === activityTypeFromSet.name;
              }));
            });
            expectedActivityTypeIDs.push($scope.filters.activity_type_id);
          });

          it('filters by the activities of the selected activity set and the activity id', function () {
            var args = crmApi.calls.mostRecent().args[0].acts[2].activity_type_id;
            expect(args).toEqual({ IN: expectedActivityTypeIDs });
          });
        });
      });

      /**
       * Initializes the activity feed controller.
       */
      function initController () {
        $scope.caseTypeId = '1';
        $scope.filters = {};
        $scope.displayOptions = {};
        $scope.params = {
          displayOptions: 1
        };

        $controller('civicaseActivityFeedController', {
          $scope: $scope
        });
      }
    });
  });

  describe('civicaseActivityDetailsAffix', function () {
    var element, $compile, $document, $rootScope, $timeout, scope, affixReturnValue, affixOriginalFunction;

    beforeEach(module('civicase'));

    beforeEach(inject(function (_$compile_, _$rootScope_, _$timeout_, _$document_) {
      $compile = _$compile_;
      $rootScope = _$rootScope_;
      scope = $rootScope.$new();
      $timeout = _$timeout_;
      $document = _$document_;
    }));

    beforeEach(function () {
      CRM.$('<div class="civicase__activity-feed__list-container"></div>').appendTo('body');
      CRM.$('<div class="civicase__activity-filter"></div>').appendTo('body');
      CRM.$('<div id="toolbar"></div>').appendTo('body');
      element = $compile(angular.element('<div civicase-activity-details-affix><div class="civicase__activity-panel"></div></div>'))(scope);
    });

    beforeEach(function () {
      affixOriginalFunction = CRM.$.fn.affix;
      CRM.$.fn.affix = jasmine.createSpy('affix');
      affixReturnValue = jasmine.createSpyObj('affix', ['on']);
      affixReturnValue.on.and.returnValue(affixReturnValue);
      CRM.$.fn.affix.and.returnValue(affixReturnValue);
    });

    afterEach(function () {
      CRM.$.fn.affix = affixOriginalFunction;
    });

    describe('when initialised', function () {
      var $activityDetailsPanel, $filter, $feedListContainer, $tabs, $toolbarDrawer;

      beforeEach(function () {
        $activityDetailsPanel = element.find('.civicase__activity-panel');
        $filter = CRM.$('.civicase__activity-filter');
        $feedListContainer = CRM.$('.civicase__activity-feed__list-container');
        $tabs = CRM.$('.civicase__dashboard').length > 0 ? CRM.$('.civicase__dashboard__tab-container ul.nav') : CRM.$('.civicase__case-body_tab');
        $toolbarDrawer = CRM.$('#toolbar');

        $timeout.flush();
      });

      it('applies static positioning to the activity details', function () {
        expect(element.find('.civicase__activity-panel').affix).toHaveBeenCalledWith({
          offset: {
            top: $activityDetailsPanel.offset().top - ($toolbarDrawer.height() + $tabs.height() + $filter.height()),
            bottom: CRM.$($document).height() - ($feedListContainer.offset().top + $feedListContainer.height())
          }
        });
      });
    });

    describe('when static positioning is applied to the Activity Panel', function () {
      var $activityDetailsPanel, $filter, $tabs, $toolbarDrawer;

      beforeEach(function () {
        $activityDetailsPanel = element.find('.civicase__activity-panel');
        $filter = CRM.$('.civicase__activity-filter');
        $tabs = CRM.$('.civicase__dashboard').length > 0 ? CRM.$('.civicase__dashboard__tab-container ul.nav') : CRM.$('.civicase__case-body_tab');
        $toolbarDrawer = CRM.$('#toolbar');

        $timeout.flush();

        $activityDetailsPanel.trigger('affixed.bs.affix');
      });

      it('sets the top positioning', function () {
        expect($activityDetailsPanel.css('top')).toBe(($toolbarDrawer.height() + $tabs.height() + $filter.height()) + 'px');
      });

      it('sets the padding-top', function () {
        expect($activityDetailsPanel.css('padding-top')).toBe('32px');
      });

      it('sets the width', function () {
        expect($activityDetailsPanel.width()).toBe(element.width());
      });
    });

    describe('when static positioning is removed to the Activity Panel', function () {
      var $activityDetailsPanel;

      beforeEach(function () {
        $activityDetailsPanel = element.find('.civicase__activity-panel');

        $timeout.flush();

        $activityDetailsPanel.trigger('affixed-top.bs.affix');
      });

      it('resets the top positioning', function () {
        expect($activityDetailsPanel.css('top')).toBe('auto');
      });

      it('resets the padding-top', function () {
        expect($activityDetailsPanel.css('padding-top')).toBe('0px');
      });

      it('resets the width', function () {
        expect($activityDetailsPanel.width()).toBe(0);
      });
    });
  });
})(CRM._);
