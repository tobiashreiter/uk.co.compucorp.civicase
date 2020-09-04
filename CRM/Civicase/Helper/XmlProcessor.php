<?php

/**
 * Wrapper class for xml settings.
 */
class CRM_Civicase_Helper_XmlProcessor {

  /**
   * Fetches settings from xml file.
   *
   * @var CRM_Case_XMLProcessor_Process
   */
  private $xmlProcessor;

  /**
   * Initizlization.
   *
   * @param CRM_Case_XMLProcessor_Process $xmlProcessor
   *   Xml processor object.
   */
  public function __construct(CRM_Case_XMLProcessor_Process $xmlProcessor) {
    $this->xmlProcessor = $xmlProcessor;
  }

  /**
   * Fetches the setting from xml file.
   *
   * @param string $key
   *   Setting name.
   *
   * @return mixed
   *   Value from xml setting file.
   */
  public function get(string $key) {
    $xml = $this->xmlProcessor->retrieve("Settings");
    $value = NULL;
    if (!empty($xml->{$key})) {
      $value = $xml->{$key}->__toString();
    }

    return $value;
  }

}
