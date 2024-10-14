<?php

use Civi\Utils\CurrencyUtils;
use CRM_Civicase_Service_CaseTypeCategoryFeatures as CaseTypeCategoryFeatures;
use Civi\Api4\OptionValue;

/**
 * Option factory class for Civicase features AngularJS module
 */
class CRM_Civicase_FeaturesAngular {

  private static $options;

  /**
   * @return string[]
   */
  public static function getOptions(): array {

    $options = [];

    $options['currencyCodes'] = CurrencyUtils::getCurrencies();
    $options['salesOrderStatus'] = self::get_case_sales_order_status();
    $options['featureCaseTypes'] = self::get_case_types_with_features_enabled();

    return $options;
  }

  /**
   * Exposes Case types that have features enabled to Angular.
   */
  public static function get_case_types_with_features_enabled() {
  	$caseTypeCategoryFeatures = new CaseTypeCategoryFeatures();

  	$featureCaseTypes = [];
  	array_map(function ($feature) use ($caseTypeCategoryFeatures, $featureCaseTypes) {
  		$caseTypeCategories = $caseTypeCategoryFeatures->retrieveCaseInstanceWithEnabledFeatures([$feature]);
  		$featureCaseTypes[$feature] = array_keys($caseTypeCategories);
  	}, ['quotations', 'invoices']);
  	return $featureCaseTypes;
  }

  /**
   * Exposes case sales order statuses to Angular.
   */
  public static function get_case_sales_order_status() {
  	$optionValues = OptionValue::get(FALSE)
  	->addSelect('id', 'value', 'name', 'label')
  	->addWhere('option_group_id:name', '=', 'case_sales_order_status')
  	->execute();

  	return $optionValues->getArrayCopy();
  }
}
