<?php
/**
 * Modul Zboží: Srovnávače zboží - export xml pro Prestashop
 *
 * PHP version 5
 *
 * LICENSE: The buyer can free use/edit/modify this software in anyway
 * The buyer is NOT allowed to redistribute this module in anyway or resell it 
 * or redistribute it to third party
 *
 * @package    zbozi
 * @author    Vaclav Mach <info@prestahost.cz>
 * @copyright 2014,2015 Vaclav Mach
 * @license   EULA
 * @version    2.9.3
 * @link       http://www.prestahost.eu
 */
include(dirname(__FILE__).'/../../config/config.inc.php');
error_reporting(E_ALL);
ini_set('display_errors', '1');

$module=Module::getInstanceByName('zbozi');
$shop=new Shop(Tools::getValue('id_shop'));
$function=Tools::getValue('function');
 

Context::getContext()->shop=$shop;

require_once(_PS_MODULE_DIR_.$module->name.'/ZboziAttributes.php'); // obsahuje cMap
$cMap=new cMap($function);

$module_url = $module->getModuleUrl();
$field=$function.'_category';
$id_lang=  (int)Tools::getValue('id_lang');
$nw=  (int)Tools::getValue('nw');

if(empty($id_lang))
   $id_lang = zbozi::getDefaultLang();
if(empty($nw))
   $nw=0;


$locale=Db::getInstance()->getValue('SELECT iso_code FROM '._DB_PREFIX_.'lang WHERE id_lang='.(int)$id_lang);
$locale_transformed = getTaxonomyByCountry($locale);

$locale=toGoogleLocale($locale); 




$local_file=_PS_MODULE_DIR_.$module->name.'/'.$function.'_'.$locale.'_taxonomy.txt';
if(isset($_POST['cmd_save']) && strlen($function)) {
       $counter=0;
       if(isset($_POST['chck_update']) && is_array($_POST['chck_update']))
	   while(list($key,$val)=each($_POST['chck_update'])) {
	   		$name=$_POST["mapped"][$key];
	   
	   	   
	   	    if(isset($_POST['chck_propagate'][$key]))  {
	   	    	propagateChange($key, $field, $name, $counter);
			}
			else {
				 $sql='UPDATE '._DB_PREFIX_.'category_lang SET '.$field.'= "'.pSQL($name).'" WHERE 
				 id_category='.(int)$key.' AND id_lang='.(int)$id_lang;
	   	         Db::getInstance()->execute($sql);
	   	         $counter++;
			}
	   	    
	   }
    	echo '<b>' .$counter.' '. $module->l(' records updated').'</b>';
}

if(!file_exists($local_file) || mktime(true)-filemtime($local_file) > 86400*3) {
	
	 switch($function) {
	 	    case 'heureka': {
	 	 
	 	    	if($locale == "sk-SK")
	 	    		$remote='https://www.heureka.sk/direct/xml-export/shops/heureka-sekce.xml';
				else 
			       $remote='https://www.heureka.cz/direct/xml-export/shops/heureka-sekce.xml';
				if($s=read_remote_file($remote)) {
	 	    			$data=transform_heureka($s);
	 	    			file_put_contents($local_file, $data);
					}
			}; break;
			case 'google': {
				
				if($s=read_remote_file('http://www.google.com/basepages/producttype/taxonomy.'.$locale_transformed.'.txt')) {
					$arr=explode(chr(10),$s);
					unset($arr[0]);
					$s=implode(chr(10),$arr);
	 	    		file_put_contents($local_file, $s);
				}
				
			}; break;
			
			case  'zbozi': { 
				 
					    $remote='https://www.zbozi.cz/static/categories.json';
                        if($s=read_remote_file($remote)) {
	 	    			$data=transform_seznam($s);
	 	    			file_put_contents($local_file, $data);
                        }
		 
			}
			
	 }
	
}



function getTaxonomyByCountry($iso_code) {
	$iso_code = strtoupper($iso_code); 	  
	
	 	 if(in_array($iso_code, array('DE', 'AT')) )
	 	     return 'de-DE';
	 	     
	 	  if(in_array($iso_code, array('AU')) )
	 	     return 'en-AU';
	 	    	
		  if(in_array($iso_code, array('GB')) )
	 	     return 'en-GB';
	 	     
	       if(in_array($iso_code, array('US')) )
	 	     return 'en-US';
	 	     
	 	   if(in_array($iso_code, array('ES')) )
	 	     return 'es-ES';
	 	     
	 	   if(in_array($iso_code, array('FR')) )
	 	     return 'fr-FR';
	 	  
	 	   if(in_array($iso_code, array('IT')) )
	 	     return 'it-IT';
	 	     
	 	     
	 	   if(in_array($iso_code, array('JP')) )
	 	     return 'ja-JP';
	 	     
	 	   if(in_array($iso_code, array('NL')) )
	 	     return 'nl-NL';
	 	  
	 	   if(in_array($iso_code, array('DK')) )
	 	     return 'da-DK';
	 	     
	 	   if(in_array($iso_code, array('IT')) )
	 	     return 'pl-PL';
	 	     
	 	     
	 	   if(in_array($iso_code, array('SE')) )
	 	     return 'sv-SE';
	 	     
	 	   if(in_array($iso_code, array('TR')) )
	 	     return 'tr-TR';
	 	  
	 	   if(in_array($iso_code, array('CS', 'SK')) )
	 	     return 'cs-CZ';
	 	     
	 	    return 'en-US';
	 	 
	 	 
	 }

