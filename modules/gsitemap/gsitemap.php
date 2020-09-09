<?php

class Gsitemap extends Module
{
    private $_html = '';
    private $_postErrors = array();

    function __construct() {
        $this->name = 'gsitemap';
        $this->tab = 'Tools';
        $this->version = 1.0;

        $this->_directory = dirname(__FILE__).'/../../';
        $this->_filename = $this->_directory.'sitemap.xml';
        $this->_filename_http = 'http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'sitemap.xml';

        parent::__construct();

        /* The parent construct is required for translations */
	$this->page = basename(__FILE__, '.php');
        $this->displayName = $this->l('Google sitemap');
        $this->description = $this->l('Generate your Google sitemap file');
    }

    function install() {
        if (!parent::install()
                        OR !Configuration::updateValue('GOOGLE_SITEMAP_CATS', 0)
                        OR !Configuration::updateValue('GOOGLE_SITEMAP_ACTIVE_PRODUCTS', 1)
        )
	return false;

	return true;
    }
    
    function uninstall() {
        if (!parent::uninstall()
                        OR !file_put_contents($this->_filename, '')
                        OR !Configuration::deleteByName('GOOGLE_SITEMAP_CATS')
                        OR !Configuration::deleteByName('GOOGLE_SITEMAP_ACTIVE_PRODUCTS')
                        OR !Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'module` WHERE `id_module` = '.intval($this->id))
        )
	return false;
	
