<?php
/**
 * Template Name: Contact Page
 */

session_start();
$security_questions = input_to_array(get_option( 'security_question_list', '' ));
$alreadySubmitted = $_COOKIE['alreadySubmitted'];

?>

<?php include 'header.php'; ?>

<?php $landing_post = $post;?>
<?php include_once 'partial_landing_page_header.php';?>
<div class="gl-mod col-2-setup clearfix">
  <div class="left-col reading-section"><?= md_sc_parse($post->post_content);?></div>

  <div class="right-col contact-page social-links">
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
    <a href="<?= trim($a[1]);?>" target="_blank" class="font-sans no-underline">
      <?= file_get_contents(get_template_directory().'/assets/svgs/icon_'.strtolower(trim($a[0])).'.svg');?>
      <?= trim($a[2]);?>
    </a>




      </li>
    <?php endforeach;?>
    </ul>

     <?php endif; ?>
  </div>


</div>






<?php

$security_number = mt_rand(0,count($security_questions)-1);
$form_errors = [];
if($_SESSION['form_errors'] !== null) {
  $form_errors = $_SESSION['form_errors'];
}
?>

<div id="contact-form-container">

<form id="main-contact-form" class="contact-page contact-form font-sans clearfix" method="POST" action="<?= $siteDir.'/service_form_processor.php';?>">
  <input type="hidden" id="security_number" name="security_number" value="<?= $security_number;?>"/>
  <?php if($_SESSION['post_status'] == 'success'):?>
    <h2> Thank you for sending me a message.</h2>

  <?php endif;?>
  <?php if($_SESSION['post_status'] == 'failure'):?>
    <h2 class="error-msg"> <span>There was an error submitting your form. Try again.<span> </h2>
	<?php endif;?>
  <?php if($alreadySubmitted === 'true' && empty($_SESSION['post_status'])):?>
    <input type="hidden" id="reset_cookie" name="reset_cookie" value="reset" />
    <h2>I think you already submitted. Answer this question to prove your human first.</h2>
  <?php endif;?>

<?php if($_SESSION['post_status']!=='success'):?>

  <?php if($alreadySubmitted !== 'true'):?>

  <div class="form-row input">
    <label for="form_name">Name</label>
    <input type="text" required id="form_name" class="<?php if(in_array('name', $form_errors)){echo 'error';} ?>" name="form_name" />
      <?php if(in_array("name", $form_errors)):?>
        <span class="error-bug "><?= file_get_contents(get_template_directory().'/assets/svgs/icon_error.svg');?></span>
      <div class="error-msg">You filled this out wrong. Try again.</div>
      <?php endif;?>
  </div>
  <div class="form-row input">
    <label for="form_email">Email</label>
    <input type="email" name="form_email" class="<?php if(in_array('email', $form_errors)){echo 'error';} ?>" id="form_email" required />
      <?php if(in_array("email", $form_errors)):?>
        <span class="error-bug "><?= file_get_contents(get_template_directory().'/assets/svgs/icon_error.svg');?></span>
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
    <input id="security_question" class="<?php if(in_array('security', $form_errors)){echo 'error';} ?>" name="security_question" type="text" required />
      <?php if(in_array("security", $form_errors)):?>
        <span class="error-bug "><?= file_get_contents(get_template_directory().'/assets/svgs/icon_error.svg');?></span>
      <div class="error-msg">You answered this question wrong. Try again.</div>
      <?php endif;?>
  </div>

  <div class="form-row submit">
    <button type="submit">Send your message</button>

  </div>



<?php endif;?>
</form>
</div>
<?php
session_unset();
session_destroy();
?>


<?php include 'footer.php'; ?>
