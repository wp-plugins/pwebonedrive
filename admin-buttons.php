<?php
/**
 * @version 1.1.0
 * @package OneDrive
 * @copyright © 2014 Perfect Web sp. z o.o., All rights reserved. http://www.perfect-web.co
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @author Piotr Moćko
 */

// No direct access
function_exists('add_action') or die;


add_action('admin_init', 'pweb_onedrive_buttons');
function pweb_onedrive_buttons() 
{
	// Only add hooks when the current user has permissions AND is in Rich Text editor mode
   if ( ( current_user_can('edit_posts') || current_user_can('edit_pages') ) && get_user_option('rich_editing') && get_option('pweb_onedrive_client_id') ) {
		
		// Live Connect JavaScript library
		wp_register_script('liveconnect', '//js.live.net/v5.0/wl'.(PWEB_ONEDRIVE_DEBUG ? '.debug' : '').'.js');
		wp_enqueue_script('liveconnect');
		
		wp_enqueue_script('jquery');
		
		wp_register_script('pwebonedrive', plugins_url('js/onedrive.js', __FILE__), array('jquery'));
		wp_enqueue_script('pwebonedrive');
		
		add_action('admin_head', 'pweb_onedrive_buttons_script');
		
		add_filter('mce_external_plugins', 'pweb_onedrive_add_buttons');
		add_filter('mce_buttons', 'pweb_onedrive_register_buttons');
	}
}
function pweb_onedrive_add_buttons($plugin_array) 
{
	$plugin_array['pwebonedrive'] = plugins_url('js/editor_plugin.js', __FILE__);
	return $plugin_array;
}
function pweb_onedrive_register_buttons($buttons) 
{
	array_push( $buttons, 'pwebonedrivegallery', 'pwebonedrivefile' );
	return $buttons;
}
function pweb_onedrive_buttons_script() 
{
	$i18n = array(
		'button_gallery' => __( 'OneDrive Galery', 'pwebonedrive' ), 
		'button_file' => __( 'OneDrive File', 'pwebonedrive' ), 
		'emergency_exit' => __( 'OneDrive emergency exit', 'pwebonedrive' ), 
		'folder_select_warning' => __( 'Perfect OneDrive Gallery: select only folder with photos!', 'pwebonedrive' ), 
		'file_select_warning' => __( 'Perfect OneDrive Gallery: select only folder with photos, not a file!', 'pwebonedrive' )
	);
	
	$options = array(
		'client_id:"'.get_option('pweb_onedrive_client_id').'"',
		'task_url:"'.admin_url( 'admin-ajax.php?action=pweb_onedrive_' ).'"',
		'redirect_url:"'.plugins_url( 'callback.php', __FILE__ ).'"',
		'spinner_url:"'.includes_url().'images/wpspin-2x.gif"'
	);
	if (PWEB_ONEDRIVE_DEBUG) $options[] = 'debug:1';
	
	echo '<script type="text/javascript">'
		.'PWebOneDrive.setOptions({'.implode(',', $options).'});'
		.'PWebOneDrive.setI18n('.json_encode($i18n).');'
		.'</script>';
}


add_action('wp_ajax_pweb_onedrive_store', 'pweb_onedrive_ajax_store');
function pweb_onedrive_ajax_store() 
{
	global $wpdb;
	
	$result = array('status' => false, 'message' => '');
	
	if (isset($_POST['resource_id']) AND ($resource_id = $_POST['resource_id']))
	{
		$sql = $wpdb->prepare('SELECT `id`, `access_id` FROM `'.$wpdb->prefix.'onedrive_storage` WHERE `resource_id` LIKE %s', like_escape($resource_id));
		$storage = $wpdb->get_row($sql, OBJECT);
		
		$user_id = LiveConnectClient::getUserIdFromResource($resource_id);
		if ($user_id)
		{
			$sql = $wpdb->prepare('SELECT `id` FROM `'.$wpdb->prefix.'onedrive_access` WHERE `user_id` LIKE %s', like_escape($user_id));
			$access_id = (int)$wpdb->get_var($sql);
			
			if ($access_id)
			{
				// create new storage
				if ($storage === null)
				{
					$result['status'] = $wpdb->insert($wpdb->prefix.'onedrive_storage', array(
							'resource_id' => $resource_id,
							'access_id' => $access_id
						), array('%s', '%d'));
				}
				// update access id in existing storage
				elseif ($storage->access_id === 0 OR $storage->access_id !== $access_id)
				{
					$result['status'] = $wpdb->update($wpdb->prefix.'onedrive_storage', array(
							'access_id' => $access_id
						), array('id' => (int)$storage->id), array('%d'));
				}
				
				if (!$result['status']) $result['message'] = __('Error while saving selected resource. Try again.', 'pwebonedrive');
			}
			else $result['message'] = __('Access token for current OneDrive session was not saved. Logout from OneDrive and try again.', 'pwebonedrive');
		}
	}
	
	die(json_encode($result));
}