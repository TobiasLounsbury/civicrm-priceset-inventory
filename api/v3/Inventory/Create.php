<?php

/**
 * Inventory.Create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_inventory_create_spec(&$spec) {
    $spec['field_id']['api.required'] = 1;
    $spec['sid']['api.required'] = 1;
    $spec['title']['api.required'] = 1;
}

/**
 * Inventory.Create API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_inventory_create($params) {
    if (array_key_exists('field_id', $params) && $params['field_id'] &&
        array_key_exists('sid', $params) && $params['sid'] &&
        array_key_exists('title', $params) && $params['title']) {

        $values = array();
        $values[2] = array($params['field_id'], "Int");
        $values[3] = array($params['sid'], "Int");
        $values[4] = array($params['title'], "String");
        //$values[5] = array((array_key_exists('field_value_id', $params) ? $params['field_value_id'] : null), "Int");
        $values[5] = (array_key_exists('field_value_id', $params) && !empty($params['field_value_id'])) ? array($params['field_value_id'], "Int") : array("", "Date");
        $values[6] = array((array_key_exists('image_path', $params) ? $params['image_path'] : ""), "String");
        $values[7] = array((array_key_exists('description', $params) ? $params['description'] : ""), "String");
        $values[8] = (array_key_exists('quantity', $params) && !empty($params['quantity']) && !is_null($params['quantity'])) ? array($params['quantity'], "Int") : array("", "Date");
        $values[9] = array((array_key_exists('is_active', $params) ? $params['is_active'] : 0), "Int");
        $values[10] = array(serialize(array_key_exists('excluded_pages', $params) ? $params['excluded_pages'] : array()), "String");
        $values[11] = array((array_key_exists('default_open', $params) ? $params['default_open'] : 0), "Int");

        if (array_key_exists('id', $params) && $params['id']) {
            $sql = "UPDATE `{PSI_TABLE}` SET `field_id` = %2, `sid` = %3, `title` = %4, `field_value_id` = %5, `image_path` = %6, `description` = %7, `quantity` = %8, `is_active` = %9, `excluded_pages` = %10, `default_open` = %11 WHERE `id` = %1 LIMIT 1";
            $values[1] = array($params['id'], "Int");
        } else {
            $sql = "INSERT INTO `{PSI_TABLE}`(`field_id`, `sid`, `title`, `field_value_id`, `image_path`, `description`, `quantity`, `is_active`, `excluded_pages`, `default_open`) VALUES(%2, %3, %4, %5, %6, %7, %8, %9, %10, %11)";
        }

        $dao =& CRM_Core_DAO::executeQuery($sql, $values);

        if ($dao) {
            if (array_key_exists('id', $params) && $params['id']) {
                return civicrm_api3_create_success(array(), $params, 'Inventory', 'Create');
            } else {
                return civicrm_api3_create_success(array(CRM_Core_DAO::singleValueQuery('SELECT LAST_INSERT_ID()')), $params, 'Inventory', 'Create');
            }
        }

        throw new API_Exception('DB Error: Could not save Inventory Item', 3);
    } else {
        throw new API_Exception('`field_id, `sid` and `title` are required fields', 4);
    }
}

