<?php

require_once 'CRM/Core/Form.php';


/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Pricesetinventory_Form_InventorySet extends CRM_Core_Form {

    protected $_sid;
    protected $_iset;
    protected $_PriceSetName;


    public function preProcess() {
        $this->_sid = CRM_Utils_Request::retrieve('sid', 'Positive', $this, false, 0);
        if ($this->_sid) {
            $this->_iset = $this->getInventorySet();
            $this->_priceSetName = $this->getPriceSetName();
            CRM_Utils_System::setTitle(ts("Edit ".$this->_iset['name']));
        } else {
            CRM_Utils_System::setTitle(ts("New Inventory Set"));
        }
        parent::preProcess();
    }

    function buildQuickForm() {

        $this->add(
            'text', // field type
            'name', // field name
            ts('Name'), // field label
            array("maxlength" => 255, "size" => 45),
            true // is required
        );

        if ($this->_sid) {
            $this->assign('sid', $this->_sid);
            $this->assign('priceSetId', $this->_iset['price_set_id']);
            $this->assign('priceSetName', $this->_priceSetName);

            $this->add('hidden', 'sid');
            $this->add('hidden', 'price_set_id');

            $pages = $this->getPages();

            if(array_key_exists("civicrm_contribution_page", $pages)) {
                $opts = array();
                foreach ($pages['civicrm_contribution_page'] as $id => $page) {
                    $opts[$page['title']] = $id;
                }
                $this->addCheckBox('excluded_pages', ts('Excluded Pages'), $opts);
                //$this->addSelect("excluded_pages", array());
            }

        } else {
            $this->add(
                'select',
                'price_set_id',
                'Price Set',
                $this->getPriceSets(),
                true
            );
        }



        $this->addElement(
            'checkbox',
            'is_active',
            ts('Is this Inventory Set active?')
        );

        $this->addButtons(array(
            array(
                'type' => 'submit',
                'name' => ts('Submit'),
                'isDefault' => TRUE,
            ),
        ));

        // export form elements
        $this->assign('elementNames', $this->getRenderableElementNames());
        parent::buildQuickForm();
    }

    function postProcess() {
        $values = $this->exportValues();
        //$options = $this->getColorOptions();
        //CRM_Core_Session::setStatus(ts('You picked color "%1"', array()));
        if(array_key_exists("excluded_pages", $values)) {
            $values['excluded_pages'] = array_keys($values['excluded_pages']);
        } else {
            $values['excluded_pages'] = array();
        }

        $inventorySet = civicrm_api3("Inventory", "Set", array("data" => $values));

        CRM_Core_Session::setStatus(ts('The inventory set \'%1\' has been saved.',
            array(1 => $values['name'])), "Saved", "success");

        parent::postProcess();
        return CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/admin/inventory'));
    }

    function setDefaultValues() {
        $defaults = $this->getInventorySet();
        $defaults['is_active'] = $this->_sid ? CRM_Utils_Array::value('is_active', $defaults) : 1;
        $defaults['excluded_pages'] = $defaults['excluded_pages'] ? $defaults['excluded_pages'] : array();
        return $defaults;
    }

    function getInventorySet() {
        if($this->_iset) {
            return $this->_iset;
        }
        $defaults = array();
        if ($this->_sid) {
            $inventorySet = civicrm_api3("Inventory", "Set", array("sid" => $this->_sid));
            if ($inventorySet['is_error'] == 0) {
                return $inventorySet['values'];
            }
        }
        return $defaults;
    }

    function getPriceSetName() {
        if ($this->_sid) {
            $params = array("id" => $this-_sid, "return" => "title");
            $pset = civicrm_api3("PriceSet", "getsingle", $params);
            return $pset['title'];
        } else {
            return "";
        }
    }

    function getPages() {
        if ($this->_iset && $this->_iset['price_set_id']) {
            return CRM_Price_BAO_PriceSet::getUsedBy($this->_iset['price_set_id']);
        }
        return array();
    }

    function getPriceSets() {
        $params = array("sequential" => 1, "return" => "title");
        $psets = civicrm_api3("PriceSet", "get", $params);

        $options = array();
        if ($psets['is_error'] == 0) {
            foreach($psets['values'] as $set) {
                $options[$set['id']] = ts($set['title']);
            }
        }
        return $options;
    }

    /**
     * Get the fields/elements defined in this form.
     *
     * @return array (string)
     */
    function getRenderableElementNames() {
        // The _elements list includes some items which should not be
        // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
        // items don't have labels.  We'll identify renderable by filtering on
        // the 'label'.
        $elementNames = array();
        foreach ($this->_elements as $element) {
            /** @var HTML_QuickForm_Element $element */
            $label = $element->getLabel();
            if (!empty($label)) {
                $elementNames[] = $element->getName();
            }
        }
        return $elementNames;
    }
}
