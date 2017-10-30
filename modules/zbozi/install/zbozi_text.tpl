{*
 * Modul Zboží: Srovnávače zboží - export xml pro Prestashop
 *
 * PHP version 5
 *
 * LICENSE: The buyer can free use/edit/modify this software in anyway
 * The buyer is NOT allowed to redistribute this module in anyway or resell it 
 * or redistribute it to third party
 *
 * @package    orderpreview
 * @author    Vaclav Mach <info@prestahost.cz>
 * @copyright 2014 Vaclav Mach
 * @license   EULA
 * @version    1.0
 * @link       http://www.prestahost.eu
 *}
 <br >
 <div class="separator"></div>
 <div  class=" product-tab-content" style="">
<h3 class="tab">
<i class="icon-info"></i>
Zboží: Rozšířené vlastnosti  
</h3>  

<div class="form-group">
        <div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="zbozi_text"   multilang="true"}</span></div>
        <label class="control-label col-lg-2" for="zbozi_text_{$id_lang}">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Modifikace názvu (Zbozi.cz)'}">
                {l s='Modifikace názvu (Zbozi.cz)'}
            </span>
        </label>
        <div class="col-lg-9">
             {include file="controllers/products/input_text_lang.tpl"
                languages=$languages
                input_class=""
                input_value=$product->zbozi_text
                input_name="zbozi_text"
                 
            }
        </div>
    </div>
    
    <div class="form-group">
        <div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="heureka_text"   multilang="true"}</span></div>
        <label class="control-label col-lg-2" for="heureka_text_{$id_lang}">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Modifikace názvu (Heureka.cz)'}">
                {l s='Modifikace názvu (Heureka.cz)'}
            </span>
        </label>
        <div class="col-lg-9">
          {include file="controllers/products/input_text_lang.tpl"
                languages=$languages
                input_class=""
                input_value=$product->heureka_text
                input_name="heureka_text"
                 
            }
        </div>
    </div>
       

 
<div class="form-group">
        <div class="col-lg-1"><span class="pull-right"></span></div>
        <label class="control-label col-lg-2" for="videourl">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Heureka: Video url'}">
             {$bullet_common_field}   {l s='Heureka: Video url'}
            </span>
        </label>
        <div class="col-lg-9">
           <input type="text" size="10" name="videourl" value="{if isset($product->videourl)}{$product->videourl}{/if}" />
        </div>
</div>
       
<div class="form-group">
        <div class="col-lg-1"><span class="pull-right"></span></div>
        <label class="control-label col-lg-2" for="productline">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Zboží: Product line'}">
               {$bullet_common_field} {l s='Zboží: Product line'}
            </span>
        </label>
        <div class="col-lg-9">
            <input type="text" size="10" name="productline" value="{if isset($product->productline)}{$product->productline}{/if}" />
        </div>
</div> 


 
<div class="form-group">
        <div class="col-lg-1"><span class="pull-right"></span></div>
        <label class="control-label col-lg-2" for="videourl">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Zbozi: EXTRA_MESSAGE'}">
              {$bullet_common_field}  {l s='Zbozi: EXTRA_MESSAGE'}
            </span>
        </label>
        <div class="col-lg-9">
           
 <input type="checkbox"   name="extramessage[0]" value="1" {if isset($product->extramessage[0]) && $product->extramessage[0]} checked="checked" {/if} /> <span style='font-size: small;'>Prodloužená záruka |</span>        
 <input type="checkbox"   name="extramessage[1]" value="1" {if isset($product->extramessage[1])& $product->extramessage[1]} checked="checked" {/if} /> <span style='font-size: small;'>Příslušenství zdarma |</span>  
 <input type="checkbox"   name="extramessage[2]" value="1" {if isset($product->extramessage[2])& $product->extramessage[2]} checked="checked" {/if} /> <span style='font-size: small;'>Pouzdro zdarma |</span>    
 <input type="checkbox"   name="extramessage[3]" value="1" {if isset($product->extramessage[3])& $product->extramessage[3]} checked="checked" {/if} /> <span style='font-size: small;'>Dárek zdarma |</span>    
 <input type="checkbox"   name="extramessage[4]" value="1" {if isset($product->extramessage[4])& $product->extramessage[4]} checked="checked" {/if} /> <span style='font-size: small;'>Montáž zdarma |</span>    
 <input type="checkbox"   name="extramessage[5]" value="1" {if isset($product->extramessage[5])& $product->extramessage[5]} checked="checked" {/if} /> <span style='font-size: small;'>Osobní odběr zdarma |</span>    
 <input type="checkbox"   name="extramessage[6]" value="1" {if isset($product->extramessage[6])& $product->extramessage[6]} checked="checked" {/if} /> <span style='font-size: small;'>Voucher na další nákup |</span>   
 <input type="checkbox"   name="extramessage[7]" value="1" {if isset($product->extramessage[7])& $product->extramessage[7]} checked="checked" {/if} /> <span style='font-size: small;'>Doprava zdarma </span>  
 
        </div>
