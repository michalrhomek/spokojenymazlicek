<?php
/* ########################################################################### */
/*                                                                             */
/*                      Copyright 2014     Miloslav Kubín                      */
/*                        http://presta-modul.shopmk.cz                        */
/*                                                                             */
/*             Please do not change this text, remove the link,                */
/*          or remove all or any part of the creator copyright notice          */
/*                                                                             */
/*    Please also note that although you are allowed to make modifications     */
/*     for your own personal use, you may not distribute the original or       */
/*                 the modified code without permission.                       */
/*                                                                             */
/*                    SELLING AND REDISTRIBUTION IS FORBIDDEN!                 */
/*             Download is allowed only from presta-modul.shopmk.cz            */
/*                                                                             */
/*       This software is provided as is, without warranty of any kind.        */
/*           The author shall not be liable for damages of any kind.           */
/*               Use of this software indicates that you agree.                */
/*                                                                             */
/*                                    ***                                      */
/*                                                                             */
/*              Prosím, neměňte tento text, nemazejte odkazy,                  */
/*      neodstraňujte části a nebo celé oznámení těchto autorských práv        */
/*                                                                             */
/*     Prosím vezměte také na vědomí, že i když máte možnost provádět změny    */
/*        pro vlastní osobní potřebu,nesmíte distribuovat původní nebo         */
/*                        upravený kód bez povolení.                           */
/*                                                                             */
/*                   PRODEJ A DISTRIBUCE JE ZAKÁZÁNA!                          */
/*          Download je povolen pouze z presta-modul.shopmk.cz                 */
/*                                                                             */
/*   Tento software je poskytován tak, jak je, bez záruky jakéhokoli druhu.    */
/*          Autor nenese odpovědnost za škody jakéhokoliv druhu.               */
/*                  Používáním tohoto softwaru znamená,                        */
/*           že souhlasíte s výše uvedenými autorskými právy .                 */
/*                                                                             */
/* ########################################################################### */
require_once(dirname(__FILE__).'/../../config/config.inc.php');

if (Tools::isSubmit('submit_date_due'))
{
	$id_order = Tools::getValue('id_order');
	Configuration::updateValue('FA_DUE_'.$id_order, Tools::getValue('date_due'));

	$array = array(
			'ok' => 1,
			'add' => Configuration::get('FA_DUE_'.$id_order)
	);
	die(Tools::jsonEncode($array));
}

elseif (Tools::isSubmit('submit_date'))
{
	$output = 1;
	$order = new Order(Tools::getValue('id_order'));

	if (Tools::getValue('datum') && ToTime(Tools::getValue('datum')) != ToTime($order->date_add))
		$order->date_add = ToTime(Tools::getValue('datum')).' '.ThisTime();

	elseif (Tools::getValue('datum_inv') && ToTime(Tools::getValue('datum_inv')) != ToTime($order->invoice_date))
		$order->invoice_date = ToTime(Tools::getValue('datum_inv')).' '.ThisTime();

	elseif (Tools::getValue('datum_dlv') && ToTime(Tools::getValue('datum_dlv')) != ToTime($order->delivery_date))
		$order->delivery_date = ToTime(Tools::getValue('datum_dlv')).' '.ThisTime();
	else
		$output = 2;

	if (!$order->update())
		$output = 3;

	$array = array(
			'ok' => $output,
			'add' => $order->date_add,
			'inv' => $order->invoice_date,
			'dlv' => $order->delivery_date
	);
	die(Tools::jsonEncode($array));
}

elseif (Tools::isSubmit('submit_slip'))
{
	$output = 1;
	$id_order = Tools::getValue('id_order');
	if (!Configuration::updateValue('FA_SLIP_DUE_'.$id_order, Tools::getValue('slipDate_due')) ||
		!Configuration::updateValue('FA_SLIP_BANKNUMBER_'.$id_order, Tools::getValue('slipBankNumber')) ||
		!Configuration::updateValue('FA_SLIP_TEXT_'.$id_order, Tools::getValue('slipText'))
	)
		$output = 2;

	$array = array(
			'ok' => $output,
			'slipDate_due' => Configuration::get('FA_SLIP_DUE_'.$id_order),
			'slipBankNumber' => Configuration::get('FA_SLIP_BANKNUMBER_'.$id_order),
			'slipText' => Configuration::get('FA_SLIP_TEXT_'.$id_order)
	);
	die(Tools::jsonEncode($array));
}

function ToTime($time)
{
	return StrFTime("%Y-%m-%d",StrToTime($time));
}

function ThisTime()
{
	return StrFTime("%H:%M:%S", Time());
}