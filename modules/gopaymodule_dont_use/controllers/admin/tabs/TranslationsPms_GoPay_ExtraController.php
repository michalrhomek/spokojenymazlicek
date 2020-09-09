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

class TranslationsPms_GoPay_ExtraController extends AdminController
{
	const TABNAME = 'TranslationsPms_GoPay_ExtraController';
	const DEFAULT_THEME_NAME = _PS_DEFAULT_THEME_NAME_;
    const TEXTAREA_SIZED = 70;

	protected $theme_selected;
	protected $total_expression = 0;
	protected $missing_translations = 0;
	protected $all_iso_lang = array();
	protected $modules_translations = array();
	protected $translations_informations = array();
	protected $languages;
	protected $themes;
	protected $type_selected;
	protected $lang_selected;
	protected $post_limit_exceed = false;
	protected static $ignore_folder = array('.', '..', '.svn', '.git', '.htaccess', 'index.php');

	public function __construct(Module $module)
	{
		$this->bootstrap = true;
	 	$this->table = 'translations';
		parent::__construct();

		$this->_M = $module;
	}

	public function init()
	{
		parent::init();
		$this->content_only = true;
	}

	public function getTranslationsInformations()
	{
		$this->translations_informations = array(
			'modules' => array(
				'name' => $this->_M->l('Installed modules translations', self::TABNAME),
				'var' => '_MODULES',
				'dir' => _PS_ROOT_DIR_.'/modules/',
				'file' => '',
				'sf_controller' => true,
				'choice_theme' => false,
			),
		);

		if (defined('_PS_THEME_SELECTED_DIR_')) {
			$this->translations_informations['modules']['override'] = array('dir' => _PS_THEME_SELECTED_DIR_.'modules/', 'file' => '');
			$this->translations_informations['mails']['override'] = array('dir' => _PS_THEME_SELECTED_DIR_.'mails/'.$this->lang_selected->iso_code.'/', 'file' => 'lang.php');
		}
	}

	public function getInformations()
	{
		// Get all Languages
		$this->languages = Language::getLanguages(false);

		// Get all iso_code of languages
		foreach ($this->languages as $language) {
			$this->all_iso_lang[] = $language['iso_code'];
		}

		// Get type of translation
		$this->type_selected = 'modules';

		// Get selected language
		if (Tools::getValue('_TRANS_LANG') || Tools::getValue('iso_code')) {
			$iso_code = Tools::getValue('_TRANS_LANG') ? Tools::getValue('_TRANS_LANG') : Tools::getValue('iso_code');

			if (!Validate::isLangIsoCode($iso_code) || !in_array($iso_code, $this->all_iso_lang)) {
				throw new PrestaShopException($this->l('Invalid iso code ').$iso_code);
			}

			$this->lang_selected = new Language((int)Language::getIdByIso($iso_code));
		} else {
			$this->lang_selected = $this->context->language;
		}

		// Get all information for translations
		$this->getTranslationsInformations();
	}