</div>  
 
 
 <div class="form-group">
        <div class="col-lg-1"><span class="pull-right"></span></div>
        <label class="control-label col-lg-2" for="heureka_cpc">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Heureka CPC'}">
             {$bullet_common_field}   {l s='Heureka CPC'}
            </span>
        </label>
        <div class="col-lg-9">
           <input type="text" size="10" name="heureka_cpc" value="{if isset($product->heureka_cpc)}{$product->heureka_cpc}{/if}" /> desetinny oddělené čárkou 
        </div>
</div>

<div class="form-group">
        <div class="col-lg-1"><span class="pull-right"></span></div>
        <label class="control-label col-lg-2" for="max_cpc">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Zboží CPC'}">
             {$bullet_common_field}   {l s='Zboží CPC'}
            </span>
        </label>
        <div class="col-lg-9">
          <input type="text" size="10" name="max_cpc" value="{if isset($product->max_cpc)}{$product->max_cpc}{/if}" /> desetinny oddělené čárkou  
        </div>
</div>
  
<div class="form-group">
        <div class="col-lg-1"><span class="pull-right"></span></div>
        <label class="control-label col-lg-2" for="max_cpc_search">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Zboží CPC Search'}">
             {$bullet_common_field}   {l s='Zboží CPC Search'}
            </span>
        </label>
        <div class="col-lg-9">
          <input type="text" size="10" name="max_cpc_search" value="{if isset($product->max_cpc_search)}{$product->max_cpc_search}{/if}" /> desetinny oddělené čárkou  <br />
        </div>
</div>     


<div class="form-group">
        <div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="heureka_text"}</span></div>
        <label class="control-label col-lg-2" for="max_cpc_search">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Vynechat tento produkt ve feedu'}">
                {l s='Vynechat tento produkt ve feedu'}
            </span>
        </label>
        <div class="col-lg-9">
             <input type="checkbox"   name="skipfeeds[0]" value="1" {if isset($product->skipfeeds[0]) && $product->skipfeeds[0]} checked="checked" {/if} /> <span style='font-size: small;'>Heureka |</span>        
             <input type="checkbox"   name="skipfeeds[1]" value="1" {if isset($product->skipfeeds[1])& $product->skipfeeds[1]} checked="checked" {/if} /> <span style='font-size: small;'>Zboží |</span>  
             <input type="checkbox"   name="skipfeeds[2]" value="1" {if isset($product->skipfeeds[2])& $product->skipfeeds[2]} checked="checked" {/if} /> <span style='font-size: small;'>Google nákupy |</span>    
             <input type="checkbox"   name="skipfeeds[3]" value="1" {if isset($product->skipfeeds[3])& $product->skipfeeds[3]} checked="checked" {/if} /> <span style='font-size: small;'>Glamy |</span>    
 
        </div>
</div>
</div> 
       
 
 