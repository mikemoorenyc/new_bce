
<div class="image cd">
  <img src="<?= $i['album']['img']; ?>" alt="<?= $i['title'];?>"/>
</div>

<div class="copy">
   <div class="time">
    <?= human_time_diff($i['timestamp'] ).' ago' ;?>
   </div>
  <h2 class="singular"><?= $i['title'];?></h2>
  <?php
  if($i['listenCount'] > 1) {
   ?>
  <div class="meta">
    Listened <?=$i['listenCount'];?> times
  </div>
  <?php
  }
  
  ?>
  <div class="byline">
   <?php
    $artistNames = array_map(function($a){
      return $a['name'];
    },$i['album']['artists']);
    echo implode(', ', $artistNames);
    ?>
  </div>
</div>
