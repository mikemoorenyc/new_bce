<?php

function artistNames($i) {
 return implode(', ',$i);
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
<h3 class="meta"><?= artistNames($i['album']['artists']);?></h3>
  <?php
  break;
 
  case 'track':
  ?>
<h2 class="single">&ldquo;<?= $i['title'];?>&rdquo;</h2>
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
<h3 class="meta"><?= artistNames($i['album']['artists']);?></h3>
<?php
 break;
 
 case 'book':
 ?>
<h2><?= $i['title'];?></h2>

<?php
  
 endswitch; 
}



?>
