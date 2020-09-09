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
/* oprava tabulky starších verzí PS kdy name mohlo mít pouze 32 znaků */
$sql[] = 'ALTER TABLE `'._DB_PREFIX_.'configuration` CHANGE `name` `name` VARCHAR(254) NOT NULL';

# instalace defaultních hodnot pro základní platební metody
# ------------------------------------------------------------
$all_button = Db::getInstance()->executeS('
		SELECT id_payment_button
		FROM `'._DB_PREFIX_.'pms_gopay_extra_buttons`
		WHERE 1'
);
foreach (Shop::getShops() as $shop)
{
	foreach (Currency::getCurrencies() as $currency)
	{
		if (in_array($currency['iso_code'], Pms_GoPay_Extra::$gopayCurrencies))
		{
			$count = 0;
			foreach ($all_button as $button)
			{
				$sql[] = 'INSERT IGNORE INTO `'._DB_PREFIX_.'pms_gopay_extra_buttons_strict`
					(`id_payment_button`, `id_currency`, `id_shop`, `position`) 
					VALUES (
						'.(int)$button['id_payment_button'].',
						'.(int)$currency['id_currency'].',
						'.(int)$shop['id_shop'].',
						'.$count.'
				)';
				$count += 1;
			}
		}
	}
}

if ($id_button = (int)Db::getInstance()->getValue('
		SELECT id_payment_button
		FROM `'._DB_PREFIX_.'pms_gopay_extra_buttons`
		WHERE `payment_code` = \'PAYMENT_CARD\'')
) {

	$PC_name['cs'] = 'Platební karty';
	$PC_name['sk'] = 'Platobné karty';
	$PC_name['en'] = 'Payment cards';
	$PC_desc['cs'] = 'Rychlá a bezpečná online platba';
	$PC_desc['sk'] = 'Rýchla a bezpečná online platba';
	$PC_desc['en'] = 'Fast and secure online payment';
	foreach (Shop::getShops() as $shop)
	{
		foreach (Language::getLanguages(false) as $language)
		{
			$sql[] = 'INSERT IGNORE INTO `'._DB_PREFIX_.'pms_gopay_extra_buttons_lang`
					(`id_payment_button`, `id_shop`, `id_lang`, `payment_name`, `payment_desc`, `payment_logo`)
					VALUES (
						'.(int)$id_button.',
						'.(int)$shop['id_shop'].',
						'.(int)$language['id_lang'].',
						\''.(isset($PC_name[$language['iso_code']]) ? $PC_name[$language['iso_code']] : $PC_name['en']).'\',
						\''.(isset($PC_desc[$language['iso_code']]) ? $PC_desc[$language['iso_code']] : $PC_desc['en']).'\',
						\'PAYMENT_CARD.gif\'
			)';
		}
	}
}

if ($id_button = (int)Db::getInstance()->getValue('
		SELECT id_payment_button
		FROM `'._DB_PREFIX_.'pms_gopay_extra_buttons`
		WHERE `payment_code` = \'BANK_ACCOUNT\'')
) {
	$PC_name['cs'] = 'Bankovní převody';
	$PC_name['sk'] = 'Bankové prevody';
	$PC_name['en'] = 'Bank transfers';
	$PC_desc['cs'] = 'Svou banku vyberu na platební bráně z dostupných platebních tlačítek';
	$PC_desc['sk'] = 'Svoju banku vyberiem na platobnej bráne z dostupných platobných tlačidiel';
	$PC_desc['en'] = 'I will select my bank on the payment gateway from the available payment buttons';
	foreach (Shop::getShops() as $shop)
	{
		foreach (Language::getLanguages(false) as $language)
		{
			$sql[] = 'INSERT IGNORE INTO `'._DB_PREFIX_.'pms_gopay_extra_buttons_lang`
					(`id_payment_button`, `id_shop`, `id_lang`, `payment_name`, `payment_desc`, `payment_logo`)
					VALUES (
						'.(int)$id_button.',
						'.(int)$shop['id_shop'].',
						'.(int)$language['id_lang'].',
						\''.(isset($PC_name[$language['iso_code']]) ? $PC_name[$language['iso_code']] : $PC_name['en']).'\',
						\''.(isset($PC_desc[$language['iso_code']]) ? $PC_desc[$language['iso_code']] : $PC_desc['en']).'\',
						\'BANK_ACCOUNT.gif\'
			)';
		}
	}
}

if ($id_button = (int)Db::getInstance()->getValue('
		SELECT id_payment_button
		FROM `'._DB_PREFIX_.'pms_gopay_extra_buttons`
		WHERE `payment_code` = \'ACCOUNT\'')
) {
	$PC_name['cs'] = 'Platbu vyberu na platební bráně';
	$PC_name['sk'] = 'Platbu vyberiem na platobnej bráne';
	$PC_name['en'] = 'Payment select to the payment gateway';
	$PC_desc['cs'] = 'Vyberu svou platební metodu ze všech dostupných po přesměrování na platební bránu';
	$PC_desc['sk'] = 'Vyberiem svoju platobnú metódu zo všetkých dostupných po presmerovaní na platobnú bránu';
	$PC_desc['en'] = 'I choose my payment method from all available when redirected to the payment gateway';
	foreach (Shop::getShops() as $shop)
	{
		foreach (Language::getLanguages(false) as $language)
		{
			$sql[] = 'INSERT IGNORE INTO `'._DB_PREFIX_.'pms_gopay_extra_buttons_lang`
					(`id_payment_button`, `id_shop`, `id_lang`, `payment_name`, `payment_desc`, `payment_logo`)
					VALUES (
						'.(int)$id_button.',
						'.(int)$shop['id_shop'].',
						'.(int)$language['id_lang'].',
						\''.(isset($PC_name[$language['iso_code']]) ? $PC_name[$language['iso_code']] : $PC_name['en']).'\',
						\''.(isset($PC_desc[$language['iso_code']]) ? $PC_desc[$language['iso_code']] : $PC_desc['en']).'\',
						\'ACCOUNT.gif\'
			)';
		}
	}
}
