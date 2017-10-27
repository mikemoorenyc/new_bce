<?php
function global_excerpter($content, $cut= 150){
  if(!$content) {
    return '';
  }
  $siteDesc = preg_replace( "/\r|\n/", " ", preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", strip_tags(md_sc_parse($content))) );
  return substr($siteDesc, 0, $cut);

}


 ?>
