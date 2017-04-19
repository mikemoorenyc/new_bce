<?php
function add_insert_image_id_button() {
  ?>
  <button id="add-image-id" class="button " type="button" data-modal-title="Insert Image ID" data-modal-button-copy="Insert ID" data-content="pull-image-id-modal" >Insert Image ID</button>
  <?php
}
add_action('media_buttons', 'add_insert_image_id_button');


 ?>
 <?php
 add_action('admin_footer-post.php', 'remove_media_script');
 add_action('admin_footer-post-new.php', 'remove_media_script');
 function remove_media_script() {
   wp_enqueue_media();
   ?>
 <script>
 jQuery(document).ready(function($){
   $("#insert-media-button").hide();
   $('#ed_toolbar').height(0).css('min-height','0');
   $('#ed_toolbar *').hide();
   $('button#add-image-id').click(function(e){
     e.preventDefault();
     var btn = $(this);
     $(this).blur();
     var end = $('textarea#content').prop("selectionStart");
     var image = wp.media({
            title: $(btn).attr('data-modal-title'),
            multiple: false
          }).open()
          .on('select', function(e){

            var uploaded_image = image.state().get('selection').first();
            var idString = uploaded_image.id.toString();
            var tValue = $('textarea#content').val();
            $('textarea#content').val(tValue.substring(0, end)+idString+ tValue.substring( end));
            $('textarea#content').focus()
            $('textarea#content').prop("selectionStart", end+idString.length).prop("selectionEnd", end+idString.length);

          });
   });
 });
 </script>

   <?php
 }
  ?>
