
</div>
<footer id="footer">
  <div class="inner font-sans">
  &copy;<?= date('Y');?> Mike Moore
  </div>

</footer>
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
  <div id="liner" style="<?= $_COOKIE["liner_styles"]; ?>"></div>
<?php endif; ?>

<script defer  src="<?= $siteDir;?>/js/main.js?v=<?= $cacheBreaker;?>"></script>
  </body>
</html>
