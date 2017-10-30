{*
 * Smartsupp Live Chat integration module.
 * 
 * @package   Smartsupp
 * @author    Smartsupp <vladimir@smartsupp.com>
 * @link      http://www.smartsupp.com
 * @copyright 2016 Smartsupp.com
 * @license   GPL-2.0+
 *
 * Plugin Name:       Smartsupp Live Chat
 * Plugin URI:        http://www.smartsupp.com
 * Description:       Adds Smartsupp Live Chat code to PrestaShop.
 * Version:           2.1.5
 * Author:            Smartsupp
 * Author URI:        http://www.smartsupp.com
 * Text Domain:       smartsupp
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 *}

<div id="smartsupp_configuration" class="panel">
	<div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    <p class="email none">{$smartsupp_email|escape:'htmlall':'UTF-8'}</p>
                </div>
		<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-center">
			<img src="{$module_dir|escape:'html':'UTF-8'}views/img/smartsupp_logo.png" alt="Smartsupp" />
		</div>
		<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <button id="deactivate_chat_do" class="btn btn-default pull-right">{l s='Deactivate chat' mod='smartsupp'}</button>
		</div>
	</div>
        <div class="row">
                <div class="col-lg-4"></div>
                <div class="col-lg-4 text-center">
                    <p class="status-information">
                            {l s='Smartsupp chat is now visible on your website.' mod='smartsupp'}
                            <br/>
                            {l s='Go to Smartsupp to start chatting with visitors, customize chat box design and access all features.' mod='smartsupp'}
                    </p>
                    <div class="center-block">
                            <form action="https://dashboard.smartsupp.com" target="_blank">
                                    <input type="hidden" name="utm_source" value="Prestashop">
                                    <input type="hidden" name="utm_medium" value="integration">
                                    <input type="hidden" name="utm_campaign" value="link">
                                    <input type="submit" class="btn btn-primary btn-lg" value="{l s='Go to Smartsupp' mod='smartsupp'}">
                            </form>                        
                    </div>
                    <p style="padding-top: 5px;">
                            ({l s='This will open a new browser tab.' mod='smartsupp'})
                    </p>
                </div>
                <div class="col-lg-4"></div>
        </div>
 </div>