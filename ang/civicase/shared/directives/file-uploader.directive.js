(function (angular, $, _) {
  var module = angular.module('civicase');

  module.directive('civicaseFileUploader', function () {
    return {
      restrict: 'A',
      templateUrl: '~/civicase/shared/directives/file-uploader.directive.html',
      controller: 'civicaseFilesUploaderController',
      scope: {
        ctx: '=civicaseFileUploader',
        onUpload: '@'
      }
    };
  });

  module.controller('civicaseFilesUploaderController', civicaseFilesUploaderController);
  /**
   * @param {object} $scope controllers scope object
   * @param {object} civicaseCrmApi service to access civicrm api
   * @param {object} crmBlocker crm blocker service
   * @param {object} crmStatus crm status service
   * @param {Function} FileUploader file uploader service
   * @param {object} $q angular queue service
   * @param {object} $timeout timeout service
   * @param {Function} civicaseCrmUrl crm url service.
   */
  function civicaseFilesUploaderController ($scope, civicaseCrmApi, crmBlocker,
    crmStatus, FileUploader, $q, $timeout, civicaseCrmUrl) {
    $scope.block = crmBlocker();
    $scope.ts = CRM.ts('uk.co.compucorp.civicase'); // NO OBVIOUS CHANGE
//    $scope.ts = CRM.ts('civicase');
    $scope.uploader = createUploader();
    $scope.tags = { all: [], selected: [] };

    $scope.deleteActivity = deleteActivity;
    $scope.isUploadActive = isUploadActive;
    $scope.saveActivity = saveActivity;

    (function init () {
      initActivity();
      getTags()
        .then(function (tags) {
          $scope.tags.all = tags;
        });

      $scope.$watchCollection('ctx.id', initActivity);
    }());

    /**
     * Returns Uploader Object
     *
     * @returns {object} File Uploader object
     */
    function createUploader () {
      return new FileUploader({
        url: civicaseCrmUrl('civicrm/ajax/attachment'),
        onAfterAddingFile: function onAfterAddingFile (item) {
          item.crmData = { description: '' };
        },
        onSuccessItem: function onSuccessItem (item, response, status, headers) {
          var ok = status === 200 && _.isObject(response) && response.file && (response.file.is_error === 0);

          if (!ok) {
            this.onErrorItem(item, response, status, headers);
          }
        },
        onErrorItem: function onErrorItem (item, response, status, headers) {
          var msg = (response && response.file && response.file.error_message) ? response.file.error_message : $scope.ts('Unknown error');

          CRM.alert(item.file.name + ' - ' + msg, $scope.ts('Attachment failed'), 'error');
        },
        // Like uploadAll(), but it returns a promise.
        uploadAllWithPromise: function () {
          var dfr = $q.defer();
          var self = this;

          self.onCompleteAll = function () {
            dfr.resolve();
            self.onCompleteAll = null;
          };
          self.uploadAll();
          return dfr.promise;
        }
      });
    }

    /**
     * Deletes Activity
     */
    function deleteActivity () {
      $scope.uploader.clearQueue();
      initActivity();
    }

    /**
     * Checks if uploading is in progress
     *
     * @returns {boolean} if uploading is in progress
     */
    function isUploadActive () {
      return ($scope.uploader.queue.length > 0);
    }

    /**
     * Saves actvitiy
     *
     * @returns {Promise} promise
     */
    function saveActivity () {
      if ($scope.activity.activity_date_time === '') {
        delete $scope.activity.activity_date_time;
      }

      _.each($scope.uploader.getNotUploadedItems(), function (item) {
        validateFileSize(item);
      });

      var promise = civicaseCrmApi('Activity', 'create', $scope.activity)
        .then(function (activity) {
          saveTags(activity.id);

          var target = { entity_table: 'civicrm_activity', entity_id: activity.id };

          _.each($scope.uploader.getNotUploadedItems(), function (item) {
            item.formData = [
              _.extend({ crm_attachment_token: CRM.crmAttachment.token }, target, item.crmData)
            ];
          });
          return $scope.uploader.uploadAllWithPromise();
        }).then(function () {
          return delayPromiseBy(1000); // Let the user absorb what happened.
        }).then(function () {
          $scope.uploader.clearQueue();
          $scope.fileUploadForm.$setPristine();
          initActivity();
          if ($scope.onUpload) {
            $scope.$parent.$eval($scope.onUpload);
          }
        });

      return $scope.block(crmStatus({
        start: $scope.ts('Uploading...'),
        success: $scope.ts('Uploaded')
      }, promise));
    }

    /**
     * Validates file size before adding to Uploader Queue
     *
     * @param {string} item selected file object
     * @returns {boolean} true if file size is valid
     * @throws Error for empty file
     */
    function validateFileSize (item) {
      if (item.file.size <= 0) {
        const msg = 'Your file(s) cannot be uploaded because one or more of your files is empty. Your file(s) will not be uploaded. Check the contents of your file(s) and then try again.';
        CRM.alert(msg, $scope.ts('Attachment failed'), 'error');
        throw new Error(msg, { cause: 'Invalid size' });
      }
      return true;
    }

    /**
     * @param {number} activityID activity id
     * @returns {Promise} promise
     */
    function saveTags (activityID) {
      return civicaseCrmApi('EntityTag', 'createByQuery', {
        entity_table: 'civicrm_activity',
        tag_id: $scope.tags.selected,
        entity_id: activityID
      });
    }

    /**
     * Initialise Activity
     */
    function initActivity () {
      $scope.tags.selected = [];
      $scope.activity = {
        case_id: $scope.ctx.id,
        activity_type_id: 'File Upload',
        subject: ''
      };
    }

    /**
     * TODO: Test interrupted transfer.
     *
     * @param {number} delay timedelay in millisecond
     * @returns {object} Promise
     */
    function delayPromiseBy (delay) {
      var dfr = $q.defer();
      $timeout(function () { dfr.resolve(); }, delay);
      return dfr.promise;
    }

    /**
     * Get the tags for Activities from API end point
     *
     * @returns {Promise} api call promise
     */
    function getTags () {
      return civicaseCrmApi('Tag', 'get', {
        sequential: 1,
        used_for: { LIKE: '%civicrm_activity%' },
        options: { limit: 0, sort: 'name ASC' }
      }).then(function (data) {
        return data.values;
      });
    }
  }
})(angular, CRM.$, CRM._);
