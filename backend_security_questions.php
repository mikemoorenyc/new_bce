<?php
add_filter('admin_init', 'security_questions_settings');

function security_questions_settings() {
  register_setting('general', 'security_question_list', 'esc_attr');
  add_settings_field('security_question_list', '<label for="security_question_list">'.__('Security Question List' , 'security_question_list' ).'</label>' , 'security_question_list_textarea', 'general');
}
function security_question_list_textarea() {
  $value = get_option( 'security_question_list', '' );
?>
<textarea class="large-text code" id="security_question_list" name="security_question_list">
  <?= $value;?>
</textarea>

<?php
}


?>
