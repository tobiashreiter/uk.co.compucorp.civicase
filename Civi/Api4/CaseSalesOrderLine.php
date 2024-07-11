<?php

namespace Civi\Api4;

use Civi\Api4\Generic\DAOEntity;

/**
 * CaseSalesOrderLine entity.
 *
 * Provided by the CiviCase extension.
 *
 * @package Civi\Api4
 */
class CaseSalesOrderLine extends DAOEntity {

  /**
   * {@inheritDoc}
   */
  public static function permissions() {
    return [
      'meta' => ['access CiviCRM'],
      'default' => ['access CiviCRM'],
    ];
  }

}
