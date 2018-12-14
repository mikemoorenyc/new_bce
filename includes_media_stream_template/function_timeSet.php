<?php
function returnS($s) {
  if($s > 1) {
    return 's';
  } else {
    return '';
  }
}


function timeSet($stamp) {



  $today = array(intval(date('j')),intval(date('n')),intval(date('Y')));
  $timeA =  array(intval(date('j',$stamp)),intval(date('n',$stamp)),intval(date('Y',$stamp)));
  $largeDiff = ($today[0]+($today[1]*30)+($today[2]*365)) - ($timeA[0]+($timeA[1]*30)+($timeA[2]*365));

  if(date('j-n-Y') === date('j-n-Y',$stamp)) {
    return 'Today';
  }
  if(date('j-n-Y',strtotime('-1 days')) === date('j-n-Y',$stamp)) {
    return 'Yesterday';
  }
  //YEARS
  if($largeDiff >= 365) {
    $diff = $today[2] - $timeA[2];
    return $diff.' year'.returnS($diff).' ago';
  }
  //MONTHS
  if($largeDiff > 30) {

    $diff = ($today[1]+($today[2]*12)) - ($timeA[1]+($timeA[2]*12));

    return $diff.' month'.returnS($diff).' ago';
  }
  //WEEKS
  if($largeDiff > 6) {
    $diff = floor($largeDiff / 7);
    return $diff.' week'.returnS($diff).' ago';
  }
  //DAYS
  return ($largeDiff).' day'.returnS($largeDiff).' ago';
}


 ?>
