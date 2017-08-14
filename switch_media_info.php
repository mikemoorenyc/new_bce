<?php

function artistNames($i) {
 $artist_list = array_map(function($a){
 return $a['name'];
 },$i['album']['artists']);
 return implode(', ',$artist_list);
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
  <h2><?= $i['show']['title']; ?></h2>
  <h3 class="single">&ldquo;<?=$i['title'];?>&rdquo;</h2>
  <?php
  break;
  
  case 'show':
  ?>
  <h2><?= $i['show']['title']; ?></h2>
  <h3 class="meta">Watched <?= $i['bingeCount'];?> episodes</h3>
  <?php
  break;
 
  case 'album':
  ?>
<h2><?= $i['album']['title'];?></h2>
<h3 class="meta"><?= artistNames($i);?></h3>
  <?php
  break;
 
  case 'track':
  ?>
<h2 class="single">&ldquo;<?= $i['title'];?>&rdquo;</h2>
<h3 class="meta"><?= artistNames($i);?></h3>
<?php
 
 
 
?>
<?php
  
 endswitch; 
}



?>
