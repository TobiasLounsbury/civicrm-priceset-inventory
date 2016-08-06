<?php

require_once 'CRM/Core/Page.php';

class CRM_Pricesetinventory_Page_InventorySetItems extends CRM_Core_Page {
    function run() {
        // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
        CRM_Utils_System::setTitle(ts('Inventory Items'));

        $sid = CRM_Utils_Request::retrieve('sid', 'Positive', $this, false, 0);
        $this->assign('sid', $sid);

        $inventoryItems = civicrm_api3("Inventory", "get", array("sid" => $sid));

        foreach($inventoryItems['values'] as &$item) {
            if (array_key_exists("field_value_id", $item) && $item['field_value_id']) {
                $result = civicrm_api3('PriceFieldValue', 'get', array(
                    'return' => "label",
                    'sequential' => 1,
                    'id' => $item['field_value_id']
                ));
                $item['priceFieldName'] = $result['values'][0]['label'];
            } elseif (array_key_exists("field_id", $item) && $item['field_id']) {
                $result = civicrm_api3('PriceField', 'get', array(
                    'return' => "label",
                    'sequential' => 1,
                    'id' => $item['field_id']
                ));
                $item['priceFieldName'] = $result['values'][0]['label'];
            }
        }

        $this->assign('inventoryItems', $inventoryItems['values']);

        parent::run();
  }
}
