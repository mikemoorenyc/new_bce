
</main>
<footer id="footer" class="media-item">



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



<!-- /* REMOVE IN PRODUCTION*/ -->
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


<script src="https://cdn.polyfill.io/v2/polyfill.min.js?features=IntersectionObserver,Array.from,CustomEvent"></script>

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

<!-- /* REMOVE IN DEV*/ -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= $keys['GA'];?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '<?= $keys['GA'];?>');
</script>
<!-- /* END REMOVE IN DEV*/ -->

<?php endif;?>



  </body>
</html>
