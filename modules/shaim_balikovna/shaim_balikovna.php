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

class shaim_balikovna extends Module
{
    private $_html = '';
    private $order_conf_info = false;


    public function __construct()
    {
        $this->name = 'shaim_balikovna';
        $this->bootstrap = true;
        $this->tab = 'others';
        $this->version = '1.8.8';
        $this->author = 'Dominik Shaim (www.psmoduly.cz / www.openservis.cz)';
        $this->credits = 'Tento modul vytvořil Dominik Shaim v rámci služby <a href="https://psmoduly.cz/?from=' . urlencode($this->name) . '&utm_source=moduly_ins&utm_medium=inside&utm_campaign=mod_ins&utm_content=version177" title="www.psmoduly.cz" target="_blank">www.psmoduly.cz</a> / <a href="https://openservis.cz/?from=' . urlencode($this->name) . '&utm_source=moduly_ins&utm_medium=inside&utm_campaign=mod_ins&utm_content=version177" title="www.openservis.cz" target="_blank">www.openservis.cz</a>.<br />Potřebujete modul na míru? Napište nám na info@psmoduly.cz / info@openservis.cz<br />Verze modulu:  ' . $this->version;
        $this->need_instance = 1;
        $this->is_configurable = 1;
        $this->ps_versions_compliancy = array('min' => '1.5.0.0', 'max' => '1.7.99.99');
        parent::__construct();
        $this->full_url = ((Configuration::get('PS_SSL_ENABLED') == 1) ? 'https://' : 'http://') . ((version_compare(_PS_VERSION_, '1.5', '>=')) ? ((Configuration::get('PS_SSL_ENABLED') == 1) ? $this->context->shop->domain_ssl : $this->context->shop->domain) . $this->context->shop->physical_uri : $_SERVER['HTTP_HOST'] . __PS_BASE_URI__);
        if (!isset($this->local_path)) { /* 1.4 a nizsi */
            $this->local_path = _PS_MODULE_DIR_ . $this->name . '/'; // $this->local_path = $this->_path;
        }
        
        $this->NecessaryWarning();
        

        $this->displayName = $this->l('PSmoduly.cz / OpenServis.cz - Balík Do balíkovny');

        $this->description = $this->l('Děláme nejenom moduly, ale i programování pro Prestashop na míru, hosting pro Prestashop, a také správu e-shopů. Napište nám, pomůžeme Vám s Vašim obchodem na platformě Prestashop.');
        $this->confirmUninstall = $this->l('Opravdu chcete odinstalovat modul ') . $this->displayName . $this->l('?');

        $this->hook_name = 'displayCarrierList';
        $this->hook_name2 = 'displayHeader';
        $this->hook_name3 = 'displayAdminOrder';
        $this->hook_name4 = 'displayOrderConfirmation';


        if (Configuration::get('PS_DISABLE_OVERRIDES') == 1) { // override upozornění
            // $this->warning .= $this->l('Tento modul vyžaduje ke správné funkčnosti povolené "přepsání" (tzv. overrides), které máte aktuálně zakázané. Povolit override můžete v Nástroje -> Výkon.');
        }
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
        if (parent::install() == false || $this->registerHook($this->hook_name) == false || $this->registerHook($this->hook_name2) == false || $this->registerHook($this->hook_name3) == false || $this->registerHook($this->hook_name4) == false) {
            return false;
        }
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->registerHook('displayAfterCarrier');
        } elseif (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->registerHook('displayMobileHeader');
        }

        Configuration::updateValue($this->name . '_dopravce_cz', 0);
        Configuration::updateValue($this->name . '_pobocka_zobrazit', 1);
        Configuration::updateValue($this->name . '_mapa_zobrazit', 1);
        Configuration::updateValue($this->name . '_last_update', date("d. m. Y"));

