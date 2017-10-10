<?php
//POST TEMPLATE
?>
<?php include_once 'header.php';?>
<div id="main-post-content">


<DIV class="bs page-content-container">




  <div class="bs post-content  reading-section  ">
    <div class="bs header ">
      <div class="meta">

        Published on <?= get_the_date('M j Y')?>
      </div>
      <h1 class="article-heading">
        <?= $post->post_title;?>
      </h1>
      <?php if(!empty(get_the_excerpt())):?>
        <h2 class="tagline"><?= get_the_excerpt();?></h2>
      <?php endif;?>


    </div>

    <?php
    if(has_post_thumbnail()) {


      $thumbid = get_post_thumbnail_id();
      $html = postimage_shortcode(array('id' => get_post_thumbnail_id()),get_post(get_post_thumbnail_id())->post_excerpt);
      echo '<div class="blog-post-hero mar-20">'.$html.'</div>';
    }
     ?>
     <div class="content">
     <?= md_sc_parse($post->post_content);?>
    </div>

    <?php
    $tagged_post_id = $post->ID;
    include_once 'partial_tagged_list.php';
    ?>

    <?php
    $cta_vals = array(
     'post_type' => 'post',
     'orderby' => 'date',
     'dir' => 'DESC',
     'heading' => 'More from The Blog'
    );
    include_once 'partial_bottom_ctas.php';

    ?>



  </div>




</DIV>


</div>






<?php include_once 'footer.php';?>
