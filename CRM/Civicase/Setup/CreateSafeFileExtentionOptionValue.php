<?php

/**
 * Class CRM_Civicase_Setup_CreateSafeFileExtentionOptionValue.
 */
class CRM_Civicase_Setup_CreateSafeFileExtentionOptionValue {

  /**
   * Installs 'zip', 'msg', 'eml', 'mbox' Safe File Extention Option values.
   */
  public function apply() {
    $file_extentions_to_be_installed = ['zip', 'msg', 'eml', 'mbox'];

    foreach ($file_extentions_to_be_installed as $file_extention_to_be_installed) {
      CRM_Core_BAO_OptionValue::ensureOptionValueExists([
        'option_group_id' => 'safe_file_extension',
        'name' => $file_extention_to_be_installed,
        'label' => $file_extention_to_be_installed,
        'is_active' => TRUE,
      ]);
    }

    return TRUE;
  }

}
