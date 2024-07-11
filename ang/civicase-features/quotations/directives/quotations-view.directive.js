(function (angular, _) {
  var module = angular.module('civicase-features');

  module.directive('quotationsView', function () {
    return {
      restrict: 'E',
      controller: 'quotationsViewController',
      templateUrl: '~/civicase-features/quotations/directives/quotations-view.directive.html',
      scope: {
        salesOrderId: '='
      }
    };
  });

  module.controller('quotationsViewController', quotationsViewController);

  /**
   * @param {object} crmApi4 api V4 service
   * @param {object} $scope the controller scope
   * @param {object} CaseUtils case utility service
   * @param {object} CurrencyCodes CurrencyCodes service
   */
  function quotationsViewController (crmApi4, $scope, CaseUtils, CurrencyCodes) {
    $scope.taxRates = [];
    $scope.currencySymbol = '';
    $scope.dashboardLink = '#';
    $scope.salesOrder = {
      total_before_tax: 0,
      total_after_tax: 0
    };
    $scope.hasCase = false;
    $scope.getContactLink = getContactLink;

    (function init () {
      if ($scope.salesOrderId) {
        getSalesOrderAndLineItems();
      }
    })();

    /**
     * Retrieves the sales order and its line items from API
     */
    function getSalesOrderAndLineItems () {
      crmApi4('CaseSalesOrder', 'get', {
        select: ['*', 'case_sales_order_line.*', 'client_id.display_name', 'owner_id.display_name', 'case_id.case_type_id:label', 'case_id.subject', 'status_id:label', 'invoicing_status_id:label', 'payment_status_id:label'],
        where: [['id', '=', $scope.salesOrderId]],
        limit: 1,
        chain: {
          items: ['CaseSalesOrderLine', 'get', {
            where: [['sales_order_id', '=', '$id']],
            select: ['*', 'product_id.name', 'financial_type_id.name']
          }],
          computedRates: ['CaseSalesOrder', 'computeTotal', { lineItems: '$items' }],
          totalAmountInvoiced: ['CaseSalesOrder', 'computeTotalAmountInvoiced', { salesOrderId: '$id' }],
          totalAmountPaid: ['CaseSalesOrder', 'computeTotalAmountPaid', { salesOrderId: '$id' }]
        }
      }).then(async function (caseSalesOrders) {
        if (Array.isArray(caseSalesOrders) && caseSalesOrders.length > 0) {
          $scope.salesOrder = caseSalesOrders.pop();
          $scope.salesOrder.taxRates = $scope.salesOrder.computedRates[0].taxRates;
          $scope.currencySymbol = CurrencyCodes.getSymbol($scope.salesOrder.currency);
          $scope.salesOrder.quotation_date = CRM.utils.formatDate($scope.salesOrder.quotation_date);
          if (!$scope.salesOrder.case_id) {
            return;
          }
          $scope.hasCase = true;
          CaseUtils.getDashboardLink($scope.salesOrder.case_id).then(link => {
            $scope.dashboardLink = `${link}&focus=1&tab=Quotations`;
          });
        }
      });
    }

    /**
     * Returns link to the contact dashboard
     *
     * @param {number} id the contact ID
     *
     * @returns {string} dashboard link
     */
    function getContactLink (id) {
      return CRM.url(`/contact/view?reset=1&cid=${id}`);
    }
  }
})(angular, CRM._);
