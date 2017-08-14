
<div class="image cd">
  <img src="<?= $i['album']['img']; ?>" alt="<?= $i['title'];?>"/>
</div>

<div class="copy">
   <div class="time">
    <?= human_time_diff($i['timestamp'] ).' ago' ;?>
   </div>
  <h2><?= $i['album']['title'];?></h2>

  <div class="byline">
   <?php
    $artistNames = array_map(function($a){
      return $a['name'];
    },$i['album']['artists']);
    echo implode(', ', $artistNames);
    ?>
  </div>
</div>
