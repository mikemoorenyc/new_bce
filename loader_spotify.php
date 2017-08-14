<?php
$sItems = [];
$albumID = '';
$trackID = '';
foreach($itemList['spotify'] as $s) {
  if($trackID === $s['ID']) {
    $sItems[count($sItems) - 1]['listenCount']++;
    continue;
  }
  if($albumID === $s['album']['ID']) {
    $sItems[count($sItems) - 1]['type'] = 'album';
    continue;
  }
  $sItems[] = $s;
  $sItems[count($sItems) - 1]['type'] = 'track';
  $sItems[count($sItems) - 1]['listenCount'] = 1;
  $albumID = $s['album']["ID"];
  $trackID = $s['ID'];
}


 ?>