function   toGoogleLocale($locale) {
	// http://www.lingoes.net/en/translator/langcode.htm
	$arr=explode('-', $locale);
	if(is_array($arr) && count($arr)==2)
	  return $arr[0].'-'.strtoupper($arr[1]);
	if($locale == 'CZ')
	   $locale='cs-CZ';
	if($locale == 'sk')
	  $locale='sk-SK';
	return $locale;
}

	  function getValue($key, $default_value = false)
    {
        if (!isset($key) || empty($key) || !is_string($key))
            return false;
        $ret = (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $default_value));

        if (is_string($ret) === true)
            $ret = urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($ret)));
        return !is_string($ret)? $ret : stripslashes($ret);
    }
    
    function read_remote_file($url) {
    	     $ch=curl_init ();
            curl_setopt($ch, CURLOPT_MAXCONNECTS, 10);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
           // curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            
            $rawdata=curl_exec($ch);
            $info = curl_getinfo($ch);
              if($info['http_code']!=200) {
          return false;
        }
        if(!$rawdata || strlen($rawdata) < 10  ) {
           return false;    
        }
        
        return  $rawdata;
    	
	}
	
	function transform_seznam($json) {
		$source = json_decode($json, true);
        $output ='';
        while(list($key, $val) = each($source)) {
	       vetevS($val, $output);
        }
        return $output;	
	}
    
     function vetevS($PARENT, &$output) {
         if(isset($PARENT['categoryText']) && strlen($PARENT['categoryText'])) {
             $output.=$PARENT['categoryText']."\n";
         }
                if(isset($PARENT['children'])) {
                     while (list($key, $val) = each($PARENT['children'])) {
                        vetevS($val, $output);
                }     
        }
        
    }
    
	function transform_heureka($s) {
		$xml=simplexml_load_string($s);
		$output='';
		vetev($xml, $output);
		return $output;
	}
	
    function vetev($PARENT, &$output) {
        if(isset($PARENT->CATEGORY)) {

        foreach($PARENT->CATEGORY as $CHILD) {
	        $CATEGORY_FULLNAME = isset($CHILD->CATEGORY_FULLNAME) ? $CHILD->CATEGORY_FULLNAME : "";
	        $s=(string) $CHILD->CATEGORY_FULLNAME;
	        if(strlen($s))
	           $output.=$s."\n";
	        vetev($CHILD, $output);
        }
        }
    }
     
    function propagateChange($key, $field, $name, &$counter) {
    	      global $id_lang;
    	     $sql='UPDATE '._DB_PREFIX_.'category_lang SET '.$field.'= "'.pSQL($name).'" 
    	     WHERE id_category='.(int)$key.' AND id_lang='.$id_lang;
	   	     Db::getInstance()->execute($sql);
	   	     $counter++; 
	   	     $sql='SELECT id_category FROM '._DB_PREFIX_.'category WHERE id_parent='.(int)$key;
	   	     $children= Db::getInstance()->executeS($sql);
	   	     if(is_array($children) && count($children)) {
	   	     	foreach($children as $child) {
	   	     	    propagateChange($child['id_category'],  $field, $name, $counter);
				} 
			 }
	}
	
	   
 function printCategories($cats) {
 	 
     $test=count($cats);
     if($test == 5 && isset($cats['id']) && (int)$cats['id']) {
     	printLine($cats);
     	if(isset($cats['children']))
 	       printCategories($cats['children']);
	 }
	 else
	 {
	   foreach($cats as $cat) {
	   if((int)$cat['id']) {
	   printLine($cat);
		   if(isset($cat['children']))
		       printCategories($cat['children']);
		   }
	   }  	 
	 }
 }   
 
 function printLine($cat) {
 	 $style='';
 	 if($cat['level'] == 2) {
 	 	$style='font-weight:800;color: blue'; 
	 }
	 elseif($cat['level'] == 3) {
	 	$style='font-weight:600';  
	 }
 	   
 	 echo '<tr>';
         echo '<td style="'.$style.'">'.str_repeat('&nbsp;', $cat['level']*4);
         echo $cat['id'].'  '.$cat['name'].'</td>';
         $value=$cat['mapped'] && strlen($cat['mapped'])?  (string)$cat['mapped']:'';
      ?>   
        <td>
<input type='text' size='80' autocomplete='off' id='t<?php echo $cat['id'];?>' name='mapped[<?php echo $cat['id'];?>]' class='txtnapoveda'  value="<?php echo  $value; ?>" /> 
</td>
<td>
<input type='checkbox'  id='ch<?php echo $cat['id'];?>' name='chck_update[<?php echo $cat['id'];?>]' value='1' />
</td> 
<td>
<input type='checkbox'   name='chck_propagate[<?php echo $cat['id'];?>]' value='1' onchange="changeUpd(this,<?php echo $cat['id'];?>)"/>
</td> 
 <?php          
 	  echo '</tr>';
 }
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <?php
        	$protocol = $module->use_ssl?'https':'http';
        ?>
        <script type="text/javascript" src="<?php echo $protocol;?>://code.jquery.com/jquery-latest.min.js"></script>
     
    </head>