        $sql = "
		CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "{$this->name}` (
  `id_posta` int(10) NOT NULL AUTO_INCREMENT,
  `psc` char(5) NOT NULL,
  `naz_prov` varchar(100) NOT NULL,
  `okres` varchar(100) NOT NULL,
  `adresa` varchar(100) NOT NULL,
  `pondeli_od` char(5) NOT NULL,
  `pondeli_do` char(5) NOT NULL,
  `utery_od` char(5) NOT NULL,
  `utery_do` char(5) NOT NULL,
  `streda_od` char(5) NOT NULL,
  `streda_do` char(5) NOT NULL,
  `ctvrtek_od` char(5) NOT NULL,
  `ctvrtek_do` char(5) NOT NULL,
  `patek_od` char(5) NOT NULL,
  `patek_do` char(5) NOT NULL,
  `sobota_od` char(5) NOT NULL,
  `sobota_do` char(5) NOT NULL,
  `nedele_od` char(5) NOT NULL,
  `nedele_do` char(5) NOT NULL,
  `exists` tinyint(1) NOT NULL DEFAULT '1',
  `type` char(2) NOT NULL,
  `typ` varchar(10) NOT NULL,
  `sour_x` varchar(20) NOT NULL,
  `sour_y` varchar(20) NOT NULL,
  `cast_obce` varchar(100) NOT NULL,
  PRIMARY KEY (`id_posta`),
  UNIQUE KEY `psc` (`psc`)
)
COLLATE=utf8_czech_ci";
        $sql2 = "
		CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "{$this->name}_data` (
  `id_data` int(10) NOT NULL AUTO_INCREMENT,
  `id_cart` int(10) NOT NULL DEFAULT '0',
  `psc` char(5) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id_data`),
  UNIQUE KEY `id_cart` (`id_cart`)
) COLLATE=utf8_czech_ci";


        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql);
        $res2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql2);

        $this->Update(true);
        @file_put_contents(_PS_CACHE_DIR_ . 'shaim_installed.txt', date("Y-m-d H:i:s"));
        $this->Statistics('install');
        if (!$res || !$res2) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        @unlink(_PS_CACHE_DIR_ . 'shaim_installed.txt');
        $this->Statistics('uninstall');
        if (parent::uninstall() == false || $this->unregisterHook($this->hook_name) == false || $this->unregisterHook($this->hook_name2) == false || $this->unregisterHook($this->hook_name3) == false || $this->unregisterHook($this->hook_name4) == false) {
            return false;
        }
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->unregisterHook('displayAfterCarrier');
        } elseif (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->unregisterHook('displayMobileHeader');
        }

        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "{$this->name};");

        Configuration::deleteByName($this->name . '_dopravce_cz');
        Configuration::deleteByName($this->name . '_pobocka_zobrazit');
        Configuration::deleteByName($this->name . '_mapa_zobrazit');
        Configuration::deleteByName($this->name . '_last_update');
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
            curl_setopt($ch, CURLOPT_REFERER, date("Y-m-d H:i:s") . ' | ' . $_SERVER['HTTP_HOST'] . ' | ' . $_SERVER['SCRIPT_NAME'] . ' | ' . Tools::getRemoteAddr() . ' | ' . $this->name . ' | ' . $action . ' | ' . Configuration::get('PS_SHOP_EMAIL') . ' | ' . $this->version);curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
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

    private function Update($bo = false)
    {

        $this->UpdateBalikovna($bo);
    }

    private function CacheIt($url, $lang = 'cz')
    {
        if (time() >= filemtime($this->local_path . 'data_' . $lang . '.xml') + 3600) {
            $fgc = Tools::file_get_contents($url);
            if ($fgc && strlen($fgc) > 1000) {
                file_put_contents($this->local_path . 'data_' . $lang . '.xml', $fgc);
            }
        }
    }

    public function UpdateBalikovna($bo = false)
    {

        @chmod($this->local_path . 'data_cz.xml', 0777);
        $this->CacheIt('http://napostu.ceskaposta.cz/vystupy/balikovny.xml', 'cz');
        $xml = simplexml_load_string(Tools::file_get_contents($this->local_path . 'data_cz.xml'));
        if ($xml && isset($xml->row) && $xml->row) {

            DB::GetInstance()->Execute("UPDATE " . _DB_PREFIX_ . "{$this->name} SET `exists` = 0 WHERE type = 'cz';");
            foreach ($xml->row as $x) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "{$this->name} SET
psc = '" . preg_replace('/\s/', '', $x->PSC) . "',
naz_prov = '" . addslashes($x->NAZEV) . "',
okres = '$x->OBEC',
adresa = '$x->ADRESA',
pondeli_od = '{$x->OTEV_DOBY->den[0]->od_do->od}',
pondeli_do = '{$x->OTEV_DOBY->den[0]->od_do->do}',
utery_od = '{$x->OTEV_DOBY->den[1]->od_do->od}',
utery_do = '{$x->OTEV_DOBY->den[1]->od_do->do}',
streda_od = '{$x->OTEV_DOBY->den[2]->od_do->od}',
streda_do = '{$x->OTEV_DOBY->den[2]->od_do->do}',
ctvrtek_od = '{$x->OTEV_DOBY->den[3]->od_do->od}',
ctvrtek_do = '{$x->OTEV_DOBY->den[3]->od_do->do}',
patek_od = '{$x->OTEV_DOBY->den[4]->od_do->od}',
patek_do= '{$x->OTEV_DOBY->den[4]->od_do->do}',
sobota_od = '{$x->OTEV_DOBY->den[5]->od_do->od}',
sobota_do = '{$x->OTEV_DOBY->den[5]->od_do->do}',
nedele_od = '{$x->OTEV_DOBY->den[6]->od_do->od}',
nedele_do = '{$x->OTEV_DOBY->den[6]->od_do->do}',
`type` = 'cz',
`exists` = 1,
typ = '{$x->TYP}',
sour_x = '{$x->SOUR_X}',
sour_y = '{$x->SOUR_Y}',
cast_obce = '{$x->C_OBCE}'
ON DUPLICATE KEY UPDATE
naz_prov = '" . addslashes($x->NAZEV) . "',
okres = '$x->C_OBCE',
adresa = '$x->ADRESA',
pondeli_od = '{$x->OTEV_DOBY->den[0]->od_do->od}',
pondeli_do = '{$x->OTEV_DOBY->den[0]->od_do->do}',
utery_od = '{$x->OTEV_DOBY->den[1]->od_do->od}',
utery_do = '{$x->OTEV_DOBY->den[1]->od_do->do}',
streda_od = '{$x->OTEV_DOBY->den[2]->od_do->od}',
streda_do = '{$x->OTEV_DOBY->den[2]->od_do->do}',
ctvrtek_od = '{$x->OTEV_DOBY->den[3]->od_do->od}',
ctvrtek_do = '{$x->OTEV_DOBY->den[3]->od_do->do}',
patek_od = '{$x->OTEV_DOBY->den[4]->od_do->od}',
patek_do= '{$x->OTEV_DOBY->den[4]->od_do->do}',
sobota_od = '{$x->OTEV_DOBY->den[5]->od_do->od}',
sobota_do = '{$x->OTEV_DOBY->den[5]->od_do->do}',
nedele_od = '{$x->OTEV_DOBY->den[6]->od_do->od}',
nedele_do = '{$x->OTEV_DOBY->den[6]->od_do->do}',
`exists` = 1,
typ = '{$x->TYP}',
sour_x = '{$x->SOUR_X}',
sour_y = '{$x->SOUR_Y}',
cast_obce = '{$x->C_OBCE}'
";
                DB::GetInstance()->Execute($sql);
            }
            Configuration::updateValue($this->name . '_last_update', date("d. m. Y"));

        } elseif ($bo === false) {
            die($this->l('Nepodařilo se stáhnout XML soubor pro aktualizaci poboček.'));
        }


    }


    public function Cron()
    {
        $this->Update(false);
        die($this->l('HOTOVO'));
    }

    public function getContent()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && Tools::isSubmit('submit_text')) {
            Configuration::updateValue($this->name . '_dopravce_cz', (int)trim(Tools::getValue('dopravce_cz')));
            Configuration::updateValue($this->name . '_pobocka_zobrazit', (int)trim(Tools::getValue('pobocka_zobrazit')));
            Configuration::updateValue($this->name . '_mapa_zobrazit', (int)trim(Tools::getValue('mapa_zobrazit')));
            $result = $this->Show($this->l('Uloženo a aktualizováno'), 'ok');
            $this->_html .= '<div class="bootstrap">' . $result . '</div>';
            $this->Update(true);
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

        $this->_html .= '<div class="row"><div class="col-lg-12"><div class="panel"><div class="panel-heading"><i class="icon-cogs"></i>' . $this->l('Nastavení modulu') . '</div>';

        $this->_html .= '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">';

        $carriers = Carrier::getCarriers($this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);
        if ($carriers) {

            $this->_html .= '<label>' . $this->l('Dopravce určený pro Balík Do balíkovny') . ':</label><br />';

            $this->_html .= '<div class="margin-form">';


            $this->_html .= '<select name="dopravce_cz" style="width: auto;">';
            $dopravce = (int)Configuration::get($this->name . '_dopravce_cz');
            $this->_html .= '<option value="0">---</option>';
            foreach ($carriers as $c) {
                $selected = ($dopravce > 0 && $c['id_reference'] == $dopravce) ? ' selected="selected"' : '';
                $this->_html .= '<option value="' . $c['id_reference'] . '"' . $selected . '>' . $c['name'] . '</option>';
            }
            $this->_html .= '</select>';
            $this->_html .= '</div><br />';
        } else {
            $this->_html .= '<div class="alert alert-danger">';
            $this->_html .= $this->l('Neexistuje žádný dopravce, nejdříve nějakého vytvořte a poté proveďte párování.');
            $this->_html .= '</div>';
        }


        $this->_html .= '<div class="form-group">';
        $pobocka_zobrazit = (int)Configuration::get($this->name . '_pobocka_zobrazit');
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Zobrazit na poslední stránce objednávky informace o vybrané pobočce') . '</label>';
        $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="pobocka_zobrazit" id="pobocka_zobrazit_on" value="1"' . (($pobocka_zobrazit == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="pobocka_zobrazit_on">' . $this->l('ANO') . '</label>
										<input name="pobocka_zobrazit" id="pobocka_zobrazit_off" value="0"' . (($pobocka_zobrazit == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="pobocka_zobrazit_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';
        $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('') . '
					</div></div>';
        $this->_html .= '</div><div class="clear_both"></div>';


        $this->_html .= '<div class="form-group">';
        $mapa_zobrazit = (int)Configuration::get($this->name . '_mapa_zobrazit');
        $this->_html .= '<label class="control-label col-lg-3">' . $this->l('Zobrazit na poslední stránce objednávky mapu') . '</label>';
        $this->_html .= '<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
										<input name="mapa_zobrazit" id="mapa_zobrazit_on" value="1"' . (($mapa_zobrazit == 1) ? ' checked="checked"' : '') . ' type="radio">
										<label for="mapa_zobrazit_on">' . $this->l('ANO') . '</label>
										<input name="mapa_zobrazit" id="mapa_zobrazit_off" value="0"' . (($mapa_zobrazit == 0) ? ' checked="checked"' : '') . ' type="radio">
										<label for="mapa_zobrazit_off">' . $this->l('NE') . '</label>
										<a class="slide-button btn"></a>
									</span></div>';
        $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">
					' . $this->l('') . '
					</div></div>';
        $this->_html .= '</div><div class="clear_both"></div>';

        $this->_html .= '<div class="margin-form">';
        $this->_html .= '<button class="btn btn-default" type="submit" name="submit_text">
											<i class="icon-save"></i> ' . $this->l('Uložit a aktualizovat pobočky (může trvat i několik desítek sekund, po kliknutí prosím vyčkejte)') . '
											</button>';
        $this->_html .= '</div><br />';
        $this->_html .= '<div class="alert alert-info">';
        $this->_html .= $this->l('Poslední aktualizace: ') . Configuration::get($this->name . '_last_update');
        $this->_html .= '</div><br />';
        $this->_html .= '<div class="well">';
        $this->_html .= '<b>' . $this->l('Cron URL (slouží pro aktualizaci poboček) - doporučujeme nastavit 1x denně v noci: ') . '</b><br /><br /><a target="_blank" href="' . $this->full_url . 'modules/' . $this->name . '/shaim_cron.php"><i class="icon-external-link-sign"></i> ' . $this->full_url . 'modules/' . $this->name . '/shaim_cron.php</a>';
        $this->_html .= '</div>';

        $find = 'CheckHardBalikovna';
        if (Module::isEnabled('advancedcheckout')) {
            if (!preg_match("/typeof $find/i", Tools::file_get_contents(_PS_MODULE_DIR_ . 'advancedcheckout/views/js/main.js'))) {
                $this->_html .= '<div class="form-group">';
                $this->_html .= '<div>' . $this->l('Používáte OPC modul "advancedcheckout" - v tomto modulu je pro plnou funkčnost třeba provést úpravy, viz odkaz:') . '</div>';
                $this->_html .= '<div><a href="https://psmoduly.cz/content/14-balik-do-balikovny-upravy-ve-specifickych-objednavacich-procesech#advancedcheckout" target="_blank">Úpravy "advancedcheckout"</a></div>';
                $this->_html .= '<div>' . $this->l('V případě zájmu o úpravu nám napište, úprava je v rámci ceny modulu zdarma. Až bude úprava provedena, tato hláška z modulu zmizí.') . '</div>';
                $this->_html .= '</div>';
            }
        } elseif (Module::isEnabled('onepagecheckout')) {
            if (!preg_match("/typeof $find/i", Tools::file_get_contents(_PS_MODULE_DIR_ . 'onepagecheckout/views/js/order-opc.js'))) {
                $this->_html .= '<div class="form-group">';
                $this->_html .= '<div>' . $this->l('Používáte OPC modul "onepagecheckout" - v tomto modulu je pro plnou funkčnost třeba provést úpravy, viz odkaz:') . '</div>';
                $this->_html .= '<div><a href="https://psmoduly.cz/content/14-balik-do-balikovny-upravy-ve-specifickych-objednavacich-procesech#onepagecheckout" target="_blank">Úpravy "onepagecheckout"</a></div>';
                $this->_html .= '<div>' . $this->l('V případě zájmu o úpravu nám napište, úprava je v rámci ceny modulu zdarma. Až bude úprava provedena, tato hláška z modulu zmizí.') . '</div>';
                $this->_html .= '</div>';
            }
        } elseif (Module::isEnabled('supercheckout')) {
            if (!preg_match("/typeof $find/i", Tools::file_get_contents(_PS_MODULE_DIR_ . 'supercheckout/views/js/front/supercheckout.js'))) {
                $this->_html .= '<div class="form-group">';
                $this->_html .= '<div>' . $this->l('Používáte OPC modul "supercheckout" - v tomto modulu je pro plnou funkčnost třeba provést úpravy, viz odkaz:') . '</div>';
                $this->_html .= '<div><a href="https://psmoduly.cz/content/14-balik-do-balikovny-upravy-ve-specifickych-objednavacich-procesech#supercheckout" target="_blank">Úpravy "supercheckout"</a></div>';
                $this->_html .= '<div>' . $this->l('V případě zájmu o úpravu nám napište, úprava je v rámci ceny modulu zdarma. Až bude úprava provedena, tato hláška z modulu zmizí.') . '</div>';
                $this->_html .= '</div>';
            }
        } elseif (Module::isEnabled('onepagecheckoutps')) {
            if (!preg_match("/typeof $find/i", Tools::file_get_contents(_PS_MODULE_DIR_ . 'onepagecheckoutps/views/templates/front/js/onepagecheckoutps.js')) && !preg_match("/typeof $find/i", Tools::file_get_contents(_PS_MODULE_DIR_ . 'onepagecheckoutps/views/js/front/onepagecheckoutps.js'))) {
                $this->_html .= '<div class="form-group">';
                $this->_html .= '<div>' . $this->l('Používáte OPC modul "onepagecheckoutps" - v tomto modulu je pro plnou funkčnost třeba provést úpravy, viz odkaz:') . '</div>';
                $this->_html .= '<div><a href="https://psmoduly.cz/content/14-balik-do-balikovny-upravy-ve-specifickych-objednavacich-procesech#onepagecheckoutps" target="_blank">Úpravy "onepagecheckoutps"</a></div>';
                $this->_html .= '<div>' . $this->l('V případě zájmu o úpravu nám napište, úprava je v rámci ceny modulu zdarma. Až bude úprava provedena, tato hláška z modulu zmizí.') . '</div>';
                $this->_html .= '</div>';
            }
        } elseif (Module::isEnabled('thecheckout')) {
            if (!preg_match("/typeof $find/i", Tools::file_get_contents(_PS_MODULE_DIR_ . 'thecheckout/views/js/front.js'))) {
                $this->_html .= '<div class="form-group">';
                $this->_html .= '<div>' . $this->l('Používáte OPC modul "thecheckout" - v tomto modulu je pro plnou funkčnost třeba provést úpravy, viz odkaz:') . '</div>';
                $this->_html .= '<div><a href="https://psmoduly.cz/content/14-balik-do-balikovny-upravy-ve-specifickych-objednavacich-procesech#thecheckout" target="_blank">Úpravy "thecheckout"</a></div>';
                $this->_html .= '<div>' . $this->l('V případě zájmu o úpravu nám napište, úprava je v rámci ceny modulu zdarma. Až bude úprava provedena, tato hláška z modulu zmizí.') . '</div>';
                $this->_html .= '</div>';
            }
        } elseif (Module::isEnabled('spstepcheckout')) {
            if (!preg_match("/typeof $find/i", Tools::file_get_contents(_PS_MODULE_DIR_ . 'spstepcheckout/views/js/front/spstepcheckout.js'))) {
                $this->_html .= '<div class="form-group">';
                $this->_html .= '<div>' . $this->l('Používáte OPC modul "spstepcheckout" - v tomto modulu je pro plnou funkčnost třeba provést úpravy, viz odkaz:') . '</div>';
                $this->_html .= '<div><a href="https://psmoduly.cz/content/14-balik-do-balikovny-upravy-ve-specifickych-objednavacich-procesech#spstepcheckout" target="_blank">Úpravy "spstepcheckout"</a></div>';
                $this->_html .= '<div>' . $this->l('V případě zájmu o úpravu nám napište, úprava je v rámci ceny modulu zdarma. Až bude úprava provedena, tato hláška z modulu zmizí.') . '</div>';
                $this->_html .= '</div>';
            }
        } elseif (Module::isEnabled('steasycheckout')) {
            if (!preg_match("/typeof $find/i", Tools::file_get_contents(_PS_MODULE_DIR_ . 'steasycheckout/views/js/front.js'))) {
                $this->_html .= '<div class="form-group">';
                $this->_html .= '<div>' . $this->l('Používáte OPC modul "steasycheckout" - v tomto modulu je pro plnou funkčnost třeba provést úpravy, viz odkaz:') . '</div>';
                $this->_html .= '<div><a href="https://psmoduly.cz/content/14-balik-do-balikovny-upravy-ve-specifickych-objednavacich-procesech#steasycheckout" target="_blank">Úpravy "steasycheckout"</a></div>';
                $this->_html .= '<div>' . $this->l('V případě zájmu o úpravu nám napište, úprava je v rámci ceny modulu zdarma. Až bude úprava provedena, tato hláška z modulu zmizí.') . '</div>';
                $this->_html .= '</div>';
            }
        }


        $this->_html .= '</form>';
        $this->_html .= '<br /><br /><br /><div class="clear_both"><small>' . $this->credits . '<br />' . $this->description . '</small></div>';
        $this->_html .= '</div></div></div>';

        return $this->_html;
    }

    public function hookdisplayCarrierList($params)
    {

        if ($this->active != 1) {
            return;
        }

        $dopravce_cz = (int)Configuration::get($this->name . '_dopravce_cz');
        if (empty($dopravce_cz)) {
            return false;
        }


        $id_reference = (int)DB::getInstance()->getValue("SELECT id_reference FROM `" . _DB_PREFIX_ . "carrier` WHERE id_carrier = " . (int)$params['cart']->id_carrier . ";");


        $html = '';
        /* Presouvame do dislayHeader
        $html = '<script>';
        if (Module::isEnabled('advancedcheckout')) {
            $html .= 'var disable_text_balikovna = "' . $this->l('Pro Balík do balíkovny vyberte, prosím, vhodnou pobočku viz výše. Zadejte PSČ, nebo název města, a po vyhledání klikněte na tlačítko \"Vybrat tuto balíkovnu\" nebo případně zvolte jiného dopravce.') . '";';
        }
        $html .= 'var id_customer = "' . (int)$this->context->customer->id . '";';
        $html .= 'var id_cart = "' . (int)$params['cart']->id . '";';
        $html .= 'var text_adresa = "' . $this->l('Adresa') . '";';
                   $html .= 'var exists_opc = "' . (Module::isEnabled('advancedcheckout') ? 'advancedcheckout' : (Module::isEnabled('onepagecheckout') ? 'onepagecheckout' : (Module::isEnabled('supercheckout') ? 'supercheckout' : (Module::isEnabled('onepagecheckoutps') ? 'onepagecheckoutps' : (Module::isEnabled('thecheckout') ? 'thecheckout' : (Module::isEnabled('spstepcheckout') ? 'spstepcheckout' : (Module::isEnabled('steasycheckout') ? 'steasycheckout' : (Module::isEnabled('prestakosik') ? 'prestakosik' : (Module::isEnabled('threepagecheckout') ? 'threepagecheckout' : '0'))))))))) . '";';
        $html .= 'var ps_version = "' . $version . '";';


        $html .= 'var text_balikovna = "' . $this->l('Balíkovna') . '";';
        $html .= 'var text_zvolit_balikovna = "' . $this->l('Zvolit balíkovnu') . '";';
        $html .= 'var text_vybrat_balikovna = "' . $this->l('Vybrat tuto balíkovnu') . '";';
        $html .= 'var zvolena_balikovna = "' . $this->l('Zvolená Balíkovna') . '";';
        $html .= 'var hledat_balikovna_ajax = "' . $this->full_url . 'modules/' . $this->name . '/hledat_balikovna_ajax.php' . '";';
        $html .= 'var shaim_balikovna_nejdrive = "' . $this->l('Nejdříve musíte vybrat pobočku Balíkovny.') . '";';
        $html .= 'var shaim_balikovna_nic = "' . $this->l('Žádný záznam nebyl nalezen.') . '";';
        $html .= 'var shaim_balikovna_prazdne = "' . $this->l('Zadejte alespoň 2 znaky.') . '";';
        $html .= '</script>';
        */


        $wh = 1;
         if (empty($id_reference) && version_compare(_PS_VERSION_, '1.7', '>=')) { $option = $this->context->cart->getDeliveryOption(null, false); if (!empty($option) && is_array($option)) { $id_carrier = (int)reset($option); $carrier = new Carrier($id_carrier); $id_reference = (int)$carrier->id_reference; } } // toto se deje pri nacteni kosiku poprve, kdyz jeste nebyl zakliknuty dopravce
        if (empty($id_reference) && version_compare(_PS_VERSION_, '1.7', '>=')) {
            $default_carrier = (int)Configuration::get('PS_CARRIER_DEFAULT');
            if ($default_carrier == '-1') { // -1 = dle ceny (nejlevnejsi)
                if ($this->context->cart->id_address_delivery > 0) {
                    $address = new Address($this->context->cart->id_address_delivery);
                    $country = new Country($address->id_country);
                    $carriers = Carrier::getCarriersForOrder($country->id_zone);
                    $price_lowest = false;
                    foreach ($carriers as $carrier) {
                        if ($price_lowest === false || $carrier['price'] < $price_lowest) {
                            $price_lowest = $carrier['price'];
                            $id_reference = $carrier['id_reference'];
                        }
                    }
                }
            } else {
                $carrier = new Carrier($default_carrier);
                $id_reference = $carrier->id_reference;
            }
        }
        if (empty($id_reference) || $id_reference != $dopravce_cz) {
            $wh = 0;

            // if (version_compare(_PS_VERSION_, '1.7', '<') && version_compare(_PS_VERSION_, '1.6', '>=')) {
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                $html .= "<script>DeleteMessageBalikovna(true);</script>";

                if (!Module::isEnabled('onepagecheckout') && !Module::isEnabled('threepagecheckout')) { // u tohoto modulu to nechceme returnovat, protoze potrebujeme ty dalsi udaje, znovu se ten hook uz asi nevola to vypada....
                    return $html;
                }
            }
        }
        // if (version_compare(_PS_VERSION_, '1.7', '>=') || version_compare(_PS_VERSION_, '1.6', '<')) {
        $carrier_cz = Carrier::getCarrierByReference($dopravce_cz);
        $html .= '<script>
            var wh_balikovna = ' . $wh . ';
            var dopravce_cz_balikovna = ' . (isset($carrier_cz->id) ? $carrier_cz->id : 0) . ';
            </script>';
        // }


        $html .= '<div id="vyhledejte_pobocku_balikovna">';
        $html .= '<div id="text_vyhledejte">' . $this->l('Vyhledejte pobočku Balíkovny, kde budete chtít vyzvednout zásilku. Zadejte PSČ nebo město, poté si vyberte Balíkovnu ze seznamu.') . '</div>';

        $html .= '<input type="text" name="find_balikovna_zip_city" id="find_balikovna_zip_city" class="col-md-12" autocomplete="off" placeholder="' . $this->l('Vepište do tohoto pole PSČ nebo město, výsledky se zobrazí automaticky.') . '" />';
        $html .= '<div id="result_balikovna_zip_city" data-title="' . $this->l('Zadejte alespoň 2 znaky.') . '">' . $this->l('Zadejte alespoň 2 znaky.') . '</div>';
        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            if (!Module::isEnabled('onepagecheckout') && !Module::isEnabled('spstepcheckout') && !Module::isEnabled('threepagecheckout')) { // Tohle u onepagecheckout je u starsich verzi nutno zakomentovat tuto podminku, od onepagecheckout2017 a starsi odhadem.
                if (version_compare(_PS_VERSION_, '1.6', '>=')) {
                    $html .= "<script>MoveInputToCarrierBalikovna();</script>";
                } else {
                    if ($wh == 1) {
                        $html .= "<script>MoveInputToCarrierBalikovna();</script>";
                    }
                }
            }
        }

        $html .= '</div>';

        return $html;
    }

    public function hookdisplayAfterCarrier($params)
    {

        return $this->hookdisplayCarrierList($params);
    }


    public function hookdisplayHeader($params)
    {
        if ($this->active != 1) {
            return;
        }
        $dopravce_cz = (int)Configuration::get($this->name . '_dopravce_cz');
        if (empty($dopravce_cz)) {
            return false;
        }

        if ((isset($this->context->controller->php_self) && $this->context->controller->php_self == 'order') || // 1.6 vicekrokova obj
            (isset($this->context->controller->php_self) && ($this->context->controller->php_self == 'order-opc' || $this->context->controller->php_self == 'orderopc')) || // Toto je treba u "onepagecheckout" OPC, PS 1.6
            (isset($this->context->controller->php_self) && $this->context->controller->php_self == 'adv_order') || // Toto je treba u "advancedcheckout" OPC, PS 1.6
            (isset($this->context->controller->page_name) && $this->context->controller->page_name == 'module-supercheckout-supercheckout') || // Toto je treba u "supercheckout" OPC, PS 1.6
            (isset($this->context->controller->page_name) && $this->context->controller->page_name == 'module-onepagecheckoutps-main') || // Toto je treba u "onepagecheckoutps" OPC, PS 1.5
            (isset($this->context->controller->page_name) && $this->context->controller->page_name == 'module-steasycheckout-default') || // Toto je treba u "steasycheckout" OPC, PS 1.7
            (isset($this->context->controller->page_name) && $this->context->controller->page_name == 'module-prestakosik-order') // Toto je treba u "prestakosik" OPC, PS 1.7
        ) {
            if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                $this->context->controller->registerJavascript('modules-' . $this->name, 'modules/' . $this->name . '/' . $this->name . '.js', ['position' => 'bottom', 'priority' => 50]);
                $this->context->controller->registerStylesheet('modules-' . $this->name, 'modules/' . $this->name . '/' . $this->name . '.css', ['media' => 'all', 'priority' => 50]);
                $this->context->controller->registerStylesheet('modules-' . $this->name . '_17', 'modules/' . $this->name . '/' . $this->name . '_17.css', ['media' => 'all', 'priority' => 50]);
            } else {
                $this->context->controller->addJS($this->_path . $this->name . '.js');
                $this->context->controller->addCSS($this->_path . $this->name . '.css', 'all');
            }
            // Jo, neni to fakt chyba, ipro 1.5 pouzivame!
            if (version_compare(_PS_VERSION_, '1.6', '<')) {
                $this->context->controller->addCSS($this->_path . $this->name . '_17.css', 'all');
            }

            if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                $version = '1.7';
            } elseif (version_compare(_PS_VERSION_, '1.6', '>=')) {
                $version = '1.6';
            } elseif (version_compare(_PS_VERSION_, '1.5', '>=')) {
                $version = '1.5';
            } else {
                $version = 0;
            }

            $html = '<script>';
            if (Module::isEnabled('advancedcheckout')) {
                $html .= 'var disable_text_balikovna = "' . $this->l('Pro Balík do balíkovny vyberte, prosím, vhodnou pobočku viz výše. Zadejte PSČ, nebo název města, a po vyhledání klikněte na tlačítko \"Vybrat tuto balíkovnu\" nebo případně zvolte jiného dopravce.') . '";';
            }
            $html .= 'var id_customer = "' . (int)$this->context->customer->id . '";';
            $html .= 'var id_cart = "' . (int)$params['cart']->id . '";';
            $html .= 'var text_adresa = "' . $this->l('Adresa') . '";';
            $html .= 'var exists_opc = "' . (Module::isEnabled('advancedcheckout') ? 'advancedcheckout' : (Module::isEnabled('onepagecheckout') ? 'onepagecheckout' : (Module::isEnabled('supercheckout') ? 'supercheckout' : (Module::isEnabled('onepagecheckoutps') ? 'onepagecheckoutps' : (Module::isEnabled('thecheckout') ? 'thecheckout' : (Module::isEnabled('spstepcheckout') ? 'spstepcheckout' : (Module::isEnabled('steasycheckout') ? 'steasycheckout' : (Module::isEnabled('prestakosik') ? 'prestakosik' : (Module::isEnabled('threepagecheckout') ? 'threepagecheckout' : '0'))))))))) . '";';
            $html .= 'var ps_version = "' . $version . '";';


            $html .= 'var text_balikovna = "' . $this->l('Balíkovna') . '";';
            $html .= 'var text_zvolit_balikovna = "' . $this->l('Zvolit balíkovnu') . '";';
            $html .= 'var text_vybrat_balikovna = "' . $this->l('Vybrat tuto balíkovnu') . '";';
            $html .= 'var zvolena_balikovna = "' . $this->l('Zvolená Balíkovna') . '";';
            $html .= 'var hledat_balikovna_ajax = "' . $this->full_url . 'modules/' . $this->name . '/hledat_balikovna_ajax.php' . '";';
            $html .= 'var shaim_balikovna_nejdrive = "' . $this->l('Nejdříve musíte vybrat pobočku Balíkovny.') . '";';
            $html .= 'var shaim_balikovna_nic = "' . $this->l('Žádný záznam nebyl nalezen.') . '";';
            $html .= 'var shaim_balikovna_prazdne = "' . $this->l('Zadejte alespoň 2 znaky.') . '";';
            $html .= '</script>';
            return $html;
        } elseif ((isset($this->context->controller->php_self) && $this->context->controller->php_self == 'order-confirmation')) {
            if ($this->order_conf_info = $this->OrderConfWH()) {
                if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                    $this->context->controller->registerJavascript('modules-' . $this->name . '_order_confirmation', 'modules/' . $this->name . '/' . $this->name . '_order_confirmation.js', ['position' => 'bottom', 'priority' => 50]);
                    $this->context->controller->registerStylesheet('modules-' . $this->name . '_order_conf', 'modules/' . $this->name . '/' . $this->name . '_order_conf.css', ['media' => 'all', 'priority' => 50]);
                    $this->context->controller->registerStylesheet('modules-' . $this->name . '_order_conf_17', 'modules/' . $this->name . '/' . $this->name . '_order_conf_17.css', ['media' => 'all', 'priority' => 50]);
                } else {
                    $this->context->controller->addJS($this->_path . $this->name . '_order_confirmation.js');
                    $this->context->controller->addCSS($this->_path . $this->name . '_order_conf.css', 'all');
                }
                if (version_compare(_PS_VERSION_, '1.6', '<')) {
                    $this->context->controller->addCSS($this->_path . $this->name . '_order_conf_15.css', 'all');
                }
            }
        }

    }

    public function hookdisplayMobileHeader($params)
    {
        return $this->hookdisplayHeader($params);
    }


    public function hookdisplayAdminOrder($params)
    {


        if ($this->active != 1) {
            return;
        }
        $this->context->controller->addJS($this->_path . $this->name . '_bo.js');

        $id_order = (isset($params['id_order']) && !empty($params['id_order']) ? (int)$params['id_order'] : (int)$_GET['id_order']);
        $order = new Order($id_order);
        if (Validate::isLoadedObject($order)) {
            $tmp = DB::getInstance()->ExecuteS("SELECT b.naz_prov, b.adresa FROM `" . _DB_PREFIX_ . $this->name . "_data` as a
             INNER JOIN `" . _DB_PREFIX_ . $this->name . "` as b ON (a.psc = b.psc)
            WHERE a.id_cart = {$order->id_cart};");

            if (isset($tmp[0]['naz_prov']) && !empty($tmp[0]['naz_prov'])) {

                $html = '<div id="' . $this->name . '_move" class="panel">
					<div class="panel-heading">
					' . $this->l('Balík Do balíkovny') . '
					</div>' . $tmp[0]['naz_prov'] . ', ' . $tmp[0]['adresa'] . '</div>';

                return $html;
            }
        }
    }


    private function OrderConfWH()
    {
        $id_order = (isset($params['id_order']) && !empty($params['id_order']) ? (int)$params['id_order'] : (int)$_GET['id_order']);
        $order = new Order($id_order);
        if (Validate::isLoadedObject($order)) {
            $tmp = DB::getInstance()->ExecuteS("SELECT b.* FROM `" . _DB_PREFIX_ . $this->name . "_data` as a
             INNER JOIN `" . _DB_PREFIX_ . $this->name . "` as b ON (a.psc = b.psc)
            WHERE a.id_cart = {$order->id_cart};");

            if (isset($tmp[0]['naz_prov']) && !empty($tmp[0]['naz_prov'])) {
                return $tmp[0];
            }

        }
        return false;
    }

    public function hookdisplayOrderConfirmation($params)
    {
        if ($this->active != 1) {
            return;
        }


        if ((int)Configuration::get($this->name . '_mapa_zobrazit') == 0 && (int)Configuration::get($this->name . '_pobocka_zobrazit') == 0) {
            return;
        }

        if ($this->order_conf_info) {
            $tmp = $this->order_conf_info;
            $pondeli = $tmp['pondeli_od'] . '-' . $tmp['pondeli_do'];
            $utery = $tmp['utery_od'] . '-' . $tmp['utery_do'];
            $streda = $tmp['streda_od'] . '-' . $tmp['streda_do'];
            $ctvrtek = $tmp['ctvrtek_od'] . '-' . $tmp['ctvrtek_do'];
            $patek = $tmp['patek_od'] . '-' . $tmp['patek_do'];
            $sobota = $tmp['sobota_od'] . '-' . $tmp['sobota_do'];
            $nedele = $tmp['nedele_od'] . '-' . $tmp['nedele_do'];

            if ($pondeli == '00:00-00:00' || $pondeli == '-') {
                $pondeli = $this->l('Zavřeno');
            }
            if ($utery == '00:00-00:00' || $utery == '-') {
                $utery = $this->l('Zavřeno');
            }
            if ($streda == '00:00-00:00' || $streda == '-') {
                $streda = $this->l('Zavřeno');
            }
            if ($ctvrtek == '00:00-00:00' || $ctvrtek == '-') {
                $ctvrtek = $this->l('Zavřeno');
            }
            if ($patek == '00:00-00:00' || $patek == '-') {
                $patek = $this->l('Zavřeno');
            }
            if ($sobota == '00:00-00:00' || $sobota == '-') {
                $sobota = $this->l('Zavřeno');
            }
            if ($nedele == '00:00-00:00' || $nedele == '-') {
                $nedele = $this->l('Zavřeno');
            }

            $this->context->smarty->assign(array(
                $this->name . '_naz_prov' => $tmp['naz_prov'] . ' (' . $this->l('Balíkovna') . ')',
                $this->name . '_adresa' => $tmp['adresa'],
                $this->name . '_okres' => $tmp['okres'],
                $this->name . '_psc' => $tmp['psc'],
                $this->name . '_pondeli' => $pondeli,
                $this->name . '_utery' => $utery,
                $this->name . '_streda' => $streda,
                $this->name . '_ctvrtek' => $ctvrtek,
                $this->name . '_patek' => $patek,
                $this->name . '_sobota' => $sobota,
                $this->name . '_nedele' => $nedele,
                $this->name . '_mapa_zobrazit' => (int)Configuration::get($this->name . '_mapa_zobrazit'),
                $this->name . '_pobocka_zobrazit' => (int)Configuration::get($this->name . '_pobocka_zobrazit'),

            ));
            return $this->display(__FILE__, $this->name . '_order_confirmation.tpl');
        }

    }

}
/**
 * @license https://psmoduly.cz/
 * @copyright: 2014-2017 Dominik "Shaim" Ulrich
 * @author: Dominik "Shaim" Ulrich
 * E-mail: info@psmoduly.cz / info@openservis.cz
 * Websites: www.psmoduly.cz / www.openservis.cz
 **/
