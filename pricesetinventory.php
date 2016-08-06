<?php

require_once 'pricesetinventory.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function pricesetinventory_civicrm_config(&$config) {
  _pricesetinventory_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function pricesetinventory_civicrm_xmlMenu(&$files) {
  _pricesetinventory_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function pricesetinventory_civicrm_install() {
  _pricesetinventory_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function pricesetinventory_civicrm_uninstall() {
  _pricesetinventory_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function pricesetinventory_civicrm_enable() {
  _pricesetinventory_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function pricesetinventory_civicrm_disable() {
  _pricesetinventory_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function pricesetinventory_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _pricesetinventory_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function pricesetinventory_civicrm_managed(&$entities) {
  _pricesetinventory_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function pricesetinventory_civicrm_caseTypes(&$caseTypes) {
  _pricesetinventory_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function pricesetinventory_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _pricesetinventory_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

function pricesetinventory_civicrm_buildForm($formName, &$form) {

    if ($formName = 'CRM_Contribute_Form_Contribution_Main') {
        $psid = $form->getVar("_priceSetId");
        $page = $form->getVar("_id");
        //todo: Take into account is_active
        if ($psid) {
            $inventorySet = civicrm_api3("Inventory", "Set", array("pid" => $psid));
            $ePages = array();
            if (array_key_exists("excluded_pages", $inventorySet['values']) && is_array($inventorySet['values']['excluded_pages'])) {
                $ePages = $inventorySet['values']['excluded_pages'];
                var_dump($ePages);
            }
            if ($inventorySet['is_error'] == 0 && $inventorySet['count'] > 0 && !in_array($page, $ePages) && $inventorySet['values']['is_active'] == 1) {
                $inventorySet = $inventorySet['values'];
                $inventoryItems = civicrm_api3("Inventory", "Get", array("sid" => $inventorySet['sid']));

                if ($inventoryItems['is_error'] == 0 && $inventoryItems['count'] > 0) {
                    $inventoryItems = $inventoryItems['values'];

                    foreach($inventoryItems as $key => &$item) {

                        //$item['excluded_pages'] = unserialize($item['excluded_pages']);
                        $ePages = array();
                        if (array_key_exists("excluded_pages", $item) && is_array($item['excluded_pages'])) {
                            $ePages = $item['excluded_pages'];
                        }
                        if (!in_array($page, $ePages) && $item['is_active'] == 1) {
                            $item['type'] = $form->_priceSet['fields'][$item['field_id']]['html_type'];
                        } else {
                            unset($inventoryItems[$key]);
                        }
                    }

                    $config = CRM_Core_Config::singleton();
                    CRM_Core_Resources::singleton()->addSetting(array('Inventory' => array('ImagePath' => $config->imageUploadURL)));
                    CRM_Core_Resources::singleton()->addSetting(array('Inventory' => array('Items' => $inventoryItems)));
                    CRM_Core_Resources::singleton()->addScriptFile('com.tobiaslounsbury.pricesetinventory', 'pricesetinventory.js', 20, 'page-footer');
                    CRM_Core_Resources::singleton()->addStyleFile('com.tobiaslounsbury.pricesetinventory', 'pricesetinventory.css');
                }
            }
            //$form->_priceSet['fields'][23]['help_post'] = "This is the song that never ends";
            //$form->_values['fee'][23]['help_post'] = "Some people started singing it";
        }
    }
}

function pricesetinventory_civicrm_validateForm( $formName, &$fields, &$files, &$form, &$errors ) {

    if ($formName = 'CRM_Contribute_Form_Contribution_Main') {
        //Look to see if we have a price set.
        if($psid = $form->getVar("_priceSetId")) {
            $page = $form->getVar("_id");
            $inventorySet = civicrm_api3("Inventory", "Set", array("pid" => $psid));
            if ($inventorySet['is_error'] == 0 && $inventorySet['count'] > 0 && !in_array($page, $inventorySet['values']['excluded_pages']) && $inventorySet['values']['is_active'] == 1) {
                $inventorySet = $inventorySet['values'];
                $inventoryItems = civicrm_api3("Inventory", "Get", array("sid" => $inventorySet['sid']));

                if ($inventoryItems['is_error'] == 0 && $inventoryItems['count'] > 0) {
                    $inventoryItems = $inventoryItems['values'];

                    foreach($inventoryItems as $key => $item) {
                        if ($item['is_active'] == 1 && !in_array($page, $item['excluded_page'])) {
                            //Do we have any logic to be added that isn't related to Quantity

                            if (!empty($item['quantity']) || $item['quantity'] == 0) {

                                switch ($form->_priceSet['fields'][$item['field_id']]['html_type']) {
                                    case "Text":
                                        if ($fields['price_'.$item['field_id']] > $item['quantity']) {
                                            if ($item['quantity'] == 0) {
                                                $errors['price_'.$item['field_id']] = ts( "I'm sorry, This item is sold out." );
                                            } else {
                                                $errors['price_'.$item['field_id']] = ts( 'I\'m sorry, We only have %1 in stock, please reduce your quantity.', array(1 => $item['quantity']));
                                            }
                                        }
                                        break;
                                    case "CheckBox":
                                        if ($item['quantity'] == 0 && is_array($fields['price_'.$item['field_id']]) && array_key_exists($item['field_value_id'], $fields['price_'.$item['field_id']]) && $fields['price_'.$item['field_id']][$item['field_value_id']] == 1) {
                                            $errors['price_'.$item['field_id']] = ts( "I'm sorry, this item is sold out." );
                                        }
                                        break;
                                    case "Radio":
                                    case "Select":
                                        if ($item['quantity'] == 0 && $fields['price_'.$item['field_id']] == $item['field_value_id']) {
                                            $errors['price_'.$item['field_id']] = ts( 'I\'m sorry, This selection is sold out');
                                        }
                                        break;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    error_log("test");
}

function pricesetinventory_civicrm_postProcess( $formName, &$form) {
    if ($formName == 'CRM_Contribute_Form_Contribution_Confirm') {
        if($psid = $form->getVar("_priceSetId")) {
            $page = $form->getVar("_id");
            $inventorySet = civicrm_api3("Inventory", "Set", array("pid" => $psid));
            if ($inventorySet['is_error'] == 0 && $inventorySet['count'] > 0 && !in_array($page, $inventorySet['values']['excluded_pages']) && $inventorySet['values']['is_active'] == 1) {
                $inventorySet = $inventorySet['values'];
                $inventoryItems = civicrm_api3("Inventory", "Get", array("sid" => $inventorySet['sid']));

                if ($inventoryItems['is_error'] == 0 && $inventoryItems['count'] > 0) {
                    $inventoryItems = $inventoryItems['values'];

                    foreach($inventoryItems as $item) {
                        if ($item['is_active'] == 1 && !in_array($page, $item['excluded_page'])) {
                            //Do we have any logic to be added that isn't related to Quantity

                            if (!empty($item['quantity']) || $item['quantity'] == 0) {

                                switch ($form->_priceSet['fields'][$item['field_id']]['html_type']) {
                                    case "Text":
                                        $value_id = array_keys($form->_priceSet['fields'][$item['field_id']]['options']);
                                        $value_id = $value_id[0];
                                        $qty = $form->_lineItem[$psid][$value_id]['qty'];
                                        if ($qty > 0) {
                                            civicrm_api3("Inventory", "Quantity", array("id" => $item['id'], "qty" => "-".$qty));
                                        }
                                        break;
                                    case "CheckBox":
                                    case "Radio":
                                    case "Select":
                                        if (array_key_exists($item['field_value_id'], $form->_lineItem[$psid])) {
                                            civicrm_api3("Inventory", "Quantity", array("id" => $item['id'], "qty" => "-1"));
                                        }
                                        break;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

function pricesetinventory_civicrm_navigationMenu( &$params ) {
    // get the id of Administer Menu
    $administerMenuId = CRM_Core_DAO::getFieldValue('CRM_Core_BAO_Navigation', 'Administer', 'id', 'name');
    $contributeMenuId = CRM_Core_DAO::getFieldValue('CRM_Core_BAO_Navigation', 'CiviContribute', 'id', 'name');

    // skip adding menu if there is no administer menu
    if ($administerMenuId) {
        // get the maximum key under adminster menu
        $maxKey = max( array_keys($params[$administerMenuId]['child']));
        $params[$administerMenuId]['child'][$contributeMenuId]['child'][$maxKey+1] =  array (
            'attributes' => array (
                'label'      => 'Inventory',
                'name'       => 'PriceSetInventory',
                'url'        => 'civicrm/admin/inventory',
                'permission' => 'administer CiviCRM',
                'operator'   => NULL,
                'separator'  => true,
                'parentID'   => $contributeMenuId,
                'navID'      => $maxKey+1,
                'active'     => 1
            )
        );
    }
}