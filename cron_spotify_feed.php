<?php

require_once("../../../wp-load.php");
require_once get_template_directory().'/partial_api_key_generator.php';
$keys = api_key_generator();
if( !isset($keys['spotify_id']) || !isset($keys['spotify_secret']) || !isset($keys['spotify_refresh'])) {
  die();
}

//GET REFRESH
$data = array(
  "grant_type" => "refresh_toke",
  "refresh_token" => $keys['spotify_refresh']
);                                                                    
$data_string = json_encode($data);                                                                                   
                                                                                                                     
$ch = curl_init('https://accounts.spotify.com/api/token');                                                                      
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);       
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json',                                                                                
    'Content-Length: ' . strlen($data_string)),
    'Authorization: Basic '.base64_encode($keys['spotify_id'].':'.$keys['spotify_secret'])
);                                                                                                                   
                                                                                                                     
$result = curl_exec($ch);
if ($result === FALSE) {
  echo "cURL Error: " . curl_error($ch);
  die();
} 

$result = json_decode($result);

$token = $result->access_token;
curl_close($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "RECENTLY PLAYED URL");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Content-Type: application/json",
  'Authorization: Bearer '.$token
));
$output = curl_exec($ch);
if ($output === FALSE) {
  echo "cURL Error: " . curl_error($ch);
  die();
} 
curl_close($ch);

var_dump($output);



?>
