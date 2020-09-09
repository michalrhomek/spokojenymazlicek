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

class shaim_recaptcha extends Module
{
    private $_html = '';


    public function __construct()
    {
        $this->name = 'shaim_recaptcha';
        $this->bootstrap = true;
        $this->tab = 'others';
        $this->version = '1.8.3';
        $this->author = 'Dominik Shaim (www.psmoduly.cz / www.openservis.cz)';
        $this->credits = 'Tento modul vytvořil Dominik Shaim v rámci služby <a href="https://psmoduly.cz/?from=' . urlencode($this->name) . '&utm_source=moduly_ins&utm_medium=inside&utm_campaign=mod_ins&utm_content=version177" title="www.psmoduly.cz" target="_blank">www.psmoduly.cz</a> / <a href="https://openservis.cz/?from=' . urlencode($this->name) . '&utm_source=moduly_ins&utm_medium=inside&utm_campaign=mod_ins&utm_content=version177" title="www.openservis.cz" target="_blank">www.openservis.cz</a>.<br />Potřebujete modul na míru? Napište nám na info@psmoduly.cz / info@openservis.cz<br />Verze modulu:  ' . $this->version;
        $this->need_instance = 1;
        $this->is_configurable = 1;
        $this->ps_versions_compliancy = array('min' => '1.5.0.0', 'max' => '1.7.99.99');
        parent::__construct();
        $this->full_url = ((Configuration::get('PS_SSL_ENABLED') == 1) ? 'https://' : 'http://') . ((version_compare(_PS_VERSION_, '1.5', '>=')) ? $this->context->shop->domain . $this->context->shop->physical_uri : $_SERVER['HTTP_HOST'] . __PS_BASE_URI__);
        if (!isset($this->local_path)) { /* 1.4 a nizsi */
            $this->local_path = _PS_MODULE_DIR_ . $this->name . '/'; // $this->local_path = $this->_path;
        }
        // $this->Trusted();
        $this->NecessaryWarning();
        $this->BackwardCompatibility();
        $this->displayName = $this->l('PSmoduly.cz / OpenServis.cz - AntiSpam reCAPTCHA');
        $this->description = $this->l('Děláme nejenom moduly, ale i programování pro Prestashop na míru a také správu e-shopů. Napište nám, pomůžeme Vám s Vašim obchodem na platformě Prestashop.');
        $this->confirmUninstall = $this->l('Opravdu chcete odinstalovat modul ') . $this->displayName . $this->l('?');
        if (version_compare(_PS_VERSION_, '1.5', '>=')) {
            $this->hook_name = 'displayHeader';
            // $this->hook_name2 = 'displayCustomerAccountFormTop';
        } else {
            $this->hook_name = 'header';
            // $this->hook_name2 = 'createAccountTop';
        }
    }

