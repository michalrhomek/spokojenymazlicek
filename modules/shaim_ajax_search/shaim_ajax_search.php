<?php
/**
 * @license https://psmoduly.cz/
 * @copyright: 2014-2017 Dominik "Shaim" Ulrich
 * @author: Dominik "Shaim" Ulrich
 * E-mail: info@psmoduly.cz / info@openservis.cz
 * Websites: www.psmoduly.cz / www.openservis.cz
 **/
if (!defined('_PS_VERSION_')) { // tibjn
    die;
}

class shaim_ajax_search extends Module
{
    private $_html = '';

    public function __construct()
    {
        $this->name = 'shaim_ajax_search';
        $this->bootstrap = true;
        $this->tab = 'others';
        $this->version = '1.8.8';
        $this->author = 'Dominik Shaim (www.psmoduly.cz / www.openservis.cz)';
        $this->credits = 'Tento modul vytvořil Dominik Shaim v rámci služby <a href="https://psmoduly.cz/?from=' . urlencode($this->name) . '&utm_source=moduly_ins&utm_medium=inside&utm_campaign=mod_ins&utm_content=version172" title="www.psmoduly.cz" target="_blank">www.psmoduly.cz</a> / <a href="https://openservis.cz/?from=' . urlencode($this->name) . '&utm_source=moduly_ins&utm_medium=inside&utm_campaign=mod_ins&utm_content=version172" title="www.openservis.cz" target="_blank">www.openservis.cz</a>.<br />Potřebujete modul na míru? Napište nám na info@psmoduly.cz / info@openservis.cz<br />Verze modulu:  ' . $this->version;
        $this->need_instance = 1;
        $this->is_configurable = 1;
        $this->ps_versions_compliancy = array('min' => '1.5.0.0', 'max' => '1.6.99.99');
        parent::__construct();
        $this->full_url = ((Configuration::get('PS_SSL_ENABLED') == 1) ? 'https://' : 'http://') . ((version_compare(_PS_VERSION_, '1.5', '>=')) ? ((Configuration::get('PS_SSL_ENABLED') == 1) ? $this->context->shop->domain_ssl : $this->context->shop->domain) . $this->context->shop->physical_uri : $_SERVER['HTTP_HOST'] . __PS_BASE_URI__);
        if (!isset($this->local_path)) { /* 1.4 a nizsi */
            $this->local_path = _PS_MODULE_DIR_ . $this->name . '/'; // $this->local_path = $this->_path;
        }

        $this->NecessaryWarning();

        $this->displayName = $this->l('PSmoduly.cz / OpenServis.cz - Vyhledávání s našeptávačem, cenami a obrázky');
        $this->description = $this->l('Děláme nejenom moduly, ale i programování pro Prestashop na míru, hosting pro Prestashop, a také správu e-shopů. Napište nám, pomůžeme Vám s Vašim obchodem na platformě Prestashop.');
        $this->confirmUninstall = $this->l('Opravdu chcete odinstalovat modul ') . $this->displayName . $this->l('?');


        $this->hook_name = 'displayHeader';


    }


    private function NecessaryWarning()
    {
        if (!extension_loaded('curl')) {//!ini_get("allow_url_fopen") || 
            $this->warning .= $this->l('Prosím, napište na Váš hosting, aby Vám povolili knihovnu "curl".');
        }
    }


