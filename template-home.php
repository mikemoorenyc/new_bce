<?php
/**
 * Template Name: Home Page
 */
?>


<?php include 'header.php'; ?>
<div class="top-content">
  <?php echo md_sc_parse($post->post_content);?>

</div>
<?php include 'footer.php'; ?>
