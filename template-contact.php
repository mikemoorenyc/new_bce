<?php
/**
 * Template Name: Contact Page
 */
?>
<?php
$security_questions = input_to_array(get_option( 'security_question_list', '' ));
$alreadySubmitted = $_COOKIE['alreadySubmitted'];

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

<?php
session_start();
$security_number = mt_rand(0,count($security_questions)-1);

?>



<form id="main-contact-form" method="POST" action="<?= $siteDir.'/service_form_processor.php';?>">
  <?php if($_SESSION['post_status'] == 'success'):?>
    <h2> Thank you for sending me a message.</h2>

  <?php endif;?>
  <?php if($_SESSION['post_status'] == 'failure'):?>
    <h2> There was an error submitting your form. Try again. </h2>
	<?php endif;?>
  <?php if($alreadySubmitted === 'true'):?>
    <h2>I think you already submitted. Answer this question to prove your human first.</h2>
  <?php endif;?>

<?php if($_SESSION['post_status']!=='success'):?>
 
  <?php if($alreadSubmitted !== 'true'):?>
  
  <div class="form-row">
    <label for="form_name">Name</label>
    <input type="text" required id="form_name" name="form_name" />
      <?php if(in_array("name", $_SESSION['form_errors']):?>
      <div class="error-msg">You filled this out wrong. Try again.</div>
      <?php endif;?>
  </div>
  <div class="form-row">
    <label for="form_email">Email</label>
    <input type="email" name="form_email" id="form_email" required />
      <?php if(in_array("email", $_SESSION['form_errors']):?>
      <div class="error-msg">You filled this out wrong. Try again.</div>
      <?php endif;?>
  </div>
  <div class="form-row">
    <label for="form_message">Message</label>
    <textarea id="form_message" name="form_message"></textarea>
  </div>

 <?php endif;?> 
  
  <div class="form-row">
    <label for="security_question"><?= $security_questions[$security_number][0];?></label>
    <input id="security_question" name="security_question" type="text" required />
      <?php if(in_array("security", $_SESSION['form_errors']):?>
      <div class="error-msg">You answered this question wrong. Try again.</div>
      <?php endif;?>
  </div>

  <div class="form-row">
    <button type="submit">Send</button>

  </div>
  <input type="hidden" id="security_number" name="security_number" value="<?= $security_number;?>"/>


<?php endif;?>
</form>
<?php
session_unset();
session_destroy();
?>


<?php include 'footer.php'; ?>
