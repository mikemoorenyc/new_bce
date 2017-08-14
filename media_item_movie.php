<div class="image movie">
  <img class="post-load" data-type="movie" data-url="<?= urlencode('https://api.themoviedb.org/3/movie/'.$i['ID']);?>" alt="<?= $i['title'];?>"/>
</div>

<div class="copy">
   <div class="time">
    <?= human_time_diff($i['timestamp'] ).' ago' ;?>
   </div>
  <h2><?= $['title'];?></h2>


</div>
