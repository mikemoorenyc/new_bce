<?php
function input_to_array($string) {
 if(empty($string)) {
  return [];
 }
 $lineSplit = preg_split("/\\r\\n|\\r|\\n/", $string);
 $newArray= [];
 foreach($lineSplit as $l) {

   $sections = explode(",",$l);
   $sectionArray = [];
   foreach($sections as $s) {

    $sectionArray[] = trim($s);
   }
   if(!empty($sectionArray)){
     $newArray[] = $sectionArray;
   }
 }
 return $newArray;
}
