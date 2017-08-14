<?php
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
  
  case 'show';
  ?>
  <h2><?= $i['show']['title']; ?></h2>
  <h3 class="meta"><?= $i['bingeCount'];?> episodes</h3>
  <?php
  
  
  
 endswitch; 
}



?>
