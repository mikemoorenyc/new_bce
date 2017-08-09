<?php
add_filter('admin_init', 'api_keys_option');
function api_keys_option() {
  register_setting('general', 'api_keys', 'esc_attr');
   add_settings_field('api_keys', '<label for="api_keys">'.__('API Keys' , 'api_keys' ).'</label>' , "api_keys_editor", 'general');
}
function api_keys_editor()
{
    $value = get_option( 'api_keys', '' );
    echo '<textarea id="api_keys" name="api_keys" rows="10" class="large-text code">'. $value.'</textarea>';
}
 ?>
