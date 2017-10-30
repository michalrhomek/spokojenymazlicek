{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2015 Knowband
*}
<script type="text/javascript">
    var uncheckAddressFieldMsg = '{l s='You cannot uncheck this field due to required field' mod='supercheckout' js=1}';
    var scp_ajax_action = '{$action}';		//Variable contains url, escape not required
    var loginizer_adv = {$velocity_supercheckout['loginizer_adv']|escape:'htmlall':'UTF-8'};
	var module_path = '{$module_dir|escape:'htmlall':'UTF-8'}';
	var remove_cnfrm_msg = '{l s='Are you really want to remove the image?' mod='supercheckout'}';
</script>

<div id="velsof_supercheckout_container" class="content">
    <div class="box">
        <div class="navbar main hidden-print">
            <!-- Brand & save buttons -->
            <ul class="pull-left">
		<div style="position: inherit;color: white;font-size: 15px;min-width: 700px;padding-left: 50px;padding-top: 5px;">
			Have some doubt or issue? Get prompt help from us.
			<a target="_blank" href="https://addons.prestashop.com/en/write-to-developper?id_product=18016" style="text-decoration: none;">
				<span style="color: white;background-color: #79BD3C;padding: 6px 20px;border-radius: 3px;font-size: 13px;margin-left: 10px;text-shadow: chartreuse;">
					Contact us
				</span>
			</a>
		</div>
                <li class="themer_eyedropper" data-toggle="collapse" data-target="#themer"></li>
            </ul>
            <div class="topbuttons">                
                <a href="javascript:void(0)" onclick="validate_data()"><span id="save_post_setting" class="btn btn-block btn-success action-btn">{l s='Save' mod='supercheckout'}</span></a>&nbsp;&nbsp;&nbsp;<a href="{$cancel_action|escape:'htmlall':'UTF-8'}"><span class="btn btn-block btn-danger action-btn">{l s='Cancel' mod='supercheckout'}</span></a>
                <span class="gritter-add-primary btn btn-default btn-block hidden">For notifications on saving</span>
            </div>
        </div>
        <div class="velsof-container">
            <div class="widget velsof-widget-left">
                <div class="widget-body velsof-widget-left">                    
                        <div id="wrapper">
                            <div id="menuVel" class="hidden-print ui-resizable">
                                <div class="slimScrollDiv">
                                    <div class="slim-scroll">
                                        <ul>
                                            <li class="active {if $ps_version eq 15}vss-tab-ver15{/if}"><a class="glyphicons settings" href="#tab_general_settings" data-toggle="tab"><i></i><span>{l s='General Settings' mod='supercheckout'}</span></a></li>
                                            <li class="{if $ps_version eq 15}vss-tab-ver15{/if}"><a class="glyphicons brush" href="#tab_customizer" data-toggle="tab"><i></i><span>{l s='Customizer' mod='supercheckout'}</span></a></li>
                                            <li class="{if $ps_version eq 15}vss-tab-ver15{/if}"><a class="glyphicons keys" id="velsof_tab_login" href="#tab_login" onclick="loginizerAdv();"data-toggle="tab"><i></i><span>{l s='Login' mod='supercheckout'}</span></a></li>                                            
                                            <li class="{if $ps_version eq 15}vss-tab-ver15{/if}"><a class="glyphicons envelope" id="velsof_tab_mailchimp" href="#tab_mailchimp" data-toggle="tab"><i></i><span>{l s='MailChimp' mod='supercheckout'}</span></a></li>                                            
                                            <li class="{if $ps_version eq 15}vss-tab-ver15{/if}"><a class="glyphicons home" href="#tab_Addr" data-toggle="tab"><i></i><span>{l s='Addresses' mod='supercheckout'}</span></a></li>
                                            <li class="{if $ps_version eq 15}vss-tab-ver15{/if}"><a class="glyphicons credit_card" href="#tab_payment_method" data-toggle="tab"><i></i><span>{l s='Payment Method' mod='supercheckout'}</span></a></li>
                                            <li class="{if $ps_version eq 15}vss-tab-ver15{/if}"><a class="glyphicons cargo" href="#tab_shipping_method" data-toggle="tab"><i></i><span>{l s='Delivery Method' mod='supercheckout'}</span></a></li>
                                            <li class="{if $ps_version eq 15}vss-tab-ver15{/if}"><a class="glyphicons boat" href="#tab_ship_to_pay" data-toggle="tab"><i></i><span>{l s='Ship2pay' mod='supercheckout'}</span></a></li>
					    <li class="{if $ps_version eq 15}vss-tab-ver15{/if}"><a class="glyphicons shopping_cart" href="#tab_cart" data-toggle="tab"><i></i><span>{l s='Cart' mod='supercheckout'}</span></a></li>
                                            <li class="{if $ps_version eq 15}vss-tab-ver15{/if}"><a class="glyphicons podium" id="velsof_tab_design" href="#tab_design" data-toggle="tab"><i></i><span>{l s='Design' mod='supercheckout'}</span></a></li>
                                            <li class="{if $ps_version eq 15}vss-tab-ver15{/if}"><a class="glyphicons conversation" href="#tab_lang_translator" data-toggle="tab"><i></i><span>{l s='Language Translator' mod='supercheckout'}</span></a></li>
                                            <li class="{if $ps_version eq 15}vss-tab-ver15{/if}"><a class="glyphicons circle_question_mark" href="#tab_faq" data-toggle="tab"><i></i><span>{l s='FAQs' mod='supercheckout'}</span></a></li>                                            
                                            <li class="{if $ps_version eq 15}vss-tab-ver15{/if}"><a class="glyphicons pen" href="#tab_suggest" data-toggle="tab"><i></i><span>{l s='Suggestions' mod='supercheckout'}</span></a></li>
                                            <li class="{if $ps_version eq 15}vss-tab-ver15{/if}"><a class="glyphicons bookmark" target="_blank" href="http://addons.prestashop.com/en/2_community?contributor=38002" target="_blank"><i></i><span>{l s='Other Plugins' mod='supercheckout'}</span></a></li>
                                        </ul>
                                        <div class="clearfix"></div>
