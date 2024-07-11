<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 *
 * Generated from uk.co.compucorp.civicase/xml/schema/CRM/Civicase/CaseCategoryFeatures.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:480eebcac1f7ce6976ee6dd5a87112e8)
 */
use CRM_Civicase_ExtensionUtil as E;

/**
 * Database access object for the CaseCategoryFeatures entity.
 */
class CRM_Civicase_DAO_CaseCategoryFeatures extends CRM_Core_DAO {
  const EXT = E::LONG_NAME;
  const TABLE_ADDED = '';

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  public static $_tableName = 'civicrm_case_category_features';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log table.
   *
   * @var bool
   */
  public static $_log = TRUE;

  /**
   * Unique CaseCategoryFeatures ID
   *
   * @var int|string|null
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $id;

  /**
   * One of the values of the case_type_categories option group
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $category_id;

  /**
   * One of the values of the case_type_category_features option group
   *
   * @var int|string
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $feature_id;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->__table = 'civicrm_case_category_features';
    parent::__construct();
  }

  /**
   * Returns localized title of this entity.
   *
   * @param bool $plural
   *   Whether to return the plural version of the title.
   */
  public static function getEntityTitle($plural = FALSE) {
    return $plural ? E::ts('Case Category Featureses') : E::ts('Case Category Features');
  }

  /**
   * Returns all the column names of this table
   *
   * @return array
   */
  public static function &fields() {
    if (!isset(Civi::$statics[__CLASS__]['fields'])) {
      Civi::$statics[__CLASS__]['fields'] = [
        'id' => [
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => E::ts('Unique CaseCategoryFeatures ID'),
          'required' => TRUE,
          'where' => 'civicrm_case_category_features.id',
          'table_name' => 'civicrm_case_category_features',
          'entity' => 'CaseCategoryFeatures',
          'bao' => 'CRM_Civicase_DAO_CaseCategoryFeatures',
          'localizable' => 0,
          'html' => [
            'type' => 'Number',
          ],
          'readonly' => TRUE,
          'add' => NULL,
        ],
        'category_id' => [
          'name' => 'category_id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => E::ts('One of the values of the case_type_categories option group'),
          'required' => TRUE,
          'where' => 'civicrm_case_category_features.category_id',
          'table_name' => 'civicrm_case_category_features',
          'entity' => 'CaseCategoryFeatures',
          'bao' => 'CRM_Civicase_DAO_CaseCategoryFeatures',
          'localizable' => 0,
          'pseudoconstant' => [
            'optionGroupName' => 'case_type_categories',
            'optionEditPath' => 'civicrm/admin/options/case_type_categories',
          ],
          'add' => NULL,
        ],
        'feature_id' => [
          'name' => 'feature_id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => E::ts('One of the values of the case_type_category_features option group'),
          'required' => TRUE,
          'where' => 'civicrm_case_category_features.feature_id',
          'table_name' => 'civicrm_case_category_features',
          'entity' => 'CaseCategoryFeatures',
          'bao' => 'CRM_Civicase_DAO_CaseCategoryFeatures',
          'localizable' => 0,
          'pseudoconstant' => [
            'optionGroupName' => 'case_type_category_features',
            'optionEditPath' => 'civicrm/admin/options/case_type_category_features',
          ],
          'add' => NULL,
        ],
      ];
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'fields_callback', Civi::$statics[__CLASS__]['fields']);
    }
    return Civi::$statics[__CLASS__]['fields'];
  }

  /**
   * Return a mapping from field-name to the corresponding key (as used in fields()).
   *
   * @return array
   *   Array(string $name => string $uniqueName).
   */
  public static function &fieldKeys() {
    if (!isset(Civi::$statics[__CLASS__]['fieldKeys'])) {
      Civi::$statics[__CLASS__]['fieldKeys'] = array_flip(CRM_Utils_Array::collect('name', self::fields()));
    }
    return Civi::$statics[__CLASS__]['fieldKeys'];
  }

  /**
   * Returns the names of this table
   *
   * @return string
   */
  public static function getTableName() {
    return self::$_tableName;
  }

  /**
   * Returns if this table needs to be logged
   *
   * @return bool
   */
  public function getLog() {
    return self::$_log;
  }

  /**
   * Returns the list of fields that can be imported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &import($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'case_category_features', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of fields that can be exported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &export($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'case_category_features', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of indices
   *
   * @param bool $localize
   *
   * @return array
   */
  public static function indices($localize = TRUE) {
    $indices = [];
    return ($localize && !empty($indices)) ? CRM_Core_DAO_AllCoreTables::multilingualize(__CLASS__, $indices) : $indices;
  }

}