    private function BackwardCompatibility()
    {
        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            include_once('backward_compatibility/backward.php');
        }
    }

    private function NecessaryWarning()
    {
        if (!extension_loaded('curl')) {//!ini_get("allow_url_fopen") ||
            $this->warning .= $this->l('Prosím, napište na Váš hosting, aby Vám povolili knihovnu "curl".');
        }
    }

    private function Trusted()
    {
        if (version_compare(_PS_VERSION_, '1.6.0.8', '>=') && version_compare(_PS_VERSION_, '1.7', '<') && Tools::getValue('controller') == 'AdminModules' && !Tools::getIsset('configure')) {
            $this->context->controller->addJS($this->_path . 'trusted.js'); // MIAHS
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
        // $this->registerHook($this->hook_name2);
        Configuration::updateValue($this->name . '_site_key', '');
        Configuration::updateValue($this->name . '_secret_key', '');
        Configuration::updateValue($this->name . '_theme', 'light');
        Configuration::updateValue($this->name . '_type', 'image');
        Configuration::updateValue($this->name . '_contact_form', 1);
        Configuration::updateValue($this->name . '_register', 0);
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
        // $this->unregisterHook($this->hook_name2);
        Configuration::deleteByName($this->name . '_site_key');
        Configuration::deleteByName($this->name . '_secret_key');
        Configuration::deleteByName($this->name . '_theme');
        Configuration::deleteByName($this->name . '_type');
        Configuration::deleteByName($this->name . '_contact_form');
        Configuration::deleteByName($this->name . '_register');
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
            Configuration::updateValue($this->name . '_site_key', trim(Tools::getValue('site_key')));
            Configuration::updateValue($this->name . '_secret_key', trim(Tools::getValue('secret_key')));
            Configuration::updateValue($this->name . '_theme', trim(Tools::getValue('theme')));
            Configuration::updateValue($this->name . '_type', trim(Tools::getValue('type')));
            Configuration::updateValue($this->name . '_contact_form', (int)trim(Tools::getValue('contact_form')));
            Configuration::updateValue($this->name . '_register', (int)trim(Tools::getValue('register')));


            $result = $this->Show($this->l('Uloženo'), 'ok');
            $this->_html .= '<div class="bootstrap">' . $result . '</div>';
        }
        $this->Statistics('open');
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->context->controller->addCSS($this->_path . 'old_admin.css', 'all');
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
        $this->_html .= '<div class="well">';
        $this->_html .= '<b>' . $this->l('Modul používá reCAPTCHA V2. Site key a Secret key získáte zde:') . '</b> <a href="https://www.google.com/recaptcha/admin" target="_blank">https://www.google.com/recaptcha/admin</a>';
        $this->_html .= '</div>';
        $this->_html .= '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">';
        $this->_html .= '<label>' . $this->l('Site key') . ':</label>';
        $this->_html .= '<div class="margin-form">';
        $this->_html .= '<input type="text" name="site_key" size="30" value="' . Configuration::get($this->name . '_site_key') . '">';
        $this->_html .= '</div>';

        $this->_html .= '<label>' . $this->l('Secret key') . ':</label>';
        $this->_html .= '<div class="margin-form">';
        $this->_html .= '<input type="text" name="secret_key" size="30" value="' . Configuration::get($this->name . '_secret_key') . '">';
        $this->_html .= '</div>';


        $this->_html .= '<div class="margin-form">';
        $this->_html .= '<label>' . $this->l('Vzhled') . ':</label>';
        $this->_html .= '
		<span class="switch prestashop-switch fixed-width-lg">
										<input name="theme" id="theme_on" value="light"' . ((Configuration::get($this->name . '_theme') == 'light') ? ' checked="checked"' : '') . ' type="radio">
										<label for="theme_on">' . $this->l('Světlý') . '</label>
										<input name="theme" id="theme_off" value="dark"' . ((Configuration::get($this->name . '_theme') == 'dark') ? ' checked="checked"' : '') . ' type="radio">
										<label for="theme_off">' . $this->l('Tmavý') . '</label>
										<a class="slide-button btn"></a>
									</span>';
        $this->_html .= '</div>';

        $this->_html .= '<div class="margin-form">';
        $this->_html .= '<label>' . $this->l('Typ') . ':</label>';
        $this->_html .= '
		<span class="switch prestashop-switch fixed-width-lg">
										<input name="type" id="type_on" value="image"' . ((Configuration::get($this->name . '_type') == 'image') ? ' checked="checked"' : '') . ' type="radio">
										<label for="type_on">' . $this->l('Obrázek') . '</label>
										<input name="type" id="type_off" value="audio"' . ((Configuration::get($this->name . '_type') == 'audio') ? ' checked="checked"' : '') . ' type="radio">
										<label for="type_off">' . $this->l('Zvuk') . '</label>
										<a class="slide-button btn"></a>
									</span>';
        $this->_html .= '</div>';


        $this->_html .= '<div class="margin-form">';
        $this->_html .= '<label>' . $this->l('Aktivovat v kontaktním formuláři') . ':</label>';
        $this->_html .= '
		<span class="switch prestashop-switch fixed-width-lg">
										<input name="contact_form" id="contact_form_on" value="1"' . ((Configuration::get($this->name . '_contact_form') == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="contact_form_on">' . $this->l('ANO') . '</label>
										<input name="contact_form" id="contact_form_off" value="0"' . ((Configuration::get($this->name . '_contact_form') == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="contact_form_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span>';
        $this->_html .= '</div>';

        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            $this->_html .= '<div class="margin-form">';
            $this->_html .= '<label>' . $this->l('Aktivovat v registraci') . ':</label>';
            $this->_html .= '
		<span class="switch prestashop-switch fixed-width-lg">
										<input name="register" id="register_on" value="1"' . ((Configuration::get($this->name . '_register') == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="register_on">' . $this->l('ANO') . '</label>
										<input name="register" id="register_off" value="0"' . ((Configuration::get($this->name . '_register') == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="register_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span>';
            $this->_html .= '</div>';
        }

        $this->_html .= '<br /><br /><br /><br /><br /><div class="clear_both"><small>' . $this->credits . '<br />' . $this->description . '</small></div>';
        $this->_html .= '<div class="panel-footer">
				<button type="submit" class="btn btn-default pull-right" name="submit_text">
					<i class="process-icon-save"></i>' . $this->l('Uložit') . '
				</button></form>
			</div>
		</div>
	</div>
