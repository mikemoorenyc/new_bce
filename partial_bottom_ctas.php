
<?php
if(!$cta_vals['dir']) {
 $cta_vals['dir'] = 'ASC';
}

$cta_args = array(
    'post_type' 		=> $cta_vals['post_type'],//project
    'orderby' 			=> $cta_vals['orderby'],//orderby
    'order' 			=> $cta_vals['dir'],
    'posts_per_page' => -1
);
$pagelist = get_posts($cta_args);
$pages = [];
foreach ($pagelist as $page) {
   $pages[] = $page->ID;
}
$current = array_search($post->ID, $pages);
$total = intval(wp_count_posts($cta_vals['post_type'])->publish);
$nextID = $pages[$current+1];
$prevID = $pages[$current-1];

if(empty($nextID) && $total > 2) {
  $nextID = $pages[$current - 2];
}
if(empty($prevID) && $total > 2) {
  $prevID = $pages[$current + 2];
}

?>
<?php if(!empty($prevID) || !empty($nextID)):?>
<div class="gl-mod bottom-ctas meta">
  <h3 class=" sub-heading with-line"><?= $cta_vals["heading"];?></h3>
  <ul class="gl-mod link-box-list">

    <?php if(!empty($prevID)):?>
      <li class="before-block"><a class="mid-heading no-underline before-block gl-line-hover h-child" href="<?= get_the_permalink($prevID);?>"><?= get_the_title($prevID);?></a></li>
    <?php endif;?>

    <?php if(!empty($nextID)):?>
      <li class="before-block"><a class="mid-heading no-underline before-block gl-line-hover h-child" href="<?= get_the_permalink($nextID);?>"><?= get_the_title($nextID);?></a></li>
    <?php endif;?>



  </ul>




</div>


<?php endif;?>
