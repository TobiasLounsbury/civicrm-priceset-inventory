<?php

/**
 * Inventory.Quantity API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_inventory_delete_spec(&$spec) {
  //$spec['field']['api.required'] = 1;
}

/**
 * Inventory.Quantity API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_inventory_delete($params) {
  if (array_key_exists('sid', $params) || array_key_exists('id', $params)) {
    $tableName = PSI_TABLE;
    if (array_key_exists('id', $params) && ($params['field_id'] || $params['field_id'] == 0)) {
      $sql = "select `Quantity` FROM `{$tableName}` WHERE `id` = {$params['id']} LIMIT 1";
    } elseif (array_key_exists('field_id', $params) && $params['field_id']) {
      if(!array_key_exists('field_value_id', $params)) {
        $sql = "select `Quantity`,`field_value_id` FROM `{$tableName}` WHERE `field_id` = {$params['field_id']} LIMIT 1";
      } else {
        $sql = "select `Quantity`,`field_value_id` FROM `{$tableName}` WHERE `field_id` = {$params['field_id']} AND `field_value_id` = {$params['field_value_id']} LIMIT 1";
      }
    } else {
      //I don't know that this is ever needed
      throw new API_Exception("Either sid or id is required", 12);
    }


    $returnValues = array();
    return civicrm_api3_create_success($returnValues, $params, 'Inventory', 'Delete');
  } else {
    throw new API_Exception("Either sid or id is required", 12);
  }
}

