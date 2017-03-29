<?php
//GET POST SLUG
global $post;
$slug = $post->post_name;
$colors = array(
  'Violet ',
  'red',
  'brown',
  'orange',
  'purple',
  'green',
  'blue'
);
//GET POST PARENT
//$parentID = $post->post_parent;
//$parentslug = get_post($parentID)->post_name;
//GET THEME DIRECTORY
global $siteDir;
$siteDir = get_bloginfo('template_url');
//GET HOME URL
global $homeURL;
$homeURL = esc_url( home_url( ) );
//DECLARE THE SITE TITLE, SAVE A DB QUERY
global $siteTitle;
$siteTitle = get_bloginfo('name');
//DECLARE THE PAGE EXCERPT
global $siteDesc;
$siteDesc = get_bloginfo('description');
?>
<!DOCTYPE html>
<html lang="en" data-current="<?php echo $slug;?>" class="slug-<?php echo $slug;?>">
<head>

<!-- ABOVE THE FOLD CSS -->
<link rel='stylesheet' href="<?php echo $siteDir;?>/css/main.css?v=<?php echo time();?>" type="text/css" />


<?php
if ( is_front_page() ) {
  $pageTitle = $siteTitle;
  ?>
  <title><?php echo $siteTitle;?></title>
  <?php
} else {
  $pageTitle = get_the_title();
  ?>

  <title><?php echo $pageTitle;?> | <?php echo $siteTitle;?></title>
  <?php
}
?>

<!-- HERE'S WHERE WE GET THE SITE DESCRIPTION -->
<?php

$excerpt = get_the_except();
if(!empty($excerpt)){
 $siteDesc = $excerpt; 
}
  
  
else: ?>

<?php endif; ?>
<meta name="description" content="<?php echo $siteDesc;?>" />

<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">


<?php wp_site_icon();?>


<?php
  $socialTitle = $pageTitle.' | '.$siteTitle;
  if($pageTitle === $siteTitle) {
    $socialTitle = $pageTitle;
    
   $twitterCard = 'summary_large_image';
   $twitterUsername = get_option('twitterHandle','');
 ?>

<!-- FACEBOOK & TWiTTER TAGS REMOVED ON COMPILATION UNLESS YOU UNCOMMENT-->
<!--
<meta property="og:site_name" content="<?php echo $siteTitle;?>" />
<meta property="og:title" content="<?php echo $pageTitle;?> " />
<meta property="og:type" content="website" />
<meta property="og:url" content="<?php echo get_the_permalink();?>" />
<meta property="og:image" content="<?php echo $siteDir;?>/assets/blue-pin.jpg" />
<meta property="og:description" content="<?php echo $siteDesc;?>" />

<?php
  if(!empty($twitterUsername)) {
    ?>
<meta name="twitter:site" content="<?php echo $twitterUsername;?>">
<meta name="twitter:creator" content="<?php echo $twitterUsername;?>">
    <?php
    
?>
<meta name="twitter:card" content="<?php echo $twitterCard;?>">
<meta name="twitter:title" content="<?php echo $pageTitle;?> ">
<meta name="twitter:description" content="<?php echo $siteDesc;?>">
<meta name="twitter:image" content="<?php echo $siteDir;?>/assets/imgs/1.jpg">
-->


<script>
var App = {
  colors: <?php echo json_encode($colors);?>
}


</script>

<link rel="canonical" href="<?php echo get_the_permalink();?>">
</head>

<body id="top" style="color: <?php echo $colors[rand(0,count($colors)-1)];?>;">
<div id="css-checker"></div>