<!--                                        <div class="separator bottom"></div> -->
                                    </div>
                                </div>
                                <div class="ui-resizable-handle ui-resizable-e" style="z-index: 1000;"></div>
                            </div>
                            
                            <div id="content">
                                <div class="box">
                                    <div class="content tabs">
                                        
                                           
                                            <div class="layout">
                                                <div class="tab-content even-height">
                                                    <!--------------- Start - General Setings -------------------->
                                            <form action="{$action|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" id="supercheckout_configuration_form">
												 <input type="hidden" name="{$submit_action|escape:'htmlall':'UTF-8'}" value="1" >
                                            <input type="hidden" name="velocity_supercheckout[adv_id]" value="{$velocity_supercheckout['adv_id']|escape:'htmlall':'UTF-8'}" >
                                            <input type="hidden" name="velocity_supercheckout[loginizer_adv]" value=1 >
                                            <input type="hidden" name="velocity_supercheckout[temp_cart_image_size][width]" value="{$velocity_supercheckout['cart_image_size']['width']|escape:'htmlall':'UTF-8'}" />
                                            <input type="hidden" name="velocity_supercheckout[temp_cart_image_size][height]" value="{$velocity_supercheckout['cart_image_size']['height']|escape:'htmlall':'UTF-8'}" />
                                                    <div id="tab_general_settings" class="tab-pane active tab-form">
                                                            <div class="block">
                                                                <h4 class='velsof-tab-heading'>{l s='General Settings' mod='supercheckout'}</h4>
                                                                <table class="form">
                                                                    <tr>
                                                                        <td class="name vertical_top_align"><span class="control-label">{l s='Enable/Disable' mod='supercheckout'}: </span>                                                                
                                                                            <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Enable/Disable Tooltip' mod='supercheckout'}"></i>
                                                                        </td>
                                                                        <td class="settings">
                                                                            <input type="hidden" value="0" name="velocity_supercheckout[enable]" />
                                                                            {if $velocity_supercheckout['enable'] eq 1}
                                                                                {if $IE7 eq true}
                                                                                    <div>
                                                                                        <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[enable]" id="supercheckout_enable" checked="checked" />
                                                                                    </div>
                                                                                {else}
                                                                                    <div class="make-switch" data-on="primary" data-off="default">
                                                                                        <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[enable]" id="supercheckout_enable" checked="checked" />
                                                                                    </div>
                                                                                {/if}                                                                    
                                                                            {else}
                                                                                {if $IE7 eq true}
                                                                                    <div>
                                                                                        <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[enable]" id="supercheckout_enable" />
                                                                                    </div>
                                                                                {else}
                                                                                    <div class="make-switch" data-on="primary" data-off="default">
                                                                                        <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[enable]" id="supercheckout_enable"/>
                                                                                    </div>
                                                                                {/if}
                                                                            {/if}
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td class="name vertical_top_align"><span class="control-label">{l s='Enable Guest Checkout' mod='supercheckout'}: </span>                                                                
                                                                            <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Enable Guest Checkout Tooltip' mod='supercheckout'}"></i>
                                                                        </td>
                                                                        <td class="settings">
                                                                            <input type="hidden" value="0" name="velocity_supercheckout[enable_guest_checkout]" />
                                                                            {if $velocity_supercheckout['enable_guest_checkout'] eq 1}
                                                                                {if $IE7 eq true}
                                                                                    <div>
                                                                                        <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[enable_guest_checkout]" id="supercheckout_enable_newsletter" checked="checked" />
                                                                                    </div>
                                                                                {else}
                                                                                    <div class="make-switch" data-on="primary" data-off="default">
                                                                                        <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[enable_guest_checkout]" id="supercheckout_enable_newsletter" checked="checked" />
                                                                                    </div>
                                                                                {/if}                                                                    
                                                                            {else}
                                                                                {if $IE7 eq true}
                                                                                    <div>
                                                                                        <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[enable_guest_checkout]" id="supercheckout_enable_newsletter" />
                                                                                    </div>
                                                                                {else}
                                                                                    <div class="make-switch" data-on="primary" data-off="default">
                                                                                        <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[enable_guest_checkout]" id="supercheckout_enable_newsletter"/>
                                                                                    </div>
                                                                                {/if}
                                                                            {/if}
                                                                            
                                                                            
                                                                        </td>
                                                                    </tr>                                                                    

                                                                    <tr>
                                                                        <td class="name vertical_top_align"><span class="control-label">{l s='Register Guest' mod='supercheckout'}: </span>                                                                
                                                                            <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Register Guest Tooltip' mod='supercheckout'}"></i>
                                                                        </td>
                                                                        <td class="settings">
                                                                            <input type="hidden" value="0" name="velocity_supercheckout[enable_guest_register]" />
                                                                            {if $velocity_supercheckout['enable_guest_register'] eq 1}
                                                                                {if $IE7 eq true}
                                                                                    <div>
                                                                                        <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[enable_guest_register]" id="supercheckout_enable_guest_register" checked="checked" />
                                                                                    </div>
                                                                                {else}
                                                                                    <div class="make-switch" data-on="primary" data-off="default">
                                                                                        <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[enable_guest_register]" id="supercheckout_enable_guest_register" checked="checked" />
                                                                                    </div>
                                                                                {/if}                                                                    
                                                                            {else}
                                                                                {if $IE7 eq true}
                                                                                    <div>
                                                                                        <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[enable_guest_register]" id="supercheckout_enable_guest_register" />
                                                                                    </div>
                                                                                {else}
                                                                                    <div class="make-switch" data-on="primary" data-off="default">
                                                                                        <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[enable_guest_register]" id="supercheckout_enable_guest_register"/>
                                                                                    </div>
                                                                                {/if}
                                                                            {/if}
                                                                        </td>
                                                                    </tr>
								<tr><td class="name vertical_top_align"><span class="control-label">{l s='Delivery address for virtual cart' mod='supercheckout'}: </span>
                                                                            <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='If set to OFF, it will hide delivery adress automatically and show invoice address for cart with virual products only' mod='supercheckout'}"></i>
                                                                        </td>
									<td class="settings">
                                                                            <input type="hidden" value="0" name="velocity_supercheckout[hide_delivery_for_virtual]" />
                                                                            {if $velocity_supercheckout['hide_delivery_for_virtual'] eq 1}
                                                                                {if $IE7 eq true}
                                                                                    <div>
											    <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[hide_delivery_for_virtual]" id="supercheckout_hide_delivery_for_virtual" checked="checked" />
                                                                                    </div>
                                                                                {else}
                                                                                    <div class="make-switch" data-on="primary" data-off="default">
                                                                                        <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[hide_delivery_for_virtual]" id="supercheckout_hide_delivery_for_virtual" checked="checked" />
                                                                                    </div>
                                                                                {/if}                                                                    
                                                                            {else}
                                                                                {if $IE7 eq true}
                                                                                    <div>
                                                                                        <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[hide_delivery_for_virtual]" id="supercheckout_hide_delivery_for_virtual" />
                                                                                    </div>
                                                                                {else}
                                                                                    <div class="make-switch" data-on="primary" data-off="default">
                                                                                        <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[hide_delivery_for_virtual]" id="supercheckout_hide_delivery_for_virtual"/>
                                                                                    </div>
                                                                                {/if}
                                                                            {/if}
                                                                        </td><tr>
                                                                    <tr>
                                                                        <td class="name vertical_top_align">
                                                                            <span>{l s='Default Option at Checkout' mod='supercheckout'}: </span><i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Default Option at Checkout Tooltip' mod='supercheckout'}"></i>
                                                                        </td>
                                                                        <td class="left settings">
                                                                            <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                                <label class="radio coupon_type_radio">
                                                                                    <input type="radio" class="radio coupon_type_radio" name="velocity_supercheckout[checkout_option]" value="0"  {if $velocity_supercheckout['checkout_option'] eq 0} checked="checked" {/if} />{l s='Login' mod='supercheckout'}                                                                        
                                                                                </label>
                                                                                <label class="radio coupon_type_radio">
                                                                                    <input type="radio" class="radio coupon_type_radio" name="velocity_supercheckout[checkout_option]" value="1" {if $velocity_supercheckout['checkout_option'] eq 1} checked="checked" {/if} />{l s='Guest' mod='supercheckout'}                                                                        
                                                                                </label>
                                                                                <label class="radio coupon_type_radio">
                                                                                    <input type="radio" class="radio coupon_type_radio" name="velocity_supercheckout[checkout_option]" value="2" {if $velocity_supercheckout['checkout_option'] eq 2} checked="checked" {/if} />{l s='Register' mod='supercheckout'}                                                                        
                                                                                </label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    
                                                                    <tr>
                                                                        <td class="name vertical_top_align"><span class="control-label">{l s='Testing Mode' mod='supercheckout'}: </span>                                                                
                                                                            <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Enable this if you want to test this plugin before making it live.' mod='supercheckout'}"></i>                                                                            
                                                                        </td>
                                                                        <td class="settings">
                                                                            <input type="hidden" value="0" name="velocity_supercheckout[super_test_mode]" />
                                                                            {if isset($velocity_supercheckout['super_test_mode']) and $velocity_supercheckout['super_test_mode'] eq 1}
                                                                                {if $IE7 eq true}
                                                                                    <div>
                                                                                        <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[super_test_mode]" id="supercheckout_test_mode" checked="checked" />
                                                                                    </div>
                                                                                {else}
                                                                                    <div class="make-switch" data-on="primary" data-off="default">
                                                                                        <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[super_test_mode]" id="supercheckout_test_mode" checked="checked" />
                                                                                    </div>
                                                                                {/if}                                                                    
                                                                            {else}
                                                                                {if $IE7 eq true}
                                                                                    <div>
                                                                                        <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[super_test_mode]" id="supercheckout_test_mode" />
                                                                                    </div>
                                                                                {else}
                                                                                    <div class="make-switch" data-on="primary" data-off="default">
                                                                                        <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[super_test_mode]" id="supercheckout_test_mode"/>
                                                                                    </div>
                                                                                {/if}
                                                                            {/if}                                                                            
                                                                        </td>
                                                                    </tr>
                                                                    <tr id="front_module_url" style="display: none;">
                                                                        <td colspan="2">
                                                                            <div class="span" style="padding:20px;">
                                                                                <p style="margin-bottom: 0;">
                                                                                    <b>{l s='Testing URL' mod='supercheckout'}:</b>
                                                                                    {$module_url|escape:'htmlall':'UTF-8'}
                                                                                </p> 
                                                                            </div>
                                                                        </td>
                                                                    </tr>

                                                                    
                                                                   
                                                                </table>
									 <div style= "  text-align:center;padding: 25px; height:140px;margin: 40px;margin-bottom:0px; background: aliceblue;{if $ps_version eq 15}height: 90px;{/if}">
                                                        <div><span style="font-size:18px;" >Buy its complementary Add to Cart popup module to increase your conversion rate.</span>
                                                        <br>
                                                        <br>
                                                         <a target="_blank" href="http://addons.prestashop.com/en/front-office-features-prestashop-modules/17893-add-to-cart-popup-ajax-cart.html"><span style="margin-left:30%;max-width:40% !important;font-size:18px;" class='btn btn-block btn-success action-btn'>Click here to know more</span></a><div>
                                                            </div>
                                                              
                                                   </div>
                                                  </div>
                                                            </div>
                                                            
                                                                <!--<div class="row">
                                                                    <div class="span">
                                                                        <p style="margin-bottom: 0; margin-right: 5px">
                                                                            <span style="font-weight: bold; font-size: 15px;">Note:</span>
                                                                            <span style="color: black; margin-left: 5px;">Please Make sure that <span style='color: red;'>Advanced Parameters-> Performance-> Debug Mode-> Disable all overrides button-> is Set to No</span>.</span>
                                                                            </p>
                                                                    </div>
                                                                </div>
                                                            <hr style="margin-top:5px;">-->
                                                    </div>

                                                    <!--------------- End - General Settings -------------------->
                                                     <!--------------- Start - Customize -------------------->
                                                    <div id="tab_customizer" class="tab-pane tab-form">
                                                        <div class="block">
                                                            <h4 class='velsof-tab-heading' style="font-size: 20px;" >{l s='Customizer' mod='supercheckout'}</h4>
                                                            <table class="form">
                                                              
                                                                <tr>
                                                                        <td class="name vertical_top_align" style="padding: 15px;">
                                                                            <span>{l s='Button Background Color' mod='supercheckout'}: </span><i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Change the Button Background Color' mod='supercheckout'}"></i>
                                                                        </td>
                                                                        <td class="settings" style="padding: 15px;">
                                                                            <div class="widget-body uniformjs" style="padding: 0 !important;">

                                                                                   <input type="text" class="color form-control colorizer-input" onchange="bg_changer(this.color);" name="velocity_supercheckout[customizer][button_color]"  value="{$velocity_supercheckout['customizer']['button_color']|escape:'htmlall':'UTF-8'}"/>

                                                                               </div>
                                                                        </td>
                                                                               <td>&nbsp;</td>

                                                                    </tr>
                                                                    <tr>
                                                                        <td class="name vertical_top_align" style="padding: 15px;">
                                                                            <span>{l s='Button Border Color' mod='supercheckout'}: </span><i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Change the Button Border Color' mod='supercheckout'}"></i>
                                                                        </td>
                                                                        <td class="settings" style="padding: 15px;">
                                                                            <div class="widget-body uniformjs" style="padding: 0 !important;">

                                                                                   <input type="text" class="color form-control colorizer-input"  onchange="border_changer(this.color);" name="velocity_supercheckout[customizer][button_border_color]" value="{$velocity_supercheckout['customizer']['button_border_color']|escape:'htmlall':'UTF-8'}"/>


                                                                                  
                                                                                   <div id="button_preview" style="background-color:#{$velocity_supercheckout['customizer']['button_color']|escape:'htmlall':'UTF-8'};border: 1px solid #{$velocity_supercheckout['customizer']['button_border_color']|escape:'htmlall':'UTF-8'} !important;color: #{$velocity_supercheckout['customizer']['button_text_color']|escape:'htmlall':'UTF-8'} !important;border-bottom:3px solid #{$velocity_supercheckout['customizer']['border_bottom_color']|escape:'htmlall':'UTF-8'} !important;width: 160px;
                                                                                        
                                                                                        display: inline-block;
                                                                                        text-align: center;
                                                                                        float: left;
                                                                                        margin-left: 65%;
                                                                                        padding: 10px;
                                                                                        font-size: 16px;
                                                                                        border-radius: 5px;
                                                                                        margin-top: -38px;
                                                                                        ">
                                                                                     <span> {l s='Button Preview' mod='supercheckout'}</span>
                                                                                   </div>
                                                                                   </div>
                                                                        </td>

                                                                          

                                                                    </tr>
                                                                       <tr>
                                                                        <td class="name vertical_top_align" style="padding: 15px;">
                                                                            <span>{l s='Button Text Color' mod='supercheckout'}: </span><i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Change the Button Text Color' mod='supercheckout'}"></i>
                                                                        </td>
                                                                        <td class="settings" style="padding: 15px;">
                                                                            <div class="widget-body uniformjs" style="padding: 0 !important;">

                                                                                   <input type="text" class="color form-control colorizer-input"  onchange="text_changer(this.color);"name="velocity_supercheckout[customizer][button_text_color]"  value="{$velocity_supercheckout['customizer']['button_text_color']|escape:'htmlall':'UTF-8'}"/>


                                                                            </div>
                                                                        </td>
                                                                      
                                                                    </tr>

                                                                    <tr>
                                                                        <td class="name vertical_top_align" style="padding: 15px;">
                                                                            <span>{l s='Button Border Bottom Color' mod='supercheckout'}: </span><i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Change the Button Border Bottom Color' mod='supercheckout'}"></i>
                                                                        </td>
                                                                        <td class="settings" style="padding: 15px;">
                                                                            <div class="widget-body uniformjs" style="padding: 0 !important;">

                                                                                   <input type="text" class="color form-control colorizer-input" onchange="border_bottom_changer(this.color);"name="velocity_supercheckout[customizer][border_bottom_color]"  value="{$velocity_supercheckout['customizer']['border_bottom_color']|escape:'htmlall':'UTF-8'}"  />


                                                                            </div>
                                                                        </td>
                                                                      
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="name vertical_top_align" style="padding: 15px;">
                                                                            <span>{l s='Custom CSS' mod='supercheckout'}: </span><i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Provide some CSS code for changes in the front end of SuperCheckout' mod='supercheckout'}"></i>
                                                                        </td>
                                                                        <td class="settings" style="padding: 15px;">
                                                                            <textarea rows="5" style="resize: both;" class="vss_sc_ver15" name="velocity_supercheckout[custom_css]">{if isset($velocity_supercheckout['custom_css'])}{$velocity_supercheckout['custom_css']}{/if}</textarea>{*Variable contains css content, escape not required*}
                                                                        </td>                                                                        
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="name vertical_top_align" style="padding: 15px;">
                                                                            <span>{l s='Custom JS' mod='supercheckout'}: </span><i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Provide some javascript code for changes in the front end of SuperCheckout' mod='supercheckout'}"></i>
                                                                        </td>
                                                                        <td class="settings" style="padding: 15px;">
                                                                            <textarea rows="5" style="resize: both;" class="vss_sc_ver15" name="velocity_supercheckout[custom_js]">{if isset($velocity_supercheckout['custom_js'])}{$velocity_supercheckout['custom_js']}{/if}</textarea>{*Variable contains js content, escape not required*}
                                                                        </td>                                                                        
                                                                    </tr>


                                                            </table>
                                                                              
                                                        </div>
                                                    </div>
                                                    <!--------------- End - Customize -------------------->

                                                    <!--------------- Start - Login -------------------->

                                                    <div id="tab_login" class="tab-pane tab-form">
                                                        <div class="block">
                                                            <h4 class='velsof-tab-heading'>{l s='Login' mod='supercheckout'}<span class="mandatory_notify">{l s='(*) are mandatory fields' mod='supercheckout'}</span></h4>
                                                            <div class="block">
                                                                <table class="form">
                                                                    <tr>
                                                                        <td class="name vertical_top_align" ><span class="control-label">{l s='Show popup' mod='supercheckout'}: </span>                                                                
                                                                            <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Show popup rather than redirect when customer clicks on Facebook or Google button' mod='supercheckout'}"></i>
                                                                        </td>
                                                                        <td class="settings" style="padding-bottom:10px;">
                                                                            <input type="hidden" value="0" name="velocity_supercheckout[social_login_popup][enable]" />
                                                                            {if $velocity_supercheckout['social_login_popup']['enable'] eq 1}
                                                                                {if $IE7 eq true}
                                                                                    <div>
                                                                                        <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[social_login_popup][enable]" id="supercheckout_social_login_popup" checked="checked" />
                                                                                    </div>
                                                                                {else}
                                                                                    <div class="make-switch" data-on="primary" data-off="default">
                                                                                        <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[social_login_popup][enable]" id="supercheckout_social_login_popup" checked="checked" />
                                                                                    </div>
                                                                                {/if}                                                                    
                                                                            {else}
                                                                                {if $IE7 eq true}
                                                                                    <div>
                                                                                        <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[social_login_popup][enable]" id="supercheckout_social_login_popup" />
                                                                                    </div>
                                                                                {else}
                                                                                    <div class="make-switch" data-on="primary" data-off="default">
                                                                                        <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[social_login_popup][enable]" id="supercheckout_social_login_popup"/>
                                                                                    </div>
                                                                                {/if}
                                                                            {/if}
                                                                        </td>
									</tr>
                                                                        
                                                                       
                                                                            <td class="name vertical_top_align"><span class="control-label">{l s='Enable Facebook Login' mod='supercheckout'}: </span>  
                                                                            <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Enable Facebook Login Tooltip' mod='supercheckout'}"></i>
                                                                        </td>
                                                                        <td class="settings">
                                                                            <input type="hidden" value="0" name="velocity_supercheckout[fb_login][enable]" />
                                                                            {if $velocity_supercheckout['fb_login']['enable'] eq 1}
                                                                                {if $IE7 eq true}
                                                                                    <div>
                                                                                        <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[fb_login][enable]" id="supercheckout_fb_login" checked="checked" />
                                                                                    </div>
                                                                                {else}
                                                                                    <div class="make-switch" data-on="primary" data-off="default">
                                                                                        <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[fb_login][enable]" id="supercheckout_fb_login" checked="checked" />
                                                                                    </div>
                                                                                {/if}                                                                    
                                                                            {else}
                                                                                {if $IE7 eq true}
                                                                                    <div>
                                                                                        <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[fb_login][enable]" id="supercheckout_fb_login" />
                                                                                    </div>
                                                                                {else}
                                                                                    <div class="make-switch" data-on="primary" data-off="default">
                                                                                        <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[fb_login][enable]" id="supercheckout_fb_login"/>
                                                                                    </div>
                                                                                {/if}
                                                                            {/if}
                                                                            <span class="pad-right" style="font-size:14px;font-weight:500;float:right; "><a href="javascript:void(0)" onclick="configurationAccordian('facebook');" {if $ps_version eq 15}style="color: #428bca;"{/if}>{l s='Click here to see Steps to configure Facebook app ' mod='supercheckout'}</a></span>
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td class="name vertical_top_align"><span class="control-label"><span class="asterisk">*</span>{l s='Facebook App Id' mod='supercheckout'}: </span>                                                                
                                                                            <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Facebook App Id Tooltip' mod='supercheckout'}"></i>
                                                                        </td>
                                                                        <td class="settings">
                                                                            <input type="text" class="text-width" name="velocity_supercheckout[fb_login][app_id]" value="{$velocity_supercheckout['fb_login']['app_id']|escape:'htmlall':'UTF-8'}"/>
                                                                            <span id="fb_app_id_error" class="supercheckout_error" ></span>
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td class="name vertical_top_align" ><span class="control-label"><span class="asterisk">*</span>{l s='Facebook App Secret' mod='supercheckout'}: </span>                                                                
                                                                            <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Facebook App Secret Tooltip' mod='supercheckout'}"></i>
                                                                        </td>
                                                                        <td class="settings" >
                                                                            <input type="text" class="text-width" name="velocity_supercheckout[fb_login][app_secret]" value="{$velocity_supercheckout['fb_login']['app_secret']|escape:'htmlall':'UTF-8'}"/>
                                                                            <span id="fb_app_secret_error" class="supercheckout_error" ></span>
                                                                        </td>
                                                                    
                                                                    <tr>
                                                                        <td class="name vertical_top_align"><span class="control-label">{l s='Enable Google Login' mod='supercheckout'}: </span>                                                                
                                                                            <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Enable Google Login Tooltip' mod='supercheckout'}"></i>
                                                                        </td>
                                                                        <td class="settings">
                                                                            <input type="hidden" value="0" name="velocity_supercheckout[google_login][enable]" />
                                                                            {if $velocity_supercheckout['google_login']['enable'] eq 1}
                                                                                {if $IE7 eq true}
                                                                                    <div>
                                                                                        <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[google_login][enable]" id="supercheckout_google_login" checked="checked" />
                                                                                    </div>
                                                                                {else}
                                                                                    <div class="make-switch" data-on="primary" data-off="default">
                                                                                        <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[google_login][enable]" id="supercheckout_google_login" checked="checked" />
                                                                                    </div>
                                                                                {/if}                                                                    
                                                                            {else}
                                                                                {if $IE7 eq true}
                                                                                    <div>
                                                                                        <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[google_login][enable]" id="supercheckout_google_login" />
                                                                                    </div>
                                                                                {else}
                                                                                    <div class="make-switch" data-on="primary" data-off="default">
                                                                                        <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[google_login][enable]" id="supercheckout_google_login"/>
                                                                                    </div>
                                                                                {/if}
                                                                            {/if}
                                                                            <span class="pad-right" style="font-size:14px;font-weight:500;float:right;"><a href="javascript:void(0)" onclick="configurationAccordian('google');" {if $ps_version eq 15}style="color: #428bca;"{/if}>{l s='Click here to see Steps to configure Google App ' mod='supercheckout'}</a></span>
                                                                        </td>
                                                                    </tr>

                                                                    <tr style="display: none;">
                                                                        <td class="name vertical_top_align"><span class="control-label">{l s='Google App Id' mod='supercheckout'}: </span>                                                                
                                                                            <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Google App Id Tooltip' mod='supercheckout'}"></i>
                                                                        </td>
                                                                        <td class="settings">
                                                                            <input type="text" class="text-width" name="velocity_supercheckout[google_login][app_id]" value="{$velocity_supercheckout['google_login']['app_id']|escape:'htmlall':'UTF-8'}"/>
                                                                            <span id="gl_app_id_error" class="supercheckout_error" ></span>
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td class="name vertical_top_align"><span class="control-label"><span class="asterisk">*</span>{l s='Google Client Id' mod='supercheckout'}: </span>                                                                
                                                                            <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Google Client Id Tooltip' mod='supercheckout'}"></i>
                                                                        </td>
                                                                        <td class="settings">
                                                                            <input type="text" class="text-width" name="velocity_supercheckout[google_login][client_id]" value="{$velocity_supercheckout['google_login']['client_id']|escape:'htmlall':'UTF-8'}"/>
                                                                            <span id="gl_client_id_error" class="supercheckout_error" ></span>
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td class="name vertical_top_align"><span class="control-label"><span class="asterisk">*</span>{l s='Google App Secret' mod='supercheckout'}: </span>                                                                
                                                                            <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Google App Secret Tooltip' mod='supercheckout'}"></i>
                                                                        </td>
                                                                        <td class="settings">
                                                                            <input type="text" class="text-width" name="velocity_supercheckout[google_login][app_secret]" value="{$velocity_supercheckout['google_login']['app_secret']|escape:'htmlall':'UTF-8'}"/>
                                                                            <span id="gl_app_secret_error" class="supercheckout_error" ></span>
                                                                        </td>
                                                                    </tr>

                                                                </table>
									    
									    <div style= "  text-align:center;padding: 25px; height:140px;margin: 40px;margin-bottom:0px; background: aliceblue;{if $ps_version eq 15}height: 100px;{/if}" id="loginizer_link">
                                                        <div><span style="font-size:18px;" >Want to add more social login options for your customers?</span>
                                                        <br>
                                                        <br>
                                                         <a target="_blank" href="http://addons.prestashop.com/en/social-commerce-facebook-prestashop-modules/18220-social-network-for-login-9-in-1-fast-secure.html"><span style="margin-left:30%;max-width:40% !important;font-size:18px;" class='btn btn-block btn-success action-btn'>Add more buttons</span></a><div>
                                                            </div>
                                                         </div>
                                                  </div>
                                                         <div id="facebook_acc" style="display:none;">
                                                             <h4 class='velsof-tab-heading'>{l s='Steps To Configure Facebook App:' mod='supercheckout'}</h4>
									<div id="facebook_accordian" class="accordian_container">
										<h3>{l s='Step 1' mod='supercheckout'} </h3>
										<div class="accdiv">
											<span class="pad-right"><a href="https://developers.facebook.com/apps/" target="_blank" style="color: #00aff0;">{l s='Click here to get Facebook app id and app secret' mod='supercheckout'}</a></span>
										</div>
										<h3>{l s='Step 2' mod='supercheckout'}</h3>
										<div class="accdiv">
											 <img src='{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/manual_steps/facebook/facebook2.jpg' />
										</div>
										<h3>{l s='Step 3' mod='supercheckout'}</h3>
										<div class="accdiv">
											 <img src='{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/manual_steps/facebook/facebook3.jpg' />
										</div>
										<h3>{l s='Step 4, 5' mod='supercheckout'}</h3>
										<div class="accdiv">
											 <img src='{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/manual_steps/facebook/facebook4.jpg' />
										</div>
										<h3>{l s='Step 6, 7' mod='supercheckout'}</h3>
										<div class="accdiv">
											 <img src='{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/manual_steps/facebook/facebook5.jpg' />
										</div>
										<h3>{l s='Step 8' mod='supercheckout'}</h3>
										<div class="accdiv">
											 <img src='{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/manual_steps/facebook/facebook6.jpg' />
										</div>
										<h3>{l s='Step 9' mod='supercheckout'}</h3>
										<div class="accdiv">
											 <img src='{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/manual_steps/facebook/facebook7.jpg' />
										</div>
										<h3>{l s='Step 10 , 11, 12' mod='supercheckout'}</h3>
										<div class="accdiv">
											<pre><b>{l s='For Step #9 use App Domain: ' mod='supercheckout'}</b>{$domain|escape:'htmlall':'UTF-8'}<br><b>{l s='For Step #11 use Site Url: ' mod='supercheckout'}</b>{$manual_dir|escape:'htmlall':'UTF-8'}</pre>
											 <img src='{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/manual_steps/facebook/facebook8.jpg' />
										</div>
										<h3>{l s='Step 13' mod='supercheckout'}</h3>
										<div class="accdiv">
											 <img src='{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/manual_steps/facebook/facebook9.jpg' />
										</div>
										<h3>{l s='Step 14, 15' mod='supercheckout'}</h3>
										<div class="accdiv">
											 <img src='{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/manual_steps/facebook/facebook10.jpg' />
										</div>
										<h3>{l s='Step 16' mod='supercheckout'}</h3>
										<div class="accdiv">
											 <img src='{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/manual_steps/facebook/facebook11.jpg' />
										</div>
										
									</div>
                                                         </div>
                                                                                <div id="google_acc" style="display:none;">
                                                                                    <h4 class='velsof-tab-heading'>{l s='Steps To Configure Google App:' mod='supercheckout'}</h4>
                                                                            <div id="google_accordian" class="accordian_container">
                                                                                    <h3>{l s='Step 1' mod='supercheckout'}</h3>
                                                                                    <div class="accdiv">
                                                                                             <span class="pad-right"><a href="https://console.developers.google.com/project" target="_blank" style="color: #00aff0;">{l s='Click here to get Google  client id and client secret' mod='supercheckout'}</a></span>
                                                                                    </div>
                                                                                    <h3>{l s='Step 2' mod='supercheckout'}</h3>
                                                                                    <div class="accdiv">
                                                                                             <img src='{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/manual_steps/google/google2.jpg' />
                                                                                    </div>
                                                                                    <h3>{l s='Step 3, 4' mod='supercheckout'}</h3>
                                                                                    <div class="accdiv">
                                                                                             <img src='{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/manual_steps/google/google3.jpg' />
                                                                                    </div>
                                                                                    <h3>{l s='Step 5, 6, 7' mod='supercheckout'}</h3>
                                                                                    <div class="accdiv">
                                                                                             <img src='{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/manual_steps/google/google4.jpg' />
                                                                                    </div>
                                                                                    <h3>{l s='Step 8' mod='supercheckout'}</h3>
                                                                                    <div class="accdiv">
                                                                                             <img src='{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/manual_steps/google/google5.jpg' />
                                                                                    </div>
                                                                                    <h3>{l s='Step 9' mod='supercheckout'}</h3>
                                                                                    <div class="accdiv">
                                                                                             <img src='{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/manual_steps/google/google6.jpg' />
                                                                                    </div>
                                                                                    <h3>{l s='Step 10, 11, 12' mod='supercheckout'}</h3>
                                                                                    <div class="accdiv">
                                                                                             <img src='{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/manual_steps/google/google7.jpg' />
                                                                                    </div>
                                                                                    <h3>{l s='Step 13, 14, 15, 16, 17' mod='supercheckout'}</h3>
                                                                                    <div class="accdiv">
                                                                                            <pre><b>{l s='For Step #15 Use Authorized javascript origins: ' mod='supercheckout'}</b>{$manual_dir|escape:'htmlall':'UTF-8'}</b></br><b>{l s='For Step #16 Use Authorized Redirect Url: ' mod='supercheckout'}</b>{$manual_dir|escape:'htmlall':'UTF-8'}{l s='index.php?fc=module&module=supercheckout&controller=supercheckout&login_type=google' mod='supercheckout'}</pre>
                                                                                             <img src='{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/manual_steps/google/google8.jpg' />
                                                                                    </div>
                                                                                    <h3>{l s='Step 18, 19' mod='supercheckout'}</h3>
                                                                                    <div class="accdiv">
                                                                                             <img src='{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/manual_steps/google/google9.jpg' />
                                                                                    </div>
                                                                                    <h3>{l s='Step 20' mod='supercheckout'}</h3>
                                                                                    <div class="accdiv">
                                                                                             <img src='{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/manual_steps/google/google10.jpg' />
                                                                                    </div>
									</div>
                                                                                </div>
                                                         
                                                            </div>    
                                                        </div>
                                                    </div>

                                                    <!--------------- End - Login -------------------->    
						    
						    <!--------------- Start - Mailchimp -------------------->

                                                    <div id="tab_mailchimp" class="tab-pane tab-form">
                                                        <div class="block">
                                                            <h4 class='velsof-tab-heading'>{l s='MailChimp' mod='supercheckout'}</h4>
                                                            <div class="block">
                                                                <table class="form">
                                                                    <tr>
                                                                        <td class="name vertical_top_align"><span class="control-label">{l s='Enable MailChimp' mod='supercheckout'}: </span>                                                                
                                                                            <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Enable/Disable Mailchimp' mod='supercheckout'}"></i>
                                                                        </td>
                                                                        <td class="settings">
                                                                            <input type="hidden" value="0" name="velocity_supercheckout[mailchimp][enable]" />
                                                                            {if $velocity_supercheckout['mailchimp']['enable'] eq 1}
                                                                                {if $IE7 eq true}
                                                                                    <div>
                                                                                        <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[mailchimp][enable]" id="supercheckout_mailchimp_enable" checked="checked" />
                                                                                    </div>
                                                                                {else}
                                                                                    <div class="make-switch" data-on="primary" data-off="default">
                                                                                        <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[mailchimp][enable]" id="supercheckout_mailchimp_enable" checked="checked" />
                                                                                    </div>
                                                                                {/if}                                                                    
                                                                            {else}
                                                                                {if $IE7 eq true}
                                                                                    <div>
                                                                                        <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[mailchimp][enable]" id="supercheckout_mailchimp_enable" />
                                                                                    </div>
                                                                                {else}
                                                                                    <div class="make-switch" data-on="primary" data-off="default">
                                                                                        <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[mailchimp][enable]" id="supercheckout_mailchimp_enable"/>
                                                                                    </div>
                                                                                {/if}
                                                                            {/if}
                                                                            <div class="widget-body uniformjs" style="padding-top: 1%;">
                                                                           
                                                                                    
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[mailchimp][default]" value="1" {if isset($velocity_supercheckout['mailchimp']['default']) && $velocity_supercheckout['mailchimp']['default'] eq 1}checked="checked"{/if} />{l s='Subscribe customers as soon as they come out from Email field' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                </div>
                                                                        </td>
                                                                    </tr>
									  <tr>
                                                                        <td class="name vertical_top_align"><span class="control-label">{l s='MailChimp Api Key' mod='supercheckout'}: </span>                                                                
                                                                            <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Enter MailChimp Api Key' mod='supercheckout'}"></i>
                                                                        </td>
                                                                        <td class="settings">
										<span style="display: inline-block;width:75%;">
                                                                            <input type="text" class="text-width" name="velocity_supercheckout[mailchimp][api]" value="{if isset($velocity_supercheckout['mailchimp']['api'])}{$velocity_supercheckout['mailchimp']['api']|escape:'htmlall':'UTF-8'}{/if}" id="supercheckout_mailchimp_key"/>
									    <input type="hidden" class="text-width" name="velocity_supercheckout[mailchimp][list]" value="{if isset($velocity_supercheckout['mailchimp']['list'])}{$velocity_supercheckout['mailchimp']['list']|escape:'htmlall':'UTF-8'}{/if}" id="supercheckout_mailchimp_list"/>
									    </span>
									    <span ><input type="button" style="padding: 7.2px 12px;" value="Get List" onclick="getMailChimpList()" id="mailchimp_listbtn" class="btn">
										</span>
                                                                        </td>
                                                                    </tr> 
								    
								    <tr>
                                                                        <td class="name vertical_top_align"><span class="control-label">{l s='MailChimp List' mod='supercheckout'}: </span>                                                                
                                                                            <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Select MailChimp List ' mod='supercheckout'}"></i>
                                                                        </td>
                                                                        <td class="settings">
										<div id="mailchimp_loading" style="background-image: url('../modules/supercheckout/views/img/admin/loading.gif');background-repeat: no-repeat;height:20px;display: none;"></div>
										<div id="supercheckout_list"></div>
                                                                        </td>
                                                                    </tr>  
								    {*<tr>
                                                                    <td class="name vertical_top_align">
                                                                        <span>{l s='Default' mod='supercheckout'}: </span><i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Check it to make it default action' mod='supercheckout'}"></i>
                                                                    </td>
                                                                    <td class="left settings">
                                                                        <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                           
                                                                                    
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[mailchimp][default]" value="1" {if isset($velocity_supercheckout['mailchimp']['default']) && $velocity_supercheckout['mailchimp']['default'] eq 1}checked="checked"{/if} />{l s='Subscribe as soon as customer move out from Email field' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                </div>
                                                                            </td>
                                                                        
                                                                        </div>
                                                                    </td>
                                                                </tr>*}
									</table>
							    </div>
							</div>
						    </div>
									
									
							<!--------------- End - Mailchimp -------------------->    

                                                    <!--------------- Start - Addresses -------------------->

                                                    <div id="tab_Addr" class="tab-pane tab-form">
                                                        {assign var='conditional' value=''}
                                                        <div class="block">
                                                            <hr style="margin-bottom:5px;">
                                                                <div class="row">
                                                                    <div class="span">
                                                                        <p style="margin-bottom: 0; margin-right: 5px">
                                                                            <span style="font-weight: bold; font-size: 15px;">Note:</span>
                                                                            <span style="  color: rgb(217, 83, 79);margin-left: 5px;font-weight: bold;}">Please don't hide fields with * if they are mandatory in following Prestashop settings.</span><br/>1. Localization->Countries->Edit your country.<br/>2. Customers->Addresses->Set required fields for this section.</span>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            <hr style="margin-top:5px;">
							    <table class="form">
								    <tr>
                                                                    <td class="name vertical_top_align"><span class="control-label">{l s='Inline Validations' mod='supercheckout'}: </span>                                                                
                                                                        <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Enable/Disable Inline Validations' mod='supercheckout'}"></i>
                                                                    </td>
                                                                    <td class="settings">
                                                                        <input type="hidden" value="0" name="velocity_supercheckout[inline_validation][enable]" />
                                                                        {if isset($velocity_supercheckout['inline_validation']['enable']) && $velocity_supercheckout['inline_validation']['enable'] eq 1}
                                                                            {if $IE7 eq true}
                                                                                <div>
                                                                                    <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[inline_validation][enable]" checked="checked" />
                                                                                </div>
                                                                            {else}
                                                                                <div class="make-switch" data-on="primary" data-off="default">
                                                                                    <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[inline_validation][enable]" checked="checked" />
                                                                                </div>
                                                                            {/if}                                                                    
                                                                        {else}
                                                                            {if $IE7 eq true}
                                                                                <div>
                                                                                    <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[inline_validation][enable]" />
                                                                                </div>
                                                                            {else}
                                                                                <div class="make-switch" data-on="primary" data-off="default">
                                                                                    <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[inline_validation][enable]" />
                                                                                </div>
                                                                            {/if}
                                                                        {/if}
                                                                    </td>
                                                                </tr>
							    </table>
                                                            <h4 class='velsof-tab-heading'>{l s='Customer Personal' mod='supercheckout'}</h4>
                                                            <table class="form alternate">
                                                                <thead>
                                                                    <tr>
                                                                        <th></th>
                                                                        <th class="left drag-col-2 col-pad-left">{l s='Guest Customer' mod='supercheckout'}</th>
                                                                        <th class="left drag-col-2">{l s='Logged in Customer' mod='supercheckout'}</th>
                                                                        <th></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="sortable ui-sortable">
                                                                    {foreach from=$velocity_supercheckout['customer_personal'] key='k' item = 'p_addr'}
                                                                        <tr id="customer_personal_{$velocity_supercheckout['customer_personal'][$k]['id']|escape:'htmlall':'UTF-8'}_input" class="sort-item" sort-data="{if isset($velocity_supercheckout['customer_personal'][$k]['sort_order'])}{$velocity_supercheckout['customer_personal'][$k]['sort_order']|intval}{/if}">
                                                                            <input type="hidden" value="{$velocity_supercheckout['customer_personal'][$k]['id']|escape:'htmlall':'UTF-8'}" name="velocity_supercheckout[customer_personal][{$k|escape:'htmlall':'UTF-8'}][id]" />
                                                                            <input type="hidden" value="{$velocity_supercheckout['customer_personal'][$k]['title']|escape:'htmlall':'UTF-8'}" name="velocity_supercheckout[customer_personal][{$k|escape:'htmlall':'UTF-8'}][title]" />
                                                                            <td class="name">
                                                                               <span>{l s=$velocity_supercheckout['customer_personal'][$k]['title']|escape:'htmlall' mod='supercheckout'}:<input class="sort" class="input-sm form-control col-md-12" type="text" value="{if isset($velocity_supercheckout['customer_personal'][$k]['sort_order'])}{$velocity_supercheckout['customer_personal'][$k]['sort_order']|intval}{/if}" name="velocity_supercheckout[customer_personal][{$k|escape:'htmlall':'UTF-8'}][sort_order]" /></span>
                                                                            </td>
                                                                            <td class="left drag-col-2 col-pad-left">
                                                                                <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input id="cus_personal_guest_{$k|escape:'htmlall':'UTF-8'}_require" type="checkbox" class="checkbox input-checkbox-option require_address_field" name="velocity_supercheckout[customer_personal][{$k|escape:'htmlall':'UTF-8'}][guest][require]" value="{$velocity_supercheckout['customer_personal'][$k]['guest']['require']|intval}" {if $velocity_supercheckout['customer_personal'][$k]['guest']['require'] eq 1}checked="checked"{/if} />{l s='Require' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input id="cus_personal_guest_{$k|escape:'htmlall':'UTF-8'}_display" type="checkbox" class="checkbox input-checkbox-option display_address_field" name="velocity_supercheckout[customer_personal][{$k|escape:'htmlall':'UTF-8'}][guest][display]" value="{$velocity_supercheckout['customer_personal'][$k]['guest']['display']|intval}" {if $velocity_supercheckout['customer_personal'][$k]['guest']['display'] eq 1}checked="checked"{/if} />{l s='Show' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                </div>
                                                                            </td>
                                                                            <td class="left drag-col-2">
                                                                                <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input id="cus_personal_logged_{$k|escape:'htmlall':'UTF-8'}_require" type="checkbox" class="checkbox input-checkbox-option require_address_field" name="velocity_supercheckout[customer_personal][{$k|escape:'htmlall':'UTF-8'}][logged][require]" value="{$velocity_supercheckout['customer_personal'][$k]['logged']['require']|intval}" {if $velocity_supercheckout['customer_personal'][$k]['logged']['require'] eq 1}checked="checked"{/if} />{l s='Require' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input id="cus_personal_logged_{$k|escape:'htmlall':'UTF-8'}_display" type="checkbox" class="checkbox input-checkbox-option display_address_field" name="velocity_supercheckout[customer_personal][{$k|escape:'htmlall':'UTF-8'}][logged][display]" value="{$velocity_supercheckout['customer_personal'][$k]['logged']['display']|intval}" {if $velocity_supercheckout['customer_personal'][$k]['logged']['display'] eq 1}checked="checked"{/if} />{l s='Show' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                </div>
                                                                            </td>
                                                                            <td class="reorder">
                                                                                <i class="icon-reorder"></i>
                                                                                <span style='font-style: italic; margin-left: 5px;'>Drag to Sort</span>
                                                                            </td>
                                                                        </tr>
                                                                    {/foreach}
                                                                </tbody>
                                                            </table>    
                                                        </div>
                                                                
                                                        <div class="block"><br>
                                                            <h4 class='velsof-tab-heading'>{l s='Customer Subscription' mod='supercheckout'}</h4>
                                                            <table class="form alternate">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width:9.5%;"></th>
                                                                        <th class="left drag-col-2 col-pad-left">{l s='Guest Customer' mod='supercheckout'}</th>
                                                                        <th></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="sortable ui-sortable">
                                                                    {foreach from=$velocity_supercheckout['customer_subscription'] key='k' item = 'p_addr'}
                                                                        <tr id="customer_subsription_{$velocity_supercheckout['customer_subscription'][$k]['id']|escape:'htmlall':'UTF-8'}_input" class="sort-item" sort-data="{if isset($velocity_supercheckout['customer_subscription'][$k]['sort_order'])}{$velocity_supercheckout['customer_subscription'][$k]['sort_order']|intval}{/if}">
                                                                            <input type="hidden" value="{$velocity_supercheckout['customer_subscription'][$k]['id']|escape:'htmlall':'UTF-8'}" name="velocity_supercheckout[customer_subscription][{$k|escape:'htmlall':'UTF-8'}][id]" />
                                                                            <input type="hidden" value="{$velocity_supercheckout['customer_subscription'][$k]['title']|escape:'htmlall':'UTF-8'}" name="velocity_supercheckout[customer_subscription][{$k|escape:'htmlall':'UTF-8'}][title]" />
                                                                            <td class="name">
                                                                               <span>{l s=$velocity_supercheckout['customer_subscription'][$k]['title']|escape:'htmlall' mod='supercheckout'}:<input class="sort" class="input-sm form-control col-md-12" type="text" value="{if isset($velocity_supercheckout['customer_subscription'][$k]['sort_order'])}{$velocity_supercheckout['customer_subscription'][$k]['sort_order']|intval}{/if}" name="velocity_supercheckout[customer_subscription][{$k|escape:'htmlall':'UTF-8'}][sort_order]" /></span>
                                                                            </td>
                                                                            <td class="left drag-col-2 col-pad-left">
                                                                                <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input id="cus_subsription_guest_{$k|escape:'htmlall':'UTF-8'}_checked" type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[customer_subscription][{$k|escape:'htmlall':'UTF-8'}][guest][checked]" value="{$velocity_supercheckout['customer_subscription'][$k]['guest']['checked']|intval}" {if $velocity_supercheckout['customer_subscription'][$k]['guest']['checked'] eq 1}checked="checked"{/if} />{l s='Show as Checked' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input id="cus_subsription_guest_{$k|escape:'htmlall':'UTF-8'}_display" type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[customer_subscription][{$k|escape:'htmlall':'UTF-8'}][guest][display]" value="{$velocity_supercheckout['customer_subscription'][$k]['guest']['display']|intval}" {if $velocity_supercheckout['customer_subscription'][$k]['guest']['display'] eq 1}checked="checked"{/if} />{l s='Show' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                </div>
                                                                            </td>
                                                                            <td class="reorder">
                                                                                <i class="icon-reorder"></i>
                                                                                <span style='font-style: italic; margin-left: 5px;'>Drag to Sort</span>
                                                                            </td>
                                                                        </tr>
                                                                    {/foreach}
                                                                </tbody>
                                                            </table>    
                                                        </div>
                                                                
                                                        <div class="block"><br>
                                                            <table class="form alternate">
                                                                <thead>
                                                                    <tr>
                                                                        <th></th>
                                                                        <th class="left drag-col-2 col-pad-left">{l s='Guest Customer' mod='supercheckout'}</th>
                                                                        <th class="left drag-col-2">{l s='Logged in Customer' mod='supercheckout'}</th>
                                                                        <th></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="">
                                                                    <tr id="use_delivery_for_payment_add" class="">
                                                                        <td class="name">
                                                                           <span><b>{l s='Use Delivery Address as Invoice Address' mod='supercheckout'}</b>:</span>
                                                                        </td>
                                                                        <td class="left drag-col-2 col-pad-left">
                                                                            <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                                <label class="checkboxinline no-bold">
                                                                                    <input id="use_delivery_for_payment_add_guest" type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[use_delivery_for_payment_add][guest]" value="{$velocity_supercheckout['use_delivery_for_payment_add']['guest']|intval}" {if $velocity_supercheckout['use_delivery_for_payment_add']['guest'] eq 1}checked="checked"{/if} />{l s='Show as Checked' mod='supercheckout'}                                                                        
                                                                                </label>
                                                                                <label class="checkboxinline no-bold">
                                                                                    <input id="show_use_delivery_for_payment_add_guest" type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[show_use_delivery_for_payment_add][guest]" value="{$velocity_supercheckout['show_use_delivery_for_payment_add']['guest']|intval}" {if $velocity_supercheckout['show_use_delivery_for_payment_add']['guest'] eq 1}checked="checked"{/if} />{l s='Show' mod='supercheckout'}                                                                        
                                                                                </label>
                                                                            </div>
                                                                        </td>
                                                                        <td class="left drag-col-2">
                                                                            <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                                <label class="checkboxinline no-bold">
                                                                                    <input id="use_delivery_for_payment_add_logged" type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[use_delivery_for_payment_add][logged]" value="{$velocity_supercheckout['use_delivery_for_payment_add']['logged']|intval}" {if $velocity_supercheckout['use_delivery_for_payment_add']['logged'] eq 1}checked="checked"{/if} />{l s='Show as Checked' mod='supercheckout'}                                                                        
                                                                                </label>
                                                                                <label class="checkboxinline no-bold">
                                                                                    <input id="show_use_delivery_for_payment_add_logged" type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[show_use_delivery_for_payment_add][logged]" value="{$velocity_supercheckout['show_use_delivery_for_payment_add']['logged']|intval}" {if $velocity_supercheckout['show_use_delivery_for_payment_add']['logged'] eq 1}checked="checked"{/if} />{l s='Show' mod='supercheckout'}
                                                                                </label>
                                                                            </div>
                                                                        </td>
									
									
                                                                        <td class="reorder"></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>    
                                                        </div>
                                                                
                                                        <div class="block"><br><br>
                                                            <h4 class='velsof-tab-heading'>{l s='Delivery Address' mod='supercheckout'}</h4>
							    <table class="form alternate">
                                                                <thead>
                                                                    <tr>
                                                                        <th></th>
                                                                        <th class="left drag-col-2 col-pad-left">{l s='Guest Customer' mod='supercheckout'}</th>
                                                                        <th class="left drag-col-2">{l s='Logged in Customer' mod='supercheckout'}</th>
                                                                        <th></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="sortable ui-sortable">
                                                            	{foreach from=$velocity_supercheckout['shipping_address'] key='k' item = 'p_addr'}
                                                                        <tr id="customer_personal_{$velocity_supercheckout['shipping_address'][$k]['id']|escape:'htmlall':'UTF-8'}_input" class="sort-item" sort-data="{if isset($velocity_supercheckout['shipping_address'][$k]['sort_order'])}{$velocity_supercheckout['shipping_address'][$k]['sort_order']|intval}{/if}">
                                                                            <input type="hidden" value="{$velocity_supercheckout['shipping_address'][$k]['id']|escape:'htmlall':'UTF-8'}" name="velocity_supercheckout[shipping_address][{$k|escape:'htmlall':'UTF-8'}][id]" />
                                                                            <input type="hidden" value="{$velocity_supercheckout['shipping_address'][$k]['title']|escape:'htmlall':'UTF-8'}" name="velocity_supercheckout[shipping_address][{$k|escape:'htmlall':'UTF-8'}][title]" />
                                                                            <input type="hidden" value="{$velocity_supercheckout['shipping_address'][$k]['conditional']|escape:'htmlall':'UTF-8'}" name="velocity_supercheckout[shipping_address][{$k|escape:'htmlall':'UTF-8'}][conditional]" />
                                                                            <td class="name">
                                                                               <span>{l s=$velocity_supercheckout['shipping_address'][$k]['title']|escape:'htmlall' mod='supercheckout'}:<input class="sort" class="input-sm form-control col-md-12" type="text" value="{if isset($velocity_supercheckout['shipping_address'][$k]['sort_order'])|intval}{$velocity_supercheckout['shipping_address'][$k]['sort_order']|intval}{/if}" name="velocity_supercheckout[shipping_address][{$k|escape:'htmlall':'UTF-8'}][sort_order]" /></span>
                                                                            </td>
                                                                            {$conditional = $velocity_supercheckout['shipping_address'][$k]['conditional']}
                                                                            <td class="left drag-col-2 col-pad-left">
                                                                                <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                                    <label class="checkboxinline no-bold">
                                                                                        {if $k eq 'vat_number'}
												<div style="width: 70px;text-align: center;">
													<i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip" data-placement="top" data-original-title="{l s='To make this field mandatory please go to Customers->Addresses->Set required fields for this section' mod='supercheckout'}"></i>{l s='Require' mod='supercheckout'}
												</div>
											{else}
												<input id="shipping_address_guest_{$k|escape:'htmlall':'UTF-8'}_require" type="checkbox" class="checkbox input-checkbox-option require_address_field" name="velocity_supercheckout[shipping_address][{$k|escape:'htmlall':'UTF-8'}][guest][require]" value="{$velocity_supercheckout['shipping_address'][$k]['guest']['require']|intval}" {if $velocity_supercheckout['shipping_address'][$k]['guest']['require'] eq 1}checked="checked"{/if} />{l s='Require' mod='supercheckout'}                                                                        
											{/if}
                                                                                    </label>
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input id="shipping_address_guest_{$k|escape:'htmlall':'UTF-8'}_display" type="checkbox" class="checkbox input-checkbox-option display_address_field" name="velocity_supercheckout[shipping_address][{$k|escape:'htmlall':'UTF-8'}][guest][display]" value="{$velocity_supercheckout['shipping_address'][$k]['guest']['display']|intval}" {if $velocity_supercheckout['shipping_address'][$k]['guest']['display'] eq 1}checked="checked"{/if} />{l s='Show' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                    {if in_array($k, $highlighted_fields)}
                                                                                        <span style="color:red; margin-left: 5px;">*</span>
                                                                                    {/if}
                                                                                </div>
                                                                            </td>
                                                                            <td class="left drag-col-2">
                                                                                <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                                    <label class="checkboxinline no-bold">
                                                                                        {if $k eq 'vat_number'}
												<div style="width: 70px;text-align: center;">
													<i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip" data-placement="top" data-original-title="{l s='To make this field mandatory please go to Customers->Addresses->Set required fields for this section' mod='supercheckout'}"></i>{l s='Require' mod='supercheckout'}
												</div>
											{else}
												<input id="shipping_address_logged_{$k|escape:'htmlall':'UTF-8'}_require" type="checkbox" class="checkbox input-checkbox-option require_address_field" name="velocity_supercheckout[shipping_address][{$k|escape:'htmlall':'UTF-8'}][logged][require]" value="{$velocity_supercheckout['shipping_address'][$k]['logged']['require']|intval}" {if $velocity_supercheckout['shipping_address'][$k]['logged']['require'] eq 1}checked="checked"{/if} />{l s='Require' mod='supercheckout'}                                                                        
											{/if}
                                                                                    </label>
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input id="shipping_address_logged_{$k|escape:'htmlall':'UTF-8'}_display" type="checkbox" class="checkbox input-checkbox-option display_address_field" name="velocity_supercheckout[shipping_address][{$k|escape:'htmlall':'UTF-8'}][logged][display]" value="{$velocity_supercheckout['shipping_address'][$k]['logged']['display']|intval}" {if $velocity_supercheckout['shipping_address'][$k]['logged']['display'] eq 1}checked="checked"{/if} />{l s='Show' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                    {if in_array($k, $highlighted_fields)}
                                                                                        <span style="color:red; margin-left: 5px;">*</span>
                                                                                    {/if}
                                                                                </div>
                                                                            </td>
                                                                            <td class="reorder">
                                                                                <i class="icon-reorder"></i>
                                                                                <span style='font-style: italic; margin-left: 5px;'>Drag to Sort</span>
                                                                            </td>
                                                                        </tr>
                                                                    {/foreach}
                                                                </tbody>
                                                            </table>    
                                                        </div>
                                                        <div class="block"><br>
                                                            <h4 class='velsof-tab-heading'>{l s='Invoice Address' mod='supercheckout'}</h4>
                                                            <table class="form alternate">
                                                                <thead>
                                                                    <tr>
                                                                        <th></th>
                                                                        <th class="left drag-col-2 col-pad-left">{l s='Guest Customer' mod='supercheckout'}</th>
                                                                        <th class="left drag-col-2">{l s='Logged in Customer' mod='supercheckout'}</th>
                                                                        <th></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="sortable ui-sortable">
                                                                    {foreach from=$velocity_supercheckout['payment_address'] key='k' item = 'p_addr'}
                                                                        <tr id="customer_personal_{$velocity_supercheckout['payment_address'][$k]['id']|escape:'htmlall':'UTF-8'}_input" class="sort-item" sort-data="{if isset($velocity_supercheckout['payment_address'][$k]['sort_order'])}{$velocity_supercheckout['payment_address'][$k]['sort_order']|intval}{/if}">
                                                                            <input type="hidden" value="{$velocity_supercheckout['payment_address'][$k]['id']|escape:'htmlall':'UTF-8'}" name="velocity_supercheckout[payment_address][{$k|escape:'htmlall':'UTF-8'}][id]" />
                                                                            <input type="hidden" value="{$velocity_supercheckout['payment_address'][$k]['title']|escape:'htmlall':'UTF-8'}" name="velocity_supercheckout[payment_address][{$k|escape:'htmlall':'UTF-8'}][title]" />
                                                                            <input type="hidden" value="{$velocity_supercheckout['payment_address'][$k]['conditional']|escape:'htmlall':'UTF-8'}" name="velocity_supercheckout[payment_address][{$k|escape:'htmlall':'UTF-8'}][conditional]" />
                                                                            <td class="name">
                                                                               <span>{l s=$velocity_supercheckout['payment_address'][$k]['title'] mod='supercheckout'}:<input class="sort" class="input-sm form-control col-md-12" type="text" value="{if isset($velocity_supercheckout['payment_address'][$k]['sort_order'])}{$velocity_supercheckout['payment_address'][$k]['sort_order']|intval}{/if}" name="velocity_supercheckout[payment_address][{$k|escape:'htmlall':'UTF-8'}][sort_order]" /></span>
                                                                            </td>
                                                                            {$conditional = $velocity_supercheckout['payment_address'][$k]['conditional']}
                                                                            <td class="left drag-col-2 col-pad-left">
                                                                                <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                                    <label class="checkboxinline no-bold">
                                                                                        {if $k eq 'vat_number'}
												<div style="width: 70px;text-align: center;">
													<i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip" data-placement="top" data-original-title="{l s='To make this field mandatory please go to Customers->Addresses->Set required fields for this section' mod='supercheckout'}"></i>{l s='Require' mod='supercheckout'}
												</div>
											{else}
												<input id="payment_address_guest_{$k|escape:'htmlall':'UTF-8'}_require" type="checkbox" class="checkbox input-checkbox-option require_address_field" name="velocity_supercheckout[payment_address][{$k|escape:'htmlall':'UTF-8'}][guest][require]" value="{$velocity_supercheckout['payment_address'][$k]['guest']['require']|intval}" {if $velocity_supercheckout['payment_address'][$k]['guest']['require'] eq 1}checked="checked"{/if} />{l s='Require' mod='supercheckout'}
											{/if}
                                                                                    </label>
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input id="payment_address_guest_{$k|escape:'htmlall':'UTF-8'}_display" type="checkbox" class="checkbox input-checkbox-option display_address_field" name="velocity_supercheckout[payment_address][{$k|escape:'htmlall':'UTF-8'}][guest][display]" value="{$velocity_supercheckout['payment_address'][$k]['guest']['display']|intval}" {if $velocity_supercheckout['payment_address'][$k]['guest']['display'] eq 1}checked="checked"{/if} />{l s='Show' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                    {if in_array($k, $highlighted_fields)}
                                                                                        <span style="color:red; margin-left: 5px;">*</span>
                                                                                    {/if}
                                                                                </div>
                                                                            </td>
                                                                            <td class="left drag-col-2">
                                                                                <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                                    <label class="checkboxinline no-bold">
                                                                                        {if $k eq 'vat_number'}
												<div style="width: 70px;text-align: center;">
													<i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip" data-placement="top" data-original-title="{l s='To make this field mandatory please go to Customers->Addresses->Set required fields for this section' mod='supercheckout'}"></i>{l s='Require' mod='supercheckout'}
												</div>
											{else}
												<input id="payment_address_logged_{$k|escape:'htmlall':'UTF-8'}_require" type="checkbox" class="checkbox input-checkbox-option require_address_field" name="velocity_supercheckout[payment_address][{$k|escape:'htmlall':'UTF-8'}][logged][require]" value="{$velocity_supercheckout['payment_address'][$k]['logged']['require']|intval}" {if $velocity_supercheckout['payment_address'][$k]['logged']['require'] eq 1}checked="checked"{/if} />{l s='Require' mod='supercheckout'}
											{/if}
                                                                                    </label>
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input id="payment_address_logged_{$k|escape:'htmlall':'UTF-8'}_display" type="checkbox" class="checkbox input-checkbox-option display_address_field" name="velocity_supercheckout[payment_address][{$k|escape:'htmlall':'UTF-8'}][logged][display]" value="{$velocity_supercheckout['payment_address'][$k]['logged']['display']|intval}" {if $velocity_supercheckout['payment_address'][$k]['logged']['display'] eq 1}checked="checked"{/if} />{l s='Show' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                    {if in_array($k, $highlighted_fields)}
                                                                                        <span style="color:red; margin-left: 5px;">*</span>
                                                                                    {/if}
                                                                                </div>
                                                                            </td>
                                                                            <td class="reorder">
                                                                                <i class="icon-reorder"></i>
                                                                                <span style='font-style: italic; margin-left: 5px;'>Drag to Sort</span>
                                                                            </td>
                                                                        </tr>
                                                                    {/foreach}
                                                                </tbody>
                                                            </table>    
                                                        </div>                                                        
                                                    </div>

                                                    <!--------------- End - Addresses -------------------->

                                                    <!--------------- Start - Payment Method -------------------->

                                                    <div id="tab_payment_method" class="tab-pane tab-form">
                                                        <div class="block">
                                                            <h4 class='velsof-tab-heading'>{l s='Payment Method' mod='supercheckout'}</h4>
                                                            <table class="form">
                                                                <tr>
                                                                    <td class="name vertical_top_align"><span class="control-label">{l s='Display Methods' mod='supercheckout'}: </span>                                                                
                                                                        <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Display Methods Tooltip' mod='supercheckout'}"></i>
                                                                    </td>
                                                                    <td class="settings">
                                                                        <input type="hidden" value="0" name="velocity_supercheckout[payment_method][enable]" />
                                                                        {if $velocity_supercheckout['payment_method']['enable'] eq 1}
                                                                            {if $IE7 eq true}
                                                                                <div>
                                                                                    <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[payment_method][enable]" checked="checked" />
                                                                                </div>
                                                                            {else}
                                                                                <div class="make-switch" data-on="primary" data-off="default">
                                                                                    <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[payment_method][enable]" checked="checked" />
                                                                                </div>
                                                                            {/if}                                                                    
                                                                        {else}
                                                                            {if $IE7 eq true}
                                                                                <div>
                                                                                    <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[payment_method][enable]" />
                                                                                </div>
                                                                            {else}
                                                                                <div class="make-switch" data-on="primary" data-off="default">
                                                                                    <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[payment_method][enable]" />
                                                                                </div>
                                                                            {/if}
                                                                        {/if}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="name vertical_top_align">
                                                                        <span>{l s='Display Style' mod='supercheckout'}: </span><i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Method Display Style Tooltip' mod='supercheckout'}"></i>
                                                                    </td>
                                                                    <td class="left settings">
                                                                        <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                            <label class="radio coupon_type_radio">
                                                                                <input type="radio" class="radio coupon_type_radio" name="velocity_supercheckout[payment_method][display_style]" value="0"  {if $velocity_supercheckout['payment_method']['display_style'] eq 0} checked="checked" {/if} />{l s='Text Only' mod='supercheckout'}                                                                        
                                                                            </label>
                                                                            <label class="radio coupon_type_radio">
                                                                                <input type="radio" class="radio coupon_type_radio" name="velocity_supercheckout[payment_method][display_style]" value="1" {if $velocity_supercheckout['payment_method']['display_style'] eq 1} checked="checked" {/if} />{l s='Text With Image' mod='supercheckout'}                                                                        
                                                                            </label>
                                                                            <label class="radio coupon_type_radio">
                                                                                <input type="radio" class="radio coupon_type_radio" name="velocity_supercheckout[payment_method][display_style]" value="2" {if $velocity_supercheckout['payment_method']['display_style'] eq 2} checked="checked" {/if} />{l s='Image Only' mod='supercheckout'}                                                                        
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="name vertical_top_align"><span class="control-label">{l s='Selected Default Method' mod='supercheckout'}: </span>                                                                
                                                                        <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Selected Default Payment Method Tooltip' mod='supercheckout'}"></i>
                                                                    </td>
                                                                    <td class="settings">
                                                                        <div class='span4'>
                                                                            <select {if $ps_version eq 15}class="selectpicker vss_sc_ver15"{/if} name="velocity_supercheckout[payment_method][default]" >
                                                                                {foreach from=$payment_methods item="pay_methods"}
                                                                                    {if $pay_methods['id_module'] eq $velocity_supercheckout['payment_method']['default']}
                                                                                        <option value="{$pay_methods['id_module']|intval}" selected='selected'>{$pay_methods['display_name']|escape:'htmlall':'UTF-8'}</option>
                                                                                    {else}
                                                                                        <option value="{$pay_methods['id_module']|intval}">{$pay_methods['display_name']|escape:'htmlall':'UTF-8'}</option>
                                                                                    {/if}
                                                                                {/foreach}
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan='2'><br>
                                                                        <p>
                                                                            <b>{l s='Note' mod='supercheckout'}:</b>
                                                                            {l s='Payment Method Style Note' mod='supercheckout'}
                                                                        </p>
                                                                    </td>
                                                                </tr>
                                                            </table>
																		<h4 class='velsof-tab-heading'>{l s='Change logo and Title of Payment Methods' mod='supercheckout'}</h4>
																		<div id="payment-accordian" class="accordian_container">
																			{foreach from=$payment_methods item="pay_methods"}
																			<h3>{$pay_methods['display_name']|escape:'htmlall':'UTF-8'}</h3>
																			<div class="accdiv-logo">
																			<table class="form">
                                                                <tr>
                                                                    <td class="name vertical_top_align"><span>{l s='Title' mod='supercheckout'}: </span>                                                                
                                                                        <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Enter payment method title' mod='supercheckout'}"></i>
                                                                    </td>
																
																	<td class="settings">
																		<table class="lang-title">
																		{foreach from=$languages item='lang'}
                                                                    
                                                                    <tr>
                                                                        
                                                                        <td><div class="span6">
                                                                            <input type="text" class="text-width" name="velocity_supercheckout_payment[payment_method][{$pay_methods['id_module']|intval}][title][{$lang['id_lang']|intval}]" value="{if isset($velocity_supercheckout_payment['payment_method'][{$pay_methods['id_module']|intval}]['title'][{$lang['id_lang']|intval}])}{$velocity_supercheckout_payment['payment_method'][{$pay_methods['id_module']|intval}]['title'][{$lang['id_lang']|intval}]|escape:'htmlall':'UTF-8'}{else}{$pay_methods['display_name']|escape:'htmlall':'UTF-8'}{/if}"/>                                                                                                                                                
                                                                           
                                                                        </div>
																		</td>
																		<td><div class='span0'><img src="{$img_lang_dir|escape:'htmlall':'UTF-8'}{$lang['id_lang']|escape:'htmlall':'UTF-8'}.jpg" alt="{$lang['name']|escape:'htmlall':'UTF-8'}" title="{$lang['name']|escape:'htmlall':'UTF-8'}"/></div></td>
                                                                    </tr>
                                                                    
                                                                    {/foreach}
																		</table>
																	</td>
																</tr>
																<tr>
																	<td class="name vertical_top_align"><span>{l s='Logo Settings' mod='supercheckout'}: </span>                                                                
                                                                        <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Set payment method logo with dimensions ' mod='supercheckout'}"></i>
                                                                    </td>
																	<td class="settings"><div>
																		<div class="logo-img" style='padding-left: 10px;padding-top:10px;margin-bottom:15px;'>
																			{if isset($velocity_supercheckout_payment['payment_method'][{$pay_methods['id_module']|intval}]['logo']['title']) && $velocity_supercheckout_payment['payment_method'][{$pay_methods['id_module']|intval}]['logo']['title'] != ""}
																				{if !file_exists("{$root_dir|escape:'htmlall':'UTF-8'}/modules/supercheckout/views/img/admin/uploads/{$velocity_supercheckout_payment['payment_method'][{$pay_methods['id_module']|intval}]['logo']['title']|escape:'htmlall':'UTF-8'}")}
																					<input type="hidden" name="velocity_supercheckout_payment[payment_method][{$pay_methods['id_module']|intval}][logo][title]" id="payment_image_title_{$pay_methods['id_module']|intval}" value="" />
                    <div><img id="payment-img-{$pay_methods['id_module']|intval}" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/no-image.jpg"   style="border: 1px solid #ccc; padding:2px; height: 115px;"/></div>
					{else}
																				<input type="hidden" name="velocity_supercheckout_payment[payment_method][{$pay_methods['id_module']|intval}][logo][title]"  id="payment_image_title_{$pay_methods['id_module']|intval}" value="{$velocity_supercheckout_payment['payment_method'][{$pay_methods['id_module']|intval}]['logo']['title']|escape:'htmlall':'UTF-8'}" />
																				<div><img id="payment-img-{$pay_methods['id_module']|intval}" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/uploads/{$velocity_supercheckout_payment['payment_method'][{$pay_methods['id_module']|intval}]['logo']['title']|escape:'htmlall':'UTF-8'}"   onerror="this.src='{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/no-image.jpg'" style="border: 1px solid #ccc; padding:2px; height: 115px;"/></div>
																				{/if}
																			{else}
																			<input type="hidden" name="velocity_supercheckout_payment[payment_method][{$pay_methods['id_module']|intval}][logo][title]" id="payment_image_title_{$pay_methods['id_module']|intval}" value="" />
                    <div><img id="payment-img-{$pay_methods['id_module']|intval}" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/no-image.jpg"   style="border: 1px solid #ccc; padding:2px; height: 115px;"/></div>
					{/if}
                    
																			
																		</div>
																			
																			
                <div style='padding-left: 10px;'>
                    <span style="display: inline-block;"> <input type="file" name="velocity_supercheckout_payment[payment_method][{$pay_methods['id_module']|intval}][logo][name]" id="payment-img-{$pay_methods['id_module']|intval}_file" onchange="readPaymentURL({$pay_methods['id_module']|intval},'payment-img-{$pay_methods['id_module']|intval}')" value=""></span><span><input type='button' class="btn btn-primary" onclick="removeFile({$pay_methods['id_module']|intval});" value='{l s='Remove' mod='supercheckout'}' /></span> <span id="payment-img-{$pay_methods['id_module']|intval}_msg" style="margin-left:10px; display:none;">{l s='Only Images allowed' mod='supercheckout'}</span>
                </div>
																		</div>
				<div style="margin-top: 10px;display:flex;padding-left: 10px;">
					<span style="padding: 5px 10px 0px 0px;">{l s='Width' mod='supercheckout'}</span>
					<div class="input-group" style="width: 20%;"><input type="text"  class="form-control" name="velocity_supercheckout_payment[payment_method][{$pay_methods['id_module']|intval}][logo][resolution][width]" value="{if isset($velocity_supercheckout_payment['payment_method'][{$pay_methods['id_module']|intval}]['logo']['resolution']['width'])}{$velocity_supercheckout_payment['payment_method'][{$pay_methods['id_module']|intval}]['logo']['resolution']['width']|escape:'htmlall':'UTF-8'}{else}auto{/if}"/><span class="input-group-addon" style="width: 10px;">{l s='px' mod='supercheckout'}</span></div>
					<span style="padding: 5px 10px 0px 10px;">{l s='Height' mod='supercheckout'}</span>		<div class="input-group" style="width: 20%;"><input type="text" class="form-control"  name="velocity_supercheckout_payment[payment_method][{$pay_methods['id_module']|intval}][logo][resolution][height]" value="{if isset($velocity_supercheckout_payment['payment_method'][{$pay_methods['id_module']|intval}]['logo']['resolution']['height'])}{$velocity_supercheckout_payment['payment_method'][{$pay_methods['id_module']|intval}]['logo']['resolution']['height']|escape:'htmlall':'UTF-8'}{else}auto{/if}"/><span class="input-group-addon" style="width: 10px;">{l s='px' mod='supercheckout'}</span></div>
				</div><p class="help-block" style='padding-left: 10px;'> {l s='(To maintain aspect ratio of image, keep either both height and width value as auto or any of them value as auto)' mod='supercheckout'}</p>
																	</td>
																</tr>
																
																
																			</table>
																			</div>
																	{/foreach}
																		</div>
																		
                                                        </div>
                                                    </div>

                                                    <!--------------- End - Payment Method -------------------->

                                                    <!--------------- Start - Shipping Method -------------------->

                                                    <div id="tab_shipping_method" class="tab-pane tab-form">
                                                        <div class="block">
                                                            <h4 class='velsof-tab-heading'>{l s='Delivery Method' mod='supercheckout'}</h4>
                                                            <table class="form">
                                                                <tr>
                                                                    <td class="name vertical_top_align"><span class="control-label">{l s='Display Methods' mod='supercheckout'}: </span>                                                                
                                                                        <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Display Methods Tooltip' mod='supercheckout'}"></i>
                                                                    </td>
                                                                    <td class="settings">
                                                                        <input type="hidden" value="0" name="velocity_supercheckout[shipping_method][enable]" />
                                                                        {if $velocity_supercheckout['shipping_method']['enable'] eq 1}
                                                                            {if $IE7 eq true}
                                                                                <div>
                                                                                    <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[shipping_method][enable]" checked="checked" />
                                                                                </div>
                                                                            {else}
                                                                                <div class="make-switch" data-on="primary" data-off="default">
                                                                                    <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[shipping_method][enable]" checked="checked" />
                                                                                </div>
                                                                            {/if}                                                                    
                                                                        {else}
                                                                            {if $IE7 eq true}
                                                                                <div>
                                                                                    <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[shipping_method][enable]" />
                                                                                </div>
                                                                            {else}
                                                                                <div class="make-switch" data-on="primary" data-off="default">
                                                                                    <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[shipping_method][enable]"/>
                                                                                </div>
                                                                            {/if}
                                                                        {/if}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="name vertical_top_align">
                                                                        <span>{l s='Display Style' mod='supercheckout'}: </span><i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Method Display Style Tooltip' mod='supercheckout'}"></i>
                                                                    </td>
                                                                    <td class="left settings">
                                                                        <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                            <label class="radio coupon_type_radio">
                                                                                <input type="radio" class="radio coupon_type_radio" name="velocity_supercheckout[shipping_method][display_style]" value="0"  {if $velocity_supercheckout['shipping_method']['display_style'] eq 0} checked="checked" {/if} />{l s='Text Only' mod='supercheckout'}                                                                        
                                                                            </label>
                                                                            <label class="radio coupon_type_radio">
                                                                                <input type="radio" class="radio coupon_type_radio" name="velocity_supercheckout[shipping_method][display_style]" value="1" {if $velocity_supercheckout['shipping_method']['display_style'] eq 1} checked="checked" {/if} />{l s='Text With Image' mod='supercheckout'}                                                                        
                                                                            </label>
                                                                            <label class="radio coupon_type_radio">
                                                                                <input type="radio" class="radio coupon_type_radio" name="velocity_supercheckout[shipping_method][display_style]" value="2" {if $velocity_supercheckout['shipping_method']['display_style'] eq 2} checked="checked" {/if} />{l s='Image Only' mod='supercheckout'}                                                                        
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td class="name vertical_top_align"><span class="control-label">{l s='Selected Default Method' mod='supercheckout'}: </span>                                                                
                                                                        <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Selected Default Shipping Method Tooltip' mod='supercheckout'}"></i>
                                                                    </td>
                                                                    <td class="settings">
                                                                        <div class='span4'>
                                                                            <select {if $ps_version eq 15}class="selectpicker vss_sc_ver15"{/if} name="velocity_supercheckout[shipping_method][default]" >
                                                                                {foreach from=$carriers item="carrier"}
                                                                                    {if $carrier['id_carrier'] eq $velocity_supercheckout['shipping_method']['default']}
                                                                                        <option value="{$carrier['id_carrier']|intval}" selected='selected'>{$carrier['name']|escape:'htmlall':'UTF-8'}</option>
                                                                                    {else}
                                                                                        <option value="{$carrier['id_carrier']|intval}">{$carrier['name']|escape:'htmlall':'UTF-8'}</option>
                                                                                    {/if}
                                                                                {/foreach}
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan='2'><br>
                                                                        <p>
                                                                            <b>{l s='Note' mod='supercheckout'}:</b>
                                                                            {l s='Delivery Method Style Note' mod='supercheckout'}
                                                                        </p>
                                                                    </td>
                                                                </tr>

                                                            </table>  
																		<h4 class='velsof-tab-heading'>{l s='Change logo and Title of Delivery Methods' mod='supercheckout'}</h4>
																		<div id="delivery-accordian" class="accordian_container">
																			{foreach from=$carriers item="carrier"}
																			<h3>{$carrier['name']|escape:'htmlall':'UTF-8'}</h3>
																			<div class="accdiv-logo">
																			<table class="form">
                                                                <tr>
                                                                    <td class="name vertical_top_align"><span>{l s='Title' mod='supercheckout'}: </span>                                                                
                                                                        <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Enter Delivery method title' mod='supercheckout'}"></i>
                                                                    </td>
																
																	<td class="settings">
																		<table class="lang-title">
																		{foreach from=$languages item='lang'}
                                                                    
                                                                    <tr>
                                                                        
                                                                        <td><div class="span6">
                                                                            <input type="text" class="text-width" name="velocity_supercheckout_payment[delivery_method][{$carrier['id_carrier']|intval}][title][{$lang['id_lang']|intval}]" value="{if isset($velocity_supercheckout_payment['delivery_method'][{$carrier['id_carrier']|intval}]['title'][{$lang['id_lang']|intval}])}{$velocity_supercheckout_payment['delivery_method'][{$carrier['id_carrier']|intval}]['title'][{$lang['id_lang']|intval}]|escape:'htmlall':'UTF-8'}{else}{$carrier['name']|escape:'htmlall':'UTF-8'}{/if}"/>                                                                                                                                                
                                                                            
                                                                        </div>
																		</td>
																		<td><div class='span0'><img src="{$img_lang_dir|escape:'htmlall':'UTF-8'}{$lang['id_lang']|escape:'htmlall':'UTF-8'}.jpg" alt="{$lang['name']|escape:'htmlall':'UTF-8'}" title="{$lang['name']|escape:'htmlall':'UTF-8'}"/></div></td>
                                                                    </tr>
                                                                    
                                                                    {/foreach}
																		</table>
																	</td>
																</tr>
																<tr>
																	<td class="name vertical_top_align"><span>{l s='Logo Setting' mod='supercheckout'}: </span>                                                                
                                                                        <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Set delivery method logo with dimensions' mod='supercheckout'}"></i>
                                                                    </td>
																	<td class="settings"><div>
																		<div class="logo-img" style='padding-left: 10px;padding-top:10px;margin-bottom:15px;'>
																			{if isset($velocity_supercheckout_payment['delivery_method'][{$carrier['id_carrier']|intval}]['logo']['title']) && $velocity_supercheckout_payment['delivery_method'][{$carrier['id_carrier']|intval}]['logo']['title'] != ""}
																				{if !file_exists("{$root_dir|escape:'htmlall':'UTF-8'}/modules/supercheckout/views/img/admin/uploads/{$velocity_supercheckout_payment['delivery_method'][{$carrier['id_carrier']|intval}]['logo']['title']|escape:'htmlall':'UTF-8'}")}
																					<input type="hidden" name="velocity_supercheckout_payment[delivery_method][{$carrier['id_carrier']|intval}][logo][title]" id="delivery_image_title_{$carrier['id_carrier']|intval}" value="" />
                    <div><img id="delivery-img-{$carrier['id_carrier']|intval}" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/no-image.jpg"   style="border: 1px solid #ccc; padding:2px; height: 115px;"/></div>
					{else}
																				<input type="hidden" name="velocity_supercheckout_payment[delivery_method][{$carrier['id_carrier']|intval}][logo][title]"  id="delivery_image_title_{$carrier['id_carrier']|intval}" value="{$velocity_supercheckout_payment['delivery_method'][{$carrier['id_carrier']|intval}]['logo']['title']|escape:'htmlall':'UTF-8'}" />
																				<div>

																					<img id="delivery-img-{$carrier['id_carrier']|intval}" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/uploads/{$velocity_supercheckout_payment['delivery_method'][{$carrier['id_carrier']|intval}]['logo']['title']|escape:'htmlall':'UTF-8'}"   onerror="this.src='{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/no-image.jpg';" style="border: 1px solid #ccc; padding:2px; height: 115px;"/>

																				</div>
																					{/if}
																			{else}
																			<input type="hidden" name="velocity_supercheckout_payment[delivery_method][{$carrier['id_carrier']|intval}][logo][title]" id="delivery_image_title_{$carrier['id_carrier']|intval}" value="" />
                    <div><img id="delivery-img-{$carrier['id_carrier']|intval}" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin/no-image.jpg"   style="border: 1px solid #ccc; padding:2px; height: 115px;"/></div>
					{/if}
                    
																			
																		</div>
																			
																			
                <div style='padding-left: 10px;'>
                    <span style="display: inline-block;"> <input type="file" name="velocity_supercheckout_payment[delivery_method][{$carrier['id_carrier']|intval}][logo][name]" id="delivery-img-{$carrier['id_carrier']|intval}_file" onchange="readDeliveryURL({$carrier['id_carrier']|intval},'delivery-img-{$carrier['id_carrier']|intval}')" value=""></span><span><input type='button' class="btn btn-primary" onclick="removeDeliveryFile({$carrier['id_carrier']|intval});" value='{l s='Remove' mod='supercheckout'}' /></span> <span id="delivery-img-{$carrier['id_carrier']|intval}_msg" style="margin-left:10px; display:none;">{l s='Only Images allowed' mod='supercheckout'}</span>
                </div>
																		</div>
				<div style="margin-top: 10px;display:flex;padding-left: 10px;">
					<span style="padding: 5px 10px 0px 0px;">{l s='Width' mod='supercheckout'}</span>
					<div class="input-group" style="width: 20%;">
						<input type="text"  class="form-control" name="velocity_supercheckout_payment[delivery_method][{$carrier['id_carrier']|intval}][logo][resolution][width]" value="{if isset($velocity_supercheckout_payment['delivery_method'][{$carrier['id_carrier']|intval}]['logo']['resolution']['width'])}{$velocity_supercheckout_payment['delivery_method'][{$carrier['id_carrier']|intval}]['logo']['resolution']['width']|escape:'htmlall':'UTF-8'}{else}auto{/if}"/><span class="input-group-addon" style="width: 10px;">{l s='px' mod='supercheckout'}</span></div>
		<span style="padding: 5px 10px 0px 10px;">{l s='Height' mod='supercheckout'}</span>		<div class="input-group" style="width: 20%;">		<input type="text" class="form-control"  name="velocity_supercheckout_payment[delivery_method][{$carrier['id_carrier']|intval}][logo][resolution][height]" value="{if isset($velocity_supercheckout_payment['delivery_method'][{$carrier['id_carrier']|intval}]['logo']['resolution']['height'])}{$velocity_supercheckout_payment['delivery_method'][{$carrier['id_carrier']|intval}]['logo']['resolution']['height']|escape:'htmlall':'UTF-8'}{else}auto{/if}"/><span class="input-group-addon" style="width: 10px;">{l s='px' mod='supercheckout'}</span></div>
				</div><p class="help-block" style='padding-left: 10px;'> {l s='(To maintain aspect ratio of image, keep either both height and width value as auto or any of them value as auto)' mod='supercheckout'}</p>
																	</td>
																</tr>
																
																
																			</table>
																			</div>
																	{/foreach}
																		</div>
                                                        </div>
                                                    </div>

                                                    <!--------------- End - Shipping Method -------------------->
						    
													<!--------------- Start - Ship to pay -------------------->

                                                    <div id="tab_ship_to_pay" class="tab-pane tab-form">
							    <div class="block">
								    <h4 class='velsof-tab-heading'>{l s='Ship2pay' mod='supercheckout'}</h4>
										    <div style="text-shadow:none;background: #f8fcfe !important;color: #31b0d5 !important;" class="alert alert-info">
											Hide payment methods for customers based upon their shipping method selection.<br>
											Click on respective payment method to disable it for desired shipping method, don't forget to click above on save button to save all settings.
										    </div>
								    {foreach from=$carriers item="carrier"}

							<div style="margin-left: 5%;  margin-top:25px;width: 40%;  float: left;  border: 1px solid rgb(0, 0, 0);"> 

								    <div style="text-align: center;  font-size: 16px;  border-bottom: 1px solid;  padding: 5px;  background-color: aliceblue;">    
									<span><a style="float:left;" class="ship2pay-glyphicons glyphicons cargo"><i></i></a></span><span style="padding-left: 14px;">{$carrier['name']|escape:'htmlall':'UTF-8'}</span>
								    </div>

								    {foreach from=$payment_methods item="pay_methods"}								    
								
								    {if isset($velocity_supercheckout[{$carrier['id_carrier']|intval}][{$pay_methods['id_module']|intval}]) AND $velocity_supercheckout[{$carrier['id_carrier']|intval}][{$pay_methods['id_module']|intval}] eq 1}
								    <div style="border: 1px solid #B13131;background-color: rgb(224, 69, 69)" class="ship2pay-div" id="velocity_supercheckout[{$carrier['id_carrier']|intval}][{$pay_methods['id_module']|intval}]">
								    <input style="display:none;" type="checkbox" name="velocity_supercheckout[{$carrier['id_carrier']|intval}][{$pay_methods['id_module']|intval}]" checked="checked" value="1">
								    <span class="tickcross-sign">&#10060;</span>{$pay_methods['display_name']|escape:'htmlall':'UTF-8'}
								    </div>
								    {else}
								    <div style="border: 1px solid #257925;background-color: rgb(83, 199, 83);" class="ship2pay-div" id="velocity_supercheckout[{$carrier['id_carrier']|intval}][{$pay_methods['id_module']|intval}]">
								    <input style="display:none;" type="checkbox" name="velocity_supercheckout[{$carrier['id_carrier']|intval}][{$pay_methods['id_module']|intval}]" value="1"> 
								    <span class="tickcross-sign">&#10004;</span>{$pay_methods['display_name']|escape:'htmlall':'UTF-8'}
								    </div>
								    
								    {/if}
								    {/foreach}
							</div>

								    {/foreach}
							    </div>
                                                    </div>

                                                    <!--------------- End - Ship to pay -------------------->
						    

                                                    <!--------------- Start - Cart -------------------->

                                                    <div id="tab_cart" class="tab-pane tab-form">
                                                        <div class="block">
                                                            <h4 class='velsof-tab-heading'>{l s='Cart' mod='supercheckout'}</h4>
                                                            <table class="form">
                                                                <tr>
                                                                    <td class="name vertical_top_align"><span class="control-label">{l s='Display Cart' mod='supercheckout'}: </span>                                                                
                                                                        <i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Display Cart Tooltip' mod='supercheckout'}"></i>
                                                                    </td>
                                                                    <td class="settings">
                                                                        <input type="hidden" value="0" name="velocity_supercheckout[display_cart]" />
                                                                        {if $velocity_supercheckout['display_cart'] eq 1}
                                                                            {if $IE7 eq true}
                                                                                <div>
                                                                                    <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[display_cart]" checked="checked" />
                                                                                </div>
                                                                            {else}
                                                                                <div class="make-switch" data-on="primary" data-off="default">
                                                                                    <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[display_cart]" checked="checked" />
                                                                                </div>
                                                                            {/if}                                                                    
                                                                        {else}
                                                                            {if $IE7 eq true}
                                                                                <div>
                                                                                    <input class="checkbox" type="checkbox" value="1" name="velocity_supercheckout[display_cart]" />
                                                                                </div>
                                                                            {else}
                                                                                <div class="make-switch" data-on="primary" data-off="default">
                                                                                    <input class="make-switch" type="checkbox" value="1" name="velocity_supercheckout[display_cart]"/>
                                                                                </div>
                                                                            {/if}
                                                                        {/if}
                                                                    </td>
                                                                </tr>

                                                            </table>

                                                            <table class="form alternate">
                                                                <thead>
                                                                    <tr>
                                                                        <th></th>
                                                                        <th class="left drag-col-2 col-pad-left">{l s='Guest Customer' mod='supercheckout'}</th>
                                                                        <th class="left drag-col-2">{l s='Logged in Customer' mod='supercheckout'}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="">
                                                                    {foreach from=$velocity_supercheckout['cart_options'] key='k' item = 'p_addr'}
                                                                        <tr>
                                                                            <input type="hidden" value="{$velocity_supercheckout['cart_options'][$k]['id']|escape:'htmlall':'UTF-8'}" name="velocity_supercheckout[cart_options][{$k|escape:'htmlall':'UTF-8'}][id]" />
                                                                            <input type="hidden" value="{$velocity_supercheckout['cart_options'][$k]['title']|escape:'htmlall':'UTF-8'}" name="velocity_supercheckout[cart_options][{$k|escape:'htmlall':'UTF-8'}][title]" />
                                                                            <td class="name"><span>{l s=$velocity_supercheckout['cart_options'][$k]['title'] mod='supercheckout'}:<input class="sort" class="input-sm form-control col-md-12" type="text" value="{if isset($velocity_supercheckout['cart_options'][$k]['sort_order'])}{$velocity_supercheckout['cart_options'][$k]['sort_order']|intval}{/if}" name="velocity_supercheckout[cart_options][{$k|escape:'htmlall':'UTF-8'}][sort_order]" /></span></td>
                                                                            <td class="left drag-col-2 col-pad-left">
                                                                                <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[cart_options][{$k|escape:'htmlall':'UTF-8'}][guest][display]" value="{$velocity_supercheckout['cart_options'][$k]['guest']['display']|intval}" {if $velocity_supercheckout['cart_options'][$k]['guest']['display'] eq 1}checked="checked"{/if} />{l s='Show' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                </div>
                                                                            </td>
                                                                            <td class="left drag-col-2">
                                                                                <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[cart_options][{$k|escape:'htmlall':'UTF-8'}][logged][display]" value="{$velocity_supercheckout['cart_options'][$k]['logged']['display']|intval}" {if $velocity_supercheckout['cart_options'][$k]['logged']['display'] eq 1}checked="checked"{/if} />{l s='Show' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    {/foreach}
                                                                    <tr{if $ps_version eq 15} class="vss_scparent_ver15"{/if}>
                                                                        <td class="name"><span>{l s='Product Image Size' mod='supercheckout'}: </span><i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Product Image Size Tooltip' mod='supercheckout'}"></i></td>
                                                                        <td class="left drag-col-2 col-pad-left">
                                                                                <div class='span1'><input type='text' {if $ps_version eq 15}class="form-control vss-form-control-ver15"{/if} name='velocity_supercheckout[cart_image_size][width]' value='{$velocity_supercheckout['cart_image_size']['width']|intval}' /></div>
                                                                                <div class="span0{if $ps_version eq 15} vss-resolution-ver15{/if}">X</div>
                                                                                <div class='span1'><input type='text' {if $ps_version eq 15}class="form-control vss-form-control-ver15"{/if} name='velocity_supercheckout[cart_image_size][height]' value='{$velocity_supercheckout['cart_image_size']['height']|intval}' /></div>
                                                                        </td>
                                                                        <td class="left drag-col-2">

                                                                        </td>
                                                                    </tr>
								    <tr{if $ps_version eq 15} class="vss_scparent_ver15"{/if}>
                                                                        <td class="name"><span>{l s='Quantity update option' mod='supercheckout'}: </span><i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Product Quantity Update option at front end in cart summary.' mod='supercheckout'}"></i></td>
                                                                        <td class="left drag-col-2 col-pad-left">
                                                                                <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                            <label class="radio coupon_type_radio">
                                                                                <input type="radio" class="radio coupon_type_radio" name="velocity_supercheckout[qty_update_option]" value="0"  {if $velocity_supercheckout['qty_update_option'] eq 0} checked="checked" {/if} />{l s='+/- Button' mod='supercheckout'}                                                                        
                                                                            </label>
                                                                            <label class="radio coupon_type_radio">
                                                                                <input type="radio" class="radio coupon_type_radio" name="velocity_supercheckout[qty_update_option]" value="1" {if $velocity_supercheckout['qty_update_option'] eq 1} checked="checked" {/if} />{l s='Update Link' mod='supercheckout'}                                                                        
                                                                            </label>
                                                                        </div>
                                                                        </td>
                                                                        <td class="left drag-col-2">

                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <br><br>
                                                            <h4 class='velsof-tab-heading'>{l s='Order Total' mod='supercheckout'}</h4>
                                                            <table class="form alternate">
                                                                <thead>
                                                                    <tr>
                                                                        <th></th>
                                                                        <th class="left drag-col-2 col-pad-left">{l s='Guest Customer' mod='supercheckout'}</th>
                                                                        <th class="left drag-col-2">{l s='Logged in Customer' mod='supercheckout'}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="">
                                                                        <tr>
                                                                            <td class="name"><span>{l s='Product(s) Sub-Total' mod='supercheckout'}: </span><i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Display Sub-Total Tooltip' mod='supercheckout'}"></i></td>
                                                                            <td class="left drag-col-2 col-pad-left">
                                                                                <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[order_total_option][product_sub_total][guest][display]" value="{$velocity_supercheckout['order_total_option']['product_sub_total']['guest']['display']|intval}" {if $velocity_supercheckout['order_total_option']['product_sub_total']['guest']['display'] eq 1}checked="checked"{/if} />{l s='Show' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                </div>
                                                                            </td>
                                                                            <td class="left drag-col-2">
                                                                                <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[order_total_option][product_sub_total][logged][display]" value="{$velocity_supercheckout['order_total_option']['product_sub_total']['logged']['display']|intval}" {if $velocity_supercheckout['order_total_option']['product_sub_total']['logged']['display'] eq 1}checked="checked"{/if} />{l s='Show' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="name"><span>{l s='Voucher Input' mod='supercheckout'}: </span><i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Display Voucher Input Tooltip' mod='supercheckout'}"></i></td>
                                                                            <td class="left drag-col-2 col-pad-left">
                                                                                <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[order_total_option][voucher][guest][display]" value="{$velocity_supercheckout['order_total_option']['voucher']['guest']['display']|intval}" {if $velocity_supercheckout['order_total_option']['voucher']['guest']['display'] eq 1}checked="checked"{/if} />{l s='Show' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                </div>
                                                                            </td>
                                                                            <td class="left drag-col-2">
                                                                                <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[order_total_option][voucher][logged][display]" value="{$velocity_supercheckout['order_total_option']['voucher']['logged']['display']|intval}" {if $velocity_supercheckout['order_total_option']['voucher']['logged']['display'] eq 1}checked="checked"{/if} />{l s='Show' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="name"><span>{l s='Shipping Price' mod='supercheckout'}:</span></td>
                                                                            <td class="left drag-col-2 col-pad-left">
                                                                                <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[order_total_option][shipping_price][guest][display]" value="{$velocity_supercheckout['order_total_option']['shipping_price']['guest']['display']|intval}" {if $velocity_supercheckout['order_total_option']['shipping_price']['guest']['display'] eq 1}checked="checked"{/if} />{l s='Show' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                </div>
                                                                            </td>
                                                                            <td class="left drag-col-2">
                                                                                <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[order_total_option][shipping_price][logged][display]" value="{$velocity_supercheckout['order_total_option']['shipping_price']['logged']['display']|intval}" {if $velocity_supercheckout['order_total_option']['shipping_price']['logged']['display'] eq 1}checked="checked"{/if} />{l s='Show' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="name"><span>{l s='Order Total' mod='supercheckout'}:</span></td>
                                                                            <td class="left drag-col-2 col-pad-left">
                                                                                <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[order_total_option][total][guest][display]" value="{$velocity_supercheckout['order_total_option']['total']['guest']['display']|intval}" {if $velocity_supercheckout['order_total_option']['total']['guest']['display'] eq 1}checked="checked"{/if} />{l s='Show' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                </div>
                                                                            </td>
                                                                            <td class="left drag-col-2">
                                                                                <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[order_total_option][total][logged][display]" value="{$velocity_supercheckout['order_total_option']['total']['logged']['display']|intval}" {if $velocity_supercheckout['order_total_option']['total']['logged']['display'] eq 1}checked="checked"{/if} />{l s='Show' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                </tbody>
                                                            </table>
                                                            <br><br>
                                                            <h4 class='velsof-tab-heading'>{l s='Confirm' mod='supercheckout'}</h4>
                                                            <table class="form alternate">
                                                                <thead>
                                                                    <tr>
                                                                        <th></th>
                                                                        <th class="left drag-col-2 col-pad-left">{l s='Guest Customer' mod='supercheckout'}</th>
                                                                        <th class="left drag-col-2">{l s='Logged in Customer' mod='supercheckout'}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="">
                                                                        <tr>
                                                                            <td class="name"><span>{l s='Term & Condition' mod='supercheckout'}: </span><i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Display Term & Condition Tooltip' mod='supercheckout'}"></i></td>
                                                                            <td class="left drag-col-2 col-pad-left">
                                                                                <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[confirm][term_condition][guest][display]" value="{$velocity_supercheckout['confirm']['term_condition']['guest']['display']|intval}" {if $velocity_supercheckout['confirm']['term_condition']['guest']['display'] eq 1}checked="checked"{/if} />{l s='Show' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[confirm][term_condition][guest][require]" value="{$velocity_supercheckout['confirm']['term_condition']['guest']['require']|intval}" {if $velocity_supercheckout['confirm']['term_condition']['guest']['require'] eq 1}checked="checked"{/if} />{l s='Require' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[confirm][term_condition][guest][checked]" value="{$velocity_supercheckout['confirm']['term_condition']['guest']['checked']|intval}" {if $velocity_supercheckout['confirm']['term_condition']['guest']['checked'] eq 1}checked="checked"{/if} />{l s='Show as Checked' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                    <i class="store_disabled" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='This option will not display, if disable from default prestashop settings' mod='supercheckout'}"><span class="store_disabled_msg">{l s='Warning' mod='supercheckout'} !</span></i>
                                                                                </div>
                                                                            </td>
                                                                            <td class="left drag-col-2">
                                                                                <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[confirm][term_condition][logged][display]" value="{$velocity_supercheckout['confirm']['term_condition']['logged']['display']|intval}" {if $velocity_supercheckout['confirm']['term_condition']['logged']['display'] eq 1}checked="checked"{/if} />{l s='Show' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[confirm][term_condition][logged][require]" value="{$velocity_supercheckout['confirm']['term_condition']['logged']['require']|intval}" {if $velocity_supercheckout['confirm']['term_condition']['logged']['require'] eq 1}checked="checked"{/if} />{l s='Require' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[confirm][term_condition][logged][checked]" value="{$velocity_supercheckout['confirm']['term_condition']['logged']['checked']|intval}" {if $velocity_supercheckout['confirm']['term_condition']['logged']['checked'] eq 1}checked="checked"{/if} />{l s='Show as Checked' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                    <i class="store_disabled" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='This option will not display, if disable from default prestashop settings' mod='supercheckout'}"><span class="store_disabled_msg">{l s='Warning' mod='supercheckout'} !</span></i>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="name"><span>{l s='Comment Box for Order' mod='supercheckout'}:</span></td>
                                                                            <td class="left drag-col-2 col-pad-left">
                                                                                <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[confirm][order_comment_box][guest][display]" value="{$velocity_supercheckout['confirm']['order_comment_box']['guest']['display']|intval}" {if $velocity_supercheckout['confirm']['order_comment_box']['guest']['display'] eq 1}checked="checked"{/if} />{l s='Show' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                </div>
                                                                            </td>
                                                                            <td class="left drag-col-2">
                                                                                <div class="widget-body uniformjs" style="padding: 0 !important;">
                                                                                    <label class="checkboxinline no-bold">
                                                                                        <input type="checkbox" class="checkbox input-checkbox-option" name="velocity_supercheckout[confirm][order_comment_box][logged][display]" value="{$velocity_supercheckout['confirm']['order_comment_box']['logged']['display']|intval}" {if $velocity_supercheckout['confirm']['order_comment_box']['logged']['display'] eq 1}checked="checked"{/if} />{l s='Show' mod='supercheckout'}                                                                        
                                                                                    </label>
                                                                                </div>
                                                                            </td>
                                                                        </tr>                                                                
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>

                                                    <!--------------- End - Cart -------------------->

                                                    <!--------------- Start - Design -------------------->

                                                    <div id="tab_design" class="tab-pane tab-form">
                                                        <div class="block">
                                                            <h4 class='velsof-tab-heading'>{l s='Design' mod='supercheckout'}</h4>                                                            
                                                                <div class="span3">
                                                                    <select {if $ps_version eq 15}class="selectpicker vss_sc_ver15"{/if} name="velocity_supercheckout[layout]" onchange='$.cookie("designTab",1); location.href = "{$action|escape:'htmlall':'UTF-8'}&velsof_layout="+$(this).val()'>
                                                                        {if $layout eq 1}
                                                                            <option value="1" selected="selected">1-{l s='Columns' mod='supercheckout'}</option>
                                                                        {else}
                                                                            <option value="1">1-{l s='Columns' mod='supercheckout'}</option>
                                                                        {/if}
                                                                        {if $layout eq 2}
                                                                            <option value="2" selected="selected">2-{l s='Columns' mod='supercheckout'}</option>
                                                                        {else}
                                                                            <option value="2">2-{l s='Columns' mod='supercheckout'}</option>
                                                                        {/if}
                                                                        {if $layout eq 3}
                                                                            <option value="3" selected="selected">3-{l s='Columns' mod='supercheckout'}</option>
                                                                        {else}
                                                                            <option value="3">3-{l s='Columns' mod='supercheckout'}</option>
                                                                        {/if}
                                                                    </select>
                                                                </div>
                                                            <table class="form">
                                                                <tbody>
                                                                    <tr>
                                                                        {foreach from=$velocity_supercheckout['design']['html'] key='k' item='v'}
                                                                            <input id="1_col_h_{$k|escape:'htmlall':'UTF-8'}"  type="hidden"  class="sort col-data" name="velocity_supercheckout[design][html][{$k|escape:'htmlall':'UTF-8'}][1_column][column]" value="{$velocity_supercheckout['design']['html'][$k]['1_column']['column']|escape:'htmlall':'UTF-8'}" />
                                                                            <input id="1_row_h_{$k|escape:'htmlall':'UTF-8'}"  type="hidden"  class="sort row-data" name="velocity_supercheckout[design][html][{$k|escape:'htmlall':'UTF-8'}][1_column][row]" value="{$velocity_supercheckout['design']['html'][$k]['1_column']['row']|escape:'htmlall':'UTF-8'}" />
                                                                            <input id="1_col_ins_h_{$k|escape:'htmlall':'UTF-8'}"  type="hidden"  class="sort col-data-inside" name="velocity_supercheckout[design][html][{$k|escape:'htmlall':'UTF-8'}][1_column][column-inside]" value="{$velocity_supercheckout['design']['html'][$k]['1_column']['column-inside']|escape:'htmlall':'UTF-8'}" />

                                                                            <input id="2_col_h_{$k|escape:'htmlall':'UTF-8'}"  type="hidden"  class="sort col-data" name="velocity_supercheckout[design][html][{$k|escape:'htmlall':'UTF-8'}][2_column][column]" value="{$velocity_supercheckout['design']['html'][$k]['2_column']['column']|escape:'htmlall':'UTF-8'}" />
                                                                            <input id="2_row_h_{$k|escape:'htmlall':'UTF-8'}"  type="hidden"  class="sort row-data" name="velocity_supercheckout[design][html][{$k|escape:'htmlall':'UTF-8'}][2_column][row]" value="{$velocity_supercheckout['design']['html'][$k]['2_column']['row']|escape:'htmlall':'UTF-8'}" />
                                                                            <input id="2_col_ins_h_{$k|escape:'htmlall':'UTF-8'}"  type="hidden"  class="sort col-data-inside" name="velocity_supercheckout[design][html][{$k|escape:'htmlall':'UTF-8'}][2_column][column-inside]" value="{$velocity_supercheckout['design']['html'][$k]['2_column']['column-inside']|escape:'htmlall':'UTF-8'}" />

                                                                            <input id="3_col_h_{$k|escape:'htmlall':'UTF-8'}"  type="hidden"  class="sort col-data" name="velocity_supercheckout[design][html][{$k|escape:'htmlall':'UTF-8'}][3_column][column]" value="{$velocity_supercheckout['design']['html'][$k]['3_column']['column']|escape:'htmlall':'UTF-8'}" />
                                                                            <input id="3_row_h_{$k|escape:'htmlall':'UTF-8'}"  type="hidden"  class="sort row-data" name="velocity_supercheckout[design][html][{$k|escape:'htmlall':'UTF-8'}][3_column][row]" value="{$velocity_supercheckout['design']['html'][$k]['3_column']['row']|escape:'htmlall':'UTF-8'}" />
                                                                            <input id="3_col_ins_h_{$k|escape:'htmlall':'UTF-8'}"  type="hidden"  class="sort col-data-inside" name="velocity_supercheckout[design][html][{$k|escape:'htmlall':'UTF-8'}][3_column][column-inside]" value="{$velocity_supercheckout['design']['html'][$k]['3_column']['column-inside']|escape:'htmlall':'UTF-8'}" />
                                                                        {/foreach}

                                                                        <!-- Start - Reserve previous values for all layouts -->
                                                                        {foreach from=$velocity_supercheckout['design'] key='tab_name' item='v'}
                                                                            {if $tab_name neq 'html'}
                                                                            {foreach from=$velocity_supercheckout['design'][$tab_name] key='col_name' item='v1'}
                                                                                <input   type="hidden"  class="sort col-data" name="velocity_supercheckout[design][{$tab_name|escape:'htmlall':'UTF-8'}][{$col_name|escape:'htmlall':'UTF-8'}][column]" value="{$velocity_supercheckout['design'][$tab_name][$col_name]['column']|escape:'htmlall':'UTF-8'}" />
                                                                                <input   type="hidden"  class="sort row-data" name="velocity_supercheckout[design][{$tab_name|escape:'htmlall':'UTF-8'}][{$col_name|escape:'htmlall':'UTF-8'}][row]" value="{$velocity_supercheckout['design'][$tab_name][$col_name]['row']|escape:'htmlall':'UTF-8'}" />
                                                                                <input   type="hidden"  class="sort col-data-inside" name="velocity_supercheckout[design][{$tab_name|escape:'htmlall':'UTF-8'}][{$col_name|escape:'htmlall':'UTF-8'}][column-inside]" value="{$velocity_supercheckout['design'][$tab_name][$col_name]['column-inside']|escape:'htmlall':'UTF-8'}" />
                                                                            {/foreach}
                                                                            {/if}
                                                                        {/foreach}                                                                
                                                                        <!-- End - Reserve previous values for all layouts -->                                                                                                                                

                                                                        <!-- Start - Header and footer Html -->                                                                    
                                                                            <input type="hidden" id="modals_bootbox_prompt_html_header_value" name="velocity_supercheckout[html_value][header]" value="{if $velocity_supercheckout['html_value']['header'] neq ''}{html_entity_decode($velocity_supercheckout['html_value']['header'])|escape:'html':'UTF-8'}{/if}" />
                                                                            <input type="hidden" id="modals_bootbox_prompt_html_footer_value" name="velocity_supercheckout[html_value][footer]" value="{if $velocity_supercheckout['html_value']['footer'] neq ''}{html_entity_decode($velocity_supercheckout['html_value']['footer'])|escape:'html':'UTF-8'}{/if}" />
                                                                        <!-- End - Header and footer html -->

                                                                        {if $layout eq 1}
                                                                            <!-- Start - Reserve previous width of all layouts -->
                                                                            {foreach from=$velocity_supercheckout['column_width'] key='tab_name' item='v'}
                                                                                <input type="hidden"  class="column-data-1 col" name="velocity_supercheckout[column_width][{$tab_name|escape:'htmlall':'UTF-8'}][1]" value="{$velocity_supercheckout['column_width'][$tab_name][1]|escape:'htmlall':'UTF-8'}" />
                                                                                <input type="hidden"  class="column-data-2 col" name="velocity_supercheckout[column_width][{$tab_name|escape:'htmlall':'UTF-8'}][2]" value="{$velocity_supercheckout['column_width'][$tab_name][2]|escape:'htmlall':'UTF-8'}" />
                                                                                <input type="hidden"  class="column-data-2 col" name="velocity_supercheckout[column_width][{$tab_name|escape:'htmlall':'UTF-8'}][3]" value="{$velocity_supercheckout['column_width'][$tab_name][3]|escape:'htmlall':'UTF-8'}" />
                                                                                <input type="hidden"  class="column-data-3 col" name="velocity_supercheckout[column_width][{$tab_name|escape:'htmlall':'UTF-8'}][inside][1]" value="{$velocity_supercheckout['column_width'][$tab_name]['inside'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                <input type="hidden"  class="column-data-3 col" name="velocity_supercheckout[column_width][{$tab_name|escape:'htmlall':'UTF-8'}][inside][2]" value="{$velocity_supercheckout['column_width'][$tab_name]['inside'][2]|escape:'htmlall':'UTF-8'}" />
                                                                            {/foreach}
                                                                            <!-- End - Reserve previous width of all layouts -->
                                                                            <td  colspan="2" style="position:static; width: 960px;">
                                                                                <div class="portlet">
                                                                                    <div class="portlet-header">{l s='HTML Header Content' mod='supercheckout'}</div>
                                                                                    <div class="portlet-content">
                                                                                        <div class="text" style="overflow:visible !important;" >
                                                                                            <a data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Edit this HTML content' mod='supercheckout'}" id="modals_bootbox_prompt_html_header" data-toggle="modal" class="glyphicons edit bootbox-design-edit-html" ><i></i></a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <ul id="column-1" class="column column-1" col-data="1" col-inside-data="1" style="width:100%  !important;">
                                                                                    <li class="portlet" row-data="{$velocity_supercheckout['design']['payment_address']['1_column']['row']|intval}" >
                                                                                        <div class="portlet-header"><i class="icon-small-payment-address"></i>{l s='Payment Address' mod='supercheckout'}</div>
                                                                                        <div class="portlet-content">
                                                                                            <div class="text">{l s='Payment Address Content' mod='supercheckout'}</div>
                                                                                            <div class="text"><i class="icon-drag"></i><i class="icon-drag"></i></div>
                                                                                            <input   type="text"  class="sort row-data" name="velocity_supercheckout[design][payment_address][1_column][row]" value="{$velocity_supercheckout['design']['payment_address']['1_column']['row']|intval}" />
                                                                                        </div>
                                                                                    </li>
                                                                                    <li class="portlet" row-data="{$velocity_supercheckout['design']['login']['1_column']['row']|intval}">
                                                                                        <div class="portlet-header"><i class="icon-small-payment-address"></i>{l s='Login' mod='supercheckout'}</div>
                                                                                        <div class="portlet-content">
                                                                                            <div class="text">{l s='Login Content' mod='supercheckout'}</div>
                                                                                            <div class="text"><i class="icon-drag"></i><i class="icon-drag"></i></div>
                                                                                            <input   type="text"  class="sort row-data" name="velocity_supercheckout[design][login][1_column][row]" value="{$velocity_supercheckout['design']['login']['1_column']['row']|intval}" />
                                                                                        </div>
                                                                                    </li>
                                                                                    <li class="portlet" row-data="{$velocity_supercheckout['design']['shipping_address']['1_column']['row']|intval}">
                                                                                        <div class="portlet-header"><i class="icon-small-shipping-address"></i>{l s='Shipping Address' mod='supercheckout'}</div>
                                                                                        <div class="portlet-content">
                                                                                            <div class="text">{l s='Shipping Address Content' mod='supercheckout'}</div>
                                                                                            <div class="text"><i class="icon-drag"></i><i class="icon-drag"></i></div>
                                                                                            <input   type="text"  class="sort row-data" name="velocity_supercheckout[design][shipping_address][1_column][row]" value="{$velocity_supercheckout['design']['shipping_address']['1_column']['row']|intval}" />
                                                                                        </div>
                                                                                    </li>
                                                                                    <li class="portlet" row-data="{$velocity_supercheckout['design']['shipping_method']['1_column']['row']|intval}">
                                                                                        <div class="portlet-header"><i class="icon-small-shipping-method"></i>{l s='Shipping Method' mod='supercheckout'}</div>
                                                                                        <div class="portlet-content">
                                                                                            <div class="text">{l s='Shipping Method Content' mod='supercheckout'}</div>
                                                                                            <div class="text"><i class="icon-drag"></i><i class="icon-drag"></i></div>
                                                                                            <input   type="text"  class="sort row-data" name="velocity_supercheckout[design][shipping_method][1_column][row]" value="{$velocity_supercheckout['design']['shipping_method']['1_column']['row']|intval}" />
                                                                                        </div>
                                                                                    </li>
                                                                                    <li class="portlet" row-data="{$velocity_supercheckout['design']['payment_method']['1_column']['row']|intval}">
                                                                                        <div class="portlet-header"><i class="icon-small-payment-method"></i>{l s='Payment Method' mod='supercheckout'}</div>
                                                                                        <div class="portlet-content">
                                                                                            <div class="text">{l s='Payment Method Content' mod='supercheckout'}</div>
                                                                                            <div class="text"><i class="icon-drag"></i><i class="icon-drag"></i></div>
                                                                                            <input   type="text"  class="sort row-data" name="velocity_supercheckout[design][payment_method][1_column][row]" value="{$velocity_supercheckout['design']['payment_method']['1_column']['row']|intval}" />
                                                                                        </div>
                                                                                    </li>
                                                                                    <li class="portlet" row-data="{$velocity_supercheckout['design']['cart']['1_column']['row']|intval}">
                                                                                        <div class="portlet-header"><i class="icon-small-confirm"></i>{l s='Cart' mod='supercheckout'}</div>
                                                                                        <div class="portlet-content">
                                                                                            <div class="text">{l s='Cart Content' mod='supercheckout'}</div>
                                                                                            <div class="text"><i class="icon-drag"></i><i class="icon-drag"></i></div>
                                                                                            <input   type="text"  class="sort row-data" name="velocity_supercheckout[design][cart][1_column][row]" value="{$velocity_supercheckout['design']['cart']['1_column']['row']|intval}" />
                                                                                        </div>
                                                                                    </li>
                                                                                    <li class="portlet"  row-data="{$velocity_supercheckout['design']['confirm']['1_column']['row']|intval}">
                                                                                        <div class="portlet-header"><i class="icon-small-confirm"></i>{l s='Confirm' mod='supercheckout'}</div>
                                                                                        <div class="portlet-content">
                                                                                            <div class="text">{l s='Confirm Content' mod='supercheckout'}</div>
                                                                                            <div class="text"><i class="icon-drag"></i><i class="icon-drag"></i></div>
                                                                                            <input   type="text"  class="sort row-data" name="velocity_supercheckout[design][confirm][1_column][row]" value="{$velocity_supercheckout['design']['confirm']['1_column']['row']|intval}" />
                                                                                        </div>
                                                                                    </li>
                                                                                    <li class="portlet" row-data="{$velocity_supercheckout['design']['html']['0_0']['1_column']['row']|intval}">
                                                                                        <div class="portlet-header"><i class="icon-small-confirm"></i>{l s='Html Content' mod='supercheckout'}</div>
                                                                                        <div class="portlet-content">
                                                                                            <div class="text">{l s='Extra html content' mod='supercheckout'}</div>
                                                                                            <div class="text"><i class="icon-drag"></i><i class="icon-drag"></i></div>
                                                                                            <input   type="text"  class="sort row-data" name="velocity_supercheckout[design][html][0_0][1_column][row]" value="{$velocity_supercheckout['design']['html']['0_0']['1_column']['row']|intval}" />
                                                                                        </div>
                                                                                    </li>
                                                                                    {foreach from=$velocity_supercheckout['design']['html'] key='k' item='v'}
                                                                                        <li class="portlet" id="portlet_{$k|escape:'htmlall':'UTF-8'}" row-data="{$velocity_supercheckout['design']['html'][$k]['1_column']['row']|intval}">
                                                                                            <div class="portlet-header">{l s='Extra html content' mod='supercheckout'}</div>
                                                                                            <div class="portlet-content" id="portlet_content_{$k|escape:'htmlall':'UTF-8'}">
                                                                                                <div class="text" style="overflow:visible !important;" >
                                                                                                <a data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Add new HTML content' mod='supercheckout'}" id="duplicate_button_{$k|escape:'htmlall':'UTF-8'}" data="0" class="glyphicons more_windows"  onClick="duplicate_html(this);" ><i></i></a>

                                                                                                <a data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Edit this HTML content' mod='supercheckout'}" id="modals-bootbox-prompt-{$k|escape:'htmlall':'UTF-8'}" data-toggle="modal" class="glyphicons edit bootbox-design-extra-html"  onClick="dialogExtraHtml(this);"><i></i></a>
                                                                                                {if $k neq "0_0"}
                                                                                                <a data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Remove this HTML content' mod='supercheckout'}" id="delete_button_{$k|escape:'htmlall':'UTF-8'}" data="{$k|escape:'htmlall':'UTF-8'}" data-toggle="modal" class="glyphicons remove"  onClick="remove_html(this);" ><i></i></a>
                                                                                                {/if}
                                                                                                </div>

                                                                                                <input id="row_text_{$k|escape:'htmlall':'UTF-8'}"  type="text"  class="sort row-data" name="velocity_supercheckout[design][html][{$k|escape:'htmlall':'UTF-8'}][1_column][row]" value="{$velocity_supercheckout['design']['html'][$k]['1_column']['row']|intval}" />
                                                                                            </div>
                                                                                        </li>
                                                                                    {/foreach}
                                                                                </ul>
                                                                                <div class="portlet">
                                                                                    <div class="portlet-header">{l s='HTML Footer Content' mod='supercheckout'}</div>
                                                                                    <div class="portlet-content">
                                                                                        <div class="text" style="overflow:visible !important;" >

                                                                                        <a data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Edit this HTML content' mod='supercheckout'}" id="modals_bootbox_prompt_html_footer" data-toggle="modal" class="glyphicons edit bootbox-design-edit-html" ><i></i></a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        {elseif $layout eq 2}
                                                                            <td  colspan="2" style="position:static">
                                                                                <div class="portlet">
                                                                                    <div class="portlet-header">{l s='HTML Header Content' mod='supercheckout'}</div>
                                                                                    <div class="portlet-content">
                                                                                        <div class="text" style="overflow:visible !important;" >

                                                                                        <a data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Edit this HTML content' mod='supercheckout'}" id="modals_bootbox_prompt_html_header" data-toggle="modal" class="glyphicons edit bootbox-design-edit-html" ><i></i></a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="columns">
                                                                                    <input type="hidden"  class="column-data-1 col" name="velocity_supercheckout[column_width][1_column][1]" value="{$velocity_supercheckout['column_width']['1_column'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                    <input type="hidden"  class="column-data-2 col" name="velocity_supercheckout[column_width][1_column][2]" value="{$velocity_supercheckout['column_width']['1_column'][2]|escape:'htmlall':'UTF-8'}" />
                                                                                    <input type="hidden"  class="column-data-2 col" name="velocity_supercheckout[column_width][1_column][3]" value="{$velocity_supercheckout['column_width']['1_column'][3]|escape:'htmlall':'UTF-8'}" />
                                                                                    <input type="hidden"  class="column-data-3 col" name="velocity_supercheckout[column_width][1_column][inside][1]" value="{$velocity_supercheckout['column_width']['1_column']['inside'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                    <input type="hidden"  class="column-data-3 col" name="velocity_supercheckout[column_width][1_column][inside][2]" value="{$velocity_supercheckout['column_width']['1_column']['inside'][2]|escape:'htmlall':'UTF-8'}" />

                                                                                    <input type="hidden"  class="column-data-1 col" name="velocity_supercheckout[column_width][3_column][1]" value="{$velocity_supercheckout['column_width']['3_column'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                    <input type="hidden"  class="column-data-2 col" name="velocity_supercheckout[column_width][3_column][2]" value="{$velocity_supercheckout['column_width']['3_column'][2]|escape:'htmlall':'UTF-8'}" />
                                                                                    <input type="hidden"  class="column-data-2 col" name="velocity_supercheckout[column_width][3_column][3]" value="{$velocity_supercheckout['column_width']['3_column'][3]|escape:'htmlall':'UTF-8'}" />
                                                                                    <input type="hidden"  class="column-data-3 col" name="velocity_supercheckout[column_width][3_column][inside][1]" value="{$velocity_supercheckout['column_width']['3_column']['inside'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                    <input type="hidden"  class="column-data-3 col" name="velocity_supercheckout[column_width][3_column][inside][2]" value="{$velocity_supercheckout['column_width']['3_column']['inside'][2]|escape:'htmlall':'UTF-8'}" />

                                                                                    <input type="hidden"  class="column-data-2 col" name="velocity_supercheckout[column_width][2_column][3]" value="{$velocity_supercheckout['column_width']['2_column'][3]|escape:'htmlall':'UTF-8'}" />

                                                                                    <input type="text" id="column-1-text"  class="column-data-1 col velsof-column-2-input" name="velocity_supercheckout[column_width][2_column][1]" value="{$velocity_supercheckout['column_width']['2_column'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                    <input type="text" id="column-2-text" class="column-data-2 col velsof-column-2-input" name="velocity_supercheckout[column_width][2_column][2]" value="{$velocity_supercheckout['column_width']['2_column'][2]|escape:'htmlall':'UTF-8'}" />
                                                                                </div>
                                                                                <div id="slider" class="ui-editRangeSlider"></div>
                                                                                <ul id="column-1" class="column column-1 layout_list_height" col-data="1" col-inside-data="1">
                                                                                    <li class="portlet" col-data="{$velocity_supercheckout['design']['payment_address']['2_column']['column']|intval}" row-data="{$velocity_supercheckout['design']['payment_address']['2_column']['row']|intval}" col-inside-data="{$velocity_supercheckout['design']['payment_address']['2_column']['column-inside']|intval}">
                                                                                        <div class="portlet-header"><i class="icon-small-payment-address"></i>{l s='Payment Address' mod='supercheckout'}</div>
                                                                                        <div class="portlet-content">
                                                                                            <div class="text">{l s='Payment Address Content' mod='supercheckout'}</div>
                                                                                            <div class="text"><i class="icon-drag"></i><i class="icon-drag"></i></div>
                                                                                            <input   type="text"  class="sort col-data" name="velocity_velocity_supercheckout[design][payment_address][2_column][column]" value="{$velocity_supercheckout['design']['payment_address']['2_column']['column']|intval}" />
                                                                                            <input   type="text"  class="sort row-data" name="velocity_supercheckout[design][payment_address][2_column][row]" value="{$velocity_supercheckout['design']['payment_address']['2_column']['row']|intval}" />
                                                                                            <input   type="text"  class="sort col-data-inside" name="velocity_supercheckout[design][payment_address][2_column][column-inside]" value="{$velocity_supercheckout['design']['payment_address']['2_column']['column-inside']|intval}" />
                                                                                        </div>
                                                                                    </li>
                                                                                    <li class="portlet" col-data="{$velocity_supercheckout['design']['login']['2_column']['column']|intval}" row-data="{$velocity_supercheckout['design']['login']['2_column']['row']|intval}" col-inside-data="{$velocity_supercheckout['design']['login']['2_column']['column-inside']|intval}">
                                                                                        <div class="portlet-header"><i class="icon-small-payment-address"></i>{l s='Login' mod='supercheckout'}</div>
                                                                                        <div class="portlet-content">
                                                                                            <div class="text">{l s='Login Content' mod='supercheckout'}</div>
                                                                                            <div class="text"><i class="icon-drag"></i><i class="icon-drag"></i></div>
                                                                                            <input   type="text"  class="sort col-data" name="velocity_supercheckout[design][login][2_column][column]" value="{$velocity_supercheckout['design']['login']['2_column']['column']|intval}" />
                                                                                            <input   type="text"  class="sort row-data" name="velocity_supercheckout[design][login][2_column][row]" value="{$velocity_supercheckout['design']['login']['2_column']['row']|intval}" />
                                                                                            <input   type="text"  class="sort col-data-inside" name="velocity_supercheckout[design][login][2_column][column-inside]" value="{$velocity_supercheckout['design']['login']['2_column']['column-inside']|intval}" />
                                                                                        </div>
                                                                                    </li>
                                                                                    <li class="portlet" col-data="{$velocity_supercheckout['design']['shipping_address']['2_column']['column']|intval}" row-data="{$velocity_supercheckout['design']['shipping_address']['2_column']['row']|intval}" col-inside-data="{$velocity_supercheckout['design']['shipping_address']['2_column']['column-inside']|intval}">
                                                                                        <div class="portlet-header"><i class="icon-small-shipping-address"></i>{l s='Shipping Address' mod='supercheckout'}</div>
                                                                                        <div class="portlet-content">
                                                                                            <div class="text">{l s='Shipping Address Content' mod='supercheckout'}</div>
                                                                                            <div class="text"><i class="icon-drag"></i><i class="icon-drag"></i></div>
                                                                                            <input   type="text"  class="sort col-data" name="velocity_supercheckout[design][shipping_address][2_column][column]" value="{$velocity_supercheckout['design']['shipping_address']['2_column']['column']|intval}" />
                                                                                            <input   type="text"  class="sort row-data" name="velocity_supercheckout[design][shipping_address][2_column][row]" value="{$velocity_supercheckout['design']['shipping_address']['2_column']['row']|intval}" />
                                                                                            <input   type="text"  class="sort col-data-inside" name="velocity_supercheckout[design][shipping_address][2_column][column-inside]" value="{$velocity_supercheckout['design']['shipping_address']['2_column']['column-inside']|intval}" />
                                                                                        </div>
                                                                                    </li>
                                                                                    <li class="portlet" col-data="{$velocity_supercheckout['design']['shipping_method']['2_column']['column']|intval}" row-data="{$velocity_supercheckout['design']['shipping_method']['2_column']['row']|intval}" col-inside-data="{$velocity_supercheckout['design']['shipping_method']['2_column']['column-inside']|intval}">
                                                                                        <div class="portlet-header"><i class="icon-small-shipping-method"></i>{l s='Shipping Method' mod='supercheckout'}</div>
                                                                                        <div class="portlet-content">
                                                                                            <div class="text">{l s='Shipping Method Content' mod='supercheckout'}</div>
                                                                                            <div class="text"><i class="icon-drag"></i><i class="icon-drag"></i></div>
                                                                                            <input   type="text"  class="sort col-data" name="velocity_supercheckout[design][shipping_method][2_column][column]" value="{$velocity_supercheckout['design']['shipping_method']['2_column']['column']|intval}" />
                                                                                            <input   type="text"  class="sort row-data" name="velocity_supercheckout[design][shipping_method][2_column][row]" value="{$velocity_supercheckout['design']['shipping_method']['2_column']['row']|intval}" />
                                                                                            <input   type="text"  class="sort col-data-inside" name="velocity_supercheckout[design][shipping_method][2_column][column-inside]" value="{$velocity_supercheckout['design']['shipping_method']['2_column']['column-inside']|intval}" />
                                                                                        </div>
                                                                                    </li>
                                                                                    <li class="portlet" col-data="{$velocity_supercheckout['design']['payment_method']['2_column']['column']|intval}" row-data="{$velocity_supercheckout['design']['payment_method']['2_column']['row']|intval}" col-inside-data="{$velocity_supercheckout['design']['payment_method']['2_column']['column-inside']|intval}">
                                                                                        <div class="portlet-header"><i class="icon-small-payment-method"></i>{l s='Payment Method' mod='supercheckout'}</div>
                                                                                        <div class="portlet-content">
                                                                                            <div class="text">{l s='Payment Method Content' mod='supercheckout'}</div>
                                                                                            <div class="text"><i class="icon-drag"></i><i class="icon-drag"></i></div>
                                                                                            <input   type="text"  class="sort col-data" name="velocity_supercheckout[design][payment_method][2_column][column]" value="{$velocity_supercheckout['design']['payment_method']['2_column']['column']|intval}" />
                                                                                            <input   type="text"  class="sort row-data" name="velocity_supercheckout[design][payment_method][2_column][row]" value="{$velocity_supercheckout['design']['payment_method']['2_column']['row']|intval}" />
                                                                                            <input   type="text"  class="sort col-data-inside" name="velocity_supercheckout[design][payment_method][2_column][column-inside]" value="{$velocity_supercheckout['design']['payment_method']['2_column']['column-inside']|intval}" />
                                                                                        </div>
                                                                                    </li>
                                                                                    <li class="portlet" col-data="{$velocity_supercheckout['design']['cart']['2_column']['column']|intval}" row-data="{$velocity_supercheckout['design']['cart']['2_column']['row']|intval}" col-inside-data="{$velocity_supercheckout['design']['cart']['2_column']['column-inside']|intval}">
                                                                                        <div class="portlet-header"><i class="icon-small-confirm"></i>{l s='Cart' mod='supercheckout'}</div>
                                                                                        <div class="portlet-content">
                                                                                            <div class="text">{l s='Cart Content' mod='supercheckout'}</div>
                                                                                            <div class="text"><i class="icon-drag"></i><i class="icon-drag"></i></div>
                                                                                            <input   type="text"  class="sort col-data" name="velocity_supercheckout[design][cart][2_column][column]" value="{$velocity_supercheckout['design']['cart']['2_column']['column']|intval}" />
                                                                                            <input   type="text"  class="sort row-data" name="velocity_supercheckout[design][cart][2_column][row]" value="{$velocity_supercheckout['design']['cart']['2_column']['row']|intval}" />
                                                                                            <input   type="text"  class="sort col-data-inside" name="velocity_supercheckout[design][cart][2_column][column-inside]" value="{$velocity_supercheckout['design']['cart']['2_column']['column-inside']|intval}" />
                                                                                        </div>
                                                                                    </li>
                                                                                    <li class="portlet" col-data="{$velocity_supercheckout['design']['confirm']['2_column']['column']|intval}" row-data="{$velocity_supercheckout['design']['confirm']['2_column']['row']|intval}" col-inside-data="{$velocity_supercheckout['design']['confirm']['2_column']['column-inside']|intval}">
                                                                                        <div class="portlet-header"><i class="icon-small-confirm"></i>{l s='Confirm' mod='supercheckout'}</div>
                                                                                        <div class="portlet-content">
                                                                                            <div class="text">{l s='Confirm Content' mod='supercheckout'}</div>
                                                                                            <div class="text"><i class="icon-drag"></i><i class="icon-drag"></i></div>
                                                                                            <input   type="text"  class="sort col-data" name="velocity_supercheckout[design][confirm][2_column][column]" value="{$velocity_supercheckout['design']['confirm']['2_column']['column']|intval}" />
                                                                                            <input   type="text"  class="sort row-data" name="velocity_supercheckout[design][confirm][2_column][row]" value="{$velocity_supercheckout['design']['confirm']['2_column']['row']|intval}" />
                                                                                            <input   type="text"  class="sort col-data-inside" name="velocity_supercheckout[design][confirm][2_column][column-inside]" value="{$velocity_supercheckout['design']['confirm']['2_column']['column-inside']|intval}" />
                                                                                        </div>
                                                                                    </li>

                                                                                    {foreach from=$velocity_supercheckout['design']['html'] key='k' item='v'}                                                                            
                                                                                        <li class="portlet" id="portlet_{$k|escape:'htmlall':'UTF-8'}" col-data="{$velocity_supercheckout['design']['html'][$k]['2_column']['column']|intval}" row-data="{$velocity_supercheckout['design']['html'][$k]['2_column']['row']|intval}" col-inside-data="{$velocity_supercheckout['design']['html'][$k]['2_column']['column-inside']|intval}">
                                                                                            <div class="portlet-header">{l s='Extra html content' mod='supercheckout'}</div>
                                                                                            <div class="portlet-content" id="portlet_content_{$k|escape:'htmlall':'UTF-8'}">
                                                                                                <div class="text" style="overflow:visible !important;" >
                                                                                                <a data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Add new HTML content' mod='supercheckout'}" id="duplicate_button_{$k|escape:'htmlall':'UTF-8'}" data="0" class="glyphicons more_windows"  onClick="duplicate_html(this);" ><i></i></a>

                                                                                                <a data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Edit this HTML content' mod='supercheckout'}" id="modals-bootbox-prompt-{$k|escape:'htmlall':'UTF-8'}" data-toggle="modal" class="glyphicons edit bootbox-design-extra-html"  onClick="dialogExtraHtml(this);"><i></i></a>
                                                                                                {if $k neq "0_0"}
                                                                                                <a data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Remove this HTML content' mod='supercheckout'}" id="delete_button_{$k|escape:'htmlall':'UTF-8'}" data="{$k|escape:'htmlall':'UTF-8'}" data-toggle="modal" class="glyphicons remove"  onClick="remove_html(this);" ><i></i></a>
                                                                                                {/if}
                                                                                                </div>

                                                                                                <input id="col_text_{$k|escape:'htmlall':'UTF-8'}"   type="text"  class="sort col-data" name="velocity_supercheckout[design][html][{$k|escape:'htmlall':'UTF-8'}][2_column][column]" value="{$velocity_supercheckout['design']['html'][$k]['2_column']['column']|intval}" />
                                                                                                <input id="row_text_{$k|escape:'htmlall':'UTF-8'}"  type="text"  class="sort row-data" name="velocity_supercheckout[design][html][{$k|escape:'htmlall':'UTF-8'}][2_column][row]" value="{$velocity_supercheckout['design']['html'][$k]['2_column']['row']|intval}" />
                                                                                                <input id="col_inside_text_{$k|escape:'htmlall':'UTF-8'}"  type="text"  class="sort col-data-inside" name="velocity_supercheckout[design][html][{$k|escape:'htmlall':'UTF-8'}][2_column][column-inside]" value="{$velocity_supercheckout['design']['html'][$k]['2_column']['column-inside']|intval}" />
                                                                                            </div>
                                                                                        </li>
                                                                                    {/foreach}
                                                                                </ul>
                                                                                <ul id="column-2" class="columnmk column-2 layout_list_height" col-data="2" >
                                                                                    <ul id="column-2-upper" class="column column-1" col-data="1" col-inside-data="2" style="min-height: 30px !important; width:100% !important; height:auto !important;">

                                                                                    </ul>
                                                                                    <div class="columns">
                                                                                        <input type="text" id="column-1-inside-text"  class="column-data-1 col" name="velocity_supercheckout[column_width][2_column][inside][1]" value="{$velocity_supercheckout['column_width']['2_column']['inside'][1]|intval}" />
                                                                                        <input type="text" id="column-2-inside-text"  class="column-data-2 col" name="velocity_supercheckout[column_width][2_column][inside][2]" value="{$velocity_supercheckout['column_width']['2_column']['inside'][2]|intval}" />
                                                                                    </div>
                                                                                    <div id="slider_inside" class="ui-editRangeSlider" style="clear:both;"></div>

                                                                                    <ul id="column-1-inside" class="column column-1" col-inside-data="3" col-data="1" style="min-height: 30px !important; height:auto !important;">

                                                                                    </ul>
                                                                                    <ul id="column-2-inside" class="column column-2" col-inside-data="3" col-data="2" style="min-height: 30px !important; height:auto !important;"></ul>
                                                                                    <hr class="design-separator" size="2">
                                                                                    <ul id="column-2-lower" class="column column-1" col-data="1" col-inside-data="4" style="min-height: 30px !important; width:100% !important; height:auto !important;">

                                                                                    </ul>        
                                                                                </ul>
                                                                                <div class="portlet">
                                                                                    <div class="portlet-header">{l s='HTML Footer Content' mod='supercheckout'}</div>
                                                                                    <div class="portlet-content">
                                                                                        <div class="text" style="overflow:visible !important;" >

                                                                                        <a data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Edit this HTML content' mod='supercheckout'}" id="modals_bootbox_prompt_html_footer" data-toggle="modal" class="glyphicons edit bootbox-design-edit-html" ><i></i></a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>                                                                        
                                                                            </td>

                                                                        {elseif $layout eq 3}
                                                                            <td  colspan="2" style="position:static">
                                                                                <div class="portlet">
                                                                                    <div class="portlet-header">{l s='HTML Header Content' mod='supercheckout'}</div>
                                                                                    <div class="portlet-content">
                                                                                        <div class="text" style="overflow:visible !important;" >

                                                                                        <a data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Edit this HTML content' mod='supercheckout'}" id="modals_bootbox_prompt_html_header" data-toggle="modal" class="glyphicons edit bootbox-design-edit-html" ><i></i></a>
                                                                                        </div>
                                                                                    </div>  
                                                                                </div>
                                                                                <div class="columns">
                                                                                    <input type="hidden"  class="column-data-1 col" name="velocity_supercheckout[column_width][1_column][1]" value="{$velocity_supercheckout['column_width']['1_column'][1]|intval}" />
                                                                                    <input type="hidden"  class="column-data-2 col" name="velocity_supercheckout[column_width][1_column][2]" value="{$velocity_supercheckout['column_width']['1_column'][2]|intval}" />
                                                                                    <input type="hidden"  class="column-data-2 col" name="velocity_supercheckout[column_width][1_column][3]" value="{$velocity_supercheckout['column_width']['1_column'][3]|intval}" />
                                                                                    <input type="hidden"  class="column-data-3 col" name="velocity_supercheckout[column_width][1_column][inside][1]" value="{$velocity_supercheckout['column_width']['1_column']['inside'][1]|intval}" />
                                                                                    <input type="hidden"  class="column-data-3 col" name="velocity_supercheckout[column_width][1_column][inside][2]" value="{$velocity_supercheckout['column_width']['1_column']['inside'][2]|intval}" />

                                                                                    <input type="hidden"  class="column-data-1 col" name="velocity_supercheckout[column_width][2_column][1]" value="{$velocity_supercheckout['column_width']['2_column'][1]|intval}" />
                                                                                    <input type="hidden"  class="column-data-2 col" name="velocity_supercheckout[column_width][2_column][2]" value="{$velocity_supercheckout['column_width']['2_column'][2]|intval}" />
                                                                                    <input type="hidden"  class="column-data-2 col" name="velocity_supercheckout[column_width][2_column][3]" value="{$velocity_supercheckout['column_width']['2_column'][3]|intval}" />
                                                                                    <input type="hidden"  class="column-data-3 col" name="velocity_supercheckout[column_width][2_column][inside][1]" value="{$velocity_supercheckout['column_width']['2_column']['inside'][1]|intval}" />
                                                                                    <input type="hidden"  class="column-data-3 col" name="velocity_supercheckout[column_width][2_column][inside][2]" value="{$velocity_supercheckout['column_width']['2_column']['inside'][2]|intval}" />

                                                                                    <input type="hidden"  class="column-data-3 col" name="velocity_supercheckout[column_width][3_column][inside][1]" value="{$velocity_supercheckout['column_width']['3_column']['inside'][1]|intval}" />
                                                                                    <input type="hidden"  class="column-data-3 col" name="velocity_supercheckout[column_width][3_column][inside][2]" value="{$velocity_supercheckout['column_width']['3_column']['inside'][2]|intval}" />
                                                                                    <input type="text" id="three-column-1" readonly="readonly" class="column-data-1 col" name="velocity_supercheckout[column_width][3_column][1]" value="{$velocity_supercheckout['column_width']['3_column'][1]|intval}" />
                                                                                    <input type="text" id="three-column-2" readonly="readonly" class="column-data-2 col" name="velocity_supercheckout[column_width][3_column][2]" value="{$velocity_supercheckout['column_width']['3_column'][2]|intval}" />
                                                                                    <input type="text" id="three-column-3" readonly="readonly" class="column-data-3 col" name="velocity_supercheckout[column_width][3_column][3]" value="{$velocity_supercheckout['column_width']['3_column'][3]|intval}" />
                                                                                </div>
                                                                                <div id="slider" class="ui-editRangeSlider"></div>
                                                                                <ul id="column_1" class="column column-1 layout_list_height" col-data="1">
                                                                                    <li class="portlet" col-data="{$velocity_supercheckout['design']['payment_address']['3_column']['column']|intval}" row-data="{$velocity_supercheckout['design']['payment_address']['3_column']['row']|intval}">
                                                                                        <div class="portlet-header"><i class="icon-small-payment-address"></i>{l s='Payment Address' mod='supercheckout'}</div>
                                                                                        <div class="portlet-content">
                                                                                            <div class="text">{l s='Payment Address Content' mod='supercheckout'}</div>
                                                                                            <div class="text"><i class="icon-drag"></i><i class="icon-drag"></i></div>
                                                                                            <input   type="text"  class="sort col-data" name="velocity_supercheckout[design][payment_address][3_column][column]" value="{$velocity_supercheckout['design']['payment_address']['3_column']['column']|intval}" />
                                                                                            <input   type="text"  class="sort row-data" name="velocity_supercheckout[design][payment_address][3_column][row]" value="{$velocity_supercheckout['design']['payment_address']['3_column']['row']|intval}" />
                                                                                        </div>
                                                                                    </li>
                                                                                    <li class="portlet" col-data="{$velocity_supercheckout['design']['login']['3_column']['column']|intval}" row-data="{$velocity_supercheckout['design']['login']['3_column']['row']|intval}">
                                                                                        <div class="portlet-header"><i class="icon-small-payment-address"></i>{l s='Login' mod='supercheckout'}</div>
                                                                                        <div class="portlet-content">
                                                                                            <div class="text">{l s='Login Content' mod='supercheckout'}</div>
                                                                                            <div class="text"><i class="icon-drag"></i><i class="icon-drag"></i></div>
                                                                                            <input   type="text"  class="sort col-data" name="velocity_supercheckout[design][login][3_column][column]" value="{$velocity_supercheckout['design']['login']['3_column']['column']|intval}" />
                                                                                            <input   type="text"  class="sort row-data" name="velocity_supercheckout[design][login][3_column][row]" value="{$velocity_supercheckout['design']['login']['3_column']['row']|intval}" />
                                                                                        </div>
                                                                                    </li>
                                                                                    <li class="portlet" col-data="{$velocity_supercheckout['design']['shipping_address']['3_column']['column']|intval}" row-data="{$velocity_supercheckout['design']['shipping_address']['3_column']['row']|intval}">
                                                                                        <div class="portlet-header"><i class="icon-small-shipping-address"></i>{l s='Shipping Address' mod='supercheckout'}</div>
                                                                                        <div class="portlet-content">
                                                                                            <div class="text">{l s='Shipping Address Content' mod='supercheckout'}</div>
                                                                                            <div class="text"><i class="icon-drag"></i><i class="icon-drag"></i></div>
                                                                                            <input   type="text"  class="sort col-data" name="velocity_supercheckout[design][shipping_address][3_column][column]" value="{$velocity_supercheckout['design']['shipping_address']['3_column']['column']|intval}" />
                                                                                            <input   type="text"  class="sort row-data" name="velocity_supercheckout[design][shipping_address][3_column][row]" value="{$velocity_supercheckout['design']['shipping_address']['3_column']['row']|intval}" />
                                                                                        </div>
                                                                                    </li>
                                                                                    <li class="portlet" col-data="{$velocity_supercheckout['design']['shipping_method']['3_column']['column']|intval}" row-data="{$velocity_supercheckout['design']['shipping_method']['3_column']['row']|intval}">
                                                                                        <div class="portlet-header"><i class="icon-small-shipping-method"></i>{l s='Shipping Method' mod='supercheckout'}</div>
                                                                                        <div class="portlet-content">
                                                                                            <div class="text">{l s='Shipping Method Content' mod='supercheckout'}</div>
                                                                                            <div class="text"><i class="icon-drag"></i><i class="icon-drag"></i></div>
                                                                                            <input   type="text"  class="sort col-data" name="velocity_supercheckout[design][shipping_method][3_column][column]" value="{$velocity_supercheckout['design']['shipping_method']['3_column']['column']|intval}" />
                                                                                            <input   type="text"  class="sort row-data" name="velocity_supercheckout[design][shipping_method][3_column][row]" value="{$velocity_supercheckout['design']['shipping_method']['3_column']['row']|intval}" />
                                                                                        </div>
                                                                                    </li>
                                                                                    <li class="portlet" col-data="{$velocity_supercheckout['design']['payment_method']['3_column']['column']|intval}" row-data="{$velocity_supercheckout['design']['payment_method']['3_column']['row']|intval}">
                                                                                        <div class="portlet-header"><i class="icon-small-payment-method"></i>{l s='Payment Method' mod='supercheckout'}</div>
                                                                                        <div class="portlet-content">
                                                                                            <div class="text">{l s='Payment Method Content' mod='supercheckout'}</div>
                                                                                            <div class="text"><i class="icon-drag"></i><i class="icon-drag"></i></div>
                                                                                            <input   type="text"  class="sort col-data" name="velocity_supercheckout[design][payment_method][3_column][column]" value="{$velocity_supercheckout['design']['payment_method']['3_column']['column']|intval}" />
                                                                                            <input   type="text"  class="sort row-data" name="velocity_supercheckout[design][payment_method][3_column][row]" value="{$velocity_supercheckout['design']['payment_method']['3_column']['row']|intval}" />
                                                                                        </div>
                                                                                    </li>
                                                                                    <li class="portlet" col-data="{$velocity_supercheckout['design']['cart']['3_column']['column']|intval}" row-data="{$velocity_supercheckout['design']['cart']['3_column']['row']|intval}">
                                                                                        <div class="portlet-header"><i class="icon-small-confirm"></i>{l s='Cart' mod='supercheckout'}</div>
                                                                                        <div class="portlet-content">
                                                                                            <div class="text">{l s='Cart Content' mod='supercheckout'}</div>
                                                                                            <div class="text"><i class="icon-drag"></i><i class="icon-drag"></i></div>
                                                                                            <input   type="text"  class="sort col-data" name="velocity_supercheckout[design][cart][3_column][column]" value="{$velocity_supercheckout['design']['cart']['3_column']['column']|intval}" />
                                                                                            <input   type="text"  class="sort row-data" name="velocity_supercheckout[design][cart][3_column][row]" value="{$velocity_supercheckout['design']['cart']['3_column']['row']|intval}" />
                                                                                        </div>
                                                                                    </li>
                                                                                    <li class="portlet" col-data="{$velocity_supercheckout['design']['confirm']['3_column']['column']|intval}" row-data="{$velocity_supercheckout['design']['confirm']['3_column']['row']|intval}">
                                                                                        <div class="portlet-header"><i class="icon-small-confirm"></i>{l s='Confirm' mod='supercheckout'}</div>
                                                                                        <div class="portlet-content">
                                                                                            <div class="text">{l s='Confirm Content' mod='supercheckout'}</div>
                                                                                            <div class="text"><i class="icon-drag"></i><i class="icon-drag"></i></div>
                                                                                            <input   type="text"  class="sort col-data" name="velocity_supercheckout[design][confirm][3_column][column]" value="{$velocity_supercheckout['design']['confirm']['3_column']['column']|intval}" />
                                                                                            <input   type="text"  class="sort row-data" name="velocity_supercheckout[design][confirm][3_column][row]" value="{$velocity_supercheckout['design']['confirm']['3_column']['row']|intval}" />
                                                                                        </div>
                                                                                    </li>


                                                                                  {foreach from=$velocity_supercheckout['design']['html'] key='k' item='v'}
                                                                                    <li class="portlet" id="portlet_{$k|escape:'htmlall':'UTF-8'}" col-data="{$velocity_supercheckout['design']['html'][$k]['3_column']['column']|intval}" row-data="{$velocity_supercheckout['design']['html'][$k]['3_column']['row']|intval}">
                                                                                        <div class="portlet-header">{l s='Extra html content' mod='supercheckout'}</div>
                                                                                        <div class="portlet-content" id="portlet_content_{$k|escape:'htmlall':'UTF-8'}">
                                                                                            <div class="text" style="overflow:visible !important;" >
                                                                                            <a data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Add new HTML content' mod='supercheckout'}" id="duplicate_button_{$k|escape:'htmlall':'UTF-8'}" data="0" class="glyphicons more_windows"  onClick="duplicate_html(this);" ><i></i></a>

                                                                                            <a data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Edit this HTML content' mod='supercheckout'}" id="modals-bootbox-prompt-{$k|escape:'htmlall':'UTF-8'}" data-toggle="modal" class="glyphicons edit bootbox-design-extra-html"  onClick="dialogExtraHtml(this);"><i></i></a>
                                                                                            {if $k neq "0_0"}
                                                                                            <a data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Remove this HTML content' mod='supercheckout'}" id="delete_button_{$k|escape:'htmlall':'UTF-8'}" data="{$k|escape:'htmlall':'UTF-8'}" data-toggle="modal" class="glyphicons remove"  onClick="remove_html(this);" ><i></i></a>
                                                                                            {/if}
                                                                                            </div>

                                                                                            <input id="col_text_{$k|escape:'htmlall':'UTF-8'}"   type="text"  class="sort col-data" name="velocity_supercheckout[design][html][{$k|escape:'htmlall':'UTF-8'}][3_column][column]" value="{$velocity_supercheckout['design']['html'][$k]['3_column']['column']|intval}" />
                                                                                            <input id="row_text_{$k|escape:'htmlall':'UTF-8'}"  type="text"  class="sort row-data" name="velocity_supercheckout[design][html][{$k|escape:'htmlall':'UTF-8'}][3_column][row]" value="{$velocity_supercheckout['design']['html'][$k]['3_column']['row']|intval}" />
                                                                                        </div>
                                                                                    </li>                                                    
                                                                                    {/foreach}
                                                                                </ul>
                                                                                <ul id="column_2" class="column column-2 layout_list_height" col-data="2"></ul>
                                                                                <ul id="column_3" class="column column-3 layout_list_height" col-data="3"></ul>
                                                                                <div class="portlet">
                                                                                    <div class="portlet-header">{l s='HTML Footer Content' mod='supercheckout'}</div>
                                                                                    <div class="portlet-content">
                                                                                        <div class="text" style="overflow:visible !important;" >

                                                                                        <a data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Edit this HTML content' mod='supercheckout'}" id="modals_bootbox_prompt_html_footer" data-toggle="modal" class="glyphicons edit bootbox-design-edit-html" ><i></i></a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        {/if}

                                                                    </tr>
                                                                </tbody>
                                                            </table>    
                                                        </div>
                                                    </div>

                                                    <!--------------- End - Design -------------------->
                                                    <input type="hidden" id="modal_value" name="velocity_supercheckout[modal_value]" value="{$velocity_supercheckout['modal_value']|escape:'htmlall':'UTF-8'}" />
                                                    <div id="extra_html_container">
                                                    {foreach from=$velocity_supercheckout['design']['html'] key='k' item='v'}
                                                        <input type="hidden" id="modals_bootbox_prompt_{$k|escape:'htmlall':'UTF-8'}" name="velocity_supercheckout[design][html][{$k|escape:'htmlall':'UTF-8'}][value]" value="{html_entity_decode($velocity_supercheckout['design']['html'][{$k|escape:'htmlall':'utf-8'}]['value'])|escape:'html':'utf-8'}" />
                                                    {/foreach}
                                                    </div>
</form>
                                                    <!--------------- Start - Language Translator -------------------->

                                                    <div id="tab_lang_translator" class="tab-pane outsideform lang-active">
                                                        <div class="block">
                                                            <h4 class='velsof-tab-heading'>{l s='Language Translator' mod='supercheckout'}</h4>
                                                            <div class="row">
                                                                <div class="span0">
                                                                    <span>{l s='Select Language' mod='supercheckout'}: </span><i class="icon-question-sign supercheckout-tooltip-color" data-toggle="tooltip" data-placement="top" data-original-title="{l s='Select Language Tooltip' mod='supercheckout'}"></i>
                                                                </div>
                                                                <div class="span2">
                                                                    <select {if $ps_version eq 15}class="selectpicker vss_sc_ver15" {/if} name="velocity_transalator[selected_language]" onChange="setChangedLanguage('{$action|escape:'htmlall':'UTF-8'}', this);" >
                                                                        {foreach from=$languages item='lang'}
                                                                            {if $lang['id_lang']|cat:'_'|cat:$lang['iso_code'] eq $default_selected_language|cat:'_'|cat:$lang['iso_code']}
                                                                                <option value="{$lang['id_lang']|cat:'_'|cat:$lang['iso_code']}" selected='selected'>{$lang['name']|escape:'htmlall':'UTF-8'}</option>
                                                                            {else}
                                                                                <option value="{$lang['id_lang']|cat:'_'|cat:$lang['iso_code']}">{$lang['name']|escape:'htmlall':'UTF-8'}</option>
                                                                            {/if}
                                                                        
                                                                        {/foreach}
                                                                    </select>
                                                                </div>
                                                                <div class="span1">
                                                                    <a href="javascript:void(0)" {if $ps_version eq 15}class="vss-action-btn-ver15"{/if} onclick="generate_language('{$action|escape:'htmlall':'UTF-8'}', 'save')"><span class="btn btn-block btn-success{if $ps_version eq 16} action-btn{/if}">{l s='Save' mod='supercheckout'}</span></a>
                                                                </div>
                                                                <div class="span1">
                                                                    <a href="javascript:void(0)" {if $ps_version eq 15}class="vss-action-btn-ver15"{/if} onclick="generate_language('{$action|escape:'htmlall':'UTF-8'}', 'download')"><span class="btn btn-block btn-warning{if $ps_version eq 16} action-btn{/if}">{l s='Download' mod='supercheckout'}</span></a>
                                                                </div>
                                                                <div class="span2">
                                                                    <a href="javascript:void(0)" {if $ps_version eq 15}class="vss-action-btn-ver15"{/if} onclick="generate_language('{$action|escape:'htmlall':'UTF-8'}', 'saveDownload')"><span class="btn btn-block btn-danger{if $ps_version eq 16} action-btn{/if}"{if $ps_version eq 16} style="max-width: 120px !important;"{/if}>{l s='Save & Download' mod='supercheckout'}</span></a>
                                                                </div>
                                                            </div>
                                                            <hr style='margin-bottom:5px;'>
                                                            <div class="row">
                                                                <div class="span">
                                                                    <p style="margin-bottom: 0;">
                                                                        <b>{l s='Note' mod='supercheckout'}:</b>
                                                                        (%s) - {l s='Do not remove %s symbol' mod='supercheckout'}
                                                                    </p> 
                                                                </div>  
                                                            </div>
                                                            <hr style='margin-top:5px;'>
                                                            <div id="velsof-lang-trans-progress" class="widget"><img src="{$root_path|escape:'htmlall':'UTF-8'}views/img/admin/ajax_loader.gif" /></div>
                                                            <div id="velsof-lang-trans-body" class="widget widget-tabs widget-tabs-double-2">
                                                                <div class="widget-head">
                                                                        <ul>
                                                                            <li class="active {if $ps_version eq 15}vss-lang-tab-ver15{/if}"><a class="glyphicons asterisk lang-tab" href="#tab_lang_admin_panel" data-toggle="tab"><i></i><span>{l s='Admin Labels' mod='supercheckout'}</span></a></li>
                                                                            <li class="{if $ps_version eq 15}vss-lang-tab-ver15{/if}"><a class="glyphicons keys lang-tab" id="velsof_tab_login" href="#tab_lang_front_panel" data-toggle="tab"><i></i><span>{l s='Front Labels' mod='supercheckout'}</span></a></li>
                                                                            <li class="{if $ps_version eq 15}vss-lang-tab-ver15{/if}"><a class="glyphicons user_add lang-tab" href="#tab_lang_common" data-toggle="tab"><i></i><span>{l s='Common Labels' mod='supercheckout'}</span></a></li>
                                                                            <li class="{if $ps_version eq 15}vss-lang-tab-ver15{/if}"><a class="glyphicons credit_card lang-tab" href="#tab_lang_messages" data-toggle="tab"><i></i><span>{l s='Messages' mod='supercheckout'}</span></a></li>
                                                                            <li class="{if $ps_version eq 15}vss-lang-tab-ver15{/if}"><a class="glyphicons podium lang-tab" href="#tab_lang_misc" data-toggle="tab"><i></i><span>{l s='Miscellaneous' mod='supercheckout'}</span></a></li>
                                                                        </ul>
                                                                </div>
                                                                <div class="widget-body" style="padding:0 !important;">
                                                                    {assign var="label" value="Label"}
                                                                    <div class="tab-content">
                                                                        <div id="tab_lang_admin_panel" class="active tab-pane widget-body-regular {if $ps_version eq 15}vss-lang-tab-pane-ver15{/if}"> 
                                                                            <h4 class='velsof-tab-heading'>{l s='General Settings' mod='supercheckout'}</h4>
                                                                            <table class="form alternate">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <th class="name vertical_top_align" style="text-align: right;">{$current_lang_translator_vars['admin_lang_translate']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                        <th class="velsof-english">{$current_lang_translator_vars['admin_lang_from']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                        <th class="left" style="font-size:14px;">{$current_lang_translator_vars['admin_lang_to']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Enable/Disable {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_enable']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_enable][label][]" value="Enable/Disable" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_enable][label][]" value="{$selected_lang_translator_vars['admin_enable']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Enable/Disable ToolTip {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_enable']['tooltip'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_enable][tooltip][]" value="Enable/Disable Tooltip" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_enable][tooltip][]" value="{$selected_lang_translator_vars['admin_enable']['tooltip'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Enable Guest Checkout {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_enable_guest_checkout']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_enable_guest_checkout][label][]" value="Enable Guest Checkout" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_enable_guest_checkout][label][]" value="{$selected_lang_translator_vars['admin_enable_guest_checkout']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Enable Guest Checkout Tooltip {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_enable_guest_checkout']['tooltip'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_enable_guest_checkout][tooltip][]" value="Enable Guest Checkout Tooltip" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_enable_guest_checkout][tooltip][]" value="{$selected_lang_translator_vars['admin_enable_guest_checkout']['tooltip'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Enable Guest Checkout Warning {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_enable_guest_checkout_warning']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_enable_guest_checkout_warning][label][]" value="This option will not work, if it is disable from default prestashop settings." />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_enable_guest_checkout_warning][label][]" value="{$selected_lang_translator_vars['admin_enable_guest_checkout_warning']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Register Guest {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_enable_guest_register']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_enable_guest_register][label][]" value="Register Guest" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_enable_guest_register][label][]" value="{$selected_lang_translator_vars['admin_enable_guest_register']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Register Guest Tooltip {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_enable_guest_register']['tooltip'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_enable_guest_register][tooltip][]" value="Register Guest Tooltip" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_enable_guest_register][tooltip][]" value="{$selected_lang_translator_vars['admin_enable_guest_register']['tooltip'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Default Option at Checkout {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_checkout_option']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_checkout_option][label][]" value="Default Option at Checkout" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_checkout_option][label][]" value="{$selected_lang_translator_vars['admin_checkout_option']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Default Option at Checkout Tooltip {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_checkout_option']['tooltip'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_checkout_option][tooltip][]" value="Default Option at Checkout Tooltip" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_checkout_option][tooltip][]" value="{$selected_lang_translator_vars['admin_checkout_option']['tooltip'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                            <br><br>
                                                                            <h4 class='velsof-tab-heading'>{l s='Login' mod='supercheckout'}</h4>
                                                                            <table class="form alternate">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <th class="name vertical_top_align" style="text-align: right;">{$current_lang_translator_vars['admin_lang_translate']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                        <th class="velsof-english">{$current_lang_translator_vars['admin_lang_from']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                        <th class="left" style="font-size:14px;">{$current_lang_translator_vars['admin_lang_to']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Enable Facebook Login {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_fb_enable']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_fb_enable][label][]" value="Enable Facebook Login" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_fb_enable][label][]" value="{$selected_lang_translator_vars['admin_fb_enable']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Enable Facebook Login Tooltip {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_fb_enable']['tooltip'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_fb_enable][tooltip][]" value="Enable Facebook Login Tooltip" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_fb_enable][tooltip][]" value="{$selected_lang_translator_vars['admin_fb_enable']['tooltip'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Facebook App Id Tooltip {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_fb_app_id']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_fb_app_id][label][]" value="Facebook App Id" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_fb_app_id][label][]" value="{$selected_lang_translator_vars['admin_fb_app_id']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Facebook App Id {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_fb_app_id']['tooltip'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_fb_app_id][tooltip][]" value="Facebook App Id Tooltip" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_fb_app_id][tooltip][]" value="{$selected_lang_translator_vars['admin_fb_app_id']['tooltip'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Facebook App Secret {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_fb_app_secret']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_fb_app_secret][label][]" value="Facebook App Secret" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_fb_app_secret][label][]" value="{$selected_lang_translator_vars['admin_fb_app_secret']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Facebook App Secret Tooltip {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_fb_app_secret']['tooltip'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_fb_app_secret][tooltip][]" value="Facebook App Secret Tooltip" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_fb_app_secret][tooltip][]" value="{$selected_lang_translator_vars['admin_fb_app_secret']['tooltip'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Enable Google Login {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_google_enable']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_google_enable][label][]" value="Enable Google Login" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_google_enable][label][]" value="{$selected_lang_translator_vars['admin_google_enable']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Enable Google Login Tooltip {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_google_enable']['tooltip'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_google_enable][tooltip][]" value="Enable Google Login Tooltip" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_google_enable][tooltip][]" value="{$selected_lang_translator_vars['admin_google_enable']['tooltip'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Google App Id {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_google_app_id']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_google_app_id][label][]" value="Google App Id" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_google_app_id][label][]" value="{$selected_lang_translator_vars['admin_google_app_id']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Google App Id Tooltip {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_google_app_id']['tooltip'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_google_app_id][tooltip][]" value="Google App Id Tooltip" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_google_app_id][tooltip][]" value="{$selected_lang_translator_vars['admin_google_app_id']['tooltip'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Google Client Id {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_google_client_id']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_google_client_id][label][]" value="Google Client Id" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_google_client_id][label][]" value="{$selected_lang_translator_vars['admin_google_client_id']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Google Client Id Tooltip {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_google_client_id']['tooltip'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_google_client_id][tooltip][]" value="Google Client Id Tooltip" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_google_client_id][tooltip][]" value="{$selected_lang_translator_vars['admin_google_client_id']['tooltip'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Google App Secret {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_google_app_secret']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_google_app_secret][label][]" value="Google App Secret" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_google_app_secret][label][]" value="{$selected_lang_translator_vars['admin_google_app_secret']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Google App Secret Tooltip {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_google_app_secret']['tooltip'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_google_app_secret][tooltip][]" value="Google App Secret Tooltip" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_google_app_secret][tooltip][]" value="{$selected_lang_translator_vars['admin_google_app_secret']['tooltip'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                            <h4 class='velsof-tab-heading'>{l s='Methods' mod='supercheckout'}</h4>
                                                                            <table class="form alternate">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <th class="name vertical_top_align" style="text-align: right;">{$current_lang_translator_vars['admin_lang_translate']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                        <th class="velsof-english">{$current_lang_translator_vars['admin_lang_from']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                        <th class="left" style="font-size:14px;">{$current_lang_translator_vars['admin_lang_to']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Display Methods {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_display_method']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_display_method][label][]" value="Display Methods" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_display_method][label][]" value="{$selected_lang_translator_vars['admin_display_method']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Display Methods Tooltip {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_display_method']['tooltip'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_display_method][tooltip][]" value="Display Methods Tooltip" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_display_method][tooltip][]" value="{$selected_lang_translator_vars['admin_display_method']['tooltip'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Display Style {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_display_method_style']['label'][1]|escape:'htmlall':'UTF-8'}Display Style</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_display_method_style][label][]" value="Display Style" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_display_method_style][label][]" value="{$selected_lang_translator_vars['admin_display_method_style']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Method Display Style Tooltip {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_display_method_style']['tooltip'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_display_method_style][tooltip][]" value="Method Display Style Tooltip" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_display_method_style][tooltip][]" value="{$selected_lang_translator_vars['admin_display_method_style']['tooltip'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Select Default Method {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_default_method']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_default_method][label][]" value="Select Default Method" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_default_method][label][]" value="{$selected_lang_translator_vars['admin_default_method']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Select Default Payment Method Tooltip {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_default_pay_method']['tooltip'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_default_pay_method][tooltip][]" value="Selected Default Payment Method Tooltip" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_default_pay_method][tooltip][]" value="{$selected_lang_translator_vars['admin_default_pay_method']['tooltip'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Payment Method Style Note {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_paymethod_style_note']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_paymethod_style_note][label][]" value="Payment Method Style Note" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_paymethod_style_note][label][]" value="{$selected_lang_translator_vars['admin_paymethod_style_note']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Select Default Shipping Method Tooltip {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_default_ship_method']['tooltip'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_default_ship_method][tooltip][]" value="Selected Default Shipping Method Tooltip" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_default_ship_method][tooltip][]" value="{$selected_lang_translator_vars['admin_default_ship_method']['tooltip'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Delivery Method Style Note {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_deliverymethod_style_note']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_deliverymethod_style_note][label][]" value="Delivery Method Style Note" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_deliverymethod_style_note][label][]" value="{$selected_lang_translator_vars['admin_deliverymethod_style_note']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                            <br><br>
                                                                            <h4 class='velsof-tab-heading'>{l s='Cart' mod='supercheckout'}</h4>
                                                                            <table class="form alternate">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <th class="name vertical_top_align" style="text-align: right;">{$current_lang_translator_vars['admin_lang_translate']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                        <th class="velsof-english">{$current_lang_translator_vars['admin_lang_from']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                        <th class="left" style="font-size:14px;">{$current_lang_translator_vars['admin_lang_to']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Display Cart {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_cart_display']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_cart_display][label][]" value="Display Cart" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_cart_display][label][]" value="{$selected_lang_translator_vars['admin_cart_display']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Display Cart Tooltip {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_cart_display']['tooltip'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_cart_display][tooltip][]" value="Display Cart Tooltip" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_cart_display][tooltip][]" value="{$selected_lang_translator_vars['admin_cart_display']['tooltip'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Image {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_cart_image']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_cart_image][label][]" value="Image" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_cart_image][label][]" value="{$selected_lang_translator_vars['admin_cart_image']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Quantity {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_cart_quantity']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_cart_quantity][label][]" value="Quantity" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_cart_quantity][label][]" value="{$selected_lang_translator_vars['admin_cart_quantity']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Product Image Size {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_cart_p_image_size']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_cart_p_image_size][label][]" value="Product Image Size" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_cart_p_image_size][label][]" value="{$selected_lang_translator_vars['admin_cart_p_image_size']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Product Image Size Tooltip {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_cart_p_image_size']['tooltip'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_cart_p_image_size][tooltip][]" value="Product Image Size Tooltip" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_cart_p_image_size][tooltip][]" value="{$selected_lang_translator_vars['admin_cart_p_image_size']['tooltip'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Product(s) Sub-Total {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_cart_sub_total']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_cart_sub_total][label][]" value="Product(s) Sub-Total" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_cart_sub_total][label][]" value="{$selected_lang_translator_vars['admin_cart_sub_total']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Product(s) Sub-Total Tooltip {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_cart_sub_total']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_cart_sub_total][tooltip][]" value="Display Sub-Total Tooltip" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_cart_sub_total][tooltip][]" value="{$selected_lang_translator_vars['admin_cart_sub_total']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Voucher Input {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_cart_voucher_input']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_cart_voucher_input][label][]" value="Voucher Input" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_cart_voucher_input][label][]" value="{$selected_lang_translator_vars['admin_cart_voucher_input']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Voucher Input Tooltip {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_cart_voucher_input']['tooltip'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_cart_voucher_input][tooltip][]" value="Display Voucher Input Tooltip" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_cart_voucher_input][tooltip][]" value="{$selected_lang_translator_vars['admin_cart_voucher_input']['tooltip'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Shipping Price {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_cart_shipping_price']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_cart_shipping_price][label][]" value="Shipping Price" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_cart_shipping_price][label][]" value="{$selected_lang_translator_vars['admin_cart_shipping_price']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Order Total {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_cart_order_total']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_cart_order_total][label][]" value="Order Total" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_cart_order_total][label][]" value="{$selected_lang_translator_vars['admin_cart_order_total']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                            <br><br>
                                                                            <h4 class='velsof-tab-heading'>{l s='Confirm' mod='supercheckout'}</h4>
                                                                            <table class="form alternate">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <th class="name vertical_top_align" style="text-align: right;">{$current_lang_translator_vars['admin_lang_translate']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                        <th class="velsof-english">{$current_lang_translator_vars['admin_lang_from']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                        <th class="left" style="font-size:14px;">{$current_lang_translator_vars['admin_lang_to']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Term & Condition {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_confirm_term_condition']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_confirm_term_condition][label][]" value="Term & Condition" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_confirm_term_condition][label][]" value="{$selected_lang_translator_vars['admin_confirm_term_condition']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Term & Condition Tooltip {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_confirm_term_condition']['tooltip'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_confirm_term_condition][tooltip][]" value="Display Term & Condition Tooltip" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_confirm_term_condition][tooltip][]" value='{$selected_lang_translator_vars['admin_confirm_term_condition']['tooltip'][1]|escape:'htmlall':'UTF-8'}' />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Term & Condition Warning {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_confirm_tc_warning']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_confirm_tc_warning][label][]" value="This option will not display, if disable from default prestashop settings" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_confirm_tc_warning][label][]" value='{$selected_lang_translator_vars['admin_confirm_tc_warning']['label'][1]|escape:'htmlall':'UTF-8'}' />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Comment Box for Order {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_confirm_comment_box']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_confirm_comment_box][label][]" value="Comment Box for Order" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_confirm_comment_box][label][]" value="{$selected_lang_translator_vars['admin_confirm_comment_box']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>                                                                                    
                                                                                </tbody>
                                                                            </table>
                                                                            <br><br>
                                                                            <h4 class='velsof-tab-heading'>{l s='Language Translator' mod='supercheckout'}</h4>
                                                                            <table class="form alternate">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <th class="name vertical_top_align" style="text-align: right;">{$current_lang_translator_vars['admin_lang_translate']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                        <th class="velsof-english">From</th>
                                                                                        <th class="left" style="font-size:14px;">To</th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Select Language {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_lang_sel_lbl']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_lang_sel_lbl][label][]" value="Select Language" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_lang_sel_lbl][label][]" value="{$selected_lang_translator_vars['admin_lang_sel_lbl']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Select Language Tooltip {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_lang_sel_lbl']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_lang_sel_lbl][tooltip][]" value="Select Language Tooltip" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_lang_sel_lbl][tooltip][]" value="{$selected_lang_translator_vars['admin_lang_sel_lbl']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Admin Panel and Front End {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_lang_admin_front_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_lang_admin_front_heading][label][]" value="Admin Panel and Front End" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_lang_admin_front_heading][label][]" value="{$selected_lang_translator_vars['admin_lang_admin_front_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Admin Panel {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_lang_admin_panel_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_lang_admin_panel_heading][label][]" value="Admin Panel" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_lang_admin_panel_heading][label][]" value="{$selected_lang_translator_vars['admin_lang_admin_panel_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Translate {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_lang_translate']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_lang_translate][label][]" value="Translate" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_lang_translate][label][]" value="{$selected_lang_translator_vars['admin_lang_translate']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>From {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_lang_from']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_lang_from][label][]" value="From" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_lang_from][label][]" value="{$selected_lang_translator_vars['admin_lang_from']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>To {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_lang_to']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_lang_to][label][]" value="To" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_lang_to][label][]" value="{$selected_lang_translator_vars['admin_lang_to']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>                                                                                    
                                                                                </tbody>
                                                                            </table>
                                                                            <br><br>
                                                                            <h4 class='velsof-tab-heading'>{l s='Design' mod='supercheckout'}</h4>
                                                                            <table class="form alternate">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <th class="name vertical_top_align" style="text-align: right;">{$current_lang_translator_vars['admin_lang_translate']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                        <th class="velsof-english">{$current_lang_translator_vars['admin_lang_from']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                        <th class="left" style="font-size:14px;">{$current_lang_translator_vars['admin_lang_to']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Columns {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_design_column']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_design_column][label][]" value="Display Methods" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_design_column][label][]" value="{$selected_lang_translator_vars['admin_design_column']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Login Content {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_design_login_content']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_design_login_content][label][]" value="Login Content" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_design_login_content][label][]" value="{$selected_lang_translator_vars['admin_design_login_content']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Payment Address Content {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_design_p_addr_content']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_design_p_addr_content][label][]" value="Payment Address Content" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_design_p_addr_content][label][]" value="{$selected_lang_translator_vars['admin_design_p_addr_content']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Shipping Address Content {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_design_ship_addr_content']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_design_ship_addr_content][label][]" value="Shipping Address Content" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_design_ship_addr_content][label][]" value="{$selected_lang_translator_vars['admin_design_ship_addr_content']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Payment Method Content {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_design_p_method_content']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_design_p_method_content][label][]" value="Payment Method Content" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_design_p_method_content][label][]" value="{$selected_lang_translator_vars['admin_design_p_method_content']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Shipping Method Content {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_design_ship_method_content']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_design_ship_method_content][label][]" value="Shipping Method Content" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_design_ship_method_content][label][]" value="{$selected_lang_translator_vars['admin_design_ship_method_content']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Cart Content {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_design_cart_content']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_design_cart_content][label][]" value="Cart Content" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_design_cart_content][label][]" value="{$selected_lang_translator_vars['admin_design_cart_content']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Confirm Content {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_design_confirm_content']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_design_confirm_content][label][]" value="Confirm Content" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_design_confirm_content][label][]" value="{$selected_lang_translator_vars['admin_design_confirm_content']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>HTML Header Content {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_design_html_header']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_design_html_header][label][]" value="HTML Header Content" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_design_html_header][label][]" value="{$selected_lang_translator_vars['admin_design_html_header']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>HTML Footer Content {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_design_html_footer']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_design_html_footer][label][]" value="HTML Footer Content" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_design_html_footer][label][]" value="{$selected_lang_translator_vars['admin_design_html_footer']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Edit Your HTML Content Here {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_design_modal_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_design_modal_heading][label][]" value="Edit Your HTML Content Here" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_design_modal_heading][label][]" value="{$selected_lang_translator_vars['admin_design_modal_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Html Content {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_design_html_content']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_design_html_content][label][]" value="Html Content" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_design_html_content][label][]" value="{$selected_lang_translator_vars['admin_design_html_content']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Add new HTML content {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_design_html_new_content']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_design_html_new_content][label][]" value="Add new HTML content" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_design_html_new_content][label][]" value="{$selected_lang_translator_vars['admin_design_html_new_content']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Edit this HTML content {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_design_html_edit_content']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_design_html_edit_content][label][]" value="Edit this HTML content" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_design_html_edit_content][label][]" value="{$selected_lang_translator_vars['admin_design_html_edit_content']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Remove this HTML content {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_design_html_rem_content']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_design_html_rem_content][label][]" value="Remove this HTML content" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_design_html_rem_content][label][]" value="{$selected_lang_translator_vars['admin_design_html_rem_content']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                            <br><br>
                                                                            <h4 class='velsof-tab-heading'>{l s='Themer' mod='supercheckout'}</h4>
                                                                            <table class="form alternate">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <th class="name vertical_top_align" style="text-align: right;">{$current_lang_translator_vars['admin_lang_translate']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                        <th class="velsof-english">{$current_lang_translator_vars['admin_lang_from']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                        <th class="left" style="font-size:14px;">{$current_lang_translator_vars['admin_lang_to']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Themer {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_themer_title']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_themer_title][label][]" value="Themer" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_themer_title][label][]" value="{$selected_lang_translator_vars['admin_themer_title']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Theme {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_themer_title1']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_themer_title1][label][]" value="Theme" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_themer_title1][label][]" value="{$selected_lang_translator_vars['admin_themer_title1']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Get LESS {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_themer_less']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_themer_less][label][]" value="Get LESS" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_themer_less][label][]" value="{$selected_lang_translator_vars['admin_themer_less']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Get CSS {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_themer_css']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_themer_css][label][]" value="Get CSS" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_themer_css][label][]" value="{$selected_lang_translator_vars['admin_themer_css']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>close {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['admin_themer_close']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[admin_themer_close][label][]" value="close" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[admin_themer_close][label][]" value="{$selected_lang_translator_vars['admin_themer_close']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>                                                                                                                                                                
                                                                        
                                                                        <div id="tab_lang_front_panel" class="tab-pane widget-body-regular {if $ps_version eq 15}vss-lang-tab-pane-ver15{/if}">
                                                                            <table class="form alternate">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <th class="name vertical_top_align" style="text-align: right;">{$current_lang_translator_vars['admin_lang_translate']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                        <th class="velsof-english">{$current_lang_translator_vars['admin_lang_from']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                        <th class="left" style="font-size:14px;">{$current_lang_translator_vars['admin_lang_to']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Login heading {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_loginoption_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_loginoption_heading][label][]" value="Login Options" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_loginoption_heading][label][]" value="{$selected_lang_translator_vars['front_loginoption_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Email {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_email_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_email_heading][label][]" value="Email" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_email_heading][label][]" value="{$selected_lang_translator_vars['front_email_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Password {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_password_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_password_heading][label][]" value="Password" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_password_heading][label][]" value="{$selected_lang_translator_vars['front_password_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Registered Users {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_register_user_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_register_user_heading][label][]" value="Registered Users" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_register_user_heading][label][]" value="{$selected_lang_translator_vars['front_register_user_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Guest Users {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_guest_user_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_guest_user_heading][label][]" value="Guest Users" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_guest_user_heading][label][]" value="{$selected_lang_translator_vars['front_guest_user_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Social Login {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_social_login_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_social_login_heading][label][]" value="Social Login" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_social_login_heading][label][]" value="{$selected_lang_translator_vars['front_social_login_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Sign in with {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_signinwith_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_signinwith_heading][label][]" value="Sign in with" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_signinwith_heading][label][]" value="{$selected_lang_translator_vars['front_signinwith_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Login into shop {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_widlogin_checkout_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_widlogin_checkout_heading][label][]" value="Login into shop" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_widlogin_checkout_heading][label][]" value="{$selected_lang_translator_vars['front_widlogin_checkout_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Guest Checkout {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_guest_checkout_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_guest_checkout_heading][label][]" value="Guest Checkout" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_guest_checkout_heading][label][]" value="{$selected_lang_translator_vars['front_guest_checkout_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>New Account {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_newaccount_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_newaccount_heading][label][]" value="Create an account for later use" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_newaccount_heading][label][]" value="{$selected_lang_translator_vars['front_newaccount_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>OR {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_OR_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_OR_heading][label][]" value="OR" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_OR_heading][label][]" value="{$selected_lang_translator_vars['front_OR_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Welcome {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_welcome_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_welcome_heading][label][]" value="Welcome" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_welcome_heading][label][]" value="{$selected_lang_translator_vars['front_welcome_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>My Account {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_myaccount_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_myaccount_heading][label][]" value="My Account" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_myaccount_heading][label][]" value="{$selected_lang_translator_vars['front_myaccount_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Logout {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_logout_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_logout_heading][label][]" value="Logout" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_logout_heading][label][]" value="{$selected_lang_translator_vars['front_logout_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Forgot Password {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_forgot_password']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_forgot_password][label][]" value="Forgot Password" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_forgot_password][label][]" value="{$selected_lang_translator_vars['front_forgot_password']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Confirm Your Order {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_confirmorder_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_confirmorder_heading][label][]" value="Confirm Your Order" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_confirmorder_heading][label][]" value="{$selected_lang_translator_vars['front_confirmorder_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Same invoice address {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_same_invoice']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_same_invoice][label][]" value="Same invoice address" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_same_invoice][label][]" value="{$selected_lang_translator_vars['front_same_invoice']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Male Title {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_male_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_male_heading][label][]" value="Mr." />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_male_heading][label][]" value="{$selected_lang_translator_vars['front_male_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Female Title{$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_female_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_female_heading][label][]" value="Miss." />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_female_heading][label][]" value="{$selected_lang_translator_vars['front_female_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Qty {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_qty_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_qty_heading][label][]" value="Qty" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_qty_heading][label][]" value="{$selected_lang_translator_vars['front_qty_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Apply {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_apply_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_apply_heading][label][]" value="Apply" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_apply_heading][label][]" value="{$selected_lang_translator_vars['front_apply_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Recycle Text {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_front_recycle']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_recycle][label][]" value="I would like to receive my order in recycled packaging." />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_front_recycle][label][]" value="{$selected_lang_translator_vars['misc_front_recycle']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_recycle][label][]" value="order-shipping-extra" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Gift Text {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_front_gift_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_gift_heading][label][]" value="I would like my order to be gift wrapped." />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_front_gift_heading][label][]" value="{$selected_lang_translator_vars['misc_front_gift_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_gift_heading][label][]" value="order-shipping-extra" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Gift Additional Cost {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_front_gift_addcost']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_gift_addcost][label][]" value="Additional cost of" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_front_gift_addcost][label][]" value="{$selected_lang_translator_vars['misc_front_gift_addcost']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_gift_addcost][label][]" value="order-shipping-extra" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Gift Comment Text {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_front_gift_comment']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_gift_comment][label][]" value="If you would like, you can add a note to the gift" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_front_gift_comment][label][]" value="{$selected_lang_translator_vars['misc_front_gift_comment']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_gift_comment][label][]" value="order-shipping-extra" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Update Text {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_front_update']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_update][label][]" value="Update" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_front_update][label][]" value="{$selected_lang_translator_vars['misc_front_update']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Gift {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_front_gift']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_gift][label][]" value="Gift" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_front_gift][label][]" value="{$selected_lang_translator_vars['misc_front_gift']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Total products {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_front_total_products']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_total_products][label][]" value="Total products" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_front_total_products][label][]" value="{$selected_lang_translator_vars['misc_front_total_products']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Gift Wrapping Cost {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_front_gift_wrappingcost']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_gift_wrappingcost][label][]" value="Total gift wrapping cost" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_front_gift_wrappingcost][label][]" value="{$selected_lang_translator_vars['misc_front_gift_wrappingcost']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Shipping {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_front_shipping']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_shipping][label][]" value="Shipping" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_front_shipping][label][]" value="{$selected_lang_translator_vars['misc_front_shipping']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Free Shipping {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_front_free_shipping']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_free_shipping][label][]" value="Free Shipping" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_front_free_shipping][label][]" value="{$selected_lang_translator_vars['misc_front_free_shipping']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Total Shipping {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_front_total_shipping']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_total_shipping][label][]" value="Total Shipping" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_front_total_shipping][label][]" value="{$selected_lang_translator_vars['misc_front_total_shipping']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Total {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_front_total']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_total][label][]" value="Total" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_front_total][label][]" value="{$selected_lang_translator_vars['misc_front_total']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Total Tax {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_front_total_tax']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_total_tax][label][]" value="Total Tax" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_front_total_tax][label][]" value="{$selected_lang_translator_vars['misc_front_total_tax']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Total Vouchers {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_front_total_vouchers']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_total_vouchers][label][]" value="Total Vouchers" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_front_total_vouchers][label][]" value="{$selected_lang_translator_vars['misc_front_total_vouchers']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Vouchers Input {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_front_voucher_input']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_voucher_input][label][]" value="Voucher" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_front_voucher_input][label][]" value="{$selected_lang_translator_vars['misc_front_voucher_input']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>                                                                                    
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Tax incl. {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_front_tax_incl']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_tax_incl][label][]" value="(Tax incl.)" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_front_tax_incl][label][]" value="{$selected_lang_translator_vars['misc_front_tax_incl']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_tax_incl][label][]" value="order-shipping|order-shipping-extra" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Tax excl. {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_front_tax_excl']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_tax_excl][label][]" value="(Tax excl.)" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_front_tax_excl][label][]" value="{$selected_lang_translator_vars['misc_front_tax_excl']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_tax_excl][label][]" value="order-shipping|order-shipping-extra" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Free {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_front_free']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_free][label][]" value="Free" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_front_free][label][]" value="{$selected_lang_translator_vars['misc_front_free']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_front_free][label][]" value="order-shipping" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Comment Box {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_comment_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_comment_heading][label][]" value="Add Comments About Your Order" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_comment_heading][label][]" value="{$selected_lang_translator_vars['front_comment_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Agree T & C {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_agree_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_agree_heading][label][]" value="I agree to the terms of service and will adhere to them unconditionally. " />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_agree_heading][label][]" value="{$selected_lang_translator_vars['front_agree_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_agree_heading][label][]" value="order-shipping-extra" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Read T & C {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_readtc_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_readtc_heading][label][]" value="Read the term of services" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_readtc_heading][label][]" value="{$selected_lang_translator_vars['front_readtc_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_readtc_heading][label][]" value="order-shipping-extra" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Confirm Order Button {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_confirmbtn_heading']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_confirmbtn_heading][label][]" value="Place Order" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_confirmbtn_heading][label][]" value="{$selected_lang_translator_vars['front_confirmbtn_heading']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Breadcrumb {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_breadcrumb_label']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_breadcrumb_label][label][]" value="Your shopping cart" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_breadcrumb_label][label][]" value="{$selected_lang_translator_vars['front_breadcrumb_label']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Use Existing Address {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_useexistaddr_label']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_useexistaddr_label][label][]" value="Use Existing Address" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_useexistaddr_label][label][]" value="{$selected_lang_translator_vars['front_useexistaddr_label']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Use New Address {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_usenewaddr_label']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_usenewaddr_label][label][]" value="Use New Address" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_usenewaddr_label][label][]" value="{$selected_lang_translator_vars['front_usenewaddr_label']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Payment Information {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_payinfo_label']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_payinfo_label][label][]" value="Payment Information" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_payinfo_label][label][]" value="{$selected_lang_translator_vars['front_payinfo_label']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Back {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_back_label']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_back_label][label][]" value="Back" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_back_label][label][]" value="{$selected_lang_translator_vars['front_back_label']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Proceed {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_proceed_label']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_proceed_label][label][]" value="Proceed" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_proceed_label][label][]" value="{$selected_lang_translator_vars['front_proceed_label']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
										    <tr>
                                                                                        <td class="name vertical_top_align"><span>January {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['month_january']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[month_january][label][]" value="January" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[month_january][label][]" value="{$selected_lang_translator_vars['month_january']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
										    <tr>
                                                                                        <td class="name vertical_top_align"><span>February {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['month_february']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[month_february][label][]" value="February" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[month_february][label][]" value="{$selected_lang_translator_vars['month_february']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
										    <tr>
                                                                                        <td class="name vertical_top_align"><span>March {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['month_march']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[month_march][label][]" value="March" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[month_march][label][]" value="{$selected_lang_translator_vars['month_march']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
										    <tr>
                                                                                        <td class="name vertical_top_align"><span>April {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['month_april']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[month_april][label][]" value="April" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[month_april][label][]" value="{$selected_lang_translator_vars['month_april']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
										    <tr>
                                                                                        <td class="name vertical_top_align"><span>May {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['month_may']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[month_may][label][]" value="May" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[month_may][label][]" value="{$selected_lang_translator_vars['month_may']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
										    <tr>
                                                                                        <td class="name vertical_top_align"><span>June {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['month_june']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[month_june][label][]" value="June" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[month_june][label][]" value="{$selected_lang_translator_vars['month_june']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
										    <tr>
                                                                                        <td class="name vertical_top_align"><span>July {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['month_july']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[month_july][label][]" value="July" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[month_july][label][]" value="{$selected_lang_translator_vars['month_july']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
										    <tr>
                                                                                        <td class="name vertical_top_align"><span>August {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['month_august']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[month_august][label][]" value="August" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[month_august][label][]" value="{$selected_lang_translator_vars['month_august']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
										    <tr>
                                                                                        <td class="name vertical_top_align"><span>September {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['month_september']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[month_september][label][]" value="September" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[month_september][label][]" value="{$selected_lang_translator_vars['month_september']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
										    <tr>
                                                                                        <td class="name vertical_top_align"><span>October {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['month_october']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[month_october][label][]" value="October" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[month_october][label][]" value="{$selected_lang_translator_vars['month_october']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
										    <tr>
                                                                                        <td class="name vertical_top_align"><span>November {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['month_november']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[month_november][label][]" value="November" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[month_november][label][]" value="{$selected_lang_translator_vars['month_november']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
										    <tr>
                                                                                        <td class="name vertical_top_align"><span>December {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['month_december']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[month_december][label][]" value="December" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[month_december][label][]" value="{$selected_lang_translator_vars['month_december']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                                        
                                                                        <div id="tab_lang_common" class="tab-pane widget-body-regular {if $ps_version eq 15}vss-lang-tab-pane-ver15{/if}">
                                                                            <h4 class='velsof-tab-heading'>{$current_lang_translator_vars['admin_lang_admin_front_heading']['label'][1]|escape:'htmlall':'UTF-8'}</h4>
                                                                            <table class="form alternate">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <th class="name vertical_top_align" style="text-align: right;">{$current_lang_translator_vars['admin_lang_translate']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                        <th class="velsof-english">{$current_lang_translator_vars['admin_lang_from']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                        <th class="left" style="font-size:14px;">{$current_lang_translator_vars['admin_lang_to']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Customer Personal {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['common_customer_personal']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[common_customer_personal][label][]" value="Customer Personal" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[common_customer_personal][label][]" value="{$selected_lang_translator_vars['common_customer_personal']['label'][1]|escape:'htmlall':'UTF-8'}" />                                                                                            
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Customer Subscription {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['common_customer_subscription']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[common_customer_subscription][label][]" value="Customer Subscription" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[common_customer_subscription][label][]" value="{$selected_lang_translator_vars['common_customer_subscription']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Use Delivery Address as Invoice Address {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['common_use_same_add_opt']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[common_use_same_add_opt][label][]" value="Use Delivery Address as Invoice Address" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[common_use_same_add_opt][label][]" value="{$selected_lang_translator_vars['common_use_same_add_opt']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Invoice Address {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['common_invoice_address']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[common_invoice_address][label][]" value="Invoice Address" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[common_invoice_address][label][]" value="{$selected_lang_translator_vars['common_invoice_address']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Delivery Address {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['common_delivery_address']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[common_delivery_address][label][]" value="Delivery Address" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[common_delivery_address][label][]" value="{$selected_lang_translator_vars['common_delivery_address']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Delivery Method {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['common_delivery_method']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[common_delivery_method][label][]" value="Delivery Method" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[common_delivery_method][label][]" value="{$selected_lang_translator_vars['common_delivery_method']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Payment Method {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['common_payment_method']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[common_payment_method][label][]" value="Payment Method" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[common_payment_method][label][]" value="{$selected_lang_translator_vars['common_payment_method']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Title {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['title']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[title][label][]" value="Title" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[title][label][]" value="{$selected_lang_translator_vars['title']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>First Name {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['firstname']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[firstname][label][]" value="First Name" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[firstname][label][]" value="{$selected_lang_translator_vars['firstname']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Last Name {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['lastname']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[lastname][label][]" value="Last Name" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[lastname][label][]" value="{$selected_lang_translator_vars['lastname']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Date Of Birth {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['dob']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[dob][label][]" value="DOB" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[dob][label][]" value="{$selected_lang_translator_vars['dob']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Sign up for NewsLetter {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['newsletter']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[newsletter][label][]" value="Sign up for NewsLetter" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[newsletter][label][]" value="{$selected_lang_translator_vars['newsletter']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Special Offer {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['optin']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[optin][label][]" value="Special Offer" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[optin][label][]" value="{$selected_lang_translator_vars['optin']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Company {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['company']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[company][label][]" value="Company" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[company][label][]" value="{$selected_lang_translator_vars['company']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Vat Number {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['vat_number']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[vat_number][label][]" value="Vat Number" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[vat_number][label][]" value="{$selected_lang_translator_vars['vat_number']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Address Line 1 {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['address_line_1']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[address_line_1][label][]" value="Address Line 1" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[address_line_1][label][]" value="{$selected_lang_translator_vars['address_line_1']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Address Line 2 {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['address_line_2']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[address_line_2][label][]" value="Address Line 2" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[address_line_2][label][]" value="{$selected_lang_translator_vars['address_line_2']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Zip/Postal Code {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['zip_code']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[zip_code][label][]" value="Zip/Postal Code" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[zip_code][label][]" value="{$selected_lang_translator_vars['zip_code']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>City {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['city']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[city][label][]" value="City" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[city][label][]" value="{$selected_lang_translator_vars['city']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Country {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['country']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[country][label][]" value="Country" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[country][label][]" value="{$selected_lang_translator_vars['country']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>State {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['state']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[state][label][]" value="State" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[state][label][]" value="{$selected_lang_translator_vars['state']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Identification Number {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['dni']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[dni][label][]" value="Identification Number" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[dni][label][]" value="{$selected_lang_translator_vars['dni']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>                                                                                    
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Identification Number Hint {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['dni_hint']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[dni_hint][label][]" value="Identification Hint" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[dni_hint][label][]" value="{$selected_lang_translator_vars['dni_hint']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Home Phone {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['home_phone']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[home_phone][label][]" value="Home Phone" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[home_phone][label][]" value="{$selected_lang_translator_vars['home_phone']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Mobile Phone {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['mobile_phone']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[mobile_phone][label][]" value="Mobile Phone" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[mobile_phone][label][]" value="{$selected_lang_translator_vars['mobile_phone']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Address Title {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['addr_title']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[addr_title][label][]" value="Address Title" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[addr_title][label][]" value="{$selected_lang_translator_vars['addr_title']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Other Information {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['other_information']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[other_information][label][]" value="Other Information" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[other_information][label][]" value="{$selected_lang_translator_vars['other_information']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Description {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['cart_description']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[cart_description][label][]" value="Description" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[cart_description][label][]" value="{$selected_lang_translator_vars['cart_description']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Model {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['cart_model']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[cart_model][label][]" value="Model" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[cart_model][label][]" value="{$selected_lang_translator_vars['cart_model']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Quantity {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['cart_quantity']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[cart_quantity][label][]" value="Quantity" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[cart_quantity][label][]" value="{$selected_lang_translator_vars['cart_quantity']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Price {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['cart_price']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[cart_price][label][]" value="Price" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[cart_price][label][]" value="{$selected_lang_translator_vars['cart_price']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Total {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['cart_total']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[cart_total][label][]" value="Total" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[cart_total][label][]" value="{$selected_lang_translator_vars['cart_total']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                                                                                                                
                                                                                        
                                                                        <div id="tab_lang_messages" class="tab-pane widget-body-regular {if $ps_version eq 15}vss-lang-tab-pane-ver15{/if}">
                                                                            <h4 class='velsof-tab-heading'>{$current_lang_translator_vars['admin_lang_admin_front_heading']['label'][1]|escape:'htmlall':'UTF-8'}</h4>
                                                                            <table class="form alternate">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <th class="name vertical_top_align" style="text-align: right;">{$current_lang_translator_vars['admin_lang_translate']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                        <th class="velsof-english">{$current_lang_translator_vars['admin_lang_from']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                        <th class="left" style="font-size:14px;">{$current_lang_translator_vars['admin_lang_to']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Notification {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_notification_title']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_notification_title][label][]" value="Notification" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_notification_title][label][]" value="{$selected_lang_translator_vars['msg_notification_title']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Warning {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_warning_title']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_warning_title][label][]" value="Warning" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_warning_title][label][]" value="{$selected_lang_translator_vars['msg_warning_title']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Admin Validation Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_admin_validation']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_admin_validation][label][]" value="Please provide required information with valid data." />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_admin_validation][label][]" value="{$selected_lang_translator_vars['msg_admin_validation']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Required Field Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_require_field']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_require_field][label][]" value="Required Field" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_require_field][label][]" value="{$selected_lang_translator_vars['msg_require_field']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Parmission Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_permission_error']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_permission_error][label][]" value="Permission errorred occur for language file creating" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_permission_error][label][]" value="{$selected_lang_translator_vars['msg_permission_error']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Login Request {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_login_rqust']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_login_rqust][label][]" value="Please login first" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_login_rqust][label][]" value="{$selected_lang_translator_vars['msg_login_rqust']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Invalid Email Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_invalid_email_error']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_invalid_email_error][label][]" value="Invalid Email" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_invalid_email_error][label][]" value="{$selected_lang_translator_vars['msg_invalid_email_error']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Invalid DOB Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_invalid_dob_error']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_invalid_dob_error][label][]" value="Invalid date of birth" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_invalid_dob_error][label][]" value="{$selected_lang_translator_vars['msg_invalid_dob_error']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Customer Existence Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_exist_email_error']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_exist_email_error][label][]" value="This customer is already exist" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_exist_email_error][label][]" value="{$selected_lang_translator_vars['msg_exist_email_error']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Invalid Password Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_invalid_pass_error']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_invalid_pass_error][label][]" value="Invalid Password" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_invalid_pass_error][label][]" value="{$selected_lang_translator_vars['msg_invalid_pass_error']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Invalid Phone Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_invalid_phone_error']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_invalid_phone_error][label][]" value="Invalid Phone Number" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_invalid_phone_error][label][]" value="{$selected_lang_translator_vars['msg_invalid_phone_error']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Invalid Zip Code Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_invalid_zip_error']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_invalid_zip_error][label][]" value="Invalid Zip Code" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_invalid_zip_error][label][]" value="{$selected_lang_translator_vars['msg_invalid_zip_error']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>DNI Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_add_dni_error']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_add_dni_error][label][]" value="DNI Error" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_add_dni_error][label][]" value="{$selected_lang_translator_vars['msg_add_dni_error']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Address Title Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_add_title_error']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_add_title_error][label][]" value="This title has already taken" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_add_title_error][label][]" value="{$selected_lang_translator_vars['msg_add_title_error']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Zip Code Hint {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_zip_hint']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_zip_hint][label][]" value="Must be typed as follows:" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_zip_hint][label][]" value="{$selected_lang_translator_vars['msg_zip_hint']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Authentication Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_authentication_error']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_authentication_error][label][]" value="Authentication failed" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_authentication_error][label][]" value="{$selected_lang_translator_vars['msg_authentication_error']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Connection fail with social site Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_connect_social_error']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_connect_social_error][label][]" value="Authentication failed" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_connect_social_error][label][]" value="{$selected_lang_translator_vars['msg_connect_social_error']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Login fail with social site Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_login_social_error']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_login_social_error][label][]" value="Not able to login with social site" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_login_social_error][label][]" value="{$selected_lang_translator_vars['msg_login_social_error']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Voucher Required Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_voucher_required_error']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_voucher_required_error][label][]" value="You must enter a voucher code" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_voucher_required_error][label][]" value="{$selected_lang_translator_vars['msg_voucher_required_error']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Invalid Voucher invalid Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_invalid_voucher_error']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_invalid_voucher_error][label][]" value="The voucher code is invalid" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_invalid_voucher_error][label][]" value="{$selected_lang_translator_vars['msg_invalid_voucher_error']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Voucher Feature not active Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_voucher_feature_error']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_voucher_feature_error][label][]" value="This feature is not active for this voucher" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_voucher_feature_error][label][]" value="{$selected_lang_translator_vars['msg_voucher_feature_error']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Voucher Limit Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_invalid_voucher_limit_error']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_invalid_voucher_limit_error][label][]" value="Vouhcer has been already used" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_invalid_voucher_limit_error][label][]" value="{$selected_lang_translator_vars['msg_invalid_voucher_limit_error']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Voucher Remove Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_voucher_remove_fail']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_voucher_remove_fail][label][]" value="Error occured while removing voucher" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_voucher_remove_fail][label][]" value="{$selected_lang_translator_vars['msg_voucher_remove_fail']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Voucher Success {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_voucher_success']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_voucher_success][label][]" value="Voucher successfully applied" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_voucher_success][label][]" value="{$selected_lang_translator_vars['msg_voucher_success']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Voucher Remove {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_voucher_remove_success']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_voucher_remove_success][label][]" value="Voucher successfully removed" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_voucher_remove_success][label][]" value="{$selected_lang_translator_vars['msg_voucher_remove_success']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Setting Saved Message {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_admin_setting_save']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_admin_setting_save][label][]" value="Settings has been updated successfully" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_admin_setting_save][label][]" value="{$selected_lang_translator_vars['msg_admin_setting_save']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Language Translate Message {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_admin_lang_translate']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_admin_lang_translate][label][]" value="Language successfully translated" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_admin_lang_translate][label][]" value="{$selected_lang_translator_vars['msg_admin_lang_translate']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>The Best Price & Speed {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_best_ps']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_best_ps][label][]" value="The best price and speed" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_best_ps][label][]" value="{$selected_lang_translator_vars['msg_best_ps']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_best_ps][label][]" value="order-shipping" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>The Fastest {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_fastest']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_fastest][label][]" value="The Fastest" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_fastest][label][]" value="{$selected_lang_translator_vars['msg_fastest']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_fastest][label][]" value="order-shipping" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>The Best Price {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_best_p']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_best_p][label][]" value="The Best Price" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_best_p][label][]" value="{$selected_lang_translator_vars['msg_best_p']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_best_p][label][]" value="order-shipping" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>No Delivery Required {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_no_shipping_required']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_no_shipping_required][label][]" value="No Delivery Method Required" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_no_shipping_required][label][]" value="{$selected_lang_translator_vars['msg_no_shipping_required']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_no_shipping_required][label][]" value="order-shipping" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Delivery Method Required {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_shipping_required']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_shipping_required][label][]" value="Delivery Method Required" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_shipping_required][label][]" value="{$selected_lang_translator_vars['msg_shipping_required']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>No Delivery Available {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_no_ship_avail']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_no_ship_avail][label][]" value="No Delivery Method Available" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_no_ship_avail][label][]" value="{$selected_lang_translator_vars['msg_no_ship_avail']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_no_ship_avail][label][]" value="order-shipping" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>No Delivery Available for Address {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_no_ship_avail_addr']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_no_ship_avail_addr][label][]" value="No Delivery Method Available for this Address" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_no_ship_avail_addr][label][]" value="{$selected_lang_translator_vars['msg_no_ship_avail_addr']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_no_ship_avail_addr][label][]" value="order-shipping" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Payment Method Required {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_payment_require']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_payment_require][label][]" value="Payment Method Required" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_payment_require][label][]" value="{$selected_lang_translator_vars['msg_payment_require']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Product Remove Success {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_prod_remove_succes']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_prod_remove_succes][label][]" value="Products successfully removed" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_prod_remove_succes][label][]" value="{$selected_lang_translator_vars['msg_prod_remove_succes']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Product Quantity Update Warning {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_prod_qty_update_warning']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_prod_qty_update_warning][label][]" value="No change found in quantity" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_prod_qty_update_warning][label][]" value="{$selected_lang_translator_vars['msg_prod_qty_update_warning']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Invalid Quantity {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_prod_qty_invalid']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_prod_qty_invalid][label][]" value="Invalid Quantity" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_prod_qty_invalid][label][]" value="{$selected_lang_translator_vars['msg_prod_qty_invalid']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Product Quantity Update Success {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_prod_qty_update_success']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_prod_qty_update_success][label][]" value="Products quantity successfully updated" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_prod_qty_update_success][label][]" value="{$selected_lang_translator_vars['msg_prod_qty_update_success']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Cart Empty {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_cart_empty']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_cart_empty][label][]" value="Your shopping cart is empty." />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_cart_empty][label][]" value="{$selected_lang_translator_vars['msg_cart_empty']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Message Invalid {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_comment_invalid']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_comment_invalid][label][]" value="Message is in invalid format" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_comment_invalid][label][]" value="{$selected_lang_translator_vars['msg_comment_invalid']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Address is not yours {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_add_is_not_your']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_add_is_not_your][label][]" value="This address is not yours" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_add_is_not_your][label][]" value="{$selected_lang_translator_vars['msg_add_is_not_your']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Address invalid area {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_add_invalid_area']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_add_invalid_area][label][]" value="This address is not in a valid area" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_add_invalid_area][label][]" value="{$selected_lang_translator_vars['msg_add_invalid_area']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Address invalid {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_add_invalid']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_add_invalid][label][]" value="This address is invalid" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_add_invalid][label][]" value="{$selected_lang_translator_vars['msg_add_invalid']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Cart Updating Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_cart_update_err']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_cart_update_err][label][]" value="An error occurred while updating your cart" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_cart_update_err][label][]" value="{$selected_lang_translator_vars['msg_cart_update_err']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Accept T & C Required {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_tos_require']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_tos_require][label][]" value="Please acccept our terms & conditions before confirming your order" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_tos_require][label][]" value="{$selected_lang_translator_vars['msg_tos_require']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Address Title Default Value: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_addr_alias']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_addr_alias][label][]" value="Title Delivery Alias" />
                                                                                            <input type="text" class="translator_input_width" maxlength='27' name="velocity_transalator[msg_addr_alias][label][]" value="{$selected_lang_translator_vars['msg_addr_alias']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Address create Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_addr_create_err']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_addr_create_err][label][]" value="Error occurred while creating new address" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_addr_create_err][label][]" value="{$selected_lang_translator_vars['msg_addr_create_err']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Address Update Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_addr_update_err']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_addr_update_err][label][]" value="Error occurred while updating address" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_addr_update_err][label][]" value="{$selected_lang_translator_vars['msg_addr_update_err']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Customer Email Send Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_acc_create_send_email_err']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_acc_create_send_email_err][label][]" value="An error ocurred while sending account confirmation email" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_acc_create_send_email_err][label][]" value="{$selected_lang_translator_vars['msg_acc_create_send_email_err']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Information Request Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['msg_requst_tech_err']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[msg_requst_tech_err][label][]" value="TECHNICAL ERROR: Request Failed" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[msg_requst_tech_err][label][]" value="{$selected_lang_translator_vars['msg_requst_tech_err']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                                        
                                                                        <div id="tab_lang_misc" class="tab-pane widget-body-regular {if $ps_version eq 15}vss-lang-tab-pane-ver15{/if}">
                                                                            <h4 class='velsof-tab-heading'>{$current_lang_translator_vars['admin_lang_admin_panel_heading']['label'][1]|escape:'htmlall'|escape:'htmlall':'UTF-8'}</h4>
                                                                            <table class="form alternate">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <th class="name vertical_top_align" style="text-align: right;">{$current_lang_translator_vars['admin_lang_translate']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                        <th class="velsof-english">{$current_lang_translator_vars['admin_lang_from']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                        <th class="left" style="font-size:14px;">{$current_lang_translator_vars['admin_lang_to']['label'][1]|escape:'htmlall':'UTF-8'}</th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>General Settings {$label|escape:'htmlall'|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_tab_general']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_tab_general][label][]" value="General Settings" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_tab_general][label][]" value="{$selected_lang_translator_vars['misc_tab_general']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Login {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_tab_login']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_tab_login][label][]" value="Login" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_tab_login][label][]" value="{$selected_lang_translator_vars['misc_tab_login']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Addresses {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_tab_addresses']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_tab_addresses][label][]" value="Addresses" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_tab_addresses][label][]" value="{$selected_lang_translator_vars['misc_tab_addresses']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Cart {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_tab_cart']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_tab_cart][label][]" value="Cart" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_tab_cart][label][]" value="{$selected_lang_translator_vars['misc_tab_cart']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Design {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_tab_design']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_tab_design][label][]" value="Design" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_tab_design][label][]" value="{$selected_lang_translator_vars['misc_tab_design']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Language Translator {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_tab_lang']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_tab_lang][label][]" value="Language Translator" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_tab_lang][label][]" value="{$selected_lang_translator_vars['misc_tab_lang']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Admin Labels {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_tab_admin_label']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_tab_admin_label][label][]" value="Admin Labels" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_tab_admin_label][label][]" value="{$selected_lang_translator_vars['misc_tab_admin_label']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Front Labels {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_tab_front_label']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_tab_front_label][label][]" value="Front Labels" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_tab_front_label][label][]" value="{$selected_lang_translator_vars['misc_tab_front_label']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Common Labels {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_tab_common_label']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_tab_common_label][label][]" value="Common Labels" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_tab_common_label][label][]" value="{$selected_lang_translator_vars['misc_tab_common_label']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Miscellaneous {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_tab_miscellaneous']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_tab_miscellaneous][label][]" value="Miscellaneous" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_tab_miscellaneous][label][]" value="{$selected_lang_translator_vars['misc_tab_miscellaneous']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Mandatory Label {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_mandatory']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_mandatory][label][]" value="(*) are mandatory fields" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_mandatory][label][]" value="{$selected_lang_translator_vars['misc_mandatory']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Guest Customer {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_gst_customer']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_gst_customer][label][]" value="Guest Customer" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_gst_customer][label][]" value="{$selected_lang_translator_vars['misc_gst_customer']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Logged in Customer {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_logged_customer']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_logged_customer][label][]" value="Logged in Customer" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_logged_customer][label][]" value="{$selected_lang_translator_vars['misc_logged_customer']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Require {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_require']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_require][label][]" value="Require" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_require][label][]" value="{$selected_lang_translator_vars['misc_require']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Show {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_show']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_show][label][]" value="Show" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_show][label][]" value="{$selected_lang_translator_vars['misc_show']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Show as Checked {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['misc_show_as_checked']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[misc_show_as_checked][label][]" value="Show as Checked" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[misc_show_as_checked][label][]" value="{$selected_lang_translator_vars['misc_show_as_checked']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Save Button {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['save_btn']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[save_btn][label][]" value="Save" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[save_btn][label][]" value="{$selected_lang_translator_vars['save_btn']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Cancel Button {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['cancel_btn']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[cancel_btn][label][]" value="Cancel" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[cancel_btn][label][]" value="{$selected_lang_translator_vars['cancel_btn']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Download Button {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['download_btn']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[download_btn][label][]" value="Download" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[download_btn][label][]" value="{$selected_lang_translator_vars['download_btn']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Save & Download Button {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['save_download_btn']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[save_download_btn][label][]" value="Save & Download" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[save_download_btn][label][]" value="{$selected_lang_translator_vars['save_download_btn']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Require {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['req_lbl']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[req_lbl][label][]" value="Require" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[req_lbl][label][]" value="{$selected_lang_translator_vars['req_lbl']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Only Text {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['only_text_lbl']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[only_text_lbl][label][]" value="Only Text" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[only_text_lbl][label][]" value="{$selected_lang_translator_vars['only_text_lbl']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Only Image {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['only_image_lbl']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[only_image_lbl][label][]" value="Only Image" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[only_image_lbl][label][]" value="{$selected_lang_translator_vars['only_image_lbl']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Text with Image {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['txt_wid_image_lbl']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[txt_wid_image_lbl][label][]" value="Text with Image" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[txt_wid_image_lbl][label][]" value="{$selected_lang_translator_vars['txt_wid_image_lbl']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Note {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['note_lbl']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[note_lbl][label][]" value="Note" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[note_lbl][label][]" value="{$selected_lang_translator_vars['note_lbl']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Symbol Replace Note {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['symbol_replace_note']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[symbol_replace_note][label][]" value="Do not remove %s symbol" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[symbol_replace_note][label][]" value="{$selected_lang_translator_vars['symbol_replace_note']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Address field warning {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['add_display_uncheck_msg']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[add_display_uncheck_msg][label][]" value="You cannot uncheck this field due to required field" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[add_display_uncheck_msg][label][]" value="{$selected_lang_translator_vars['add_display_uncheck_msg']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Other Warning {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['other_warning_1']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[other_warning_1][label][]" value="This field will require on the basis of store configuration" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[other_warning_1][label][]" value="{$selected_lang_translator_vars['other_warning_1']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Other Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['other_error_1']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[other_error_1][label][]" value="Technical Error Occured. Please contact to support." />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[other_error_1][label][]" value="{$selected_lang_translator_vars['other_error_1']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Please provide required Information {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['other_validate_msg']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[other_validate_msg][label][]" value="Please provide required Information" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[other_validate_msg][label][]" value="{$selected_lang_translator_vars['other_validate_msg']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Email Address Required {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_validation_msg1']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_validation_msg1][label][]" value="An email address required." />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_validation_msg1][label][]" value="{$selected_lang_translator_vars['front_validation_msg1']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Email Address Invalid {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_validation_msg2']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_validation_msg2][label][]" value="Invalid email address." />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_validation_msg2][label][]" value="{$selected_lang_translator_vars['front_validation_msg2']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Password Required {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_validation_msg3']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_validation_msg3][label][]" value="Password is required." />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_validation_msg3][label][]" value="{$selected_lang_translator_vars['front_validation_msg3']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>State Required for Country {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_validation_msg4']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_validation_msg4][label][]" value="This country requires you to chose a State" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_validation_msg4][label][]" value="{$selected_lang_translator_vars['front_validation_msg4']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>                                                                                    
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Country not Active {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_validation_msg5']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_validation_msg5][label][]" value="This country is not active" />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_validation_msg5][label][]" value="{$selected_lang_translator_vars['front_validation_msg5']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>                                                                                    
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Account Creation Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_validation_msg6']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_validation_msg6][label][]" value="An error occurred while creating your account." />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_validation_msg6][label][]" value="{$selected_lang_translator_vars['front_validation_msg6']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>                                                                                    
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>Login Authentication Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_validation_msg7']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_validation_msg7][label][]" value="Authentication failed." />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_validation_msg7][label][]" value="{$selected_lang_translator_vars['front_validation_msg7']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="name vertical_top_align"><span>No Payment Methods Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_validation_msg8']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_validation_msg8][label][]" value="No payment modules have been installed." />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_validation_msg8][label][]" value="{$selected_lang_translator_vars['front_validation_msg8']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
										    <tr>
                                                                                        <td class="name vertical_top_align"><span>No Shipping Method Selected Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_validation_msg9']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_validation_msg9][label][]" value="No Shipping Method Selected." />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_validation_msg9][label][]" value="{$selected_lang_translator_vars['front_validation_msg9']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
										    <tr>
                                                                                        <td class="name vertical_top_align"><span>Minimum Purchase Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_validation_msg10']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_validation_msg10][label][]" value="A minimum purchase total of %1s (tax excl.) is required in order to validate your order, current purchase is %2s (tax excl.)." />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_validation_msg10][label][]" value="{$selected_lang_translator_vars['front_validation_msg10']['label'][1]|escape:'htmlall':'UTF-8'}" />
                                                                                        </td>
                                                                                    </tr>
										    <tr>
                                                                                        <td class="name vertical_top_align"><span>No Payment Method Required Error {$label|escape:'htmlall':'UTF-8'}: </span></td>
                                                                                        <td class="velsof-english">{$current_lang_translator_vars['front_validation_msg11']['label'][1]|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="left">
                                                                                            <input type="hidden" class="translator_input_width" name="velocity_transalator[front_validation_msg11][label][]" value="No payment method required." />
                                                                                            <input type="text" class="translator_input_width" name="velocity_transalator[front_validation_msg11][label][]" value="{$selected_lang_translator_vars['front_validation_msg11']['label'][1]|escape:'htmlall':'UTF-8'}" />

                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                            <br>
                                                                        </div>
                                                                                        
                                                                    </div>
                                                                </div>
                                                            </div>     
                                                        </div>
                                                    </div>

                                                    <!--------------- End - Language Translator -------------------->
                                                    
                                                    
                                                    <!--------------- Start - Frequently Asked Questions -------------------->
                                                                                                        
                                                    <div id="tab_faq" class="tab-pane outsideform">
                                                        <div class="block">
                                                            <h4 class='velsof-tab-heading'>{l s='Frequently Asked Questions (Click to expand)' mod='supercheckout'}</h4>
                                                            <br>
                                                            
									    <div class="row faq-row" id="1">
                                                                <div class="span faq-span" id="faq-span1">
                                                                    <p style="margin-bottom: 0; margin-right: 5px">
                                                                        <span class="question" style="font-weight: bold; font-size: 15px;">1. Need to change small icons next to "login options, delivery address, delivery method, payment method, confirm your order heading" in front end?</span><br><br>
                                                                        <span class="answer" id="answer1" style="color: black;">
									    To change those icons, replace those imaes in following directory on your server.<br>
									    /modules/supercheckout/views/img/front/
                                                                        </span>
                                                                    </p>
                                                                </div>
                                                            </div>
							    
							    
                                                            <div class="row faq-row" id="2">
                                                                <div class="span faq-span" id="faq-span2">
                                                                    <p style="margin-bottom: 0; margin-right: 5px">
                                                                        <span class="question" style="font-weight: bold; font-size: 15px;">2. Radio buttons for both Mr and Mrs. is always checked?</span><br><br>
                                                                        <div class="answer" id="answer2" style="color:black;"> If both Mr and Mrs. radio buttons are always checked, then add below code in custom css field of our module customizer tab to fix the issue.
									    <br> <br><pre>
#customer_person_information_table div.radio input {
opacity: 99999;
position: relative !important;
margin: 0px !important;
}
</pre></div>
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            

                                                            <div class="row faq-row" id="3">
                                                                <div class="span faq-span" id="faq-span3">
                                                                    <p style="margin-bottom: 0; margin-right: 5px">
                                                                        <span class="question" style="font-weight: bold; font-size: 15px;">3. Third column is not correctly aligned or full width issue in Desktop?</span><br><br>
                                                                        <div class="answer" id="answer3" style="color: black;">
                                                                        Most probably your theme template CSS is conflicting with our module. Fix for this issue is very simple. Kindly add following code in Custom CSS field in Customizer tab of our module admin setting.<br>
                                                                        <br><pre>
#columnleft-3{
width:28% !important;  
}
<br>
OR
<br>
#center_column{
width:100% !important;  
}									
</pre><br>
                                                                        In case your issue is not solved, try changing this percentage to suit your theme otherwise <a target="_blank" href="https://addons.prestashop.com/en/write-to-developper?id_product=18016">contact us</a> with admin and FTP login details.
                                                                        </div>
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <div class="row faq-row" id="4">
                                                                <div class="span faq-span" id="faq-span4">
                                                                    <p style="margin-bottom: 0; margin-right: 5px">
                                                                        <span class="question" style="font-weight: bold; font-size: 15px;">4. Want to add an extra field in address form?</span><br><br>
                                                                        <span class="answer" id="answer4" style="color: black;">
                                                                        By default it is not possible to add custom field in our module, if you wish we can make this custom change for you for additional cost. Kindly <a target="_blank" href="https://addons.prestashop.com/en/write-to-developper?id_product=18016">contact us </a> with your complete requirements.
                                                                        </span>
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <div class="row faq-row" id="5">
                                                                <div class="span faq-span" id="faq-span5">
                                                                    <p style="margin-bottom: 0; margin-right: 5px">
                                                                        <span class="question" style="font-weight: bold; font-size: 15px;">5.  Some third party module is not working?</span><br><br>
                                                                        <span class="answer" id="answer5" style="color: black;">
                                                                        Third party modules are only made for default checkout of Prestashop. They may or may not work with our module. In case they are not working with our module, some custom changes need to be made to make them compatible with our module.
                                                                        </span>
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <div class="row faq-row" id="6">
                                                                <div class="span faq-span" id="faq-span6">
                                                                    <p style="margin-bottom: 0; margin-right: 5px">
                                                                        <span class="question" style="font-weight: bold; font-size: 15px;">6. Want us to implement some specific feature for additional cost?</span><br><br>
                                                                        <span class="answer" id="answer6" style="color: black;">
                                                                        Yes, you can <a target="_blank" href="https://addons.prestashop.com/en/write-to-developper?id_product=18016">contact us</a> with complete requirements. If changes are feasible, we can implement them for additional cost.
                                                                        </span>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            
                                                           <div class="row faq-row" id="7">
                                                                <div class="span faq-span" id="faq-span7">
                                                                    <p style="margin-bottom: 0; margin-right: 5px">
                                                                        <span class="question" style="font-size: 15px;font-weight: bold; font-size: 15px;">7. Facing any of these issues ?
									</span>
									    <div class="answer" id="answer7" style="color:black;">
										    <br><pre>TECHNICAL ERROR: Request Failed Details:Error thrown: [object Object]Text status: error</pre>
										<pre>500 Internal Server error</pre>
										<pre>Progress Bar stuck on 80% after click on Place order</pre>
                                                                          Reason for these errors are not specific. If you are facing any of these issues, kindly <a target="_blank" href="https://addons.prestashop.com/en/write-to-developper?id_product=18016">contact us</a> with your admin and FTP details.
                                                                        </div>
                                                                    </p>
                                                                </div>
                                                            </div>
							    
							    <div class="row faq-row" id="8">
                                                                <div class="span faq-span" id="faq-span8">
                                                                    <p style="margin-bottom: 0; margin-right: 5px">
                                                                        <span class="question" style="font-weight: bold; font-size: 15px;">8. Payment method is not displaying additinal cost?</span><br><br>
                                                                        <span class="answer" id="answer8" style="color: black;">
                                                                       It is very rare issue and in case you face it ,kindly <a target="_blank" href="https://addons.prestashop.com/en/write-to-developper?id_product=18016">contact us</a> with your admin and FTP login details so that we can fix this issue for you.
                                                                        </span>
                                                                    </p>
                                                                </div>
                                                            </div>
							    
                                                            <div class="row faq-row" id="9">
                                                                <div class="span faq-span" id="faq-span9">
                                                                    <p style="margin-bottom: 0; margin-right: 5px">
                                                                        <span class="question" style="font-weight: bold; font-size: 15px;">9. Translated text is not reflecting in front-side?</span><br><br>
                                                                        <span class="answer" id="answer9" style="color: black;">
                                                                            Kindly try again after clearing your Prestashop cache using Advance Parameter->Performance->Clear cache button. If your issue persists even after that, make sure that your theme directory don't contain our module translation file.
                                                                            To check this, go to your theme directory 
                                                                            /your_theme_name/modules/ . Inside this modules directory, there should no Supercheckout directory, in case it exist just rename it to anything else.<br><br>
                                                                            When you translate text from our module admin panel, our module save translated text in /modules/supercheckout/translations/ directory.
                                                                            But when there is some translation exist in your theme directory, our module picks text from there and your translated text don't reflect in front side.<br>
                                                                            <br>
                                                                            Now question arise, in which case theme directory can have our module translated text file.<br>
                                                                            When you use default translation feature of Prestashop, then it save translated text in theme directory and our module use text from this themes directory rather than using text from module directory.
                                                                        </span>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="row faq-row" id="10">
                                                                <div class="span faq-span" id="faq-span10">
                                                                    <p style="margin-bottom: 0; margin-right: 5px">
                                                                        <span class="question" style="font-size: 15px;font-weight: bold; font-size: 15px;">10. Payment method image is not coming?<br><br></span><span class="answer" id="answer10" style="color: black;">
                                                                          Our module shows payment methods images from their root directory ("/modules/payment_method_name"). If some payment method don't have any image in their root directory then no image will be shown.<br><br>
                                                                          To display that payment method image, kindly upload image to the payment module directory. Image name should be same as payment method directory name. You can use any image format eg. jpg, png etc. 
                                                                          <br><br>For example: To display Iupay payment module image, you need to add its image in "/modules/iupay/" directory and image name must be iupay. Image extension can be anything and recommended image resolution is 95x20.<br><br> Don't hesistate to <a target="_blank" href="https://addons.prestashop.com/en/write-to-developper?id_product=18016">contact us</a> if you need any assistance from our side.
                                                                        </span>
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <div class="row faq-row" id="11">
                                                                <div class="span faq-span" id="faq-span11">
                                                                    <p style="margin-bottom: 0; margin-right: 5px">
                                                                        <span class="question" style="font-size: 15px;font-weight: bold; font-size: 15px;">11. VAT and DNI fields are not coming in front-end?<br><br></span><span class="answer" id="answer11" style="color: black;">
                                                                VAT field<br>
                                                                To show this field, "European VAT number v1.7.2 - by PrestaShop" module must be installed on your store. It is a free module and by default is available with prestashop installation. You can configure it to show VAT field for any specific country.
                                                                <br><br>
                                                                DNI field<br>
                                                                To show this field, go to Localization->Countries->Edit your country and enable "Identification Number" field from there.          
                                                                        </span>
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <div class="row faq-row" id="12">
                                                                <div class="span faq-span" id="faq-span12">
                                                                    <p style="margin-bottom: 0; margin-right: 5px">
                                                                        <span class="question" style="font-size: 15px;font-weight: bold; font-size: 15px;">12. In front end when you click login or place order button nothing happens?<br><br></span><span class="answer" id="answer12" style="color: black;">
                                                                         To fix this issue, Under Advance parameters->performance->Turn ON "Move Javascript to the end". Don't forget to clear Prestashop cache using Advance Parameter->Performance->Clear cache button.<br>
                                                                         <br>In case your issue persists, <a target="_blank" href="https://addons.prestashop.com/en/write-to-developper?id_product=18016">contact us</a> with your admin and FTP details.
                                                                        </span>
                                                                    </p>
                                                                </div>
                                                            </div>
							    
                                                            <div class="row faq-row" id="13">
                                                                <div class="span faq-span" id="faq-span13">
                                                                    <p style="margin-bottom: 0; margin-right: 5px">
                                                                        <div class="question" style="font-size: 15px;font-weight: bold; font-size: 15px;">13. How to translate custom HTML header/footer content?<br><br></div><div class="answer" id="answer13" style="color: black;">
										In order to translate custom HTML header/footer you have to add HTML content (in Custom HTML header or footer field in design tab) for all the languages as follows:<br><br>
									 <pre>
&lt;div id="LANGISO1_content" style="display: none;"&gt;Your HTML content for the language&lt;/div&gt;

&lt;div id="LANGISO2_content" style="display: none;"&gt;Your HTML content for the language&lt;/div&gt;
	.
	.
	.
&lt;div id="LANGISOn_content" style="display: none;"&gt;Your HTML content for the language&lt;/div&gt;
									 </pre>
                                                                        </div>
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            

                                                        </div>    
                                                    </div>

                                                    <!--------------- End - Frequently Asked Questions -------------------->
                                                    
                                                    <!--------------- Start - Suggestions Tab -------------------->
                                                                                                        
                                                    <div id="tab_suggest" class="tab-pane outsideform">
                                                        <div class="block">
                                                            <h4 class='velsof-tab-heading'>{l s='Suggestions' mod='supercheckout'}</h4>
                                                        <div style= "  text-align:center;padding: 25px; height:140px;margin: 40px;margin-bottom:0px; background: aliceblue;{if $ps_version eq 15}height: 100px;{/if}">
                                                        <div><span style="font-size:18px;" >Want us to include some feature in next version of this module?</span>
                                                        <br>
                                                        <br>
                                                         <a target="_blank" href="http://addons.prestashop.com/ratings.php"><span style="margin-left:30%;max-width:40% !important;font-size:18px;" class='btn btn-block btn-success action-btn'>Share your idea</span></a><div>
                                                            </div>
                                                              
                                                   </div>
                                                  </div>
                                                  <div style="margin: 40px;border: 1px solid;color: rgb(240, 29, 53);padding: 15px;padding-top: 0px;"><br>*** If you like our module, don't forget to give us 5 STAR rating on the above link. This will definitely boost our morale.
						</div>          
						<div style="margin:40px;border:1px solid;">
						<p style="font-size: 18px;font-weight:600;border-bottom: 1px solid #000;padding: 5px;text-align: center;background-color: aliceblue;{if $ps_version eq 15}margin:0px;{/if}" >Features that we have added till yet based upon our customers suggestions.</p>
						<ol style="font-size:16px;{if $ps_version eq 15}padding-left: 35px;padding-top: 10px;{/if}" {if $ps_version eq 15}class="sug-ol"{/if}>
							<li style="padding-bottom:5px;"> Customizable product compatibility <i style="color:rgb(237, 30, 121);">- by Massimiliano, Italy</i></li>
							<li style="padding-bottom:5px;"> Prestashop's Date of delivery module compatibility <i style="color:rgb(237, 30, 121);">- by Massimiliano, Italy</i></li>
							<li style="padding-bottom:5px;"> Popup when customer click on Facebook or Google login buttons <i style="color:rgb(237, 30, 121);">- by Elena Perrone, Ukraine</i></li>
							<li style="padding-bottom:5px;"> Option to add custom CSS in front end <i style="color:rgb(237, 30, 121);">- by Keith, United Kingdom</i></li>
                                                        <li style="padding-bottom:5px;"> Option to change Button colors <i style="color:rgb(237, 30, 121);">- by Guru, Singapore</i></li>
							<li style="padding-bottom:5px;"> Several other Payment methods compatibility <i style="color:rgb(237, 30, 121);">- can't mention 30+ names here ;) </i></li>
						</ol>
						<span style="font-size:16px;padding-left:40px;">Thanks to all, as you helped us improve this module by sharing your ideas and pointing out bugs.</span><br/><br/><span style="font-size:16px;padding-left:40px;">Regards,</span><br/><span style="font-size:16px;padding-left:40px;">Knowband Team<br/><br/></span>
						</div>
						<!--------------- End - Suggestions Tab -------------------->
                                                    
                                                    
                                                </div>
                                            </div>
                                        
                                    </div>
                                </div>

                            </div>
                        </div>                    
                </div>
            </div>          
        </div>
                                                                
        <!-- Start - Variables which will not submit and save -->
        <input type="hidden" id="modals_bootbox_prompt_header_html" value="{l s='Edit Your HTML Content Here' mod='supercheckout'}" />
        <!-- Start - Variables which will not submit and save -->
    </div>
</div>

<!-- Themer -->
<div id="themer" class="collapse">
    <div class="wrapper">
        <span class="close2">&times; {l s='close' mod='supercheckout'}</span>
        <h4>{l s='Themer' mod='supercheckout'}</h4>
        <ul>
            <li>{l s='Theme' mod='supercheckout'}: <select id="themer-theme" class="pull-right"></select><div class="clearfix"></div></li>            
        </ul>
        <div id="themer-getcode" class="hide">
            <hr class="separator" />
            <button class="btn btn-primary btn-small pull-right btn-icon glyphicons download" id="themer-getcode-less"><i></i>{l s='Get LESS' mod='supercheckout'}</button>
            <button class="btn btn-inverse btn-small pull-right btn-icon glyphicons download" id="themer-getcode-css"><i></i>{l s='Get CSS' mod='supercheckout'}</button>
            <div class="clearfix"></div>
        </div>
    </div>
</div>

{assign "column_1" ""}
{assign "column_2" ""}
{assign "column_3" ""}
{assign "column_4" ""}
{assign "column_5" ""}
{assign "main_width" 1}
{if $layout eq 2}
    {$column_1 = $velocity_supercheckout['column_width']['2_column'][1]/$main_width}
    {$column_2 = $velocity_supercheckout['column_width']['2_column'][2]/$main_width}
    {$column_4 = $velocity_supercheckout['column_width']['2_column']['inside'][1]/$main_width}
    {$column_5 = $velocity_supercheckout['column_width']['2_column']['inside'][2]/$main_width}
{else if $layout eq 3}
    {$column_1 = $velocity_supercheckout['column_width']['3_column'][1]/$main_width}
    {$column_2 = $velocity_supercheckout['column_width']['3_column'][2]/$main_width}
    {$column_3 = $velocity_supercheckout['column_width']['3_column'][3]/$main_width}
{/if}

<style type="text/css">
    {literal}
	.ship2pay-glyphicons i:before{    
	    font-size: 17px;
    padding: 3px;
	}
	.ship2pay-div{
	cursor:pointer;
  padding: 5px;
  margin: 10px;
  text-align: center;
  font-size: 13px;
  color: white;
  width: 60%;
  margin-left: 20%;
	}
	.tickcross-sign{
	padding-right: 10px;
    font-weight: bold;
    font-size: 14px;
	}
	    .faq-span{max-height:10px;}
	    .faq-row{background: rgba(230, 230, 236, 0.37);
  border-radius:3px;
  margin-top:10px;
  padding: 30px;
  cursor: pointer;
  padding-left: 10px;
  padding-top: 15px;}
    .question{font-family:initial;color:rgb(213, 81, 81) !important;font-size:17px !important;}
    .answer{display:none;font-family:initial;font-size:15px;line-height:20px;letter-spacing:1px;}
    tr.even { background-color: #EDEDED; }
    tr.odd { background-color: white;}
    .column-1, .column-data-1{width:{/literal}{$column_1|escape:'htmlall':'UTF-8'}{literal}% ;}
    .column-2, .column-data-2{width:{/literal}{$column_2|escape:'htmlall':'UTF-8'}{literal}%;} 
    .column-3, .column-data-3{width:{/literal}{$column_3|escape:'htmlall':'UTF-8'}{literal}%;}
    #column-1-inside,#column-1-inside-text {width:{/literal}{$column_4-1|escape:'htmlall':'UTF-8'}{literal}%;}
    #column-2-inside,#column-2-inside-text {width:{/literal}{$column_4-1|escape:'htmlall':'UTF-8'}{literal}%;}
    {/literal}
.lang-title td
{
	padding: 2px 0px;
}
</style>

<script type="text/javascript">
	var ps_ver = '{$ps_version|escape:'quotes':'UTF-8'}';
    {if $layout eq 1}
        {literal}
        $( ".column" ).sortable({
            connectWith: ".column",
            scroll: false,
            stop: function( event, ui ) {
                $('.column').find("li").each(function(i, el){

                    $(this).find(".row-data").val($(el).index())                

                });
            }
        });
 
        $( ".column" ).disableSelection();
        $('#column-1 > li').tsort({attr:'row-data'});
        $('#column-1 > li').each(function(){
            $(this).appendTo('#column-1' );    

        })
        {/literal}
    {else if $layout eq 2}
        {literal}
        var main_width = 100 / 100;

        $( "#slider" ).slider({
            range: false,	  
            min: 0,
            max: 100,
            design: 1.00,
            values: [ {/literal}{$column_1|escape:'htmlall':'UTF-8'}{literal}],
            slide: function( event, ui ) {

                $('#column-1-text').val(Math.round(main_width*(ui.values[ 0 ])))
                .attr('width-data', ui.values[ 0 ])
                .attr('left-data', 0)
                .css({'width' : parseInt( ui.values[ 0 ] ) + '%'})
                $('#column-2-text').val(Math.round(main_width*(100 - ui.values[ 0 ])))
                .attr('width-data',100 - ui.values[ 0 ]-1)
                .attr('left-data', parseInt(ui.values[ 0 ]))
                .css({'width' : parseInt( 100 - ui.values[ 0 ]-1) + '%'})           
                $('#column-1').css({'width' :  parseInt( ui.values[ 0 ]) +'%' })
                $('#column-2').css({'width' :  parseInt(100 - ui.values[ 0 ]-1) +'%'})
            }
        });
        var main_width_inside = 100 / 100;

        $( "#slider_inside" ).slider({
            range: false,	  
            min: 0,
            max: 100,
            design: 1.00,
            values: [{/literal}{$column_4|escape:'htmlall':'UTF-8'}{literal}],
            slide: function( event, ui ) {

                $('#column-1-inside-text').val(Math.round(main_width_inside*(ui.values[ 0 ])))
                .attr('width-data', ui.values[ 0 ])
                .attr('left-data', 0)
                .css({'width' : parseInt( ui.values[ 0 ] ) + '%'})
                $('#column-2-inside-text').val(Math.round(main_width_inside*(100 - ui.values[ 0 ])))
                .attr('width-data',100 - ui.values[ 0 ]-1)
                .attr('left-data', parseInt(ui.values[ 0 ]))
                .css({'width' : parseInt( 100 - ui.values[ 0 ]-1) + '%'})           
                $('#column-1-inside').css({'width' :  parseInt( ui.values[ 0 ]) +'%' })
                $('#column-2-inside').css({'width' :  parseInt(100 - ui.values[ 0 ]-1) +'%'})
            }
        });
        $( ".column" ).sortable({
            connectWith: ".column",
            scroll: false,
            stop: function( event, ui ) {
                $('.column').find("li").each(function(i, el){

                    $(this).find(".row-data").val($(el).index())
                    $(this).find(".col-data").val($(this).parent().attr('col-data'))
                    $(this).find(".col-data-inside").val($(this).parent().attr('col-inside-data'))

                });
            }
        });

        $( ".column" ).disableSelection();
        $('.column > li').tsort({attr:'col-inside-data'});
        $('.column > li').each(function(){
        if($(this).attr('col-inside-data')=="4"){    
            $(this).appendTo('#column-2-lower' );
        }
        else if($(this).attr('col-inside-data')=="3"){    
            $(this).appendTo('#column-1-inside' );
        }else if($(this).attr('col-inside-data')=="2"){
            $(this).appendTo('#column-2-upper');
        }else{
            $(this).appendTo('#column-1');
        }

        })
        $('#column-1 > li').tsort({attr:'row-data'});
        $('#column-1 > li').each(function(){
            $(this).appendTo('#column-' + $(this).attr('col-data') );    

        })
        $('#column-2-upper > li').tsort({attr:'row-data'});
        $('#column-2-upper > li').each(function(){
            $(this).appendTo('#column-2-upper' );    

        })
        $('#column-2-lower > li').tsort({attr:'row-data'});
        $('#column-2-lower > li').each(function(){
            $(this).appendTo('#column-2-lower' );    

        })
        $('#column-1-inside > li').tsort({attr:'row-data'});
        $('#column-1-inside > li').each(function(){
            $(this).appendTo('#column-' + $(this).attr('col-data')+'-inside' );    

        })
        {/literal}
    {else if $layout eq 3}
        {literal}
        var main_width = 100 / 100;

        $( "#slider" ).slider({
            range: true,	  
            min: 0,
            max: 100,
            step: 1.00,
            values: [{/literal}{$column_1|escape:'htmlall':'UTF-8'}{literal},  {/literal}{($column_1+$column_2)|escape:'htmlall':'UTF-8'}{literal}],
            slide: function( event, ui ) {

                $('#three-column-1').val(Math.round(main_width*(ui.values[ 0 ])))
                .attr('width-data', ui.values[ 0 ])
                .attr('left-data', 0)
                .css({'width' : parseInt( ui.values[ 0 ] ) + '%'})
                $('#three-column-2').val(Math.round(main_width*(ui.values[ 1 ] - ui.values[ 0 ])))
                .attr('width-data',ui.values[ 1 ] - ui.values[ 0 ])
                .attr('left-data', parseInt(ui.values[ 0 ]+10))
                .css({'width' : parseInt( ui.values[ 1 ] - ui.values[ 0 ]) + '%'})
                $('#three-column-3').val(Math.round(main_width*(100 - ui.values[ 1 ])))
                .attr('width-data',100 - ui.values[ 1 ]-1)
                .attr('left-data', parseInt(ui.values[ 1 ]))
                .css({'width' : parseInt( 100 - ui.values[ 1 ]-1) + '%'})
                $('.column-1').css({'width' :  parseInt( ui.values[ 0 ]) +'%' })
                $('.column-2').css({'width' : parseInt( ui.values[ 1 ] - ui.values[ 0 ])+'%'})
                $('.column-3').css({'width' :  parseInt(100 - ui.values[ 1 ]) +'%'})


            }
        });
        $( ".column" ).sortable({
            connectWith: ".column",
            scroll: false,
            stop: function( event, ui ) {
                $('.column').find("li").each(function(i, el){

                    $(this).find(".row-data").val($(el).index())
                    $(this).find(".col-data").val($(this).parent().attr('col-data'))

                });
            }
        });

        $( ".column" ).disableSelection();
        $('.column > li').tsort({attr:'row-data'});
        $('.column > li').each(function(){
            $(this).appendTo('.column-' + $(this).attr('col-data'));					
        })  
        {/literal}
    {/if}
        
{literal}
    
function duplicate_html(e){
    var portlet_id = $(e).parent().parent().attr('id');
    var col_data=$('#'+portlet_id +' .col-data').val();
    var row_data=$('#'+portlet_id +' .row-data').val();
    if("{/literal}{$layout|escape:'htmlall':'UTF-8'}{literal}"== 2){ 
        var col_data_inside=$('#'+portlet_id +' .col-data-inside').val();
    }else{
        var col_data_inside=4;
    }
    var data = parseInt($('#modal_value').val());
    data++;
    $('#modal_value').val(data);                                                            
    string = '<li id="portlet_'+ data +'_'+ data +'" class="portlet" col-data="" row-data="" col-inside-data="">';
    string += '<div class="portlet-header">{/literal}{l s='Html Content' mod='supercheckout'}{literal}</div>';
    string += '<div id="portlet_content_'+  data+'_'+ data +'" class="portlet-content">';
    string += '<div class="text" style="overflow:visible !important;" >';
    string += '<a data-toggle="tooltip"  data-placement="top" data-original-title="{/literal}{l s='Add new HTML content' mod='supercheckout'}{literal}" id="duplicate_button_'+  data+'_'+ data +'" data="'+ (data) +'" class="glyphicons more_windows" onClick="duplicate_html(this);" ><i></i></a>';
    string += '<a data-toggle="tooltip"  data-placement="top" data-original-title="{/literal}{l s='Edit this HTML content' mod='supercheckout'}{literal}" id="modals-bootbox-prompt-'+  data+'_'+ data +'" data-toggle="modal" class="glyphicons edit bootbox-design-extra-html" onClick="dialogExtraHtml(this);"><i></i></a>';
    string += '<a data-toggle="tooltip"  data-placement="top" data-original-title="{/literal}{l s='Remove this HTML content' mod='supercheckout'}{literal}" id="delete_button_'+  data+'_'+ data +'" data="'+  data+'_'+ data +'" data-toggle="modal" class="glyphicons remove"  onClick="remove_html(this);" ><i></i></a>';
    string += '</div>';
    
    string += '<input   type="hidden"  class="sort col-data" name="velocity_supercheckout[design][html]['+  data+'_'+ data +'][2_column][column]" value="'+ col_data +'" />';
    string += '<input   type="hidden"  class="sort row-data" name="velocity_supercheckout[design][html]['+  data+'_'+ data +'][2_column][row]" value="'+ row_data +'" />';
    string += '<input   type="hidden"  class="sort col-data-inside" name="velocity_supercheckout[design][html]['+  data+'_'+ data +'][2_column][column-inside]" value="'+ col_data_inside +'" />';
    
    
    string += '<input   type="hidden"  class="sort col-data" name="velocity_supercheckout[design][html]['+  data+'_'+ data +'][1_column][column]" value="'+ col_data +'" />';
    string += '<input   type="hidden"  class="sort row-data" name="velocity_supercheckout[design][html]['+  data+'_'+ data +'][1_column][row]" value="'+ row_data +'" />';
    string += '<input   type="hidden"  class="sort col-data-inside" name="velocity_supercheckout[design][html]['+  data+'_'+ data +'][1_column][column-inside]" value="'+ col_data_inside +'" />';
    
    
    string += '<input   type="hidden"  class="sort col-data" name="velocity_supercheckout[design][html]['+  data+'_'+ data +'][3_column][column]" value="'+ col_data +'" />';
    string += '<input   type="hidden"  class="sort row-data" name="velocity_supercheckout[design][html]['+  data+'_'+ data +'][3_column][row]" value="'+ row_data +'" />';
    string += '<input   type="hidden"  class="sort col-data-inside" name="velocity_supercheckout[design][html]['+  data+'_'+ data +'][3_column][column-inside]" value="'+ col_data_inside +'" />';

    if({/literal}{$layout|escape:'htmlall':'UTF-8'}{literal}== 3){ 
        string += '<input id="col_text_'+  data+'_'+ data +'"  type="text"  class="sort col-data" name="velocity_supercheckout[design][html]['+ data+'_'+ data +'][3_column][column]" value="'+ col_data +'" />';
        string += '<input id="row_text_'+  data+'_'+ data +'"  type="text"  class="sort row-data" name="velocity_supercheckout[design][html]['+ data+'_'+ data +'][3_column][row]" value="'+ row_data +'" />';
    }    
    if({/literal}{$layout|escape:'htmlall':'UTF-8'}{literal}== 2){ 
        string += '<input id="col_text_'+  data+'_'+ data +'"  type="text"  class="sort col-data" name="velocity_supercheckout[design][html]['+ data+'_'+ data +'][2_column][column]" value="'+ col_data +'" />';
        string += '<input id="row_text_'+  data+'_'+ data +'"  type="text"  class="sort row-data" name="velocity_supercheckout[design][html]['+ data+'_'+ data +'][2_column][row]" value="'+ row_data +'" />';
        string += '<input id="col_inside_text_'+  data+'_'+ data +'"  type="text"  class="sort col-data-inside" name="velocity_supercheckout[design][html]['+ data+'_'+ data +'][2_column][row]" value="'+ col_data_inside +'" />';
    }
    if({/literal}{$layout|escape:'htmlall':'UTF-8'}{literal}== 1){ 
        string += '<input id="row_text_'+  data+'_'+ data +'"  type="text"  class="sort row-data" name="velocity_supercheckout[design][html]['+ data+'_'+ data +'][2_column][row]" value="'+ row_data +'" />';
    }
    string += '</div>';
    string += '</li>';
    
    $(e).parent().parent().parent().parent().append(string);
    
    $('#extra_html_container').append('<input type="hidden" id="modals_bootbox_prompt_'+data+'_'+data+'" name="velocity_supercheckout[design][html]['+data+'_'+data+'][value]" value="" />') 

}

if($.cookie('designTab')==1){
    $('#velsof_supercheckout_container').find('li').removeClass('active');
    $("#velsof_tab_design").trigger('click');
    $.cookie('designTab',0);
}

$(document).ready(function() {
	
	$('.ship2pay-div').click(function(){
	    var element_id=this.id;
	    
	if($('.ship2pay-div input[name=\''+element_id+'\']').is(":checked"))
	{   
	    $(this).css('background-color','rgb(83, 199, 83)'); //green
	    $(this).css('border','1px solid #257925'); //dark green color border
	    $('.ship2pay-div input[name=\''+element_id+'\']').prop('checked', false);
	    $(this).children('.tickcross-sign').html('&#10004;');
	    
	}
	else
	{  
	    $('.ship2pay-div input[name=\''+element_id+'\']').prop('checked', true);
	    $(this).css('background-color','rgb(224, 69, 69)'); //red
	    $(this).css('border','1px solid #B13131'); //dark red color border
	    $(this).children('.tickcross-sign').html('&#10060;');
	}
	    
	  
	    
	});
	//added below two lines to show answer of first FAQ
		$('#faq-span1').css('max-height','none');
		$('#answer1').css('display','block')
		
	// Carousal in FAQ
	$('.faq-row').off( 'click' ).on( 'click', function() {
		var element_id=this.id;
		var i=1;
		for(i=1;i<20;i++)
		{
			if(i!=element_id){
				//to hide answer of previously opened FAQ question
			$('#faq-span'+i).css('max-height','10px');
			$('#answer'+i).css('display','none');
			}
		}
		//added below to lines to show answer of question, when admin click on it
		$('#faq-span'+element_id).css('max-height','none');
		$('#answer'+element_id).css('display','block');
		
	});
	
    $('#tab_lang_translator').css('width',$('#tab_general_settings').width()+'px');
    if ($('input#supercheckout_test_mode').is(':checked')) {
        $('#front_module_url').show();
    }
    $('#supercheckout_test_mode').change(function() {
        if($(this).is(":checked")) {
            $('#front_module_url').show();
        }
        else
            $('#front_module_url').hide();
    });
});

{/literal}        
</script>
