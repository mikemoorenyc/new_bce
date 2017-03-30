<?php
add_filter('admin_init', 'social_icon_setting');
function social_icon_setting() {
register_setting('general', 'social_icon_image', 'esc_attr');
add_settings_field('social_icon_image', '<label for="social_icon_image">'.__('Social Icon Image' , 'social_icon_image' ).'</label>' , 'social_icon_selector', 'general');
}
function social_icon_selector() {
  wp_enqueue_media();
  $value = get_option( 'social_icon_image', '' );
  if(empty($value)) {
    $verb = 'Upload';
    $smimg = '';
    $id = '';
  } else {
    $verb = 'Change';
    $imgArray = wp_get_attachment_image_src($value, 'thumbnail');
    $smimg = $imgArray[0];
    $smimg = $smimg;
    $id = $value;
  }
  ?>
  <div id="map-thumb" style="margin-bottom: 10px;"> </div>
    <input type="hidden" id="social_icon_image" name="social_icon_image" value="<?php echo $value;?>" class="regular-text"/ >
    <button id="social-icon-opener" class="button"><?php echo $verb;?> Add Social Icon</button>

<style>
  #map-thumb {
   background: #ccc;
   width: 100px;
   height: 100px;
   background-size:cover;
   background-position:center center;
  }

</style>

  <script>
    jQuery(document).ready(function($){
      function stateUpdater(id,url) {
        if(id) {
          $('#social-icon-opener').text('Change Social Icon');
        } else {
          $('#social-icon-opener').text('Add Social Icon');
        }
        $('input#social_icon_image').val(id);
        if(url) {
          var fileName = url;
          $('#map-thumb').css({
            'background-image': 'url('+fileName+')'
          });
        } else {
          $('#map-thumb').css({
            'background-image':'none'
          });
        }
      }
      stateUpdater('<?php echo $id;?>', '<?php echo $smimg;?>');
      $('#social-icon-opener').click(function(e) {
        e.preventDefault();
        var image = wp.media({
            title: 'Select or Upload a Social Icon',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){

            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image

            var theurl;
            theurl = uploaded_image.attributes.url;
            var image_url = uploaded_image.url;
            stateUpdater(uploaded_image.id, theurl);
            // Let's assign the url value to the input field
            //$('#image_url').val(image_url);
        });
      });
    });
    </script>

  <?php

}




?>
