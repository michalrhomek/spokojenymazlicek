<?php
  class CsvCategory {
      private $cats;

      public function lineariseCategories($categories) {
         $this->_levelCategories($categories);
         
       
         $this->_makeCompatible();
         return $this->_orderByParent($this->cats);
          
      }
      
      private function _levelCategories($categories) {
          if(isset($categories['infos'])) 
            $this->cats[]=$categories['infos'];
            
          elseif(is_array($categories)) {
              foreach($categories as $category) {
                  $this->_levelCategories($category);
              }
              
          }
          
      }
  
  
  private function _makeCompatible() {
       for($i=0; $i < count( $this->cats); $i++) {
           if(!isset($this->cats[$i]['is_root_category']))
               $this->cats[$i]['is_root_category']=0;  
         $keys=array("description", "meta_title","meta_keywords","meta_description","link_rewrite");
         foreach($keys as $key) {
            if(isset($this->cats[$i][$key]))
               $this->cats[$i][$key]=strip_tags($this->cats[$i][$key]); 
         }      
               
               
       }
  
  }    
      private function _orderByParent($cats) {
         // Obtain a list of columns
foreach ($cats as $key => $row) {
    //$parents[$key]  = $row['id_parent'];
      $parents[$key]  = $row['level_depth'];
    $ids[$key] = $row['id_category'];
}

// Sort the data with volume descending, edition ascending
// Add $data as the last parameter, to sort by the common key
    array_multisort($parents, SORT_ASC,  $cats);
   return $cats;
      }
  }
?>
