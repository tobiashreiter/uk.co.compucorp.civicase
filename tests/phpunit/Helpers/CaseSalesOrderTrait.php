<?php

use Civi\Api4\CaseSalesOrder;
use Civi\Api4\OptionValue;
use CRM_Civicase_Test_Fabricator_Case as CaseFabricator;
use CRM_Civicase_Test_Fabricator_CaseType as CaseTypeFabricator;
use CRM_Civicase_Test_Fabricator_Contact as ContactFabricator;
use CRM_Civicase_Test_Fabricator_Product as ProductFabricator;

/**
 * Case sales order helper trait.
 */
trait Helpers_CaseSalesOrderTrait {

  /**
   * Returns list of available statuses.
   *
   * @return array
   *   Array of sales order statuses
   */
  public function getCaseSalesOrderStatus() {
    $salesOrderStatus = OptionValue::get()
      ->addSelect('id', 'value', 'name', 'label')
      ->addWhere('option_group_id:name', '=', 'case_sales_order_status')
      ->execute();

    return $salesOrderStatus;
  }

  /**
   * Returns list of available statuses.
   *
   * @return array
   *   Array of sales order invoicing statuses
   */
  public function getCaseSalesOrderInvoicingStatus() {
    return OptionValue::get()
      ->addSelect('id', 'value', 'name', 'label')
      ->addWhere('option_group_id:name', '=', 'case_sales_order_invoicing_status')
      ->execute()
      ->getArrayCopy();
  }

  /**
   * Returns list of available statuses.
   *
   * @return array
   *   Array of sales order payment statuses
   */
  public function getCaseSalesOrderPaymentStatus() {
    return OptionValue::get()
      ->addSelect('id', 'value', 'name', 'label')
      ->addWhere('option_group_id:name', '=', 'case_sales_order_payment_status')
      ->execute()
      ->getArrayCopy();
  }

  /**
   * Returns fabricated case sales order data.
   *
   * @param array $default
   *   Default value.
   *
   * @return array
   *   Key-Value pair of a case sales order fields and values
   */
  public function getCaseSalesOrderData(array $default = []) {
    $client = ContactFabricator::fabricate();
    $caseType = CaseTypeFabricator::fabricate();
    $case = CaseFabricator::fabricate(
      [
        'case_type_id' => $caseType['id'],
        'contact_id' => $client['id'],
        'creator_id' => $client['id'],
      ]
    );

    return array_merge([
      'client_id' => $client['id'],
      'owner_id' => $client['id'],
      'case_id' => $case['id'],
      'currency' => 'GBP',
      'status_id' => $this->getCaseSalesOrderStatus()[0]['value'],
      'invoicing_status_id' => $this->getCaseSalesOrderInvoicingStatus()[0]['value'],
      'payment_status_id' => $this->getCaseSalesOrderPaymentStatus()[0]['value'],
      'description' => 'test',
      'notes' => 'test',
      'total_before_tax' => 0,
      'total_after_tax' => 0,
      'quotation_date' => '2022-08-09',
      'items' => [],
    ], $default);
  }

  /**
   * Returns fabricated case sales order line data.
   *
   * @param array $default
   *   Default value.
   *
   * @return array
   *   Key-Value pair of a case sales order line item fields and values
   */
  public function getCaseSalesOrderLineData(array $default = []) {
    $product = ProductFabricator::fabricate();
    $quantity = rand(2, 9);
    $unitPrice = rand(50, 1000);

    return array_merge([
      'financial_type_id' => 1,
      'product_id' => $product['id'],
      'item_description' => 'test',
      'quantity' => $quantity,
      'unit_price' => $unitPrice,
      'tax_rate' => NULL,
      'discounted_percentage' => NULL,
      'subtotal_amount' => $quantity * $unitPrice,
    ], $default);
  }

  /**
   * Creates case sales order.
   *
   * @param array $params
   *   Extra paramters.
   *
   * @return array
   *   Created case sales order
   */
  public function createCaseSalesOrder(array $params = []): array {
    $salesOrder = $this->getCaseSalesOrderData();
    $salesOrder['items'][] = $this->getCaseSalesOrderLineData();
    $salesOrder['items'][] = $this->getCaseSalesOrderLineData();

    if (!empty($params['items']['discounted_percentage'])) {
      $salesOrder['items'][0]['discounted_percentage'] = $params['items']['discounted_percentage'];
    }

    if (!empty($params['items']['tax_rate'])) {
      $salesOrder['items'][0]['tax_rate'] = $params['items']['tax_rate'];
    }

    $salesOrder['id'] = CaseSalesOrder::save()
      ->addRecord($salesOrder)
      ->execute()
      ->jsonSerialize()[0]['id'];

    return $salesOrder;
  }

}
