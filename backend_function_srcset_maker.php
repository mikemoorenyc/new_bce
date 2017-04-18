<?php
function srcset_maker($imgs) {
  $srcset="";
  $looper = 0;
  foreach($imgs as $i) {
    if($looper > 0) {
      $srcset .= ',';
    }
    $srcset .= ($i['url'].' '.$i['width'].'w');

    $looper++;
  }
  return $srcset;
}


 ?>
