<?php
/**
 * Template Name: Contact Page
 */
?>
<?php
$security_questions = input_to_array(get_option( 'security_question_list', '' ));
?>
<?php if(!empty($_POST['security_number'])):?>
<?php
//WE HAVE A SUBMITTED FORM
$bad = [];
$submitted= false;
if(strtolower(trim($_POST['security_question'])) != $security_questions[$_POST['security_number'][1]) {
  $bad[] = 'security_question';
}
if(!filter_var(trim($_POST['form_email']), FILTER_VALIDATE_EMAIL)) {
    $bad[] = 'form_email';
}
if(empty(trim($_POST['form_name']))) {
	$bad[] = 'form_name';
}

if(empty($bad)) {
	//WE HAVE A WINNER
  $name = filter_var(trim($_POST['form_name']), FILTER_SANITIZE_STRING);
  $email = filter_var(trim($_POST['form_email']), FILTER_SANITIZE_EMAIL);
  $message = filter_var(trim($_POST['form_message']), FILTER_SANITIZE_STRING);
  $content_message = 'Name:'.$name.'<br/><br/>'.'Message:<br/>'.$message;
  $insert = wp_insert_post( array(
    'post_title' =>$email ,
    'post_type' => 'message',
    'post_status'=> 'publish',
    'post_content'=> $content_message
  ) );
  if($insert) {
   setcookie("alreadySubmitted", 'true', time()+3600);  
  }
}


                                                                        
                                                                        


?>
<?php endif;?>

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
$security_number = mt_rand(0,count($security_questions)-1);

?>


<form method="POST" action="<?= get_the_permalink();?>">
  <div class="form-row">
    <label for="form_name">Name</label>
    <input type="text" required id="form_name" name="form_name" />
  </div>
  <div class="form-row">
    <label for="form_email">Email</label>
    <input type="email" name="form_email" id="form_email" required />
  </div>
  <div class="form-row">
    <label for="form_message">Message</label>
    <textarea id="form_message" name="form_message"></textarea>
  </div>
  <div class="form-row">
    <label for="security_question"><?= $security_questions[$security_number][0];?></label>
    <input id="security_question" name="security_question" type="text" required />
  </div>

  <div class="form-row">
    <button type="submit">Send</button>

  </div>
  <input type="hidden" id="security_number" name="security_number" value="<?= $security_number;?>"/>


</form>



<?php include 'footer.php'; ?>