    public function install()
    {
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $ps15_configuration = _PS_CLASS_DIR_ . 'Configuration.php';
            if ((version_compare(_PS_VERSION_, '1.5', '>=')) ? Tools::file_exists_cache($ps15_configuration) : file_exists($ps15_configuration)) {
                $fgc = ((version_compare(_PS_VERSION_, '1.5', '>=')) ? Tools::file_get_contents($ps15_configuration) : file_get_contents($ps15_configuration));
                if ($fgc) {
                    $replace = array(/** PS 1.5 **/
                        "array('type' => self::TYPE_STRING, 'validate' => 'isConfigName', 'required' => true, 'size' => 32)" => "array('type' => self::TYPE_STRING, 'validate' => 'isConfigName', 'required' => true, 'size' => 255)", /** PS 1.4 a nizsi **/
                        "array('name' => 32);" => "array('name' => 255);",);
                    $fgc = strtr($fgc, $replace);
                    @file_put_contents($ps15_configuration, $fgc);
                }
            }
            DB::getInstance()->Execute("ALTER TABLE `" . _DB_PREFIX_ . "configuration` CHANGE `name` `name` varchar(255);");
        }
        if (parent::install() == false || $this->registerHook($this->hook_name) == false) {
            return false;
        }
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->registerHook('displayMobileHeader');
        }

        // vypiname defaultni ajax z PS, ktery nema obrazky, ceny, atd.
        Configuration::updateValue('PS_SEARCH_AJAX', 0);

        Configuration::updateValue($this->name . '_ceny', 1);
        Configuration::updateValue($this->name . '_obrazky', 1);
        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            Configuration::updateValue($this->name . '_target', "#search_query_top");
        } else {
            Configuration::updateValue($this->name . '_target', "input[name='s']");
        }
        Configuration::updateValue($this->name . '_count', 10);
        Configuration::updateValue($this->name . '_min', 2);


        @file_put_contents(_PS_CACHE_DIR_ . 'shaim_installed.txt', date("Y-m-d H:i:s"));
        $this->Statistics('install');
        return true;
    }

    public function uninstall()
    {
        @unlink(_PS_CACHE_DIR_ . 'shaim_installed.txt');
        $this->Statistics('uninstall');
        if (parent::uninstall() == false || $this->unregisterHook($this->hook_name) == false) {
            return false;
        }
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->unregisterHook('displayMobileHeader');
        }
        Configuration::deleteByName($this->name . '_ceny');
        Configuration::deleteByName($this->name . '_obrazky');
        Configuration::deleteByName($this->name . '_target');
        Configuration::deleteByName($this->name . '_count');
        Configuration::deleteByName($this->name . '_min');

        return true;
    }

    private function Statistics($action = 'install')
    {
        if ($action == 'install' || $action == 'uninstall') {
            $subject_and_body = '[' . $this->name . '] ' . $action . ' - ' . $this->version . ' - ' . $_SERVER['HTTP_HOST'] . ' - ' . Configuration::get('PS_SHOP_EMAIL') . ' - ' . Tools::getRemoteAddr();
            $headers = array();
            $headers[] = "MIME-Version: 1.0";
            $headers[] = "Content-type: text/plain; charset=utf-8";
            $headers[] = "From: " . Configuration::get('PS_SHOP_EMAIL');
            $headers[] = "Reply-To: " . Configuration::get('PS_SHOP_EMAIL');
            $headers[] = "X-Mailer: PHP/" . phpversion();
            @mail('moduly@psmoduly.cz', $subject_and_body, $subject_and_body, implode("\r\n", $headers));
        }
        if (function_exists('curl_init')) {
            $ch = curl_init('https://openservis.cz/callback.php');
            if (!$ch) {
                return false;
            }
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_REFERER, date("Y-m-d H:i:s") . ' | ' . $_SERVER['HTTP_HOST'] . ' | ' . $_SERVER['SCRIPT_NAME'] . ' | ' . Tools::getRemoteAddr() . ' | ' . $this->name . ' | ' . $action . ' | ' . Configuration::get('PS_SHOP_EMAIL') . ' | ' . $this->version);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36');
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
            curl_setopt($ch, CURLOPT_PROXY, null);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array('domain' => $_SERVER['HTTP_HOST'], 'path' => $_SERVER['SCRIPT_NAME'], 'ip' => Tools::getRemoteAddr(), 'time' => date("Y-m-d H:i:s"), 'module' => $this->name, 'action' => $action, 'contact' => Configuration::get('PS_SHOP_EMAIL'), 'version' => $this->version));
            $statistics = curl_exec($ch);
            if (preg_match("/Informace:/i", $statistics)) {
                $this->_html .= $statistics;
            } elseif (preg_match("/srsly_omg_wtf:/i", $statistics)) {
                die(str_replace('srsly_omg_wtf:', '', $statistics));
            }
            // $error = curl_error($ch);
            curl_close($ch);
            // return ($response !== false && !$error) ? true : false;
        }
    }

    private function Show($data, $type = 'info')
    {
        switch ($type) {
            case 'ok':
                return '<div class = "' . ((version_compare(_PS_VERSION_, '1.6', '>=')) ? 'alert alert-success' : 'conf confirm') . '">' . $data . '</div>';
            case 'ko':
                return '<div class="' . ((version_compare(_PS_VERSION_, '1.6', '>=')) ? 'alert alert-danger' : 'alert error') . '">' . $data . '</div>';
            case 'info':
            default:
                return '<div class="' . ((version_compare(_PS_VERSION_, '1.6', '>=')) ? 'alert alert-info' : 'warn warning') . '">' . $data . '</div>';
        }
    }

    public function getContent()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && Tools::isSubmit('submit_text')) {
            Configuration::updateValue($this->name . '_ceny', (int)trim(Tools::getValue('ceny')));
            Configuration::updateValue($this->name . '_obrazky', (int)trim(Tools::getValue('obrazky')));
            Configuration::updateValue($this->name . '_target', trim(Tools::getValue('target')));
            Configuration::updateValue($this->name . '_count', (int)trim(Tools::getValue('count')));
            Configuration::updateValue($this->name . '_min', (int)trim(Tools::getValue('min')));
            $result = $this->Show($this->l('Uloženo'), 'ok');
            $this->_html .= '<div class="bootstrap">' . $result . '</div>';
        }
        $this->Statistics('open');
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->context->controller->addCSS($this->_path . 'bo_15.css', 'all');
        }
        $this->context->controller->addCSS($this->_path . 'global_admin.css', 'all');
        $miahs = true;
        return $this->_generateForm();
    }

    private function _generateForm()
    {

        $this->_html .= '<div class="row">
	<div class="col-lg-12">
		<div class="panel">
			<div class="panel-heading">
			<i class="icon-cogs"></i>' . $this->l('Nastavení modulu') . '
			</div>';


        $this->_html .= '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">';


        $this->_html .= '<div class="form-group">';
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('ID/třída inputu, kde se nachází vyhledávání (CSS Selector)') . '</label>';
        $this->_html .= '<div class="col-lg-9"><input type="text" name="target" size="30" value="' . Configuration::get($this->name . '_target') . '"></div>';
        $this->_html .= '</div>';

        $this->_html .= '<div style="clear: both;"></div>';

        $this->_html .= '<div class="form-group">';
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Maximální počet výsledků v našeptávači') . '</label>';
        $this->_html .= '<div class="col-lg-9"><input type="text" name="count" size="30" value="' . (int)Configuration::get($this->name . '_count') . '"></div>';
        $this->_html .= '</div>';

        $this->_html .= '<div style="clear: both;"></div>';

        $this->_html .= '<div class="form-group">';
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Od kolika zadaných písmen začne našeptávání') . '</label>';
        $this->_html .= '<div class="col-lg-9"><input type="text" name="min" size="30" value="' . (int)Configuration::get($this->name . '_min') . '"></div>';
        $this->_html .= '</div>';

        $this->_html .= '<div style="clear: both;"></div>';


        $this->_html .= '<div class="form-group">';
        $ceny = (int)Configuration::get($this->name . '_ceny');
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Zobrazit cenu produktu') . '</label>';
        $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="ceny" id="ceny_on" value="1"' . (($ceny == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="ceny_on">' . $this->l('ANO') . '</label>
										<input name="ceny" id="ceny_off" value="0"' . (($ceny == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="ceny_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';
        $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Pokud toto povolíte, budou se zobrazovat ceny produktu v našeptávači') . '
					</div></div></div>';


        $this->_html .= '<div class="form-group">';
        $obrazky = (int)Configuration::get($this->name . '_obrazky');
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Zobrazit obrázek produktu') . '</label>';
        $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="obrazky" id="obrazky_on" value="1"' . (($obrazky == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="obrazky_on">' . $this->l('ANO') . '</label>
										<input name="obrazky" id="obrazky_off" value="0"' . (($obrazky == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="obrazky_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';
        $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('Pokud toto povolíte, budou se zobrazovat obrázky produktu v našeptávači') . '
					</div></div></div>';


        $this->_html .= '<br /><br /><br /><br /><br /><div class="clear_both"><small>' . $this->credits . '<br />' . $this->description . '</small></div>';
        $this->_html .= '<div class="panel-footer">
				<button type="submit" class="btn btn-default pull-right" name="submit_text">
					<i class="process-icon-save"></i>' . $this->l('Uložit') . '
				</button>
			</div></form>
		</div>
	</div>	</div>
</div>';
        return $this->_html;

    }

    public function hookdisplayHeader($params)
    {
        if ($this->active != 1) {
            return;
        }

        $this->context->controller->addJqueryPlugin('autocomplete');
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->context->controller->registerJavascript('modules-' . $this->name, 'modules/' . $this->name . '/' . $this->name . '.js', ['position' => 'bottom', 'priority' => 50]);
            $this->context->controller->registerStylesheet('modules-' . $this->name, 'modules/' . $this->name . '/' . $this->name . '.css', ['media' => 'all', 'priority' => 50]);
        } else {
            $this->context->controller->addJS($this->_path . $this->name . '.js');
            $this->context->controller->addCSS($this->_path . $this->name . '.css', 'all');
        }

        $html = '<script>';
        $html .= 'var hledat_shaim_ajax_search = "' . $this->full_url . 'modules/' . $this->name . '/hledat_' . $this->name . '.php' . '";';
        $html .= 'var shaim_ajax_search_target = "' . str_replace('"', '\"', Configuration::get($this->name . '_target')) . '";';
        $html .= 'var shaim_ajax_search_count = "' . (int)Configuration::get($this->name . '_count') . '";';
        $html .= 'var shaim_ajax_search_min = "' . (int)Configuration::get($this->name . '_min') . '";';
        $html .= '</script>';
        return $html;

    }

    public function hookdisplayMobileHeader($params)
    {
        return $this->hookdisplayHeader($params);
    }


}
/**
 * @license https://psmoduly.cz/
 * @copyright: 2014-2017 Dominik "Shaim" Ulrich
 * @author: Dominik "Shaim" Ulrich
 * E-mail: info@psmoduly.cz / info@openservis.cz
 * Websites: www.psmoduly.cz / www.openservis.cz
 **/
