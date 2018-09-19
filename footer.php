
</main>
<footer id="footer">



  <div class="content-centerer no-padding inner font-sans">

    <?php
     $nav_items = wp_get_nav_menu_items('main-menu') ?: array();

    if(!empty($nav_items)):?>
    <ul class="contact-footer">
      <?php
      foreach($nav_items as $item) {

        ?>
        <li>
          <a href="<?= $item->url;?>" >
            <?= $item->title;?>

          </a>

        </li>

        <?php
      }


      ?>

    </ul>


    <?php endif; ?>
    <div class="copyright">
  &copy;<?= date('Y');?> Mike Moore
</div>



  </div>

</footer>

</div>
<?php if(!$_COOKIE['idc_ie9']):?>

<!--[if lte IE 9]>
<div id="ie9_mask" style="position:fixed; z-index:9999; background:currentColor; padding: 20px; text-align:center; left: 0; top: 0; width: 100%; height:100%;">
  <span style="color:white">I don't support your browser.</span><br/>
<a style="color:white;" href="https://www.google.com/chrome/browser/desktop/">Get a different one</a><br/><br/>
<button  style="color:white;  font-size:.75em; text-decoration:underline;">idc, let me look at your site</button>
</div>
<![endif]-->

<?php endif;?>

<!-- [/*REMOVE FROM PRODUCTION*/] -->

<div id="grid-lines" style="display:none;">
  <hr/>
  <hr/>
  <hr/>
  <hr/>
  <hr/>
  <hr/>
  <hr/>
  <hr/>
  <hr/>
  <hr/>
  <hr/>
  <hr/>
</div>

<!-- [/*END REMOVE FROM PRODUCTION*/] -->

<?php if($_COOKIE["liner_styles"]):?>
  <script>document.write('<div id="liner" style="<?= $_COOKIE["liner_styles"]; ?>"></div>');</script>
<?php endif; ?>
<script src="https://cdn.polyfill.io/v2/polyfill.min.js?features=IntersectionObserver,Array.from"></script>

<script defer  src="<?= $siteDir;?>/js/main.js?v=<?= $cacheBreaker;?>"></script>

<noscript>
  <style>
    .media-stream.container .media-item.blank {
      color: inherit;
      visibility: visible;
    }
    
  </style>
  
</noscript>


<!-- Global site tag (gtag.js) - Google Analytics -->
<?php if(!is_user_logged_in()):?>

<?php
include_once get_template_directory().'/partial_api_key_generator.php';
$keys = api_key_generator();
?>


<script async src="https://www.googletagmanager.com/gtag/js?id=<?= $keys['GA'];?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '<?= $keys['GA'];?>');
</script>


<?php endif;?>



  </body>
</html>
