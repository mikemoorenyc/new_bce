<?php
function md_sc_parse($string) {
  $theReturn = '';
  $Parsedown = new Parsedown();
  $theReturn = do_shortcode($string);
  $content_html = $Parsedown->text($theReturn);


   return $content_html;

}


 ?>
