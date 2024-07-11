<?php

use CRM_Civicase_ExtensionUtil as E;
use CRM_Civicase_Service_CaseSalesOrderLineItemsGenerator as salesOrderlineItemGenerator;

/**
 * Adds the neccessary script to get sales order line items.
 */
class CRM_Civicase_Hook_BuildForm_AddSalesOrderLineItemsToContribution {

  /**
   * Populates the contribution form if triggered from sales order.
   *
   * @param CRM_Core_Form $form
   *   Form object class.
   * @param string $formName
   *   Form name.
   */
  public function run(CRM_Core_Form &$form, $formName) {
    $salesOrderId = CRM_Utils_Request::retrieve('sales_order', 'Integer');
    $status = CRM_Utils_Request::retrieve('sales_order_status_id', 'Integer');
    $toBeInvoiced = CRM_Utils_Request::retrieve('to_be_invoiced', 'String');
    $percentValue = CRM_Utils_Request::retrieve('percent_value', 'Float');

    if (!$this->shouldRun($form, $formName, $salesOrderId)) {
      return;
    }
    $lineItemGenerator = new salesOrderlineItemGenerator($salesOrderId, $toBeInvoiced, $percentValue);
    $lineItems = $lineItemGenerator->generateLineItems();

    CRM_Core_Resources::singleton()
      ->addScriptFile(E::LONG_NAME, 'js/sales-order-contribution.js')
      ->addVars(E::LONG_NAME, [
        'sales_order' => $salesOrderId,
        'sales_order_status_id' => $status,
        'to_be_invoiced' => $toBeInvoiced,
        'percent_value' => $percentValue,
        'line_items' => json_encode($lineItems),
        'quotation_custom_field' => CRM_Core_BAO_CustomField::getCustomFieldID('Quotation', 'Opportunity_Details', TRUE),
        'case_custom_field' => CRM_Core_BAO_CustomField::getCustomFieldID('Case_Opportunity', 'Opportunity_Details', TRUE),
      ]);
  }

  /**
   * Determines if the hook will run.
   *
   * This hook is only valid for the Case form.
   *
   * The civicase client id parameter must be defined.
   *
   * @param CRM_Core_Form $form
   *   Form class.
   * @param string $formName
   *   Form Name.
   * @param int|null $salesOrderId
   *   Sales Order ID.
   */
  public function shouldRun(CRM_Core_Form $form, string $formName, ?int $salesOrderId) {
    return $formName === 'CRM_Contribute_Form_Contribution'
      && $form->_action == CRM_Core_Action::ADD
      && !empty($salesOrderId);
  }

}
