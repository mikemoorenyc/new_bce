<?php
require_once("../../../../wp-load.php");
if(empty(get_option( 'api_keys', '' ))) {
  die();
}
$keys = explode("\n",get_option( 'api_keys', '' ));
$keyArray = array();
foreach($keys as $k) {
  $ex = explode(',',$k);
  $keyArray[trim($ex[0]) ] = trim($ex[1]);
}
$keys = $keyArray;
if(!$keys['goodreads'] || !$keys['goodreads_url']) {
  die();
}

$status = new SimpleXMLElement(file_get_contents($keys['goodreads_url']));
