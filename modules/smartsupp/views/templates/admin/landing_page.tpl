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

<script type="text/javascript">
    var ajax_controller_url = "{$ajax_controller_url|escape:'htmlall':'UTF-8'}";    
</script>
<input id="smartsupp_key" type="hidden" value="{$smartsupp_key|escape:'htmlall':'UTF-8'}">
<div class="bootstrap smartsupp_landing_page">
        <div class="module_error alert alert-danger">
                <button class="close" data-dismiss="alert" type="button">×</button>
                <span></span>
        </div>
</div>
<div id="smartsupp_landing_page" class="panel">
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
			<img src="{$module_dir|escape:'html':'UTF-8'}views/img/smartsupp_logo.png" alt="Smartsupp" />
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <button id="connect_existing_account_btn1" class="btn btn-default pull-right">{l s='Connect existing account' mod='smartsupp'}</button>
		</div>
	</div>
	<hr/>
        <div class="row text-center">
                <div class="row">
                        <p class="title">
                                <strong>{l s='Free live chat with visitor recording' mod='smartsupp'}</strong>
                        </p>
                        <p class="title">
                                {l s='Your customers are on your website right now.' mod='smartsupp'}
                                <br/>
                                {l s='Chat with them and see what they do.' mod='smartsupp'}
                        </p>
                        <div class="center-block">
                                <button id="create_account_btn1" class="btn btn-primary btn-lg">{l s='Create free account' mod='smartsupp'}</button>
                        </div>
                        <p class="col-lg-12 none">
                                <img class="dashboard" src="{$module_dir|escape:'html':'UTF-8'}views/img/dashboard_en.png" alt="" /><br />
                        </p>
                </div>
        </div>
        <div class="row text-center">
                <div class="row">
                        <p class="one">
                                <strong class="heading">{l s='Enjoy unlimited agents and chats forever for free' mod='smartsupp'}</strong>
                                <br/>
                                <strong class="heading">{l s='or take advantage of premium packages with advanced features.' mod='smartsupp'}</strong>
                        </p>
                        <p>
                                <strong>{l s='See all features on' mod='smartsupp'} <a href="https://www.smartsupp.com/?utm_source=Prestashop&utm_medium=integration&utm_campaign=link" target="_blank">{l s='our website' mod='smartsupp'}</a>.</strong>
                        </p>
                </div>
        </div>
        <div class="row text-center advantages">
                <div class="col-lg-4 text-center">
                        <img src="{$module_dir|escape:'html':'UTF-8'}views/img/chat_with_visitors.png" alt="{l s='Chat with visitors in real-time' mod='smartsupp'}" />
                        <p class="heading one">{l s='Chat with visitors in real-time' mod='smartsupp'}</p>
                        <p class="column-60">{l s='Answering questions right away improves loyalty and helps you build closer relationship with your customers.' mod='smartsupp'}</p>
                </div>
                <div class="col-lg-4 text-center">
                        <img src="{$module_dir|escape:'html':'UTF-8'}views/img/increase_sales.png" alt="{l s='Increase online sales' mod='smartsupp'}" />
                        <p class="heading one">{l s='Increase online sales' mod='smartsupp'}</p>
                        <p class="column-60">{l s='Turn your visitors into customers. Visitors who chat with you buy up to 5x more often - measurable in Google Analytics.' mod='smartsupp'}</p>
                </div>
                <div class="col-lg-4 text-center">
                        <img src="{$module_dir|escape:'html':'UTF-8'}views/img/visitor_screen_recording.png" alt="{l s='Visitor screen recording' mod='smartsupp'}" />
                        <p class="heading one">{l s='Visitor screen recording' mod='smartsupp'}</p>
                        <p class="column-60">{l s='Watch visitor behavior on your store. You see his screen, mouse movement, clicks and what he filled into forms.' mod='smartsupp'}</p>
                </div>
        </div>
        <br/>
        <div class="row text-center">
                <p>
                        <strong class="heading">{l s='Trusted by more that 55 000 companies' mod='smartsupp'}</strong>
                </p>
                <div class="customers">
                        <a>
                                <img alt="ŠKODA AUTO" src="{$module_dir|escape:'html':'UTF-8'}views/img/skoda.png">
                        </a>
                        <a>
                                <img alt="Gekko Computer" src="{$module_dir|escape:'html':'UTF-8'}views/img/gekko_computer.png">
                        </a>
                        <a>
                                <img alt="Lememo" src="{$module_dir|escape:'html':'UTF-8'}views/img/lememo.png">
                        </a>
                        <a>
                                <img alt="Vinoselección" src="{$module_dir|escape:'html':'UTF-8'}views/img/vinoseleccion.png">
                        </a>
                        <a>
                                <img alt="Conrad" src="{$module_dir|escape:'html':'UTF-8'}views/img/conrad.png">
                        </a>
                </div>
        </div>                                        
</div>
