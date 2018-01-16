<?php
function md_sc_parse($string) {
  $theReturn = '';
  $Parsedown = new Parsedown();
  $theReturn = do_shortcode($string);
  return $Parsedown->text($theReturn);
  
}


 ?>
