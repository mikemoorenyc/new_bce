<?php
$traktItems = [];
$bingeCount = 0;
$currentshowID = '';
foreach($itemList['trakt']as $i) {

  if($i['type'] === 'movie') {
    $traktItems[] = $i;
    $bingeCount = 0;
    $currentshowID = '';
    continue;
  }
  if($currentshowID === $i['show']['ID']) {
    $traktItems[count($traktItems) - 1]['bingeCount']++;
    $traktItems[count($traktItems) - 1]['type'] = 'show';
    if(!$traktItems[count($traktItems)-1]['show']['img']) {
      $traktItems[count($traktItems)-1]['img'] = $traktItems[count($traktItems)-1]['show']['tvdb_fallback'];
    } else {
      $traktItems[count($traktItems)-1]['img'] = $traktItems[count($traktItems)-1]['show']['img'];
    }

    continue;
  }
  $traktItems[] = $i;
  $traktItems[count($traktItems)-1]['bingeCount'] = 1;
  $traktItems[count($traktItems)-1]['type'] = 'episode';
  if(!$traktItems[count($traktItems)-1]['img']) {
   $traktItems[count($traktItems)-1]['img'] = $traktItems[count($traktItems)-1]['show']['img'];
  }
  $currentshowID = $i['show']['ID'];
}



 ?>
