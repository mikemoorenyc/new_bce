<?php
global $homeURL;
$homeURL = esc_url( home_url( ) );

$homeArray =parse_url($homeURL);


//GET POST SLUG
global $post;

$slug = slug_generator($post->ID);
$colors = array(
  '#f39',
  'red',
  '#960',
  '#f60',
  '#606',
  '#090',
  '#00f',
  "#222"
);
//GET POST PARENT
//$parentID = $post->post_parent;
//$parentslug = get_post($parentID)->post_name;
//GET THEME DIRECTORY
global $siteDir;
$siteDir = get_bloginfo('template_url');
//GET HOME URL

//var_dump(parse_url($homeURL));
//DECLARE THE SITE TITLE, SAVE A DB QUERY
global $siteTitle;
$siteTitle = get_bloginfo('name');
//DECLARE THE PAGE EXCERPT
global $siteDesc;
$siteDesc = get_bloginfo('description');

$pageThumb = false;
if(get_post_thumbnail_id()) {
  $pageThumb = get_all_image_sizes(get_post_thumbnail_id());
}


?>
<!DOCTYPE html>
<html lang="en" data-current="<?php echo $slug;?>" class="slug-<?php echo $slug;?>">
<head>

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

$excerpt = get_the_excerpt();
if(!empty($excerpt)){
 $siteDesc = strip_tags(md_sd_parse($excerpt));
}


?>
<meta name="description" content="<?php echo $siteDesc;?>" />

<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">


<?php wp_site_icon();?>


<?php
  $socialTitle = $pageTitle.' | '.$siteTitle;
  if($pageTitle === $siteTitle) {
    $socialTitle = $pageTitle;
  }
   $twitterCard = 'summary_large_image';
   $twitterUsername = get_option('twitterHandle','');


  if($pageThumb) {
    $socialImg = $pageThumb['full']['url'];
  } else {
    $socialImg = '';
    $twitterCard = 'summary';
    if(get_option( 'social_icon_image', '' )) {
      $socialImg = get_all_image_sizes(get_option( 'social_icon_image', '' ));
      $socialImg = $socialImg['thumbnail']['url'];
    }

  }

 ?>


<meta property="og:site_name" content="<?php echo $siteTitle;?>" />
<meta property="og:title" content="<?php echo $pageTitle;?> " />
<meta property="og:type" content="website" />
<meta property="og:url" content="<?php echo get_the_permalink();?>" />
<meta property="og:image" content="<?php echo $socialImg;?>" />
<meta property="og:description" content="<?php echo $siteDesc;?>" />

<?php
  if(!empty($twitterUsername)) {
    ?>
<meta name="twitter:site" content="<?php echo $twitterUsername;?>">
<meta name="twitter:creator" content="<?php echo $twitterUsername;?>">
    <?php
}
?>
<meta name="twitter:card" content="<?php echo $twitterCard;?>">
<meta name="twitter:title" content="<?php echo $pageTitle;?> ">
<meta name="twitter:description" content="<?php echo $siteDesc;?>">
<meta name="twitter:image" content="<?php echo $socialImg;?>">



<script>
var App = {
  colors: <?php echo json_encode($colors);?>,
  colormode: <?php echo json_encode($colormode);?>,
  urlParts: {
    homeURL: <?php echo json_encode($homeURL);?>,
    path: <?php echo json_encode($homeArray['path'].'/');?>,
    domain: <?php echo json_encode($homeArray['host']);?>
  }
}


</script>

<link rel="canonical" href="<?php echo get_the_permalink();?>">
</head>
<?php
  $firstColor = 'color:'.$colors[rand(0,count($colors)-1)].';';
  if($colormode === 'bw') {
    $firstColor = '';
  }

 ?>
<body id="top" data-colormode="bw">
<div id="css-checker"></div>

 <header id="top-header">
   <a id="spinner" alt="<?php echo $siteTitle;?>" href="<?php echo $homeURL;?>">&nbsp;</a>
   <button id="nav-opener" class="button-style">
     <span class="hide">Open Menu</span>
     <?php echo file_get_contents($siteDir.'/assets/svgs/icon_menu.svg');?>
   </button>
   <nav>
     <h1 id="logo">
       <a href="<?php echo $homeURL;?>">
         <div class="ball"></div>
         <span class="title"><?php echo $siteTitle;?></span>
       </a>
     </h1>
     <?php
     $nav_items = wp_get_nav_menu_items('main-menu');
     foreach($nav_items as $item) {

       $activeClass="";

       if($slug == slug_generator(url_to_postid($item->url ))) {
        $activeClass="active";
       }
       ?>
        <div class="nav-item <?php echo $activeClass;?>">
          <a href="<?php echo $item->url;?>"><?php echo $item->title;?></a>
        </div>
       <?php
     }

     ?>
    <?php
       $refreshURL = $_SERVER['REQUEST_URI'];
       $modeText = 'Simple Mode';
       $modeAlt = 'Colors are cool, but this is a little much.';
       $modeVar = 'bw';
       if($colormode === 'bw') {
        $modeText = 'Color Mode';
        $modeAlt = 'My eyes can take it!';
        $modeVar = 'color';
       }
     ?>
    <a href="<?php echo get_the_permalink().'?cmode='.$modeVar;?>" class="color-switcher" title="<?php echo $modeAlt;?>">
      <?php echo $modeText;?>
     </a>

   </nav>
 </header>
