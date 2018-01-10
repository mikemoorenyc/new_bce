<?php
$files = array(
    '2018_6-2018_12',
    '2018_1-2018_5',
    '2019_1-2019_5'
);


usort($files,function($a,$b){
    $da = explode('_',explode('-',$a)[0]);
    $db = explode('_',explode('-',$b)[0]);
    return intval($da[0].$da[1]) - intval($db[0].$db[1]);
});
$files = array_reverse($files);
var_dump($files);

foreach($files as $f) {
    $dates = explode('-',$f);
    $from = explode('_',$dates[0]);
    $to = explode('_',$dates[1]);
    $fromStr = date('F Y', strtotime('1.'.$from[1].'.'.$from[0]));
    $toStr = date('F Y', strtotime('1.'.$to[1].'.'.$to[0]));
    echo $fromStr.' - '.$toStr.'<br/><br/>';
}
?>
