<?php

/**
 * Inventory.Get API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_inventory_get_spec(&$spec) {
  //$spec['magicword']['api.required'] = 1;
}

/**
 * Inventory.Get API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_inventory_get($params) {
  $tableName = PSI_TABLE;
  if (array_key_exists('sid', $params) && $params['sid']) {

    $returnValues = array();
    $sql = "SELECT * FROM `{$tableName}` WHERE sid = %1";
    $dao =& CRM_Core_DAO::executeQuery($sql, array(1 => array($params['sid'], "Int")));
    while ($dao->fetch()) {
      $vals = (array) $dao;
      $vals['excluded_pages'] = unserialize($vals['excluded_pages']);
      $returnValues[] = $vals;
    }

    return civicrm_api3_create_success($returnValues, $params, 'Inventory', 'Get');
  } elseif (array_key_exists('id', $params) && $params['id']) {

    $returnValues = array();
    $sql = "SELECT * FROM `{$tableName}` WHERE id = %1";
    $dao =& CRM_Core_DAO::executeQuery($sql, array(1 => array($params['id'], "Int")));
    if ($dao->fetch()) {
      $returnValues = (array) $dao;
      $returnValues['excluded_pages'] = unserialize($returnValues['excluded_pages']);
    }

    return civicrm_api3_create_success($returnValues, $params, 'Inventory', 'Get');
  } else {
    //throw new API_Exception(/*errorMessage*/ 'Everyone knows that the magicword is "Get"', /*errorCode*/ 1234);
    $returnValues = array();
    $sql = "SELECT * FROM `{$tableName}`";
    $dao =& CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $vals = (array) $dao;
      $vals['excluded_pages'] = unserialize($vals['excluded_pages']);
      $returnValues[] = $vals;
    }

    return civicrm_api3_create_success($returnValues, $params, 'Inventory', 'Get');
  }
}

