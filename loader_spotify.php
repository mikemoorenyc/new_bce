<?php
$sItems = [];
$albumID = '';
$trackID = '';
$playDate = null;
foreach($itemList['spotify'] as $s) {
  if($trackID === $s['ID']) {
    $sItems[count($sItems) - 1]['listenCount']++;
    continue;
  }
  if($albumID === $s['album']['ID']) {
    if(date('j',$s['timestamp']) !== date('j',$sItems[count($sItems) - 1]['timestamp'] )) {
      $sItems[] = $s;
      $sItems[count($sItems) - 1]['type'] = 'track';
      $sItems[count($sItems) - 1]['listenCount'] = 1;
      $albumID = $s['album']["ID"];
      $trackID = $s['ID'];
      continue;
    }
    $sItems[count($sItems) - 1]['type'] = 'album';
    $sItems[count($sItems) - 1]['title'] = $s['album']['title'];
    continue;
  }
  $sItems[] = $s;
  $sItems[count($sItems) - 1]['type'] = 'track';
  $sItems[count($sItems) - 1]['listenCount'] = 1;
  $albumID = $s['album']["ID"];
  $trackID = $s['ID'];
}


 ?>
