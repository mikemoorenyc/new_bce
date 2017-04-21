<?php
function bottom_cta_maker($post_type,$orderby,$empty_link) {
  global $siteDir;
  $args = array(
    'post_type' 		=> $post_type,//project
    'orderby' 			=> $orderby,//orderby
    'order' 			=> 'ASC',
    'posts_per_page' => -1
  );
  $pagelist = get_pages($args);
  $pages = array();
  foreach ($pagelist as $page) {
     $pages[] += $page->ID;
  }
  $current = array_search($post->ID, $pages);
  $nextID = $pages[$current+1];
  $prevID = $pages[$current-1];

   ?>

  <div class="bottom-cta-links">
  <?php
  $url = $empty_link['url'];
  $title = $empty_link['title'];
  if(!empty($prevID)) {
    $url = get_the_permalink($prevID);
    $title = get_the_title($prevID);
  }
   ?>
   <a href="<?= $url;?>" class="prev-link">
     <span>
       <?= file_get_contents($siteDir.'/assets/svgs/icon_arrow_right.svg');?> <?= $title;?>
     </span>
   </a>

   <?php
   $url = $emptyLink['url'];
   $title = $emptyLink['title'];
   if(!empty($nextID)) {
     $url = get_the_permalink($nextID);
     $title = get_the_title($nextID);
   }
    ?>

    <a href="<?= $url;?>" class="next-link">
      <span>
        <?= $title;?><?= file_get_contents($siteDir.'/assets/svgs/icon_arrow_right.svg');?>
      </span>
    </a>

  </div>

<?php
}
 ?>
