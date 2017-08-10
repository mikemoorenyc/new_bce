<?php
$code = $_GET['code'];

require_once 'secret_codes.php';

$data = array(
  "grant_type" => "authorization_code",
  "code" => $code,
  "redirect_uri" => "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
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
    'Authorization: Basic '.base64_encode($client_id.':'.$client_secret)
);                                                                                                                   
                                                                                                                     
$result = curl_exec($ch);

if ($result === FALSE) {
  echo "cURL Error: " . curl_error($ch);
  die();
} else {
  var_dump(json_decode($result));
  die();
}

?>