	return true;
    }
    
    private function _postValidation()
    {
		@unlink($this->_filename);
		if (!$fp = @fopen($this->_filename, 'w'))
			$this->_postErrors[] = $this->l('Cannot create').' '.realpath(dirname(__FILE__.'/../..')).'/'.$this->l('sitemap.xml file.');
		else
			fclose($fp);
    }

    private function _postProcess() {
    
        $gsite_cats = intval(Tools::getValue('gsite_cats'));
	$gsite_active = Tools::getValue('gsite_active');
	if ($gsite_cats != 0 AND $gsite_cats != 1)
		$this->_html .= '<div class="alert error">'.$this->l('Invalid choice.').'</div>';
	elseif ($gsite_active != 0 AND $gsite_active != 1)
		$this->_html .= '<div class="alert error">'.$this->l('Invalid choice.').'</div>';
	else {
		Configuration::updateValue('GOOGLE_SITEMAP_CATS', intval($gsite_cats));
		Configuration::updateValue('GOOGLE_SITEMAP_ACTIVE_PRODUCTS', intval($gsite_active));
	}
			
        $http = 'http://'. htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8');
	$link = new Link();
	if (file_exists($this->_filename)) $fp = fopen($this->_filename, 'w');
	                              else $fp = fopen($this->_filename, 'x');
        $xml = new SimpleXMLElement('<urlset xmlns="http://www.google.com/schemas/sitemap/0.84"></urlset>');

        // Root page
        $sitemap = $xml->addChild('url');
	$sitemap->addChild('loc', 'http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__);
	$sitemap->addChild('priority', '1.00');
	$sitemap->addChild('lastmod', date("Y-m-d"));
	$sitemap->addChild('changefreq', 'daily');

        // CMS
        $cmss = Db::getInstance()->ExecuteS('
	        SELECT DISTINCT b.id_cms, cl.link_rewrite, cl.id_lang
		FROM '._DB_PREFIX_.'block_cms b
		LEFT JOIN '._DB_PREFIX_.'cms_lang cl ON (b.id_cms = cl.id_cms)
		LEFT JOIN '._DB_PREFIX_.'lang l ON (cl.id_lang = l.id_lang)
		WHERE l.`active` = 1
		ORDER BY cl.id_cms, cl.id_lang ASC');
		
      	foreach($cmss AS $cms) {
      	        $tmp_link = htmlspecialchars($link->getCMSLink($cms['id_cms'], $cms['link_rewrite']));
	        $sitemap = $xml->addChild('url');
                $sitemap->addChild('loc', (!strstr($tmp_link, 'http')?$http:'') . $tmp_link);
                $sitemap->addChild('priority', '0.8');
                $sitemap->addChild('changefreq', 'monthly');
	}

        // Categories
        $categories = Db::getInstance()->ExecuteS('
		SELECT c.id_category, c.level_depth, link_rewrite, DATE_FORMAT(date_add, \'%Y-%m-%d\') AS date_add, cl.id_lang
		FROM '._DB_PREFIX_.'category c
		LEFT JOIN '._DB_PREFIX_.'category_lang cl ON c.id_category = cl.id_category
		LEFT JOIN '._DB_PREFIX_.'lang l ON cl.id_lang = l.id_lang
		WHERE l.`active` = 1 AND c.`active` = 1 AND c.id_category != 1
		'.(!Configuration::get('GOOGLE_SITEMAP_CATS') ?
		'AND ((SELECT COUNT(cp.id_product) FROM '._DB_PREFIX_.'category_product cp
                      LEFT JOIN '._DB_PREFIX_.'product p ON (cp.id_product = p.id_product)
                      WHERE cp.id_category = c.id_category
                      AND p.active = 1 AND p.quantity > 0
                     )
	             OR
                     (SELECT COUNT(cat.id_category) FROM '._DB_PREFIX_.'category cat
                      LEFT JOIN '._DB_PREFIX_.'category_product cp2 ON (cat.id_category = cp2.id_category)
                      LEFT JOIN '._DB_PREFIX_.'product p2 ON (p2.id_product = cp2.id_product)
                      WHERE cat.id_parent = c.id_category
                      AND p2.active = 1 AND p2.quantity > 0
                     )
                    )' : '').'
                ORDER BY date_upd DESC
        ');
	foreach($categories as $category) {
	        $tmp_link = htmlspecialchars($link->getCategoryLink($category['id_category'], $category['link_rewrite']).'?id_lang='.intval($category['id_lang']));
	        if (($priority = 0.9 - ($category['level_depth'] / 10)) < 0.1) {
		      $priority = 0.1;
		}
                $sitemap = $xml->addChild('url');
                $sitemap->addChild('loc', (!strstr($tmp_link, 'http')?$http:'') . $tmp_link);
                $sitemap->addChild('priority', $priority);
                $sitemap->addChild('lastmod', $category['date_add']);
                $sitemap->addChild('changefreq', 'monthly');
        }
        
        // Products
        $products = Db::getInstance()->ExecuteS('
		SELECT p.id_product, pl.link_rewrite, DATE_FORMAT(date_add, \'%Y-%m-%d\') AS date_add, pl.id_lang, cl.`link_rewrite` AS category, (
			SELECT MIN(level_depth)
			FROM '._DB_PREFIX_.'product p2
			LEFT JOIN '._DB_PREFIX_.'category_product cp2 ON p2.id_product = cp2.id_product
			LEFT JOIN '._DB_PREFIX_.'category c2 ON cp2.id_category = c2.id_category
			WHERE p2.id_product = p.id_product) AS level_depth
		FROM '._DB_PREFIX_.'product p
		LEFT JOIN '._DB_PREFIX_.'product_lang pl ON p.id_product = pl.id_product
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (p.`id_category_default` = cl.`id_category` AND pl.`id_lang` = cl.`id_lang`)
		LEFT JOIN '._DB_PREFIX_.'lang l ON cl.id_lang = l.id_lang
                WHERE l.active = 1
                '.(Configuration::get('GOOGLE_SITEMAP_ACTIVE_PRODUCTS') ? 'AND p.active = 1'. (!Configuration::get('PS_ORDER_OUT_OF_STOCK')?' AND p.quantity > 0':''): '').'
        ');
        foreach($products as $product) {
                $tmp_link = htmlspecialchars($link->getProductLink($product['id_product'], $product['link_rewrite']).'?id_lang='.intval($product['id_lang']));
		if (($priority = 0.9 - ($product['level_depth'] / 10)) < 0.1) {
		      $priority = 0.1;
		}
                $sitemap = $xml->addChild('url');
                $sitemap->addChild('loc', (!strstr($tmp_link, 'http')?$http:'') . $tmp_link);
                $sitemap->addChild('priority', $priority);
                $sitemap->addChild('lastmod', $product['date_add']);
                $sitemap->addChild('changefreq', 'weekly');
        }

        $xmlString = $xml->asXML();
        fwrite($fp, $xmlString, Tools::strlen($xmlString));
        fclose($fp);

        $res = file_exists($this->_filename);
        $this->_html .= '<h3 class="'. ($res ? 'conf confirm' : 'alert error') .'" style="margin-bottom: 20px">';
        $this->_html .= $res ? $this->l('Sitemap file successfully generated') : $this->l('Error while creating sitemap file');
        $this->_html .= '</h3>';
    }

    private function _displaySitemap()
    {
        if (file_exists($this->_filename))
        {			
            $fp = fopen($this->_filename, 'r');
            $fstat = fstat($fp);
            fclose($fp);
            $xml = simplexml_load_file($this->_filename);
            $nbPages = sizeof($xml->url);

            $this->_html .= '<p>'.$this->l('Your Google sitemap file is online at the following address:').' <a style="text-decoration:underline" href="'.$this->_filename_http.'"><b>'.$this->_filename_http.'</b></a></p>';

            $this->_html .= $this->l('Update:').' <b>'.date('H:i:s d-m-Y', $fstat['mtime']).'</b><br />';
            $this->_html .= $this->l('Filesize:').' <b>'.number_format(($fstat['size']*.000001), 3).'mb</b><br />';
            $this->_html .= $this->l('Indexed pages:').' <b>'.$nbPages.'</b><br /><br />';
        }
    }

    private function _displayForm()
    {
        $this->_html .=
        '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
                <fieldset>
			<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
                        <label style="width:auto">'.$this->l('Empty categories:').'</label>
			<div class="margin-form">
				<input type="radio" name="gsite_cats" id="gsite_cats_on" value="1" '.(Tools::getValue('gsite_cats', Configuration::get('GOOGLE_SITEMAP_CATS')) ? 'checked="checked" ' : '').'/>
				<label class="t" for="gsite_cats_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
				<input type="radio" name="gsite_cats" id="gsite_cats_off" value="0" '.(!Tools::getValue('gsite_cats', Configuration::get('GOOGLE_SITEMAP_CATS')) ? 'checked="checked" ' : '').'/>
				<label class="t" for="gsite_cats_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
				<p class="clear">'.$this->l('Generates categories containing any products.').'</p>
			</div><br />
			
			<label style="width:auto">'.$this->l('Active products:').'</label>
			<div class="margin-form">
				<input type="radio" name="gsite_active" id="gsite_active_on" value="1" '.(Tools::getValue('gsite_active', Configuration::get('GOOGLE_SITEMAP_ACTIVE_PRODUCTS')) ? 'checked="checked" ' : '').'/>
				<label class="t" for="gsite_active_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
				<input type="radio" name="gsite_active" id="gsite_active_off" value="0" '.(!Tools::getValue('gsite_active', Configuration::get('GOOGLE_SITEMAP_ACTIVE_PRODUCTS')) ? 'checked="checked" ' : '').'/>
				<label class="t" for="gsite_active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
				<p class="clear">'.$this->l('Generates active products with quantity > 0.').'</p>
			</div>
                </fieldset>
		<p><input name="btnSubmit" class="button" value="'.((!file_exists($this->_filename)) ? $this->l('Generate sitemap file') : $this->l('Update sitemap file')).'" type="submit" /></p>
        </form>';
    }
    
    function getContent()
    {
        $this->_html .= '<h2>'.$this->l('Search Engine Optimization').'</h2>';
        if (!empty($_POST)) {
            $this->_postValidation();
            if (!sizeof($this->_postErrors))
                $this->_postProcess();
            else
                foreach ($this->_postErrors AS $err)
                    $this->_html .= '<div class="alert error">'.$err.'</div>';
        }
        else
            $this->_html .= '<br />';

        $this->_displaySitemap();
        
        $this->_html .= $this->l('See').' <a style="text-decoration:underline" href="https://www.google.com/webmasters/tools/docs/en/about.html"> '.$this->l('this page').'</a> '.$this->l('for more information').'<br /><br />';
        
        $this->_displayForm();

        return $this->_html;
    }
}


?>
