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


add_action( 'admin_menu', 'pweb_onedrive_admin_menu' );
function pweb_onedrive_admin_menu()
{
	add_submenu_page('plugins.php', __('Perfect OneDrive Gallery & File', 'pwebonedrive'), __('Perfect OneDrive', 'pwebonedrive'), 'manage_options', 'pwebonedrive-config', 'pweb_onedrive_conf');
}

add_filter('plugin_action_links', 'pweb_onedrive_plugin_action_links', 10, 2);
function pweb_onedrive_plugin_action_links( $links, $file ) 
{
	if ( $file == plugin_basename( dirname(__FILE__).'/pwebonedrive.php' ) ) {
		$links[] = '<a href="' . admin_url( 'admin.php?page=pwebonedrive-config' ) . '">'.__( 'Settings' ).'</a>';
	}

	return $links;
}


// displays the page content for the settings submenu
function pweb_onedrive_conf() 
{
    global $wp_version;
    
    //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
		wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if (isset($_POST['submitConfig'])) {
        
		$errors = array();
		
		if ( isset( $_POST['client_id'] ) AND $_POST['client_id'] )
		{
			if (preg_match('/^[0-9a-zA-Z]{16}$/', $_POST['client_id'])) {
				update_option( 'pweb_onedrive_client_id', preg_replace('/[^0-9a-zA-Z]$/', '', $_POST['client_id']) );
			}
			else {
				$errors[] = __('Incorrect Client ID.', 'pwebonedrive' );
			}
		}
		else {
			$errors[] = __('Missing Client ID.', 'pwebonedrive' );
		}
		
		if ( isset( $_POST['client_secret'] ) AND $_POST['client_secret'] ) {
			update_option( 'pweb_onedrive_client_secret', addslashes(preg_replace('/[\'"\/]/', '', $_POST['client_secret'])) );
		}
		else {
			$errors[] = __('Missing Client secret.', 'pwebonedrive' );
		}

		if (count($errors)) {
?>
<div class="error"><p><strong><?php echo implode('<br>', $errors); ?></strong></p></div>
<?php
		}
		else {
?>
<div class="updated"><p><strong><?php _e('Settings saved.', 'pwebonedrive' ); ?></strong></p></div>
<?php
		}
    }
?>
<div class="wrap">
	
	<div style="float:right;padding:9px 0 4px">
		<?php echo __('Version') .' '. pweb_onedrive_get_version(); ?>
	</div>
	
	<h2>
		<?php _e('Perfect OneDrive Gallery & File Settings', 'pwebonedrive'); ?>
		
		<a class="add-new-h2" href="http://www.perfect-web.co/wordpress/microsoft-onedrive-gallery-file/documentation" target="_blank">
			<?php _e( 'Documentation' ); ?></a>
		
		<a class="add-new-h2" href="http://www.perfect-web.co/wordpress/microsoft-onedrive-gallery-file" target="_blank">
			<?php _e( 'Buy Support' ); ?></a>
	</h2>
	
	<?php if (version_compare( $wp_version, '2.8', '<' ) ) { ?>
		<div class="error"><p><strong><?php _e('This plugin is compatible with WordPress 2.8 or higher.', 'pwebonedrive' ); ?></strong></p></div>
	<?php } ?>
	
	<div id="wp_updates"></div>
	
	<p><?php _e('Share easily your photos and files stored on Microsoft OneDrive. You can display a gallery with your photos or a link to a file for download.', 'pwebonedrive'); ?></p>

	<form name="config" method="post" action="<?php echo admin_url('plugins.php?page=pwebonedrive-config'); ?>">
		<table class="form-table">
			<tbody>
				<tr>
					<th>
						<a href="http://onedrive.live.com" target="_blank"><img src="<?php echo plugins_url(null, __FILE__); ?>/images/onedrive-logo.png" alt="OneDrive"></a>
					</th>
					<td>
						<p>
							<?php _e('Register your site in', 'pwebonedrive'); ?>
							<a target="_blank" href="https://account.live.com/developers/applications/index"><?php _e('Windows Live application management', 'pwebonedrive'); ?></a>.<br>
							<?php _e('Remember to set', 'pwebonedrive'); ?> <strong><?php _e('Redirect URL', 'pwebonedrive'); ?></strong>: 
							<a href="<?php echo plugins_url( 'callback.php', __FILE__ ); ?>" target="_blank"><?php echo plugins_url( 'callback.php', __FILE__ ); ?></a><br>
							<?php _e('and', 'pwebonedrive'); ?> <strong><?php _e('Mobile client app: No', 'pwebonedrive'); ?></strong><br>
							<?php _e('and if available', 'pwebonedrive'); ?> <strong><?php _e('Enhanced redirection security: Enabled', 'pwebonedrive'); ?></strong> <?php _e('(for applications created before June 2014)', 'pwebonedrive'); ?><br>
							<?php _e('Read how to', 'pwebonedrive'); ?> <a target="_blank" href="http://msdn.microsoft.com/library/cc287659.aspx"><?php _e('get your Client ID', 'pwebonedrive'); ?></a>.
						</p>
					</td>
				</tr>
				<tr>
					<th><label for="pweb-client_id"><?php _e('Client ID', 'pwebonedrive'); ?></label></th>
					<td>
						<input id="pweb-client_id" name="client_id" type="text" size="15" maxlength="16" value="<?php echo esc_attr( get_option('pweb_onedrive_client_id') ); ?>" class="regular-text code">
					</td>
				</tr>
				<tr>
					<th><label for="pweb-client_secret"><?php _e('Client secret', 'pwebonedrive'); ?></label></th>
					<td>
						<input id="pweb-client_secret" name="client_secret" type="password" size="15" maxlength="50" value="<?php echo esc_attr( get_option('pweb_onedrive_client_secret') ); ?>" class="regular-text code">
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<input type="submit" name="submitConfig" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes'); ?>">
		</p>
	</form>

	<p>
		<img src="<?php echo plugins_url(null, __FILE__); ?>/images/MSFT_logo.png" alt="Microsoft" >
	</p>
	<p>
		<em>Inspired by <a href="http://www.microsoft.com/openness/default.aspx" target="_blank"><strong>Microsoft</strong></a>.
		Copyright &copy; 2014 <strong>Perfect Web</strong> sp. z o.o. All rights reserved. Distributed under GPL by
		<a href="http://www.perfect-web.co/wordpress" target="_blank"><strong>Perfect-Web.co</strong></a>.<br>
		All other trademarks and copyrights are property of their respective owners.</em>
	</p>

	<script type="text/javascript">
	// Updates feed
	(function(){
		var pw=document.createElement("script");pw.type="text/javascript";pw.async=true;
		pw.src="https://www.perfect-web.co/index.php?option=com_pwebshop&view=updates&format=raw&extension=wp_onedrive&version=<?php echo pweb_onedrive_get_version(); ?>&wpversion=<?php echo $wp_version; ?>";
		var s=document.getElementsByTagName("script")[0];s.parentNode.insertBefore(pw,s);
	})();
	</script>
</div>

<?php
}


function pweb_onedrive_get_version() {
    
	$data = get_plugin_data(dirname(__FILE__).'/pwebonedrive.php');
	return $data['Version'];
}