<div class="image book">
  <img src="<?= $i['img']; ?>" alt="<?= $i['title'];?>"/>
</div>

<div class="copy">
  <div class="time">
    <?= human_time_diff($i['timestamp'] ).' ago' ;?>
  </div>
  <h2 class="title"><?= $i['title'];?></h2>
  <div class="progress">
    <?php
    if(!empty($i['percent'])) {
    echo 'I&rsquo;ve read '.$i['percent'].'%';
    }
    if($i['status'] === 'read') {
    echo 'Finished reading';
    }
    if($i['status'] === 'currently-reading' && empty($i['percent']) {
    echo 'Started reading';
    }
    
    
    ?>
  </div>
  
  
</div>
