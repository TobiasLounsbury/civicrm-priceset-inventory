<?php

/**
 * Inventory.Get API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_inventory_set_spec(&$spec) {
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
function civicrm_api3_inventory_set($params) {
    if (array_key_exists('data', $params) && !empty($params['data'])) {

        $values = array();
        $values[2] = array($params['data']['name'], "String");
        $values[3] = array($params['data']['price_set_id'], "Int");
        $values[4] = array(serialize(array_key_exists('excluded_pages', $params['data']) ? $params['data']['excluded_pages'] : array()), "String");
        $values[5] = array((array_key_exists('is_active', $params['data']) ? $params['data']['is_active'] : 0), "Int");
        if(array_key_exists("sid", $params['data'])) {
            $values[1] = array($params['data']['sid'], "Int");
            $sql = "UPDATE `{PSI_SET_TABLE}` SET `name` = %2, `price_set_id` = %3, `excluded_pages` = %4, `is_active` = %5 WHERE sid = %1 LIMIT 1";
        } else {
            $sql = "INSERT INTO `{PSI_SET_TABLE}` (`name`, `price_set_id`, `excluded_pages`, `is_active`) VALUES(%2, %3, %4, %5)";
        }
        $dao =& CRM_Core_DAO::executeQuery($sql, $values);

        if ($dao) {
            if(array_key_exists("sid", $params['data'])) {
                return civicrm_api3_create_success(array(), $params, 'Inventory', 'Set');
            } else {
                return civicrm_api3_create_success(array(CRM_Core_DAO::singleValueQuery('SELECT LAST_INSERT_ID()')), $params, 'Inventory', 'Set');
            }
        }
    } elseif (array_key_exists('sid', $params) && $params['sid']) {

        $returnValues = array();

        $sql = "SELECT * FROM `{PSI_SET_TABLE}` WHERE sid = %1 LIMIT 1";
        $dao =& CRM_Core_DAO::executeQuery($sql, array(1 => array($params['sid'], "Int")));
        if ($dao->fetch()) {
            $returnValues = array(
                "sid" => $dao->sid,
                "name" => $dao->name,
                "price_set_id" => $dao->price_set_id,
                "is_active" => $dao->is_active,
                "excluded_pages" => unserialize($dao->excluded_pages)
            );
        }

        return civicrm_api3_create_success($returnValues, $params, 'Inventory', 'Set');
    } elseif (array_key_exists('pid', $params) && $params['pid']) {
        $returnValues = array();

        $sql = "SELECT * FROM `{PSI_SET_TABLE}` WHERE price_set_id = %1 LIMIT 1";
        $dao =& CRM_Core_DAO::executeQuery($sql, array(1 => array($params['pid'], "Int")));
        if ($dao->fetch()) {
            $returnValues = array(
                "sid" => $dao->sid,
                "name" => $dao->name,
                "price_set_id" => $dao->price_set_id,
                "is_active" => $dao->is_active,
                "excluded_pages" => unserialize($dao->excluded_pages)
            );
        }

        return civicrm_api3_create_success($returnValues, $params, 'Inventory', 'Set');
    } else {
        //throw new API_Exception(/*errorMessage*/ 'Everyone knows that the magicword is "Set"', /*errorCode*/ 1234);
        $returnValues = array();

        $sql = "SELECT * FROM `{PSI_SET_TABLE}`";
        $dao =& CRM_Core_DAO::executeQuery($sql);
        while ($dao->fetch()) {
            $returnValues[] = array(
                "sid" => $dao->sid,
                "name" => $dao->name,
                "price_set_id" => $dao->price_set_id,
                "is_active" => $dao->is_active,
                "excluded_pages" => unserialize($dao->excluded_pages)

            );
        }

        return civicrm_api3_create_success($returnValues, $params, 'Inventory', 'Set');
    }
}

