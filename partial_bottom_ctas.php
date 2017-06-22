
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
$pagelist = get_posts($args);
$pages = [];
foreach ($pagelist as $page) {
   $pages[] = $page->ID;
}
$current = array_search($post->ID, $pages);
$nextID = $pages[$current+1];
$prevID = $pages[$current-1];

?>
<div class="gl-mod bottom-ctas meta">
<?php
  $url = $cta_vals['empty_link']['url'];
  $title = $cta_vals['empty_link']['title'];
  if(!empty($prevID)) {
    $url = get_the_permalink($prevID);
    $title = get_the_title($prevID);
  }
?>
<a href="<?= $url;?>" class="previous-link">
     <span>
       <?= file_get_contents(get_template_directory().'/assets/svgs/icon_arrow_right.svg');?> <?= $title;?>
     </span>
</a>



<?php
  $url = $cta_vals['empty_link']['url'];
  $title = $cta_vals['empty_link']['title'];
  if(!empty($nextID)) {
    $url = get_the_permalink($nextID);
    $title = get_the_title($nextID);
  }
?>
<a href="<?= $url;?>" class="previous-link">
     <span>
      <?= $title;?> <?= file_get_contents(get_template_directory().'/assets/svgs/icon_arrow_right.svg');?>
     </span>
</a>


</div>
