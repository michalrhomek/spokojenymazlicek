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

class AdminPms_GoPay_ExtraController extends ModuleAdminController
{
	public function __construct()
	{
		parent::__construct();

		if (Tools::isSubmit('action') && Tools::isSubmit('tabName'))
		{
			$class = 'Pms_GoPay_Extra';
			$tabName = Tools::getValue('tabName');
			$tabFolder = Tools::getValue('tabFolder');
			$action = Tools::getValue('action');
			if ($tabName == 'core')
				$tabName = strtolower($class);
			else
				$class = $class.'_'.ucfirst($tabName);

			include_once(str_replace('/controllers/admin', '', dirname(__FILE__)).'/'.$tabFolder.$tabName.'.php');

			$trans = new $class();
			if (method_exists($trans, $action))
			{
				$data_type = 'json';
				if (Tools::isSubmit('dataType'))
				{
					$data_type = Tools::getValue('dataType');
				}
				switch ($data_type)
				{
					case 'html':
						die($trans->$action());
					case 'json':
						$response = Tools::jsonEncode($trans->$action());
						die($response);
					default:
						die('Invalid data type.');
				}
			} else {
				die('403 Forbidden method not exist');
			}
		}
	}
}