<?php

function title_formatter($title,$classString ='') {
  $title = trim(preg_replace("/\([^)]+\)/","",$title));
  if(strlen($title) >= 35) {
    $long_title = 'long-title';
  }
  if(strlen($title) < 15) {
    $long_title = 'short-title';
  }
  return '<h2 class="'.$long_title.' '.$classString.'"><span class="hover">'.$title.'</span></h2>';
}

function artistNames($i) {
  if(count($i) === 1) {
    return '<span class="name">'.$i[0].'</span>';
  }
  $string = '';
  foreach($i as $k => $n) {
    switch($index) :
      case (0):
      $sep = '';
      break;
      case (count($i) -1):
      $sep = ' & ';
      break;
      default:
      $sep = ", ";
      break;
    endswitch;

}
return $string;
}

function create_media_info($p) {
  $data = json_decode($p->post_content,true);
  $type = (get_the_terms($p->ID, 'consumed_types')) ? get_the_terms($p->ID, 'consumed_types')[0]->slug : '' ;



  //PLAY LIST, Album , Movie, show
  if(($type === 'album') || $type === 'movie' || $type == 'show'):
    $meta = ($type == 'movie') ? "Watched" : "Listened to" ;
    $meta = ($type == 'show') ? 'Watched '.$data['bingeCount'].' episodes' : $meta;
    $byline = ($type == "album" && !$data['playlist']) ? '<h3 class="byline">by '.artistNames($data['album']['artists']).'</h3>' : '';
    echo '<div class="extra font-sans">'.$meta.'</div>';
    echo title_formatter($p->post_title);
    echo $byline;
    return ;
  endif;
  //Book
  if($type == "book"):
    $status = ($data['status'] === 'read') ? 'Finished Reading' : "Started Reading";
    
    ?>
    <div class="extra font-sans"><?= $status;?></div>
    <?= title_formatter($p->post_title);?>
    <h3 class="byline"><?=  'by '.artistNames($data['authors']);?></h3>
    <?php
    return ;
  endif;
  //SINGLE EPISODE
  if($type == 'episode'):
    echo '<div class="extra font-sans">Watched</div>';
    echo title_formatter( $p->post_title,'single');
    echo '<div class="show-title font-sans ">'.$data['show']['title'].'</div>';
    return ;
  endif;
  //SINGLE TRACK
  if($type === 'track'):
    $xV = ($data['listenCount'] > 1) ? $data['listenCount'].'x ' : '';
    echo '<div class="extra font-sans">Listened '.$xV.'to</div>';
    echo title_formatter($p->post_title, 'single');
    ?> <h3 class="byline"><?=  'by '.artistNames($data['album']['artists']);?></h3> <?php
    return;
  endif;
  //SINGLE TRACK
  echo title_formatter($p->post_title);
  return ;
}
?>
