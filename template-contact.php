<?php
/**
 * Template Name: Contact Page
 */
?>
<?php
$security_questions = input_to_array(get_option( 'security_question_list', '' ));
?>



<?php include 'header.php'; ?>
<h1 class="contact-page-header"><?= $post->post_title;?></h1>
<div class="contact-page-content"><?= md_sc_parse($post->post_content);?></div>


<?php
$social_links = get_post_meta( $post->ID, 'social media link');

if(!empty($social_links)):?>
<h2 class="social-media-links-header">Social Media Links</h2>
<ul class="social-links-list">
<?php foreach($social_links as $s):?>
  <li class="social-link">
<?php
$a = explode(',',$s);
?>
<a href="<?= trim($a[1]);?>" target="_blank"><?= trim($a[0]);?></a>




  </li>
<?php endforeach;?>
</ul>

 <?php endif; ?>

<form method="POST" action="<?= get_the_permalink();?>">
  <div class="form-row">
    <label >Name</label>
    <input type="text" required />
  </div>
  <div class="form-row">
    <label >Email</label>
    <input type="email" required />
  </div>
  <div class="form-row">
    <label >Message</label>
    <textarea></textarea>
  </div>
  <div class="form-row">
    <label >What is our president's last name?</label>
    <input type="text" required />
  </div>

  <div class="form-row">
    <button type="submit">Send</button>

  </div>



</form>



<?php include 'footer.php'; ?>
