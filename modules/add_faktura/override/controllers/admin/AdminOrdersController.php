<?php
class AdminOrdersController extends AdminOrdersControllerCore
{
	 public function initToolbar()
	 {
		if(Module::isInstalled('add_faktura') && Module::isEnabled('add_faktura'))
		{

			if (Tools::isSubmit('submitLangAjax'))
			{
				include_once(_PS_MODULE_DIR_.'/add_faktura/add_faktura.php');
				$module = new Add_Faktura();
				$module_languages = Language::getLanguages(false);

				$tpl_enable = '
					<div class="bootstrap">
						<div class="modal-body">
							<div class="input-group">
								<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
									<i class="icon-flag"></i>
									'.$module->TRANSLATIONS_MESSAGE.'
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu">
				';

				$currentIndex = self::$currentIndex.'&token='.$this->token;
				foreach ($module_languages as $language)
					$tpl_enable .= '
									<li>
										<a href="'.$currentIndex.'&langFaktura='.$language['id_lang'].'&currentIndex='.$currentIndex.'">
											'.$language['name'].'
										</a>
									</li>
					';

				$tpl_enable .= '
								</ul>
							</div>
						</div>
					</div>
				';

				die(Tools::jsonEncode($tpl_enable));
			}

			elseif (Tools::isSubmit('langFaktura'))
			{
				$employee = new Employee(Context::getContext()->employee->id);
				$employee->id_lang = Tools::getValue('langFaktura');

				if ($employee->update())
					Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminOrders'));
			}

			$this->page_header_toolbar_btn['langFaktura'] = array(
					'desc' => $this->l('PDF lang - ').Context::getContext()->language->iso_code,
					'class' => 'icon-file-pdf-o',
					'js' => 'selectLang(\''.self::$currentIndex.'&token='.$this->token.'\');'
			);
		}

        parent::initToolbar();
	 }
}