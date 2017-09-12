<?php
$tvdb_jwt = false;

//GET REFRESH
$headers = array(
            "Accept: */*",
            "Content-Type: application/json",
            "User-Agent: runscope/0.1");


$data = 'apikey='.$keys['tvdb_api'].'userkey='.$keys['tvdb_userkey'].'&username='.$keys['tvdb_username'];
$d_array = array(
  'apikey' => $keys['tvdb_api'],
  'userkey' => $keys['tvdb_userkey'],
  'username' => $keys['tvdb_username']
);

$ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, 'https://api.thetvdb.com/login');
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
       curl_setopt($ch, CURLOPT_POST, 1);
       curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($d_array));
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
       $output = curl_exec($ch);
       $output = json_decode($output,true);
       if (!empty($output['token'])) {
        $tvdb_jwt = $output['token'];
       }

curl_close($ch);



$tvdbCURLs = 0;

function get_tvdb($type, $id) {
  global $tvdb_jwt;
  global $tvdbCURLs;
  $url = null;
  if($tvdb_jwt === false) {
    return $url;
  }
  if($tvdbCURLS > 25) {return false;}

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  $headers = array(
              "Accept: */*",
              "Content-Type: application/json",
              "User-Agent: runscope/0.1",
              'Authorization: Bearer '.$tvdb_jwt);

  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  if($type === 'episode') {
    curl_setopt($ch, CURLOPT_URL, 'https://api.thetvdb.com/episodes/'.$id);
    $output = curl_exec($ch);
    
    $tvdbCURLs++;          
           
    if($output === false) {
      echo curl_error($ch);
      return $url;
    }
    $out = json_decode($output,true);

    if(!empty($out['data']['filename'])) {

      return 'http://thetvdb.com/banners/'.$out['data']['filename'];
    }

  }

  if($type == 'show') {
    curl_setopt($ch, CURLOPT_URL, 'https://api.thetvdb.com/series/'.$id.'/images/query?keyType=fanart');
    $output = curl_exec($ch);
    if($output === false) {

      return $url;
    }

    $out = json_decode($output,true);

    if(!$out['data'] || count($out['data']) < 1) {
      return $url;
    }
    $h = 0;
    $r_url = '';
    foreach($out['data'] as $k => $d) {
      if($d["ratingsInfo"]['average'] > $h) {
        $h = $d["ratingsInfo"]['average'];
        $r_url = 'http://thetvdb.com/banners/'.$d["thumbnail"];
      }
    }
    return $r_url;

  }

  curl_close($ch);
  return $url;
}



 ?>
