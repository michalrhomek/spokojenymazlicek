<?php
$i=1;
   $products[$i]['description_short']='<div> <div> <div> <div> <div> <p>Rozměr role: 0,53 x 10,05m; Balení: 12 rolí/karton</p> <p><strong>Při odběru celého balení bude poskytnuta <span>sleva 10%<span>.</span></span></strong></p> <p><strong><br /></strong></p> </div> </div> </div> </div> </div>';
     $test=    strlen($products[$i]['description_short']);
     
     if(strlen( $products[$i]['description_short']) > 800) {
                      $products[$i]['description_short']=strip_tags($products[$i]['description_short']);     
                      if(function_exists('mb_substr'))
                          $products[$i]['description_short']=mb_substr($products[$i]['description_short'], 0,800, 'UTF-8');
                      else
                         $products[$i]['description_short']=substr($products[$i]['description_short'], 0,800);
                 
                 }    
?>
