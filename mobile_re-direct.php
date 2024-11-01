<?php
/*
Plugin Name: Mobile Re-direct
Description: Forwards mobile users to a specific web address.
Author: <a href="http://www.mobilebusinessplus.com">MobileBusinessPlus.com</a>
Version: 1.0

*/

function jc_mobile_redirect_active() {
  $mobile_redirect_options['jc_mobile_web_address'] = '';
  $mobile_redirect_options['jc_mobile_redirection_type'] = 'php';

  add_option('mobile_redirect_options', $mobile_redirect_options);
}

register_activation_hook(__FILE__, 'jc_mobile_redirect_active');

function jc_mobile_redirect_deactive() {
  //delete_option('mobile_redirect_options');
}

register_deactivation_hook(__FILE__, 'jc_mobile_redirect_deactive');

$mobile_redirect_options = get_option('mobile_redirect_options');
if (!$mobile_redirect_options) {
  $mobile_redirect_options = array('jc_mobile_web_address' => '', 'jc_mobile_redirection_type' => 'php');
  update_option('mobile_redirect_options', $mobile_redirect_options);
}

if (is_admin()) {

  add_action('admin_menu', 'jc_option_page_and_plugin_action_links');

  function jc_option_page_and_plugin_action_links() {
	add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'jc_set_plugin_action_link', 10, 2 );
	add_options_page('Mobile Re-direct', 'Mobile Re-direct', 'manage_options', basename(__FILE__), 'jc_display_admin_options_page');
  }

  function jc_set_plugin_action_link($links, $file) {
    $settings_link = '<a href="options-general.php?page=' . basename(__FILE__) . '">' . __('Settings', 'jc_mobile_redirect') . '</a>';
    array_unshift($links, $settings_link); // before other links

    return $links;
  }

  function save_mobile_redirect_options(){
	return update_option('mobile_redirect_options', $mobile_redirect_options);
  }

  function jc_display_admin_options_page() {
    global $mobile_redirect_options;

	if (isset($_POST['mobile_redirect_save'])) {
	  if (wp_verify_nonce($_POST['_wpnonce'], 'mobile_redirect_nonce')) {
		$mobile_redirect_options['jc_mobile_web_address'] = $_POST['jc_mobile_web_address'];
		$mobile_redirect_options['jc_mobile_redirection_type'] = $_POST['jc_mobile_redirection_type'];
		update_option('mobile_redirect_options', $mobile_redirect_options);

		echo '<div class="updated"><p>' . __('Success! Your changes were successfully saved!', 'jc_mobile_redirect') . '</p></div>';
	  }	else {
		echo '<div class="error"><p>' . __('Whoops! There was a problem with the data you posted. Please try again.', 'jc_mobile_redirect') . '</p></div>';
	  }
	}
    //echo '<pre>';
    //var_dump($mobile_redirect_options);
    //echo '</pre>';
?>

<div class="wrap">
<div class="icon32" id="icon-options-general"><br/></div>
<h2>Mobile Re-direct</h2>
<form method="post" id="mobile_redirect_options">
<?php wp_nonce_field('mobile_redirect_nonce'); ?>
<table class="form-table">
<tr valign="top">
  <th scope="row"><?php _e('User Agent:', 'jc_mobile_redirect'); ?></th>
  <td>
  <input name="jc_mobile_user_agent" type="text" id="jc_mobile_user_agent" size="70" readonly="readonly" value="<?php echo stripslashes(htmlspecialchars($_SERVER['HTTP_USER_AGENT'])); ?>"/>
  <!--<span class="description"><?php _e('The text/HTML to display before the list of pages.', 'jc_mobile_redirect'); ?></span>-->
  </td>
</tr>
<tr valign="top">
  <th scope="row"><?php _e('Web Address:', 'jc_mobile_redirect'); ?></th>
  <td>
  <input name="jc_mobile_web_address" type="text" id="jc_mobile_web_address" size="70" value="<?php echo stripslashes(htmlspecialchars($mobile_redirect_options['jc_mobile_web_address'])); ?>"/>
  <!--<span class="description"><?php _e('The text/HTML to display before the list of pages.', 'jc_mobile_redirect'); ?></span>-->
  </td>
</tr>
<tr valign="top">
  <th scope="row"><?php _e('Redirection Type:', 'jc_mobile_redirect'); ?></th>
  <td>
  <select name="jc_mobile_redirection_type" id="jc_mobile_redirection_type">
  <option value="php" <?php echo ($mobile_redirect_options['jc_mobile_redirection_type']=='php' ? 'selected="selected"' : ''); ?>>PHP</option>
  <option value="javascript" <?php echo ($mobile_redirect_options['jc_mobile_redirection_type']=='javascript' ? 'selected="selected"' : ''); ?>>JavaScript</option>
  </select>
  <span class="description"><?php _e('If the mobile doesnt support javascript, visitors will stay on the normal website.', 'jc_mobile_redirect'); ?></span>
  </td>
</tr>
</table>
	
<p class="submit">
<input type="submit" value="Save Changes" name="mobile_redirect_save" class="button-primary" />
</p>
</form>

</div>

<?php
  }


} // end if is_admin()

if ($mobile_redirect_options['jc_mobile_redirection_type']=='javascript' && strlen(trim($mobile_redirect_options['jc_mobile_web_address']))>0) {
  add_action('wp_head', 'jc_output_javascript_redirection_code');
  
  function jc_output_javascript_redirection_code() {
    global $mobile_redirect_options;
?>
<script type="text/javascript">
(function mobileRedirect(a,b) {
   if (/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) {
	 window.location=b;
   }
})(navigator.userAgent||navigator.vendor||window.opera,'<?php echo $mobile_redirect_options['jc_mobile_web_address']; ?>');
</script>
<?php
  }

} elseif ($mobile_redirect_options['jc_mobile_redirection_type']=='php' && strlen(trim($mobile_redirect_options['jc_mobile_web_address']))>0) {

    add_action('init', 'jc_php_redirection_function');

    function jc_php_redirection_function() {
      global $mobile_redirect_options;
      if (!is_admin() && (ereg('iPhone|iPod|iPad|Android|Dream|Cupcake|BlackBerry9500|BlackBerry9530|Mini|WebOS|Incognito|Webmate',$_SERVER['HTTP_USER_AGENT'])) > 0) {
        header("Location: {$mobile_redirect_options['jc_mobile_web_address']}");
        exit;
      }
    }

}
