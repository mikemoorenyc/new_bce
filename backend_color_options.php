<?php
add_filter('admin_init', 'color_options_setting');

function color_options_setting() {
  register_setting('general', 'color_options', 'esc_attr');
  add_settings_field('color_options', '<label for="color_options">'.__('Color Options' , 'color_options' ).'</label>' , 'color_options_input', 'general');
}
function color_options_input() {
  $value = get_option( 'color_options', '' );
?>
<textarea rows="10" cols="50" class="large-text code" id="color_optionst" name="color_options"><?= $value;?></textarea>

<?php
}


?>
