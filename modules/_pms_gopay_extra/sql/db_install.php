<?php
/** ########################################################################### * 
 *                                                                             * 
 *                      Presta Module Shop | Copyright 2018                    * 
 *                           www.prestamoduleshop.com                          * 
 *                                                                             * 
 *             Please do not change this text, remove the link,                * 
 *          or remove all or any part of the creator copyright notice          * 
 *                                                                             * 
 *    Please also note that although you are allowed to make modifications     * 
 *     for your own personal use, you may not distribute the original or       * 
 *                 the modified code without permission.                       * 
 *                                                                             * 
 *                    SELLING AND REDISTRIBUTION IS FORBIDDEN!                 * 
 *             Download is allowed only from www.prestamoduleshop.com          * 
 *                                                                             * 
 *       This software is provided as is, without warranty of any kind.        * 
 *           The author shall not be liable for damages of any kind.           * 
 *               Use of this software indicates that you agree.                * 
 *                                                                             * 
 *                                    ***                                      * 
 *                                                                             * 
 *              Prosím, neměňte tento text, nemažte odkazy,                    * 
 *      neodstraňujte části a nebo celé oznámení těchto autorských práv        * 
 *                                                                             * 
 *     Prosím vezměte také na vědomí, že i když máte možnost provádět změny    * 
 *        pro vlastní osobní potřebu, nesmíte distribuovat původní nebo        * 
 *                        upravený kód bez povolení.                           * 
 *                                                                             * 
 *                   PRODEJ A DISTRIBUCE JE ZAKÁZÁNA!                          * 
 *          Stažení je povoleno pouze z www.prestamoduleshop.com               * 
 *                                                                             * 
 *   Tento software je poskytován tak, jak je, bez záruky jakéhokoli druhu.    * 
 *          Autor nenese odpovědnost za škody jakéhokoliv druhu.               * 
 *                  Používáním tohoto softwaru znamená,                        * 
 *           že souhlasíte s výše uvedenými autorskými právy.                  * 
 *                                                                             * 
 * ########################################################################### **/

/* Init */
$sql = array();

