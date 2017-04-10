function md_sc_parse($string) {
  $theReturn = '';
  $Parsedown = new Parsedown();
  $theReturn = $Parsedown->text($string);
  return do_shortcode( $theReturn );
}
