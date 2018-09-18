<?php
function md_sc_parse($string) {
  $theReturn = '';
  $Parsedown = new Parsedown();
  $theReturn = do_shortcode($string);
  $content_html = $Parsedown->text($theReturn);
  $doc = new DOMDocument();
  $doc->loadHTML($content_html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
  foreach($doc->getElementsByTagName('a') as $d){
    
    if(strpos($d->getAttribute("href"), get_bloginfo('template_url')) !== false) {
        continue;
    }
    $d->setAttribute("target", "_blank");
  }
  $html=$doc->saveHTML();
  return $html;
  
}


 ?>
