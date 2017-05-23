<?php
/**
 * Template Name: About Page
 */
?>
<?php include_once 'header.php';?>

<div id="main-about-content">
 <?=  md_sc_parse($post->post_content); ?>
</div>

<?php
$things_i_like = input_to_array(get_post_meta( $post->ID, 'things_i_like', true));
if(!empty($things_i_like)):?>
<div class="like-block">
  <h2>Things I Like</h2>
  <ul>
    <?php foreach($things_i_likes as $t):?>
    <li><?= $t;?></li>
  </ul>
</div>

<?php endif;?>
<?php
$things_i_dont_like = input_to_array(get_post_meta( $post->ID, 'things_i_dont_like', true));
if(!empty($things_i_dont_like)):?>
<div class="like-block">
  <h2>Things I Like</h2>
  <ul>
    <?php foreach($things_i_dont_likes as $t):?>
    <li><?= $t;?></li>
  </ul>
</div>

<?php endif;?>



<?php include_once 'footer.php';?>
