<?php

require_once "pricesetinventory_const.php";

/**
 * Inventory.Item API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_inventory_item_spec(&$spec) {
  $spec['id']['api.required'] = 0;
  $spec['field_id']['api.required'] = 0;
  $spec['field_value_id']['api.required'] = 0;
}

/**
 * Inventory.Item API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_inventory_item($params) {

  $index = 1;
  $queryData = array();
  $wheres = array();

  $keys = array("id", "field_id", "field_value_id");
  foreach($keys as $key) {
    if(array_key_exists($key, $params) && $params[$key]) {
      $wheres[] = "`{$key}` = %{$index}";
      $queryData[$index] = array($params[$key], "Int");
      $index++;
    }
  }

  if(empty($wheres)) {
    throw new API_Exception("Missing one of required fields: `id`, `field_id`, `field_value_id`", 12);
  }

  $tableName = PSI_TABLE;
  $sql = "SELECT * FROM `{$tableName}` WHERE ". implode(" AND ", $wheres);
  $dao =& CRM_Core_DAO::executeQuery($sql, $queryData);

  if($dao->fetch()) {
    return civicrm_api3_create_success($dao->toArray(), $params, 'Inventory', 'Item');
  } else {
    return civicrm_api3_create_success(array(), $params, 'Inventory', 'Item');
  }
}