<script type="text/javascript">
   <?php if ($function != 'glami') { ?> 
              var currentId=0;

			$(document).ready(function(){
				$('.txtnapoveda').keyup(function(){   
				   
				    var text= $(this).val();
				    if(text.length < 1)
				      return;
				    
				    currentId = $(this).attr('id');
				    $(this).parent().append($('#selnapoveda'));
					$.ajax({url: '<?php echo $module_url;?>/ajax.php?text='+ text + '&function=<?php echo $function;?>' + '&locale=<?php echo $locale;?>',
					success: function(output) {
					   if(output.length > 0) {
						$('#selnapoveda').html(output);
						$('#selnapoveda').show();
					   }
					   else {
					   $('#selnapoveda').hide();
					   }
					}
					});
				});
				$('.txtnapoveda').mouseleave(function(){   
				 // $('#selnapoveda').hide();
				 // currentId=0;
 
				});
			});
         
            function setSelected() {
            if(currentId) {
               var selval=$('#selnapoveda').val();
               $('#'+currentId).val(selval);
               
               currentId = currentId.replace("t", "ch");
               $( '#' + currentId ).prop( "checked", true );
			}
               
           $('#selnapoveda').hide();
            currentId=0;	
			}    
            
          <?php }  else { ?>    
           $(document).ready(function(){
                $('.txtnapoveda').keyup(function(){   
                    
                   var chid = $(this).attr('id');
                   setSelectedGlami(chid);
                });
              
            });
          
          
           <?php }    ?>    
           function changeUpd(el, id) {
           	  if(el.checked) {
           	  	   $( '#ch' + id ).prop( "checked", true );
			  }
		   }
           
           function setSelectedGlami(chid) {
                 chid = chid.replace("t", "ch");
               $( '#' + chid ).prop( "checked", true );
           }    
</script>
     
<body>

<form method=post>
<input type='hidden' value='<?php echo $function;?>' name='function' />
<input type='hidden' value='<?php echo Tools::getValue('id_shop');?>' name='id_shop' />
<input type='hidden' value='<?php echo $id_lang;?>' name='id_lang' />
<input type='hidden' value='<?php echo $nw;?>' name='nw' />
<input type='submit' name='cmd_save' value='<?php echo $module->l('Save');?>'  />
<?php
if($nw  == 1)  {  ?>
 &nbsp; &nbsp; &nbsp; <input type='submit' name='cmd_save' value='<?php echo $module->l('Uložit a zavřít');?>' onclick="self.close()"/>
<?php } ?>
<select   id='selnapoveda' onclick='setSelected()' style='display:none;max-width:90%;' size='10'></select>  
<h1><?php echo $module->l('Mapa kategorií '); echo $function; ?></h1>
<?php if($function == 'glami') {
    if((int)Configuration::get('ZBOZI_GLCATMODE') == 0) {
    echo "Pokud máte na eshopu např kategorii Punčochy|Dlouhé|Bavlněné a do kolonky Předřadit text dopíšete Dámské, bude výsledná kategorie
     Dámské|Punčochy|Dlouhé|Bavlněné";
    }
    elseif((int)Configuration::get('ZBOZI_GLCATMODE') == 1) {
      echo " dopište celou požadovanou cestu například Dámské|Punčochy|Dlouhé|Bavlněné";
    }
}
?>

<fieldset>
<legend></legend>
<table> 
<tr>
<th><?php echo $module->l('Kategorie na eshopu');?></th>
<th><?php if($function == 'glami' && (int)Configuration::get('ZBOZI_GLCATMODE') == 0) echo $module->l('Předřadit text'); else echo $module->l('Kategorie zdroj');?></th>
<th style='max-width:50px'><?php echo $module->l('Změnit');?></th>
<th style='max-width:100px'><?php echo $module->l('Zahrnout podkategorie');?></th>
</tr>
<?php 
 
    $blockCategTree=$cMap->getTree(6, $id_lang);  
    
            printCategories($blockCategTree);
?>
</table>
</fieldset>     
<input type='submit' name='cmd_save' value='<?php echo $module->l('Save');?>' />

</form>
</body>