# tabulky parametrů payment buttons
# ------------------------------------------------------------
$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pms_gopay_extra_buttons` (
	`id_payment_button`		 int(11)		 unsigned NOT NULL AUTO_INCREMENT,
	`payment_code`			 varchar(30)	 NOT NULL,
	`isGroup`				 tinyint(1)		 unsigned NOT NULL DEFAULT 0,
	`isSwift`				 tinyint(1)		 unsigned NOT NULL DEFAULT 0,
	`payment_group`			 varchar(56)	 NULL,
	`isOnline`				 tinyint(1)		 unsigned NOT NULL DEFAULT 0,
	`isPreauthorized`		 tinyint(1)		 unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id_payment_button`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pms_gopay_extra_buttons_lang` (
	`id_payment_button`		 int(11)		 unsigned NOT NULL,
	`id_shop`				 int(10)		 unsigned NOT NULL DEFAULT 1,
	`id_lang`				 int(11)		 unsigned NOT NULL,
	`payment_name`			 varchar(128)	 NOT NULL,
	`payment_desc`			 longtext,
	`payment_logo`			 varchar(256),
  PRIMARY KEY (`id_payment_button`, `id_shop`, `id_lang`),
  KEY `id_payment_button_fk` (`id_payment_button`),
  CONSTRAINT `id_payment_button_fk` FOREIGN KEY (`id_payment_button`) REFERENCES `'._DB_PREFIX_.'pms_gopay_extra_buttons` (`id_payment_button`) ON DELETE CASCADE
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pms_gopay_extra_buttons_strict` (
	`id_payment_button`		 int(11)		 unsigned NOT NULL,
	`id_shop`				 int(10)		 unsigned NOT NULL DEFAULT 1,
	`id_currency`			 int(10)		 unsigned NOT NULL DEFAULT 1,
	`payment_fee`			 decimal(20,2)	 DEFAULT 0,
	`payment_fee_type`		 tinyint(1)		 unsigned NOT NULL DEFAULT 0,
	`position`				 int(10)		 unsigned NOT NULL DEFAULT 0,
	`visible`				 tinyint(1)		 unsigned NOT NULL DEFAULT 1,
	`active`				 tinyint(1)		 unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_payment_button`, `id_shop`, `id_currency`),
  KEY `id_payment_button_fs` (`id_payment_button`),
  CONSTRAINT `id_payment_button_fs` FOREIGN KEY (`id_payment_button`) REFERENCES `'._DB_PREFIX_.'pms_gopay_extra_buttons` (`id_payment_button`) ON DELETE CASCADE
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';


# instalace defaultních hodnot pro základní platební metody
$sql[] = 'INSERT IGNORE INTO `'._DB_PREFIX_.'pms_gopay_extra_buttons`
	(`id_payment_button`, `payment_code`, `isGroup`, `payment_group`, `isPreauthorized`) 
		VALUES
		(1, \'PAYMENT_CARD\', 1, \'card-payment\', 1)
	';

$sql[] = 'INSERT IGNORE INTO `'._DB_PREFIX_.'pms_gopay_extra_buttons`
	(`id_payment_button`, `payment_code`, `isGroup`, `payment_group`) 
		VALUES
		(2, \'BANK_ACCOUNT\', 1, \'bank-transfer\')
	';

$sql[] = 'INSERT IGNORE INTO `'._DB_PREFIX_.'pms_gopay_extra_buttons`
	(`id_payment_button`, `payment_code`, `isGroup`) 
	VALUES
	(3, \'ACCOUNT\', 1)
';


//**** vykopírováno
$sql[] = '
	CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pms_gopay_extra_bills` (
		`id_gopay_bill` int(11) NOT NULL AUTO_INCREMENT,
		`id_order` int(11) NOT NULL,
		`id_session` bigint(10) NOT NULL,
		`dat_trzby` datetime NOT NULL,
		`fik` varchar(39) NOT NULL,
		`bkp` varchar(44) NOT NULL,
		`pkp` varchar(344) NOT NULL,
		PRIMARY KEY (`id_gopay_bill`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
	';

$sql[] = '
	CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pms_gopay_extra_order` (
		`id_order` int(10) NOT NULL,
		`id_cart` int(10) NOT NULL,
		`id_session` varchar(15) NOT NULL,
		`currency` int(10) NOT NULL,
		`total_paid` decimal(20,6) NOT NULL,
		`recurrent` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
		`preauthorized` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
		`payment_date` datetime NOT NULL,
		`update_date` datetime NOT NULL,
		`payment_status` varchar(255) DEFAULT NULL,
		PRIMARY KEY (`id_order`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
	';

$sql[] = '
	CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pms_gopay_extra_refund` (
		`id_gopay_refund` int(11) NOT NULL AUTO_INCREMENT,
		`id_order` int(11) NOT NULL,
		`refund_amount` decimal(20,6) NOT NULL,
		`result` text NOT NULL,
		`date_add` datetime NOT NULL,
		PRIMARY KEY (`id_gopay_refund`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
	';

$sql[] = '
	CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pms_gopay_extra_refund_products` (
		`id_refund_product` int(11) NOT NULL AUTO_INCREMENT,
		`id_order` int(11) NOT NULL,
		`id_order_detail` int(11) NOT NULL,
		`quantity` int(11) NOT NULL,
		`refund_shipping` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
		PRIMARY KEY (`id_refund_product`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
	';

$sql[] = '
	CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pms_gopay_extra_recurrent` (
		`id_gopay_recurrent` int(11) NOT NULL AUTO_INCREMENT,
		`id_order` int(11) NOT NULL,
		`id_session` bigint(10) NOT NULL,
		`id_parent_session` int(10) NOT NULL,
		`date_add` datetime NOT NULL,
		`recurrence_period` int(11) NOT NULL,
		`recurrence_cycle` varchar(255) NOT NULL,
		`recurrence_date_to` date NOT NULL,
		`recurrence_state` text NOT NULL,
		PRIMARY KEY (`id_gopay_recurrent`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
	';



