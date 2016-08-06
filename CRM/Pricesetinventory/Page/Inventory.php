<?php

require_once 'CRM/Core/Page.php';

class CRM_Pricesetinventory_Page_Inventory extends CRM_Core_Page {

    function run() {
        CRM_Utils_System::setTitle(ts('Inventory'));

        $inventorySets = civicrm_api3("Inventory", "Set", array());

        foreach($inventorySets['values'] as &$set) {
            $params = array("id" => $set['price_set_id'], "return" => "title");
            $pset = civicrm_api3("PriceSet", "getsingle", $params);
            $set['priceSetName'] = $pset['title'];

        }



        $this->assign('inventorySets', $inventorySets['values']);

        parent::run();
    }

}
