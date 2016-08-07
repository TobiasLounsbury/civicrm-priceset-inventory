<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Pricesetinventory_Form_InventoryItem extends CRM_Core_Form {

    protected $_sid;
    protected $_id;
    protected $_iset;
    protected $_item;

    public function preProcess() {
        $this->_sid = CRM_Utils_Request::retrieve('sid', 'Positive', $this, false, 0);
        $this->_id = CRM_Utils_Request::retrieve('id', 'Positive', $this, false, 0);


        if($this->_sid) {
            $this->_iset = $this->getInventorySet();
            CRM_Utils_System::appendBreadCrumb(array(array("title" => ts($this->_iset['name']), "url" => CRM_Utils_System::url('civicrm/admin/inventory/items', 'reset=1&sid='.$this->_sid))));
        }

        if ($this->_id) {
            $this->_item = $this->getInventoryItem();
            CRM_Utils_System::setTitle(ts("Edit ". $this->_item['title']));
        } else {
            CRM_Utils_System::setTitle(ts("New Inventory Item"));
        }

        parent::preProcess();
    }

    function buildQuickForm() {

        // add form elements

        //id
        if ($this->_id) {
            $this->add("hidden", "id", $this->_id);
        }

        //sid
        $this->add("hidden", "sid", $this->_sid);

        //field_id
        //field_value_id

        $priceSetId = $this->_iset['price_set_id'];


       $result = civicrm_api3("PriceSet", "get", array(
            'sequential' => 1,
            'id' => $priceSetId,
            'api.PriceField.get' => array(
                'options' => array('limit' => 0),
                'is_active' => 1,
                'api.PriceFieldValue.get' => array(
                    'options' => array('limit' => 0),
                    'is_active' => 1,
                    'return' => array('label')
                )
            )
        ));
        //TODO: Add error checking
        $priceSetTitle = $result['values'][0]['title'];
        $fields = array();
        $values = array();
        foreach ($result['values'][0]['api.PriceField.get']['values'] as $field) {
            $values[$field['id']] = array();
            foreach($field['api.PriceFieldValue.get']['values'] as $value) {
                $values[$field['id']][$value['id']] = $value['label'];
            }
            $fields[$field['id']] = $field['label'];
        }
        $this->addElement('select', "field_id", ts('Price Set Field'), $fields);

        if ($this->_item && array_key_exists("field_id", $this->_item) && sizeof($values[$this->_item['field_id']]) > 1){
            $field_values = $values[$this->_item['field_id']];
        } else {
            $field_values = array();
        }
        $this->addElement('select', "field_value_id", ts('Price Set Option Field'), $field_values);

        CRM_Core_Resources::singleton()->addSetting(array('Inventory' => array('fields' => $fields, 'priceFieldValues' => $values)));


        //title
        $this->add(
            'text',
            'title',
            ts('Title'),
            array("maxlength" => 255, "size" => 45),
            true
        );

      $version = substr(CRM_Utils_System::version(), 0, 3);
      if($version <= 4.6) {
        //description
        $this->addWysiwyg("description", "Description", array());
      } else {
        //description
        $this->add("wysiwyg", "description", "Description", array());
      }

        //image_path
        $this->add(
            'text',
            'image_path',
            ts('Image'),
            array("maxlength" => 255, "size" => 45),
            false
        );

        //Some extra info for handling GUI selection of images and relative paths
        $config = CRM_Core_Config::singleton();
        CRM_Core_Resources::singleton()->addSetting(array('Inventory' => array('ImagePath' => $config->imageUploadURL)));

        //image_data

        //quantity
        $this->add(
            'text',
            'quantity',
            ts('Quantity'),
            array("maxlength" => 5, "size" => 2),
            false
        );

        //excluded_pages
        $pages = $this->getPages();

        if(array_key_exists("civicrm_contribution_page", $pages)) {
            $opts = array();
            foreach ($pages['civicrm_contribution_page'] as $id => $page) {
                $opts[$page['title']] = $id;
            }
            $this->addCheckBox('excluded_pages', ts('Excluded Pages'), $opts);
        }

        //Show as open by default
        $this->addElement(
            'checkbox',
            'default_open',
            ts('Display More Information for this field by default')
        );

        //is_active
        $this->addElement(
            'checkbox',
            'is_active',
            ts('Is this Inventory Item active?')
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
        //CRM_Core_Session::setStatus(ts(''), "Title", 'error');
        $values['excluded_pages'] = (array_key_exists("excluded_pages", $values)) ? array_keys($values['excluded_pages']) : array();
        $inventorySet = civicrm_api3("Inventory", "Create", $values);

        CRM_Core_Session::setStatus(ts('This inventory item has been saved.'), "Saved", "success");
        parent::postProcess();
        return CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/admin/inventory/items', 'reset=1&sid='.$values['sid']));
    }

    function setDefaultValues() {
        $defaults = $this->getInventoryItem();
        $defaults['is_active'] = $this->_id ? CRM_Utils_Array::value('is_active', $defaults) : 1;
        if ($defaults['excluded_pages']) {
            $pages = array();
            foreach($defaults['excluded_pages'] as $page) {
                $pages[$page] = 1;
            }

            $defaults['excluded_pages'] = $pages;
        } else {
            $defaults['excluded_pages'] = array();
        }
        return $defaults;
    }

    function getInventoryItem() {
        if($this->_item) {
            return $this->_item;
        }
        if ($this->_id) {
            $inventoryItem = civicrm_api3("Inventory", "Get", array("id" => $this->_id));
            if ($inventoryItem['is_error'] == 0) {
                return $inventoryItem['values'];
            }
        }
        return array();
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

    function getPages() {
        if ($this->_iset && $this->_iset['price_set_id']) {
            return CRM_Price_BAO_PriceSet::getUsedBy($this->_iset['price_set_id']);
        }
        return array();
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
