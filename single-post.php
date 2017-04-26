<?php
//POST TEMPLATE
?>
<?php include_once 'header.php';?>
<div id="main-post-content">
  <div class="blog-post-header content-padding-spacer nav-spacer">
    <div class="post-date font-sans">
      <?php
          $theDate = get_the_date('Y-m-d');
        $pdate = new DateTime($theDate);
        $ctime = current_time('Y-m-d');
        $currentdate = new DateTime($ctime);
        $diff = date_diff($pdate,$currentdate);
        if($diff->y > 0) {
          $yearstamp = get_the_date('Y');
        }
       ?>
      Published on <?= get_the_date('M j')?> <?= $yearstamp;?>
    </div>
    <h1 class="blog-post-title story-title">
      <?= $post->post_title;?>
    </h1>
    <?php if(!empty(get_the_excerpt())):?>
      <h2 class="blog-post-excerpt story-sub-title"><?= get_the_excerpt();?></h2>
    <?php endif;?>


  </div>


  <?php
  if(has_post_thumbnail()) {


    $thumbid = get_post_thumbnail_id();
    $html = postimage_shortcode(array('id' => get_post_thumbnail_id()),get_post(get_post_thumbnail_id())->post_excerpt);
    echo '<div class="blog-post-hero">'.$html.'</div>';
  }
   ?>

  <div class="post-content content-padding-spacer story-content">
     <?= md_sc_parse($post->post_content);?>

     <?php
         $tagged = wp_get_post_terms( $post->ID, 'post_tag' );
         if(!empty($tagged)):?>

     <div class="project-tagged-in font-sans">
     <h3>Tagged in:</h3>
     <?php foreach($tagged as $t):?>

     <span><?= $t->name;?></span>
     <?php endforeach;?>
     </div>

     <?php endif; ?>

  </div>


</div>


<?php
bottom_cta_maker(
  'post',
  'date',
  array(
    "title" => 'All Blog Posts',
    "url" => $homeURL.'/blog/'
  ),
    'DESC'
);

?>


<?php include_once 'footer.php';?>
