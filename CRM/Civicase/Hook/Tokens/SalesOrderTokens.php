<?php

use Civi\Api4\CaseSalesOrder;
use Civi\Token\AbstractTokenSubscriber;
use Civi\Token\Event\TokenRegisterEvent;
use Civi\Token\Event\TokenValueEvent;
use Civi\Token\TokenRow;
use CRM_Civicase_ExtensionUtil as E;

/**
 * Sales Order Specific Tokens.
 */
class CRM_Civicase_Hook_Tokens_SalesOrderTokens extends AbstractTokenSubscriber {

  /**
   * {@inheritDoc}
   */
  public function evaluateToken(TokenRow $row, $entity, $field, $prefetch = NULL) {
  }

  /**
   * Key for token.
   *
   * @var string
   */
  const TOKEN = 'sales_order';

  /**
   * Sets sales order available tokens.
   *
   * @param \Civi\Token\Event\TokenRegisterEvent $e
   *   Register Token Event.
   */
  public static function listSalesOrderTokens(TokenRegisterEvent $e) {
    $fields = CaseSalesOrder::getFields()->execute()->jsonSerialize();
    foreach ($fields as $field) {
      $label = E::ts('Quotation ' . ucwords(str_replace("_", " ", $field['name'])));
      $e->entity(self::TOKEN)->register($field['name'], $label);
    }
  }

  /**
   * Evaluates Token values.
   *
   * @param \Civi\Token\Event\TokenValueEvent $e
   *   TokenValue Event.
   */
  public static function evaluateSalesOrderTokens(TokenValueEvent $e) {
    $context = $e->getTokenProcessor()->context;

    if (array_key_exists('schema', $context) && in_array('salesOrderId', $context['schema'])) {
      foreach ($e->getRows() as $row) {
        if (!empty($row->context['salesOrderId'])) {
          $salesOrderId = $row->context['salesOrderId'];

          $caseSalesOrder = CaseSalesOrder::get(FALSE)
            ->addWhere('id', '=', $salesOrderId)
            ->execute()
            ->first();
          foreach ($caseSalesOrder as $key => $value) {
            try {
              $row->tokens(self::TOKEN, $key, $value ?? '');
            }
            catch (\Throwable $th) {
              \Civi::log()->error(
                'Error resolving token: ' . self::TOKEN . '.' . $key,
                [
                  'context' => [
                    'backtrace' => $th->getTraceAsString(),
                    'message' => $th->getMessage(),
                  ],
                ]
              );
            }
          }
        }
      }
    }

  }

}
