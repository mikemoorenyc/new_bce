<?php
header('Content-type: image/svg+xml');

?>
<svg id="FavLogo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
  <style>
    #FavLogo {
      fill: none;
      stroke:<?= ($_GET["color"]) ? $_GET["color"] : "white" ; ?>;
    }
    
  </style>
  <circle cx="16" cy="16" r="15.5"/>
  <circle cx="16" cy="16" r="10.5" stroke-dasharray="3.5,2.5"/>
  
</svg>