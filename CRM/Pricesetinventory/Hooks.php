<?php

class CRM_Pricesetinventory_Hooks {

  static $_nullObject = NULL;

  /**
   * Alters a single Inventory Item
   *
   * @param $item
   * @return mixed
   */
  public static function alterInventoryItem($context, &$item) {
    return CRM_Utils_Hook::singleton()->invoke(2, $context, $item,
      self::$_nullObject, self::$_nullObject, self::$_nullObject, self::$_nullObject,
      'civicrm_alterInventory'
    );
  }


  /**
   * Alters an Inventory Set
   *
   * @param $set
   * @return mixed
   */
  public static function alterInventorySet(&$set) {
    return CRM_Utils_Hook::singleton()->invoke(1, $set, self::$_nullObject,
      self::$_nullObject, self::$_nullObject, self::$_nullObject, self::$_nullObject,
      'civicrm_alterInventorySet'
    );
  }

}