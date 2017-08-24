<?php

$cacheBreaker = time();

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
  "#222",
  "#002FA7 "
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
<html lang="en" data-current="<?= $slug;?>" class="slug-<?= $slug;?>">
<head>

<link rel='stylesheet' href="<?= $siteDir;?>/css/main.css?v=<?= $cacheBreaker;?>" type="text/css" />


<?php
if ( is_front_page() ) {
  $html_title = $siteTitle;
  $pageTitle = $siteTitle;
} else {
  $pageTitle = $post->post_title;
  $html_title =  $post->post_title.' | '.$siteTitle;

}
if($tagged_as_page) {
  $html_title = $tagged_as_page.' | '.$siteTitle;
  $pageTitle = $tagged_as_page;
}
?>

<title><?= $html_title;?></title>
<!-- HERE'S WHERE WE GET THE SITE DESCRIPTION -->
<?php
$excerpt = $post->post_content;

if(!$excerpt){
 $excerpt = get_the_excerpt();
}
if(!$excerpt) {
  $excerpt = get_bloginfo('description');
}
$siteDesc = preg_replace( "/\r|\n/", " ", preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", strip_tags(md_sc_parse($excerpt))) );
$siteDesc = substr($siteDesc, 0, 150);
?>
<meta name="description" content="<?= $siteDesc;?>" />

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


<meta property="og:site_name" content="<?= $siteTitle;?>" />
<meta property="og:title" content="<?= $pageTitle;?> " />
<meta property="og:type" content="website" />
<meta property="og:url" content="<?= get_the_permalink();?>" />
<meta property="og:image" content="<?= $socialImg;?>" />
<meta property="og:description" content="<?= $siteDesc;?>" />

<?php
  if(!empty($twitterUsername)) {
    ?>
<meta name="twitter:site" content="<?= $twitterUsername;?>">
<meta name="twitter:creator" content="<?= $twitterUsername;?>">
    <?php
}
?>
<meta name="twitter:card" content="<?= $twitterCard;?>">
<meta name="twitter:title" content="<?= $pageTitle;?> ">
<meta name="twitter:description" content="<?= $siteDesc;?>">
<meta name="twitter:image" content="<?= $socialImg;?>">



<script>
var App = {
  colors: <?= json_encode($colors);?>,
  colormode: <?= json_encode($colormode);?>,
  URL: {
    homeURL: <?= json_encode($homeURL);?>,
    path: <?= json_encode($homeArray['path'].'/');?>,
    domain: <?= json_encode($homeArray['host']);?>
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

 <header id="top-header">
   <a id="spinner" alt="<?= $siteTitle;?>" href="<?= $homeURL;?>"></a>
   <input type="checkbox" alt="Toggle Navigation" role="button" id="navigation-toggle" class="button-style" />
   <div class="scrim"></div>
   <nav>
     <div class="lockup">
     <div id="top-logo">
       <a href="<?= $homeURL;?>">
         <span class="title font-sans">

           <?php
           $titleBreak = explode(' ',$siteTitle);
           foreach($titleBreak as $t) {
             echo '<span>'.$t.'</span> ';
           }

          ?>

         </span>
       </a>
     </div>
     <div class="top-tagline"><?php echo get_bloginfo('description');?></div>
    </div>
     <div class="nav-items">
     <?php
     $nav_items = wp_get_nav_menu_items('main-menu');
     foreach($nav_items as $item) {

       $activeClass="";

       if($slug == slug_generator(url_to_postid($item->url ))) {
        $activeClass="active";
       }
       ?>
        <div class="nav-item <?= $activeClass;?>">
          <a href="<?= $item->url;?>"><?= $item->title;?></a>
        </div>
       <?php
     }

     ?>
   </div>
     <button id="color-mode-switcher" class="color-mode-switcher" style="visibility:hidden;">
       <span class="slider"></span>
     </button>

   </nav>
   <div role="presentation" id="nav-opener" class="button-style">

     <span class="open middle-center">
       <?= file_get_contents(get_template_directory().'/assets/svgs/icon_menu.svg');?>
     </span>
     <span class="close middle-center">
       <?= file_get_contents(get_template_directory().'/assets/svgs/icon_x.svg');?>
     </span>
   </div>
 </header>

 <div id="main-content-container">
