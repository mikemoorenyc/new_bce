<?php

function artistNames($i) {

 if(count($i) === 1) {
  return '<span class="name">'.$i[0].'</span>';
 }
 $string = '';
 foreach($i as $k => $n) {
  $sep = ', ';
  if($k === 0) {
   $sep = '';
  }
  if($k === count($i) - 1) {
   $sep = ' & ';
  }
  $string .= $sep.'<span class="name">'.$n."</span>";
 }
 return $string;
}

function book_status_maker($i) {
 if(!empty($i['percent'])) {
  return 'Finished '.$i['percent'].'%';
 }
 if($i['status'] === 'read') {
  return 'Finished reading';
 }
 if($i['status'] === 'currently-reading') {
  return 'Started reading';
 }

}

function switch_media_info($i) {
 switch($i['type']):
  case 'movie':
  ?>
  <h2><?= $i['title']; ?></h2>
  <?php
  break;

  case 'episode':
  ?>

  <h2 class="single">&ldquo;<?=$i['title'];?>&rdquo;</h2>
  <div class="show-title"><?= $i['show']['title']; ?></div>
  <?php
  break;

  case 'show':
  ?>
  <div class="extra">Watched <?= $i['bingeCount'];?> episodes</div>
  <h2><?= $i['show']['title']; ?></h2>
  <?php
  break;

  case 'album':

  ?>
<h2><?= $i['album']['title'];?></h2>
<h3 class="byline"><?=  'by '.artistNames($i['album']['artists']);?></h3>
  <?php
  break;

  case 'track':
  
  ?>
<?php
 if($i['listenCount'] > 1) {
  if($i['listenCount'] > 3) {
   $s = 's';
  }
  ?>
 <div class="extra">
 <?= $i['listenCount']-1;?> repeat<?= $s;?>
 </div>

  <?php
 }

 ?>
<h2 class="single">&ldquo;<?= $i['title'];?>&rdquo;</h2>
<h3 class="byline"><?=  'by '.artistNames($i['album']['artists']);?></h3>


<?php
 break;

 case 'book':
 ?>


<div class="extra"><?= book_status_maker($i);?></div>
<h2><?= $i['title'];?></h2>
<h3 class="byline"><?=  'by '.artistNames($i['authors']);?></h3>

<?php

 endswitch;
}



?>