	public function initFormModules()
	{
		$this->getInformations();
		// Get list of installed modules
		$installed_modules = $this->getListModules();

		// get selected module
		$modules[0] = $this->_M->name;
		if (!empty($modules)) {
			// Get all modules files and include all translation files
			$arr_files = $this->getAllModuleFiles($modules, null, $this->lang_selected->iso_code, true);

			foreach ($arr_files as $value) {
				$this->findAndFillTranslations($value['files'], $value['theme'], $value['module'], $value['dir']);
			}

			$this->context->smarty->assign($this->tpl_view_vars);

			$this->context->smarty->assign(array(
				'toggle_button' => $this->displayToggleButton(),
				'default_theme_name' => self::DEFAULT_THEME_NAME,
				'count' => $this->total_expression,
				'limit_warning' => $this->displayLimitPostWarning($this->total_expression),
				'mod_security_warning' => Tools::apacheModExists('mod_security'),
				'textarea_sized' => self::TEXTAREA_SIZED,
				'cancel_url' => $this->context->link->getAdminLink('AdminTranslations'),
				'modules_translations' => isset($this->modules_translations) ? $this->modules_translations : array(),
				'missing_translations' => $this->missing_translations/2,
				'module_name' => $modules[0],
				'installed_modules' => $installed_modules
			));

			return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->_M->name.'/views/templates/admin/classes/tabs/translation.tpl');
		}
	}

	protected function getModuleTranslations()
    {
        global $_MODULE;
        $name_var = (empty($this->translations_informations[$this->type_selected]['var']) ? false: $this->translations_informations[$this->type_selected]['var']);

        if (!isset($_MODULE) && !isset($GLOBALS[$name_var])) {
            $GLOBALS[$name_var] = array();
        } elseif (isset($_MODULE)) {
            if (is_array($GLOBALS[$name_var]) && is_array($_MODULE)) {
                $GLOBALS[$name_var] = $_MODULE;
            } else {
                $GLOBALS[$name_var] = $_MODULE;
            }
        }
    }

	public function postProcess()
	{
		$this->getInformations();

		if (Tools::isSubmit('submitTransModules'))
		{
			$modules[0] = $this->_M->name;
			// Get list of modules
			if ($modules)
			{
				$return_trans = array(
					'total_translations' => 0,
					'total_missing_translations' => 0,
					'templates' => array()
				);
				$array_trans = isset($_POST['array_translation']) ? $_POST['array_translation'] : '';
				if (is_array($array_trans))
				{
					foreach ($array_trans as $key => $value)
					{
						foreach ($value as $val)
						{
							$_POST[$val['key_translation']] = $val['value_translation'];
							$return_trans['total_translations']++;

							$return_trans['templates'][$key]['translations'][$val['key_translation']] = $val['value_translation'];
							if (empty($val['value_translation']))
							{
								$return_trans['total_missing_translations']++;
								if (!isset($return_trans['templates'][$key]['missing_translations']))
									$return_trans['templates'][$key]['missing_translations'] = 1;
								else
									$return_trans['templates'][$key]['missing_translations']++;
							}
						}
					}
				}

				// Get files of all modules
				$arr_files = $this->getAllModuleFiles($modules, null, $this->lang_selected->iso_code, true);

				// Find and write all translation modules files
				foreach ($arr_files as $value)
					$this->findAndWriteTranslationsIntoFile($value['file_name'], $value['files'], $value['theme'], $value['module'], $value['dir']);

				// Clear modules cache
				Tools::clearCache(Context::getContext()->smarty);

				return $return_trans;
			} else
				return false;
		} else
			return false;
	}

	/* pouze zkopírované funkce */
	public function getListModules()
	{
		if (!Tools::file_exists_cache($this->translations_informations['modules']['dir']))
			throw new PrestaShopException(Tools::displayError('Fatal error: The module directory does not exist.').'('.$this->translations_informations['modules']['dir'].')');
		if (!is_writable($this->translations_informations['modules']['dir']))
			throw new PrestaShopException(Tools::displayError('The module directory must be writable.'));

		$modules = array();
		// Get all module which are installed for to have a minimum of POST
		$modules = Module::getModulesInstalled();
		foreach ($modules as &$module)
			$module = $module['name'];

		return $modules;
	}

	protected function getAllModuleFiles($modules, $root_dir = null, $lang, $is_default = false)
	{
		$array_files = array();
		$initial_root_dir = $root_dir;
		foreach ($modules as $module)
		{
			$root_dir = $initial_root_dir;
			if ($module{0} == '.')
				continue;

			// First we load the default translation file
			if ($root_dir == null)
			{
				$i18n_dir = $this->translations_informations[$this->type_selected]['dir'];
				if (is_dir($i18n_dir.$module))
					$root_dir = $i18n_dir;

				$lang_file = $root_dir.$module.'/translations/'.$lang.'.php';
				if (!Tools::file_exists_cache($root_dir.$module.'/translations/'.$lang.'.php') && Tools::file_exists_cache($root_dir.$module.'/'.$lang.'.php'))
					$lang_file = $root_dir.$module.'/'.$lang.'.php';
				@include($lang_file);
				$this->getModuleTranslations();
				// If a theme is selected, then the destination translation file must be in the theme
				if ($this->theme_selected)
					$lang_file = $this->translations_informations[$this->type_selected]['override']['dir'].$module.'/translations/'.$lang.'.php';
				$this->recursiveGetModuleFiles($root_dir.$module.'/', $array_files, $module, $lang_file, $is_default);
			}

			$root_dir = $initial_root_dir;
			// Then we load the overriden translation file
			if ($this->theme_selected && isset($this->translations_informations[$this->type_selected]['override']))
			{
				$i18n_dir = $this->translations_informations[$this->type_selected]['override']['dir'];
				if (is_dir($i18n_dir.$module))
					$root_dir = $i18n_dir;
				if (Tools::file_exists_cache($root_dir.$module.'/translations/'.$lang.'.php'))
					$lang_file = $root_dir.$module.'/translations/'.$lang.'.php';
				elseif (Tools::file_exists_cache($root_dir.$module.'/'.$lang.'.php'))
					$lang_file = $root_dir.$module.'/'.$lang.'.php';
				@include($lang_file);
				$this->getModuleTranslations();
				$this->recursiveGetModuleFiles($root_dir.$module.'/', $array_files, $module, $lang_file, $is_default);
			}
		}
		return $array_files;
	}

	protected function recursiveGetModuleFiles($path, &$array_files, $module_name, $lang_file, $is_default = false)
	{
		$files_module = array();
		if (Tools::file_exists_cache($path))
			$files_module = scandir($path);
		$files_for_module = $this->clearModuleFiles($files_module, 'file');
		if (!empty($files_for_module))
			$array_files[] = array(
				'file_name'		=> $lang_file,
				'dir'			=> $path,
				'files'			=> $files_for_module,
				'module'		=> $module_name,
				'is_default'	=> $is_default,
				'theme'			=> $this->theme_selected,
			);

		$dir_module = $this->clearModuleFiles($files_module, 'directory', $path);

		if (!empty($dir_module))
			foreach ($dir_module as $folder)
				$this->recursiveGetModuleFiles($path.$folder.'/', $array_files, $module_name, $lang_file, $is_default);
	}

	public function clearModuleFiles($files, $type_clear = 'file', $path = '')
	{
		// List of directory which not must be parsed
		$arr_exclude = array('img', 'js', 'mails','override');

		// List of good extention files
		$arr_good_ext = array('.tpl', '.php');

		foreach ($files as $key => $file)
		{
			if ($file{0} === '.' || in_array(substr($file, 0, strrpos($file, '.')), $this->all_iso_lang))
				unset($files[$key]);
			else if ($type_clear === 'file' && !in_array(substr($file, strrpos($file, '.')), $arr_good_ext))
				unset($files[$key]);
			else if ($type_clear === 'directory' && (!is_dir($path.$file) || in_array($file, $arr_exclude)))
				unset($files[$key]);
		}

		return $files;
	}

	protected function findAndFillTranslations($files, $theme_name, $module_name, $dir = false)
	{
		$name_var = $this->translations_informations[$this->type_selected]['var'];

		// added for compatibility
		$GLOBALS[$name_var] = array_change_key_case($GLOBALS[$name_var]);

		// Thank to this var similar keys are not duplicate
		// in AndminTranslation::modules_translations array
		// see below
		$array_check_duplicate = array();
		foreach ($files as $file)
		{
			if ((preg_match('/^(.*).tpl$/', $file) || preg_match('/^(.*).php$/', $file)) && Tools::file_exists_cache($file_path = $dir.$file))
			{
				// Get content for this file
				$content = file_get_contents($file_path);

				// Module files can now be ignored by adding this string in a file
				if (strpos($content, 'IGNORE_THIS_FILE_FOR_TRANSLATION') !== false)
					continue;

				// Get file type
				$type_file = substr($file, -4) == '.tpl' ? 'tpl' : 'php';

				// Parse this content
				$matches = $this->userParseFile($content, $this->type_selected, $type_file, $module_name);

				// Write each translation on its module file
				$template_name = substr(basename($file), 0, -4);

				foreach ($matches as $key)
				{
					$md5_key = md5($key);
					$module_key = '<{'.Tools::strtolower($module_name).'}'.strtolower($theme_name).'>'.Tools::strtolower($template_name).'_'.$md5_key;
					$default_key = '<{'.Tools::strtolower($module_name).'}prestashop>'.Tools::strtolower($template_name).'_'.$md5_key;
					// to avoid duplicate entry
					if (!in_array($module_key, $array_check_duplicate))
					{
						$array_check_duplicate[] = $module_key;
						if (!isset($this->modules_translations[$theme_name][$module_name][$template_name][$key]['trad']))
							$this->total_expression++;
						if ($theme_name && array_key_exists($module_key, $GLOBALS[$name_var]))
							$this->modules_translations[$theme_name][$module_name][$template_name][$key]['trad'] = html_entity_decode($GLOBALS[$name_var][$module_key], ENT_COMPAT, 'UTF-8');
						elseif (array_key_exists($default_key, $GLOBALS[$name_var]))
							$this->modules_translations[$theme_name][$module_name][$template_name][$key]['trad'] = html_entity_decode($GLOBALS[$name_var][$default_key], ENT_COMPAT, 'UTF-8');
						else
						{
							$this->modules_translations[$theme_name][$module_name][$template_name][$key]['trad'] = '';
							$this->missing_translations++;
						}
						$this->modules_translations[$theme_name][$module_name][$template_name][$key]['use_sprintf'] = $this->checkIfKeyUseSprintf($key);
					}
				}
			}
		}
	}

	protected function userParseFile($content, $type_translation, $type_file = false, $module_name = '')
	{
		switch ($type_translation)
		{
			case 'front':
					// Parsing file in Front office
					$regex = '/\{l\s*s=([\'\"])'._PS_TRANS_PATTERN_.'\1(\s*sprintf=.*)?(\s*js=1)?\s*\}/U';
				break;

			case 'back':
					// Parsing file in Back office
					if ($type_file == 'php')
						$regex = '/this->l\((\')'._PS_TRANS_PATTERN_.'\'[\)|\,]/U';
					else if ($type_file == 'specific')
						$regex = '/Translate::getAdminTranslation\((\')'._PS_TRANS_PATTERN_.'\'(?:,.*)*\)/U';
					else
						$regex = '/\{l\s*s\s*=([\'\"])'._PS_TRANS_PATTERN_.'\1(\s*sprintf=.*)?(\s*js=1)?(\s*slashes=1)?.*\}/U';
				break;

			case 'errors':
					// Parsing file for all errors syntax
					$regex = '/Tools::displayError\((\')'._PS_TRANS_PATTERN_.'\'(,\s*(.+))?\)/U';
				break;

			case 'modules':
					// Parsing modules file
					if ($type_file == 'php')
						$regex = '/->l\((\')'._PS_TRANS_PATTERN_.'\'(, ?\'(.+)\')?(, ?(.+))?\)/U';
					else
						// In tpl file look for something that should contain mod='module_name' according to the documentation
						$regex = '/\{l\s*s=([\'\"])'._PS_TRANS_PATTERN_.'\1.*\s+mod=\''.$module_name.'\'.*\}/U';
				break;

			case 'pdf':
					// Parsing PDF file
					if ($type_file == 'php')
						$regex = '/HTMLTemplate.*::l\((\')'._PS_TRANS_PATTERN_.'\'[\)|\,]/U';
					else
						$regex = '/\{l\s*s=([\'\"])'._PS_TRANS_PATTERN_.'\1(\s*sprintf=.*)?(\s*js=1)?(\s*pdf=\'true\')?\s*\}/U';
				break;
		}

		if (!is_array($regex))
			$regex = array($regex);

		$strings = array();
		foreach ($regex as $regex_row)
		{
			$matches = array();
			$n = preg_match_all($regex_row, $content, $matches);
			for ($i = 0; $i < $n; $i += 1)
			{
				$quote = $matches[1][$i];
				$string = $matches[2][$i];

				if ($quote === '"')
				{
					// Escape single quotes because the core will do it when looking for the translation of this string
					$string = str_replace('\'', '\\\'', $string);
					// Unescape double quotes
					$string = preg_replace('/\\\\+"/', '"', $string);
				}

				$strings[] = $string;
			}
		}

		return array_unique($strings);
	}

	public function checkIfKeyUseSprintf($key)
	{
		if (preg_match_all('#(?:%%|%(?:[0-9]+\$)?[+-]?(?:[ 0]|\'.)?-?[0-9]*(?:\.[0-9]+)?[bcdeufFosxX])#', $key, $matches))
			return implode(', ', $matches[0]);
		return false;
	}

	public function displayToggleButton($closed = false)
	{
		$str_output = '
		<script type="text/javascript">';
		if (Tools::getValue('type') == 'mails')
			$str_output .= '$(document).ready(function(){
				toggleDiv(\''.$this->type_selected.'_div\'); toggleButtonValue(this.id, openAll, closeAll);
				});';
		$str_output .= '
			var openAll = \''.html_entity_decode($this->l('Expand all fieldsets'), ENT_NOQUOTES, 'UTF-8').'\';
			var closeAll = \''.html_entity_decode($this->l('Close all fieldsets'), ENT_NOQUOTES, 'UTF-8').'\';
		</script>
		<button type="button" class="btn btn-default" id="buttonall" data-status="open" onclick="toggleDiv(\''.$this->type_selected.'_div\', $(this).data(\'status\')); toggleButtonValue(this.id, openAll, closeAll);"><i class="process-icon-compress"></i> <span>'.$this->l('Close all fieldsets').'</span></button>';
		return $str_output;
	}

	public function displayLimitPostWarning($count)
	{
		$return = array();
		if ((ini_get('suhosin.post.max_vars') && ini_get('suhosin.post.max_vars') < $count) || (ini_get('suhosin.request.max_vars') && ini_get('suhosin.request.max_vars') < $count))
		{
			$return['error_type'] = 'suhosin';
			$return['post.max_vars'] = ini_get('suhosin.post.max_vars');
			$return['request.max_vars'] = ini_get('suhosin.request.max_vars');
			$return['needed_limit'] = $count + 100;
		}
		elseif (ini_get('max_input_vars') && ini_get('max_input_vars') < $count)
		{
			$return['error_type'] = 'conf';
			$return['max_input_vars'] = ini_get('max_input_vars');
			$return['needed_limit'] = $count + 100;
		}
		return $return;
	}

	protected function findAndWriteTranslationsIntoFile($file_name, $files, $theme_name, $module_name, $dir = false)
	{
		// These static vars allow to use file to write just one time.
		static $cache_file = array();
		static $str_write = '';
		static $array_check_duplicate = array();

		// Set file_name in static var, this allow to open and wright the file just one time
		if (!isset($cache_file[$theme_name.'-'.$file_name]))
		{
			$str_write = '';
			$cache_file[$theme_name.'-'.$file_name] = true;
			if (!Tools::file_exists_cache(dirname($file_name)))
				mkdir(dirname($file_name), 0777, true);
			if (!Tools::file_exists_cache($file_name))
				file_put_contents($file_name, '');
			if (!is_writable($file_name))
				throw new PrestaShopException(sprintf(
					Tools::displayError('Cannot write to the theme\'s language file (%s). Please check write permissions.'),
					$file_name
				));

			// this string is initialized one time for a file
			$str_write .= "<?php\n\nglobal \$_MODULE;\n\$_MODULE = array();\n";
			$array_check_duplicate = array();
		}

		foreach ($files as $file)
		{
			if (preg_match('/^(.*)\.(tpl|php)$/', $file) && Tools::file_exists_cache($dir.$file) && !in_array($file, self::$ignore_folder))
			{
				// Get content for this file
				$content = file_get_contents($dir.$file);

				// Get file type
				$type_file = substr($file, -4) == '.tpl' ? 'tpl' : 'php';

				// Parse this content
				$matches = $this->userParseFile($content, $this->type_selected, $type_file, $module_name);

				// Write each translation on its module file
				$template_name = substr(basename($file), 0, -4);

				foreach ($matches as $key)
				{
					if ($theme_name)
					{
						$post_key = md5(strtolower($module_name).'_'.strtolower($theme_name).'_'.strtolower($template_name).'_'.md5($key));
						$pattern = '\'<{'.strtolower($module_name).'}'.strtolower($theme_name).'>'.strtolower($template_name).'_'.md5($key).'\'';
					}
					else
					{
						$post_key = md5(strtolower($module_name).'_'.strtolower($template_name).'_'.md5($key));
						$pattern = '\'<{'.strtolower($module_name).'}prestashop>'.strtolower($template_name).'_'.md5($key).'\'';
					}

					if (array_key_exists($post_key, $_POST) && !empty($_POST[$post_key]) && !in_array($pattern, $array_check_duplicate))
					{
						$array_check_duplicate[] = $pattern;
						$str_write .= '$_MODULE['.$pattern.'] = \''.pSQL(str_replace(array("\r\n", "\r", "\n"), ' ', $_POST[$post_key])).'\';'."\n";
						$this->total_expression++;
					}
				}
			}
		}

		if (isset($cache_file[$theme_name.'-'.$file_name]) && $str_write != "<?php\n\nglobal \$_MODULE;\n\$_MODULE = array();\n")
			file_put_contents($file_name, $str_write);
	}
}
