
DROP TABLE IF EXISTS `civicrm_priceset_inventory`;

CREATE TABLE `civicrm_priceset_inventory` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `field_id` INT UNSIGNED NOT NULL,
  `field_value_id` INT NULL,
  `sid` INT UNSIGNED NOT NULL,
  `image_path` VARCHAR(255) NULL,
  `image_data` BLOB NULL,
  `title` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `quantity` INT NULL,
  `is_active` TINYINT(1) NULL DEFAULT 1,
  `default_open` TINYINT(1) NULL DEFAULT 0,
  `excluded_pages` VARCHAR(255) NULL DEFAULT 'a:0:{}',
  PRIMARY KEY (`id`));


DROP TABLE IF EXISTS `civicrm_priceset_inventory_set`;

CREATE TABLE `civicrm_priceset_inventory_set` (
  `sid` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `price_set_id` INT UNSIGNED NOT NULL,
  `is_active` TINYINT(1) NULL DEFAULT 1,
  `excluded_pages` VARCHAR(255) NULL DEFAULT 'a:0:{}',
  PRIMARY KEY (`sid`));