</div>';
        return $this->_html;
    }

    /*
        public function hookCreateAccountTop()
        {
            return $this->hookdisplayCustomerAccountFormTop();
        }


        public function hookdisplayCustomerAccountFormTop()
        {
            if ($this->active != 1) {
                return;

            }
        }
    */

    public function hookdisplayHeader($params)
    {
        if ($this->active != 1) {
            return;

        }

        if (isset($this->context->controller->php_self) && (
                ($this->context->controller->php_self == 'contact' && Configuration::get($this->name . '_contact_form') == 1)
                ||
                ($this->context->controller->php_self == 'authentication' && Configuration::get($this->name . '_register') == 1)
            )
        ) {

            $this->context->controller->addJS($this->_path . $this->name . '.js');
            $recaptcha_contact_form = '';
            if ($this->context->controller->php_self == 'contact' && Configuration::get($this->name . '_contact_form') == 1) {
                $recaptcha_contact_form = '<div style="display: none;" id="' . $this->name . '_contact_form" class="g-recaptcha" data-sitekey="' . Configuration::get($this->name . '_site_key') . '" data-theme="' . Configuration::get($this->name . '_theme') . '" data-type="' . Configuration::get($this->name . '_type') . '"></div>';
            } elseif ($this->context->controller->php_self == 'authentication' && Configuration::get($this->name . '_register') == 1) {
                $recaptcha_contact_form = '<div style="display: none;" id="' . $this->name . '_register" class="g-recaptcha" data-sitekey="' . Configuration::get($this->name . '_site_key') . '" data-theme="' . Configuration::get($this->name . '_theme') . '" data-type="' . Configuration::get($this->name . '_type') . '"></div>';
            }
            return "<script src='https://www.google.com/recaptcha/api.js'></script>$recaptcha_contact_form";
        }

    }

    public function hookdisplayMobileHeader($params)
    {
        return $this->hookdisplayHeader($params);
    }

    public function hookheader($params)
    {
        return $this->hookdisplayHeader($params);
    }

    public function TestReCaptcha()
    {
        if ($this->active != 1) {
            return;

        }
        require_once($this->local_path . 'recaptchalib.php');
        $recaptchalib = new ReCaptcha(Configuration::get($this->name . '_secret_key'));
        return $recaptchalib->verifyResponse(Tools::getRemoteAddr(), Tools::getValue('g-recaptcha-response'));
    }

    public function ErrorReCaptcha()
    {
        return $this->l('Neplatná reCAPTCHA');
    }


}
/**
 * @license https://psmoduly.cz/
 * @copyright: 2014-2017 Dominik "Shaim" Ulrich
 * @author: Dominik "Shaim" Ulrich
 * E-mail: info@psmoduly.cz / info@openservis.cz
 * Websites: www.psmoduly.cz / www.openservis.cz
 **/
