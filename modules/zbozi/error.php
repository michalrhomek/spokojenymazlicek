<?php
 
 if(isset($_GET['error'])) {
 $error = json_decode($_GET['error'], true);
 while(list($key, $val) = each($error)) {
      echo $val.'<br />';
 }
  
 }
