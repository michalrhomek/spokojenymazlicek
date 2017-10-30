<?php

class FacebookLike extends Module
{
 	private $_html = '';
	private $_fl_layout = '';
	private $_fl_faces = '';
	private $_fl_width = '';
	private $_fl_text = '';
	private $_fl_font = '';
	private $_fl_color = '';
	private $_fl_send = '';
 	private $_full_version = 16000;
 	private $_last_updated = '';
	
	private $_ps_version_id = 0;
	
 	function __construct()
	{
	
		$ps_version_array = explode('.', _PS_VERSION_);
		$this->_ps_version_id = 10000 * intval($ps_version_array[0]) + 100 * intval($ps_version_array[1]);
		if (count($ps_version_array) >= 3)
			$this->_ps_version_id += intval($ps_version_array[2]);
			
		$this->name = 'facebooklike';
		$this->tab = floatval(substr(_PS_VERSION_,0,3))<1.4?'Presto-Changeo':'social_networks';
		$this->version = '1.6';
		if (floatval(substr(_PS_VERSION_,0,3)) >= 1.4)
			$this->author = 'Presto-Changeo';
		
		parent::__construct();
		
		if ($this->_ps_version_id >= 10600)	
			$this->bootstrap = true;
			
		$this->_refreshProperties();
		
		$this->displayName = $this->l('Facebook Like');
		$this->description = $this->l('Adds a Facebook "Like" button to product page');
		if ($this->upgradeCheck('FBL'))
			$this->warning = $this->l('We have released a new version of the module,') .' '.$this->l('request an upgrade at ').' https://www.presto-changeo.com/en/contact_us';
	}
	
	
	function install()
	{
		if (!parent::install())
			return false;
		$ps_version_array = explode('.', _PS_VERSION_);
		$ps_version_id = (int)($ps_version_array[0].$ps_version_array[1].$ps_version_array[2]);
		if ($ps_version_id < 155)
		{
			$hooked = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'hook` WHERE name = "facebookLike"');
			if (!is_array($hooked) || sizeof($hooked) == 0)
				Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'hook` (
				`id_hook` ,`name` ,`title` ,`description` ,`position`)
				VALUES (NULL , "facebookLike", "Facebook Like", "Custom hook for Facebook Like Module", "1");');
		}
		if (!$this->registerHook('extraLeft') || !$this->registerHook('facebookLike') || !$this->registerHook('header'))
			return false;
		Configuration::updateValue('FL_LAYOUT','standard');			
		Configuration::updateValue('FL_FACES','false');			
		Configuration::updateValue('FL_WIDTH','260');			
		Configuration::updateValue('FL_TEXT','like');			
		Configuration::updateValue('FL_FONT','arial');			
		Configuration::updateValue('FL_COLOR','light');			
		Configuration::updateValue('FL_SEND','');			
		Configuration::updateValue('PRESTO_CHANGEO_UC',time());			
		return true;
	}

	private function _refreshProperties()
	{
		$this->_fl_layout = Configuration::get('FL_LAYOUT');
		$this->_fl_faces = Configuration::get('FL_FACES');
		$this->_fl_width = max(intval(Configuration::get('FL_WIDTH')),$this->_fl_layout == "standard"?225:90);
		$this->_fl_text = Configuration::get('FL_TEXT');
		$this->_fl_font = Configuration::get('FL_FONT');
		$this->_fl_color = Configuration::get('FL_COLOR');
		$this->_fl_send = Configuration::get('FL_SEND');
		$this->_last_updated = Configuration::get('PRESTO_CHANGEO_UC');
	}

	public function getContent()
	{
		$ps_version  = floatval(substr(_PS_VERSION_,0,3));
		$this->_html = ''.($ps_version >= 1.5 ? ''.($this->_ps_version_id < 10600 ? '<div style="width: 850px; margin: 0 auto;">' : '<div>').'' : '').$this->getModuleRecommendations('FBL').'<h2 style="clear:both;padding-top:5px;">'.$this->displayName.' '.$this->version.'</h2>';
		$this->_postProcess();
		$this->_displayForm();
		return $this->_html.''.($ps_version >= 1.5 ? '</div> ' : '');
	}
	
    private function _displayForm()
    {
    	global $cookie;
		$ps_version  = floatval(substr(_PS_VERSION_,0,3));
		if ($url = $this->upgradeCheck('FBL'))
			$this->_html .= '
			
			
			'.($this->_ps_version_id < 10600 ? '<fieldset class="width3" style="background-color:#FFFAC6;width:800px;">' : '<div class="panel">').' 
				'.($this->_ps_version_id < 10600 ? '<legend>' : '<h3> ').'
					<img src="'.$this->_path.'logo.gif" />&nbsp;&nbsp;&nbsp;'.$this->l('New Version Available').'
				'.($this->_ps_version_id < 10600 ? '</legend>' : '</h3>' ).'
			'.$this->l('We have released a new version of the module. For a list of new features, improvements and bug fixes, view the ').'<a href="'.$url.'" target="_index"><b><u>'.$this->l('Change Log').'</b></u></a> '.$this->l('on our site.').'
			<br />
			'.$this->l('For real-time alerts about module updates, be sure to join us on our') .' <a href="http://www.facebook.com/pages/Presto-Changeo/333091712684" target="_index"><u><b>Facebook</b></u></a> / <a href="http://twitter.com/prestochangeo1" target="_index"><u><b>Twitter</b></u></a> '.$this->l('pages').'.
			<br />
			<br />
			'.$this->l('Please').' <a href="https://www.presto-changeo.com/en/contact_us" target="_index"><b><u>'.$this->l('contact us').'</u></b></a> '.$this->l('to request an upgrade to the latest version (Free modules should be downloaded directly from our site again)').'.
			'.($this->_ps_version_id < 10600 ? '</fieldset>' : '</div>' ).'
			
			<br />';
    	$this->_html .= '
		<form action="'.$_SERVER['REQUEST_URI'].'" name="facebooklike_form" id="facebooklike_form" method="post">
			
			'.($this->_ps_version_id < 10600 ? '<fieldset class="width3" style="width:850px;">' : '<div class="panel">').' 
				'.($this->_ps_version_id < 10600 ? '<legend>' : '<h3>').'
					<img src="'.$this->_path.'logo.gif" />&nbsp;&nbsp;&nbsp;'.$this->l('Installation Instructions (Optional)').'
				'.($this->_ps_version_id < 10600 ? '</legend>' : '</h3>' ).'
				<b style="color:blue">'.$this->l('To display the "Like" button in a different hook').'</b>:
				<br />
				<br />
				'.$this->l('Add').' <b style="color:green">'.($ps_version <= 1.4 ? $this->l('{$HOOK_FACEBOOK_LIKE}') : '{hook h="facebookLike"}').'</b> '.$this->l('in the tpl file you want it to show').'.
				<br />
				<br />';
				
		if ($ps_version == 1.4)
			$this->_html .= $this->l('Copy /modules/facebooklike/override/classes/FrontController.php to /override/classes/ (If the file already exists, you will have to merge both files)');
		else if ($ps_version < 1.4)
			$this->_html .=$this->l('Add').' <b style="color:green">\'HOOK_FACEBOOK_LIKE\' => Module::hookExec(\'facebookLike\'),</b> '.$this->l('to /header.php below HOOK_TOP around line #15');
		
			$this->_html .= '
			'.($this->_ps_version_id < 10600 ? '</fieldset>' : '</div>' ).'
			<br />
		
		'.($this->_ps_version_id < 10600 ? '<fieldset class="width3" style="width:850px;">' : '<div class="panel">').' 
			'.($this->_ps_version_id < 10600 ? '<legend>' : '<h3>').'
				<img src="'.$this->_path.'logo.gif" />&nbsp;&nbsp;&nbsp;'.$this->l('Facebook Like Settings').'
			'.($this->_ps_version_id < 10600 ? '</legend>' : '</h3>' ).'
			<table border="0" '.($this->_ps_version_id < 10600 ? 'width="850"' : '').'>
			<tr '.($this->_ps_version_id < 10600 ? 'height="30"' : 'height="36"').'>
				<td align="left" '.($this->_ps_version_id < 10600 ? 'valign="top"' : 'valign="middle"').'  width="120">
					<b>'.$this->l('Layout Style').':</b> 
				</td>
				<td align="left" '.($this->_ps_version_id < 10600 ? 'valign="top"' : 'valign="middle"').'>
   					<select name="fl_layout" style="width:150px">
   						<option value="standard" '.(Tools::getValue('fl_layout', $this->_fl_layout) == "standard"?"selected":"").'>'.$this->l('Standard').'</option>
   						<option value="button_count" '.(Tools::getValue('fl_layout', $this->_fl_layout) == "button_count"?"selected":"").'>'.$this->l('Compact (Count)').'</option>
   						<option value="box_count" '.(Tools::getValue('fl_layout', $this->_fl_layout) == "box_count"?"selected":"").'>'.$this->l('Box (Count)').'</option>
   					</select>
				</td>
			</tr>
			<tr '.($this->_ps_version_id < 10600 ? 'height="30"' : 'height="36"').'>
				<td align="left" '.($this->_ps_version_id < 10600 ? 'valign="top"' : 'valign="middle"').'>
					<b>'.$this->l('Show Faces').':</b> 
				</td>
				<td align="left" '.($this->_ps_version_id < 10600 ? 'valign="top"' : 'valign="middle"').'>
   					<select name="fl_faces" style="width:150px">
   						<option value="false" '.(Tools::getValue('fl_faces', $this->_fl_faces) == "false"?"selected":"").'>'.$this->l('No').'</option>
   						<option value="true" '.(Tools::getValue('fl_faces', $this->_fl_faces) == "true"?"selected":"").'>'.$this->l('Yes').'</option>
   					</select>
				</td>
			</tr>
			<tr '.($this->_ps_version_id < 10600 ? 'height="30"' : 'height="36"').'>
				<td align="left" '.($this->_ps_version_id < 10600 ? 'valign="top"' : 'valign="middle"').'>
					<b>'.$this->l('Send Button').':</b> 
				</td>
				<td align="left" '.($this->_ps_version_id < 10600 ? 'valign="top"' : 'valign="middle"').'>
   					<select name="fl_send" style="width:150px">
   						<option value="false" '.(Tools::getValue('fl_send', $this->_fl_send) == "false"?"selected":"").'>'.$this->l('No').'</option>
   						<option value="true" '.(Tools::getValue('fl_send', $this->_fl_send) == "true"?"selected":"").'>'.$this->l('Yes').'</option>
   					</select>
				</td>
			</tr>
			<tr '.($this->_ps_version_id < 10600 ? 'height="30"' : 'height="36"').'>
				<td align="left" '.($this->_ps_version_id < 10600 ? 'valign="top"' : 'valign="middle"').'>
					<b>'.$this->l('Width').':</b> 
				</td>
				<td align="left" '.($this->_ps_version_id < 10600 ? 'valign="top"' : 'valign="middle"').'>
   					<input type="text" style="width:140px" name="fl_width" value="'.max(Tools::getValue('fl_width', $this->_fl_width),$this->_fl_layout == "standard"?225:90).'">
   					'.$this->l('Minimum 225 (Standard), 90 (Compact)').'
				</td>
			</tr>
			<tr '.($this->_ps_version_id < 10600 ? 'height="30"' : 'height="36"').'>
				<td align="left" '.($this->_ps_version_id < 10600 ? 'valign="top"' : 'valign="middle"').'>
					<b>'.$this->l('Text').':</b> 
				</td>
				<td align="left" '.($this->_ps_version_id < 10600 ? 'valign="top"' : 'valign="middle"').'>
   					<select name="fl_text" style="width:150px">
   						<option value="like" '.(Tools::getValue('fl_text', $this->_fl_text) == "like"?"selected":"").'>'.$this->l('Like').'</option>
   						<option value="recommend" '.(Tools::getValue('fl_text', $this->_fl_text) == "recommend"?"selected":"").'>'.$this->l('Recommend').'</option>
   					</select>
				</td>
			</tr>
			<tr '.($this->_ps_version_id < 10600 ? 'height="30"' : 'height="36"').'>
				<td align="left" '.($this->_ps_version_id < 10600 ? 'valign="top"' : 'valign="middle"').'>
					<b>'.$this->l('Font').':</b> 
				</td>
				<td align="left" '.($this->_ps_version_id < 10600 ? 'valign="top"' : 'valign="middle"').'>
   					<select name="fl_font" style="width:150px">
   						<option value="arial" '.(Tools::getValue('fl_font', $this->_fl_font) == "arial"?"selected":"").'>'.$this->l('Arial').'</option>
   						<option value="lucida grande" '.(Tools::getValue('fl_font', $this->_fl_font) == "lucida grande"?"selected":"").'>'.$this->l('Lucida Grande').'</option>
   						<option value="segoe ui" '.(Tools::getValue('fl_font', $this->_fl_font) == "segoe ui"?"selected":"").'>'.$this->l('Segoe Ui').'</option>
   						<option value="tahoma" '.(Tools::getValue('fl_font', $this->_fl_font) == "tahoma"?"selected":"").'>'.$this->l('Tahoma').'</option>
   						<option value="trebuchet ms" '.(Tools::getValue('fl_font', $this->_fl_font) == "trebuchet ms"?"selected":"").'>'.$this->l('Trebuchet MS').'</option>
   						<option value="verdana" '.(Tools::getValue('fl_font', $this->_fl_font) == "verdana"?"selected":"").'>'.$this->l('Verdana').'</option>
   					</select>
				</td>
			</tr>
			<tr '.($this->_ps_version_id < 10600 ? 'height="30"' : 'height="36"').' >
				<td align="left" '.($this->_ps_version_id < 10600 ? 'valign="top"' : 'valign="middle"').'>
					<b>'.$this->l('Color').':</b> 
				</td>
				<td align="left" '.($this->_ps_version_id < 10600 ? 'valign="top"' : 'valign="middle"').'>
   					<select name="fl_color" style="width:150px">
   						<option value="light" '.(Tools::getValue('fl_color', $this->_fl_color) == "light"?"selected":"").'>'.$this->l('Light').'</option>
   						<option value="dark" '.(Tools::getValue('fl_color', $this->_fl_color) == "dark"?"selected":"").'>'.$this->l('Dark').'</option>
   					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input type="submit" value="'.$this->l('Update').'" name="submitChanges" '.($this->_ps_version_id >= 10600 ? 'class="btn btn-default"' : 'class="button" ' ).' />
				</td>
			</tr>
			</table>
			'.($this->_ps_version_id < 10600 ? '</fieldset>' : '</div>' ).'
		</form>
		
		';
   	}
    	    
	private function _postProcess()
	{
		if (Tools::isSubmit('submitChanges'))
		{
			if (!Configuration::updateValue('FL_LAYOUT', Tools::getValue('fl_layout'))
				|| !Configuration::updateValue('FL_FACES', Tools::getValue('fl_faces'))
				|| !Configuration::updateValue('FL_WIDTH', Tools::getValue('fl_width'))
				|| !Configuration::updateValue('FL_TEXT', Tools::getValue('fl_text'))
				|| !Configuration::updateValue('FL_FONT', Tools::getValue('fl_font'))
				|| !Configuration::updateValue('FL_SEND', Tools::getValue('fl_send'))
				|| !Configuration::updateValue('FL_COLOR', Tools::getValue('fl_color')))
				$this->_html .= '<div class="alert error">'.$this->l('Cannot update settings').'</div>';
			else
				$this->_html .= ($this->_ps_version_id >= 10600 ?  '<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">×</button>
			'.$this->l('Settings updated').'
		</div>' : '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Settings updated').'</div>');
		}
		$this->_refreshProperties();
	}
	
	function hookExtraLeft($params)
	{
		global $smarty;
		if (stripos($_SERVER['HTTP_USER_AGENT'],'bot') !== false ||
			 stripos($_SERVER['HTTP_USER_AGENT'],'baidu') !== false ||
			 stripos($_SERVER['HTTP_USER_AGENT'],'spider') !== false ||
			 stripos($_SERVER['HTTP_USER_AGENT'],'Ask Jeeves') !== false ||
			 stripos($_SERVER['HTTP_USER_AGENT'],'slurp') !== false ||
			 stripos($_SERVER['HTTP_USER_AGENT'],'crawl') !== false)
			return;
		$smarty->assign(array('fl_layout' => $this->_fl_layout, 'fl_faces' => $this->_fl_faces,'fl_width' => $this->_fl_width,
			'fl_height' => $this->_fl_faces == "true"?80:35,'fl_text' => $this->_fl_text,'fl_send' => $this->_fl_send,'fl_font' => $this->_fl_font,
			'fl_color' => $this->_fl_color, 'fl_page' => 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
			'fl_default_hook' => isset($params['fb_hookFacebookLike'])?0:1));
		return $this->display(__FILE__, 'facebooklike.tpl');
	}

	function hookHeader()
	{
		global $smarty, $cookie, $link;
		if (stripos($_SERVER['HTTP_USER_AGENT'],'bot') !== false ||
			stripos($_SERVER['HTTP_USER_AGENT'],'baidu') !== false ||
			stripos($_SERVER['HTTP_USER_AGENT'],'spider') !== false ||
			stripos($_SERVER['HTTP_USER_AGENT'],'Ask Jeeves') !== false ||
			stripos($_SERVER['HTTP_USER_AGENT'],'slurp') !== false ||
			stripos($_SERVER['HTTP_USER_AGENT'],'crawl') !== false)
			return;
		$lang = strtolower(Language::getIsoById($cookie->id_lang));
		if ($lang == 'af')
			$lang = 'af_ZA';
		elseif ($lang == 'az')
			$lang = 'az_AZ';
		elseif ($lang == 'id')
			$lang = 'id_ID';
		elseif ($lang == 'ms')
			$lang = 'ms_MY';
		elseif ($lang == 'bs')
			$lang = 'bs_BA';
		elseif ($lang == 'ca')
			$lang = 'ca_ES';
		elseif ($lang == 'cs')
			$lang = 'cs_CZ';
		elseif ($lang == 'cy')
			$lang = 'cy_GB';
		elseif ($lang == 'da')
			$lang = 'da_DK';
		elseif ($lang == 'de')
			$lang = 'de_DE';
		elseif ($lang == 'et')
			$lang = 'et_EE';
		elseif ($lang == 'es')
			$lang = 'es_ES';
		elseif ($lang == 'tl')
			$lang = 'tl_PH';
		elseif ($lang == 'fr')
			$lang = 'fr_FR';
		elseif ($lang == 'it')
			$lang = 'it_IT';
		elseif ($lang == 'ka')
			$lang = 'ka_GE';
		elseif ($lang == 'sw')
			$lang = 'sw_KE';
		elseif ($lang == 'ku')
			$lang = 'ku_TR';
		elseif ($lang == 'lv')
			$lang = 'lv_LV';
		elseif ($lang == 'hu')
			$lang = 'hu_HU';
		elseif ($lang == 'nl')
			$lang = 'nl_NL';
		elseif ($lang == 'ja')
			$lang = 'ja_JP';
		elseif ($lang == 'no')
			$lang = 'nn_NO';
		elseif ($lang == 'pl')
			$lang = 'pl_PL';
		elseif ($lang == 'pt')
			$lang = 'pt_PT';
		elseif ($lang == 'ro')
			$lang = 'ro_RO';
		elseif ($lang == 'ru')
			$lang = 'ru_RU';
		elseif ($lang == 'sq')
			$lang = 'sq_AL';
		elseif ($lang == 'sk')
			$lang = 'sk_SK';
		elseif ($lang == 'si')
			$lang = 'si_SI';
		elseif ($lang == 'fi')
			$lang = 'fi_FI';
		elseif ($lang == 'sv')
			$lang = 'sv_SE';
		elseif ($lang == 'th')
			$lang = 'th_TH';
		elseif ($lang == 'vi')
			$lang = 'vi_VN';
		elseif ($lang == 'tr')
			$lang = 'tr_TR';
		elseif ($lang == 'zh')
			$lang = 'zh_TW';
		elseif ($lang == 'tw')
			$lang = 'zh_TW';
		elseif ($lang == 'el')
			$lang = 'el_GR';
		elseif ($lang == 'be')
			$lang = 'be_BY';
		elseif ($lang == 'bg')
			$lang = 'bg_BG';
		elseif ($lang == 'mk')
			$lang = 'mk_MK';
		elseif ($lang == 'sr')
			$lang = 'sr_RS';
		elseif ($lang == 'uk')
			$lang = 'uk_UA';
		elseif ($lang == 'hy')
			$lang = 'hy_AM';
		elseif ($lang == 'he')
			$lang = 'he_IL';
		elseif ($lang == 'ar')
			$lang = 'ar_AR';
		elseif ($lang == 'ps')
			$lang = 'ps_AF';
		elseif ($lang == 'fa')
			$lang = 'fa_IR';
		elseif ($lang == 'ne')
			$lang = 'ne_NP';
		elseif ($lang == 'hi')
			$lang = 'hi_IN';
		elseif ($lang == 'bn')
			$lang = 'bn_IN';
		elseif ($lang == 'pa')
			$lang = 'pa_IN';
		elseif ($lang == 'ta')
			$lang = 'ta_IN';
		elseif ($lang == 'te')
			$lang = 'te_IN';
		elseif ($lang == 'ml')
			$lang = 'ml_IN';
		else
			$lang = 'en_US';
		$fb_cover = '';
    	$ps_version3  = substr(_PS_VERSION_,0,5);
    	$psv = floatval(substr(_PS_VERSION_,0,3));
		$protocol_link = @$_SERVER['HTTPS'] == "on"?"https://":"http://";
		$cover = Product::getCover(intval(Tools::getValue('id_product')));
		if (is_array($cover) && sizeof($cover) == 1)
		{
			$product = new Product((int)Tools::getValue('id_product'));
			if ($psv >= 1.4)
				$fb_cover = $link->getImageLink($product->link_rewrite[$cookie->id_lang], Tools::getValue('id_product').'-'.$cover['id_image'],'thickbox'.($psv >= 1.5 && ($ps_version3 != '1.5.0' && $ps_version3 != '1.5.1')?'_default':''));
			else
				$fb_cover = 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'img/p/'.Tools::getValue('id_product').'-'.$cover['id_image'].'.jpg';
		}
		$smarty->assign(array('fl_protocol_link' => $protocol_link, 'fl_lang_code' => $lang, 'fl_default_image' => $fb_cover));
		return $this->display(__FILE__, 'header.tpl');
	}

	function hookHome($params)
	{
		$this->_fl_width = "560";
		return $this->hookExtraLeft($params);
	}

	function hookFacebookLike($params)
	{
		$params['fb_hookFacebookLike'] = 1;
		return $this->hookExtraLeft($params);
	}
	
	private function upgradeCheck($module)
	{
		global $cookie;
		$ps_version  = floatval(substr(_PS_VERSION_,0,3));
		// Only run upgrae check if module is loaded in the backoffice.
		if (($ps_version > 1.1  && $ps_version < 1.5) && (!is_object($cookie) || !$cookie->isLoggedBack()))
			return;
		if ($ps_version >= 1.5)
		{
			$context = Context::getContext();
			if (!isset($context->employee) || !$context->employee->isLoggedBack())
				return;			
		}
		// Get Presto-Changeo's module version info
		$mod_info_str = Configuration::get('PRESTO_CHANGEO_SV');
		if (!function_exists('json_decode'))
		{
			if (!file_exists(dirname(__FILE__).'/JSON.php'))
				return false; 
			include_once(dirname(__FILE__).'/JSON.php');
			$j = new JSON();
			$mod_info = $j->unserialize($mod_info_str);
		}
		else
			$mod_info = json_decode($mod_info_str);
		// Get last update time.
		$time = time();
		// If not set, assign it the current time, and skip the check for the next 7 days. 
		if ($this->_last_updated <= 0)
		{
			Configuration::updateValue('PRESTO_CHANGEO_UC', $time);
			$this->_last_updated = $time;
		}
		// If haven't checked in the last 1-7+ days
		$update_frequency = max(86400, isset($mod_info->{$module}->{'T'})?$mod_info->{$module}->{'T'}:86400);
		if ($this->_last_updated < $time - $update_frequency)
		{	
			// If server version number exists and is different that current version, return URL
			if (isset($mod_info->{$module}->{'V'}) && $mod_info->{$module}->{'V'} > $this->_full_version)
				return $mod_info->{$module}->{'U'};
			$url = 'http://updates.presto-changeo.com/?module_info='.$module.'_'.$this->version.'_'.$this->_last_updated.'_'.$time.'_'.$update_frequency;
			$mod = @file_get_contents($url);
			if ($mod == '' && function_exists('curl_init'))
			{
				$ch = curl_init();
				curl_setopt ($ch, CURLOPT_URL, $url);
				curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
				$mod = curl_exec($ch);
			}
			Configuration::updateValue('PRESTO_CHANGEO_UC', $time);
			$this->_last_updated = $time;
			if (!function_exists('json_decode') )
			{
				$j = new JSON();
				$mod_info = $j->unserialize($mod);
			}
			else
				$mod_info = json_decode($mod);
			if (!isset($mod_info->{$module}->{'V'}))
				return false;
			if (Validate::isCleanHtml($mod))
				Configuration::updateValue('PRESTO_CHANGEO_SV', $mod);
			if ($mod_info->{$module}->{'V'} > $this->_full_version)
				return $mod_info->{$module}->{'U'};
			else 
				return false;
		}
		elseif (isset($mod_info->{$module}->{'V'}) && $mod_info->{$module}->{'V'} > $this->_full_version)
			return $mod_info->{$module}->{'U'};
		else
			return false;
	}

	public function getModuleRecommendations($module)
	{
		$arr = unserialize(Configuration::get('PC_RECOMMENDED_LIST'));
		// Get a new recommended module list every 10 days //
		if (!is_array($arr) || sizeof($arr) == 0 || Configuration::get('PC_RECOMMENDED_LAST') < time() - 864000)
		{
			$url = 'http://updates.presto-changeo.com/recommended.php';
			$str = @file_get_contents($url);
			if ($str == '' && function_exists('curl_init'))
			{
				$ch = curl_init();
				curl_setopt ($ch, CURLOPT_URL, $url);
				curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
				$str = curl_exec($ch);
			}
			Configuration::updateValue('PC_RECOMMENDED_LIST', $str);
			Configuration::updateValue('PC_RECOMMENDED_LAST', time());
			$arr = unserialize($str);
		}
		
		$ps_version_array = explode('.', _PS_VERSION_);
		$_ps_version_id = 10000 * intval($ps_version_array[0]) + 100 * intval($ps_version_array[1]);
		
		$dupe = false;
		$rand = array_rand($arr, 5);
		$out = '<div style="width:100%">
					<div style="float:left;width:100%;">
						<div style="float:left; padding: 10px;">
							<a href="https://www.presto-changeo.com/en/contact_us" target="_index"><img src="http://updates.presto-changeo.com/logo.jpg" border="0" /></a>
						</div>
						<div style="min-height:69px;float:left;border: 1px solid #c0d2d2;background-color: #e3edee">
							<div style="width: 80px;float: left;padding-top: 12px;">
								<div style="color:#5d707e;font-weight:bold;text-align:center">'.$this->l('Explore').'<br />'.$this->l('Our').'<br />'.$this->l('Modules').'</div>
							</div>
							<div style="float: left;">';
		for ($j = 0 ; $j < 4 ; $j++)
		{
			// Make sure to exclude the current module //
			if ($arr[$rand[$j]]['code'] == $module)
				$dupe = true;
			$i = $rand[$dupe?$j+1:$j];
			$out .= '
							<div style="margin-right: 8px;width: 143px;height:57px;float: left;margin-top:5px;border: 1px solid #c0d2d2;background-color: #ffffff">
								<div style="width:45px; height: 45px;margin: 6px 8px 6px 6px; float:left;">
									<a target="_index" href="'.$arr[$i]['url'].'">
										<img border="0" src="'.$arr[$i]['img'].'" width="45" height="45" />
									</a>
								</div>
								<div style="width:80px; height: 45px; float:left;margin-top: 6px;font-weight: bold;">
									<a style="color:#085372;" target="_index" href="'.$arr[$i]['url'].'">
										'.$arr[$i]['name'].'
									</a>
								</div>
							</div>';
		}
		$out .= '
							</div>
						</div>
					</div>
				</div>';
		return $out;
	}		
}
?>