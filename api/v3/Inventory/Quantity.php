<?php

/**
 * Inventory.Quantity API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_inventory_quantity_spec(&$spec) {
  $spec['id']['api.required'] = 1;
  $spec['qty']['api.required'] = 1;
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
function civicrm_api3_inventory_quantity($params) {
  //TODO: Allow for checking of quantity by field id, field, value id, and id.
  if (array_key_exists('id', $params) && array_key_exists('qty', $params)) {

    $tableName = PSI_TABLE;
    $sql = "UPDATE `{$tableName}` SET `quantity` = %0 WHERE id = %2";
    $values = array(2 => array($params['id'], "Int"));

    if ($params['qty'][0] == "-") {
      $sql = str_replace("%0", "`quantity` - %1", $sql);
      $values[1] = array(substr($params['qty'], 1), "Int");
    } elseif ($params['qty'][0] == "+") {
      $sql = str_replace("%0", "`quantity` + %1", $sql);
      $values[1] = array(substr($params['qty'], 1), "Int");
    } else {
      $values[0] = array($params['qty'], "Int");
    }

    $dao =& CRM_Core_DAO::executeQuery($sql, $values);

    if ($dao) {
      return civicrm_api3_create_success(array(), $params, 'Inventory', 'Quantity');
    } else {
      throw new API_Exception("Unable to set quantity", 3);
    }
  } else {
    throw new API_Exception("id and qty are both required fields", 12);
  }
}

