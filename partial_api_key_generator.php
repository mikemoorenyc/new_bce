<?php
function api_key_generator() {
 $keys = get_option( 'api_keys', '' );

 if(empty($keys)) {

  return array();
 }

 $keys = explode("\n",get_option( 'api_keys', '' ));
 $newArray = [];
 foreach($keys as $k) {
  $ex = explode(',',$k);

  if(count($ex) !== 2) {
    continue;
  }
  $newArray[trim($ex[0]) ] = trim($ex[1]);

 }
 return $newArray;
}
