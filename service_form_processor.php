
<?php
require_once("../../../wp-load.php");

if(empty($_POST)) {
 wp_redirect( $_SERVER['HTTP_REFERER'] ) ;
 die();
}



$security_questions = input_to_array(get_option( 'security_question_list', '' ));
$security_question = strtolower(trim($_POST['security_question']));
$security_number = intval($_POST['security_number']);
$security_combo = $security_questions[$security_number];
$get_url = esc_url( home_url( ) ).'/contact/?';


if($_POST['reset_cookie'] === 'reset') {
 if($security_question == strtolower($security_combo[1])) {
  setcookie("alreadySubmitted", '', time()+3600);
   $get_url .='';
 } else {
   $get_url .= 'badinput=security';
 }
} 
if($_POST['reset_cookie']!=='reset') {
   $badString = [];
   $parameters = [];
   if($security_question == strtolower($security_combo[1])) {
     $badString[] = 'security';
   }
   if(!filter_var(trim($_POST['form_email']), FILTER_VALIDATE_EMAIL)) {
      $badString[] = 'email';
   }
   if(empty(trim($_POST['form_name']))) {
    $badString = 'name';
   } 
  if(empty($badString)) {
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
      wp_mail(get_option('admin_email'), 'New Message from: '.$email,$message);
      $submitted = 'success';
    } else {
     $parameters[] = 'posterror=true'; 
    }
  }
  $parameters[] = 'badinput='.implode('|',$badString);
  $get_url .= implode('&',$parameters);
}
wp_redirect($get_url);
die();
?>
