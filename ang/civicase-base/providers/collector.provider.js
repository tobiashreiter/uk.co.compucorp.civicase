(function (angular, $, _, CRM, civicaseBaseSettings) {
  var module = angular.module('civicase-base');

  module.provider('Collector', CollectorProvider);

  /**
   * Collector Service Provider
   */
  function CollectorProvider () {
    var collectorInstanceMapping = civicaseBaseSettings.collectorInstanceMapping;
    var collectorInstanceType = civicaseBaseSettings.collectorInstanceType;
    var allCollectors = civicaseBaseSettings.collectors;

    this.$get = $get;
    this.findAllByInstance = findAllByInstance;
    this.getCollectorInstance = getCollectorInstance;
    this.getAll = getAll;
    this.isInstance = isInstance;

    /**
     * Returns the collector service.
     *
     * @returns {object} the collector service.
     */
    function $get () {
      return {
        getAll: getAll,
        findAllByInstance: findAllByInstance,
        getCollectorInstance: getCollectorInstance,
        isInstance: isInstance
      };
    }

    /**
     * Find all case type categories belonging to sent instance name
     *
     * @param {string} instanceName name of the instance
     * @returns {object[]} list of case type categories matching the sent instance
     */
    function findAllByInstance (instanceName) {
      return _.filter(getAll(), function (collector) {
        return isInstance(collector.value, instanceName);
      });
    }

    /**
     * Check if the sent collector is part of the sent instance
     *
     * @param {string} collectorId collector name or id
     * @param {string} instanceName instance name
     * @returns {boolean} if the sent collector is part of the sent instance
     */
    function isInstance (collectorId, instanceName) {
      var collectorObject = findById(collectorId);

      if (!collectorObject) {
        return;
      }

      var collector = _.find(collectorInstanceMapping, function (instanceMap) {
        return instanceMap.collector_id === collectorObject.value;
      });

      if (!collector) {
        return;
      }

      var instanceID = _.find(collectorInstanceType, function (instance) {
        return instance.name === instanceName;
      }).value;

      return collector.instance_id === instanceID;
    }

    /**
     * Get instance object for the sent collector value
     *
     * @param {string} collectorValue collector value
     * @returns {boolean} if the sent collector is part of the sent instance
     */
    function getCollectorInstance (collectorValue) {
      var instanceID = _.find(collectorInstanceMapping, function (instanceMap) {
        return instanceMap.collector_id === collectorValue;
      }).instance_id;

      return _.find(collectorInstanceType, function (instance) {
        return instance.value === instanceID;
      });
    }

    /**
     * Returns all collectors.
     *
     * @returns {object[]} all the case type categories.
     */
    function getAll () {
      return allCollectors;
    }
  }
})(angular, CRM.$, CRM._, CRM, CRM['civicase-base']);
