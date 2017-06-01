<?php
require_once("../../../wp-load.php");

if(empty($_POST)) {
 wp_redirect( $_SERVER['HTTP_REFERER'] ) ;
 die();
}
session_start();
$url_path = parse_url(home_url( ));


$security_questions = input_to_array(get_option( 'security_question_list', '' ));
$security_question = strtolower(trim($_POST['security_question']));
$security_number = intval($_POST['security_number']);
$security_combo = $security_questions[$security_number];
$get_url = esc_url( home_url( ) ).'/contact/?';
$form_errors = [];
$post_status = 'success';

if($_POST['reset_cookie'] === 'reset') {
 if($security_question == strtolower($security_combo[1])) {
  $_SESSION['alreadySubmitted'] = 'reset';
  $post_status = '';
 } else {
   $form_errors[] = 'security';
   $post_status = '';
 }
}
if($_POST['reset_cookie']!=='reset') {
   $badString = [];
   $parameters = [];
   if($security_question != strtolower($security_combo[1])) {
     $form_errors[] = 'security';
   }
   if(!filter_var(trim($_POST['form_email']), FILTER_VALIDATE_EMAIL)) {
      $form_errors[] = 'email';
   }
   if(empty(trim($_POST['form_name']))) {
    $form_errors[] = 'name';
   }
  if(empty($form_errors)) {
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

      wp_mail(get_option('admin_email'), 'New Message from: '.$email,$message);
      $_SESSION['alreadySubmitted'] = 'true';
    } else {
     $post_status = 'failure';
    }
  } else {
    $post_status = 'failure';
  }
}



$_SESSION['post_status'] = $post_status;
$_SESSION['form_errors'] = $form_errors;

if($_GET['format'] === 'json') {
 $form_html = file_get_contents($_SERVER['HTTP_REFERER']);
 $doc = new DOMDocument();
 $doc->loadHTML($form_html);
 echo $doc->getElementById('main-contact-form');
 die();
}
wp_redirect( $_SERVER['HTTP_REFERER'] ) ;
die();
?>
