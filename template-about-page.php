<?php
/**
 * Template Name: About Page
 */
?>
<?php include_once 'header.php';?>
<?php $landing_post = $post;?>

<?php include_once 'partial_landing_page_header.php';?>
<div class="about-page gl-mod col-2-setup " >
<div class="picture" style=" background:green;"><div style="height:200px">asdf</div></div>
<div id="main-about-content" class=" left-col about-page reading-section">
 <?=  md_sc_parse($post->post_content); ?>
</div>

<div class="right-col about-page like-lists">
<?php
$things_i_like = input_to_array(get_post_meta( $post->ID, 'things_i_like', true));

if(!empty($things_i_like)):?>
<div class="about-page like-block">
  <h2>Things I Like</h2>
  <ul>
    <?php foreach($things_i_like as $t):?>
    <li><?= $t[0];?></li>
  <?php endforeach;?>
  </ul>
</div>

<?php endif;?>
<?php
$things_i_dont_like = input_to_array(get_post_meta( $post->ID, 'things_i_dont_like', true));
if(!empty($things_i_dont_like)):?>
<div class="about-page like-block">
  <h2>Things I Don't Like</h2>
  <ul>
    <?php foreach($things_i_dont_like as $t):?>
    <li><?= $t[0];?></li>
  <?php endforeach;?>
  </ul>
</div>

<?php endif;?>
</div>

</div>


<?php include_once 'footer.php';?>
