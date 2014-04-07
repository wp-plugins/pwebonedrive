<?php 
/**
 * Plugin Name: Perfect OneDrive Gallery & File
 * Plugin URI: http://www.perfect-web.co/wordpress/microsoft-onedrive-gallery-file
 * Description: Share easily your photos and files stored on Microsoft OneDrive. You can display a gallery with your photos or a link to a file for download.
 * Version: 1.0.3
 * Author: Piotr Moćko
 * Author URI: http://www.perfect-web.co
 * License: GPLv3
 */

// No direct access
function_exists('add_action') or die;

if (!defined('PWEB_ONEDRIVE_DEBUG')) define('PWEB_ONEDRIVE_DEBUG', WP_DEBUG);

function pweb_onedrive_init() 
{
	$plugin_dir = basename(dirname(__FILE__));
	load_plugin_textdomain( 'pwebonedrive', false, $plugin_dir );
	
	wp_register_style('pweb_onedrive_file_style', plugins_url('css/onedrivefile.css', __FILE__));
	wp_register_style('pweb_onedrive_gallery_style', plugins_url('css/onedrivegallery.css', __FILE__));
	
	wp_register_style('pweb_onedrive_prettyphoto_style', plugins_url('css/prettyPhoto.css', __FILE__));
	wp_register_script('pweb_onedrive_prettyphoto_script', plugins_url('js/jquery.prettyPhoto'.(PWEB_ONEDRIVE_DEBUG ? '' : '.min').'.js', __FILE__), array('jquery'));
}
add_action('plugins_loaded', 'pweb_onedrive_init');


require_once dirname( __FILE__ ) . '/liveconnect.php';

if ( is_admin() ) {
	require_once dirname( __FILE__ ) . '/admin.php';
	require_once dirname( __FILE__ ) . '/admin-buttons.php';
}


function pweb_onedrive_gallery_assets() 
{
	wp_enqueue_style('pweb_onedrive_gallery_style');
	
	// Load prettyPhoto
	wp_enqueue_style('pweb_onedrive_prettyphoto_style');
	wp_enqueue_script('pweb_onedrive_prettyphoto_script');
	
	// Init prettyPhoto
	add_action('wp_footer', 'pweb_onedrive_prettyphoto_init');
}

function pweb_onedrive_prettyphoto_init() 
{
	if (!defined('PRETTYPHOTOINIT'))
	{
		define('PRETTYPHOTOINIT', 1);
		
		$options = array('deeplinking:false,social_tools:false');
		//if ($this->params->get('gallery_theme')) $options[] = 'theme:"'.$this->params->get('gallery_theme').'"';
		//if (!$this->params->get('gallery_overlay', 1)) $options[] = 'overlay_gallery:false';
		
		echo '<script type="text/javascript">'
			.'var oneDrivePrettyPhotoConfig={'.implode(',', $options).'};'
			.'jQuery(document).ready(function($){'
				.'$("a[rel^=\'onedrivegallery\']").prettyPhoto(oneDrivePrettyPhotoConfig)'
			.'});'
			.'</script>';
	}
}

function pweb_onedrive_file_assets() 
{
	wp_enqueue_style('pweb_onedrive_file_style');
}


add_shortcode('onedrivegallery', 'pweb_onedrive_galery_shortcode');
function pweb_onedrive_galery_shortcode($atts, $content = null, $tag) 
{
	extract( shortcode_atts( array (
		'id' 		=> '',
		'thumbnail' => 'thumbnail', //TODO gallery_thumnail_size
		'full' 		=> 'normal', //TODO gallery_image_size
		'class' 	=> ''
	), $atts ) );
	
	$output = '';
	
	if (!$id) return $output;
	
	pweb_onedrive_gallery_assets();
	
	$images = pweb_onedrive_get_gallery($id);
	if (is_object($images) AND isset($images->data))
	{
		if (count($images->data))
		{
			// Display gallery
			if (!in_array($full, array('normal', 'full')))
				$full = 'normal'; //TODO load option gallery_image_size
			
			// Select thumbnail image size
			switch ($thumbnail) {
				case 'album':
					$index = 1;
					break;
				case 'thumbnail':
				default:
					$index = 2;
			}

			if ($class) 
				$class = ' '.$class;
			
			$galleryId = md5($id);
			
			// Output gallery
			$output = '<span id="onedrivegallery-'.$galleryId.'" class="onedrivegallery'.$class.(PWEB_ONEDRIVE_DEBUG ? ' debug' : '').'">';
			foreach ($images->data as $image) 
			{
				// File extension
				$dot = strrpos($image->name, '.') + 1;
				$image->ext = substr($image->name, $dot);
				
				// Image url
				$url = plugins_url('do.php', __FILE__).'?action=display_photo&aid='.$images->access_id.'&code='.base64_encode($image->id.'/picture?type='.$full).'#'.$image->ext;
				$src = plugins_url('do.php', __FILE__).'?action=display_photo&aid='.$images->access_id.'&code='.base64_encode($image->id.'/picture?type='.$thumbnail);
				
				// Output image
				$output .= 
					 '<a href="'.$url.'"'
					 .' rel="onedrivegallery['.$galleryId.']"'.($image->description ? ' title="'.htmlentities($image->description, ENT_QUOTES, 'UTF-8').'"' : '').'>'
					.'<img src="'.$src.'"'
					.' width="'.$image->images[$index]->width.'" height="'.$image->images[$index]->height.'"'
					.' alt="'.($image->description ? htmlentities($image->description, ENT_QUOTES, 'UTF-8') : '').'" />'
					.'</a>';
			}
			$output .= '</span>';
		}
		else 
		{
			// Output message about no images
			$output = '<span class="onedrivegallery-error">'.__('There are no images in this gallery!', 'pwebonedrive').'</span>';
		}
	}
	else
	{
		// Output message about error
		$output = '<span class="onedrivegallery-error">'.__('Can not load images!', 'pwebonedrive').(is_string($images) ? ' '.$images : '').'</span>';
	}
	
	return $output;
}


add_shortcode('onedrivefile', 'pweb_onedrive_file_shortcode');
function pweb_onedrive_file_shortcode($atts, $content = null, $tag) 
{
	extract( shortcode_atts( array (
		'id' 		=> '',
		'image' 	=> '', //TODO load option
		'width' 	=> '',
		'height' 	=> '',
		'icon' 		=> '1', //TODO load option
		'size' 		=> '1', //TODO load option
		'class' 	=> ''
	), $atts ) );
	
	$output = '';
	
	if (!$id) return $output;
	
	pweb_onedrive_file_assets();
	
	$file = pweb_onedrive_get_file($id);
	if (is_object($file))
	{
		if ($class) 
			$class = ' '.$class;
		
		// Display photo
		if ($file->type == 'photo' AND $image != 'download')
		{
			if (!in_array($image, array('normal', 'album', 'thumbnail', 'full')))
				$image = 'normal'; //TODO load option
			
			if ($content)
			{
				$file->description = $content;
			}
			
			// Image url
			$src = plugins_url('do.php', __FILE__).'?action=display_photo&aid='.$file->access_id.'&code='.base64_encode($file->id.'/picture?type='.$image);
			
			// Output image
			$output = '<img src="'.$src.'" class="onedrivefile onedrivefile-photo'. $class .(PWEB_ONEDRIVE_DEBUG ? ' debug' : '').'"';
			if ($width OR $height) {
				if ($width) $output .= ' width="'.$width.'"';
				if ($height) $output .= ' height="'.$height.'"';
			}
			else {
				// Select image size
				switch ($image) {
					case 'thumbnail':
						$index = 2;
						break;
					case 'normal':
						$index = 0;
						break;
					case 'full':
						$index = 3;
						break;
					case 'album':
					default:
						$index = 1;
				}
				$output .= ' width="'.$file->images[$index]->width.'" height="'.$file->images[$index]->height.'"';
			}
			$output .= ' alt="'.htmlentities($file->description, ENT_QUOTES, 'UTF-8').'" />';
		}
		// Display file link
		else 
		{
			if ($content)
			{
				$file->name = $content;
			}
			
			// File url
			$url = plugins_url('do.php', __FILE__).'?action=download_file&aid='.$file->access_id.'&code='.base64_encode($file->id.'/content?download=true');
			
			// Output file
			$output = 
				 '<a href="'.$url.'" target="_blank" rel="nofollow" class="onedrivefile onedrivefile-'.$file->ext . $class .(PWEB_ONEDRIVE_DEBUG ? ' debug' : '').'"'
				.($file->description ? ' title="'.htmlentities($file->description, ENT_QUOTES, 'UTF-8').'"' : '')
				.'>'
				.($icon ? '<span class="icon"></span>' : '')
				.$file->name
				.($size ? ' <span class="size">('.$file->size.')</span>' : '')
				.'</a>';
		}
	}
	else
	{
		// Output message about error
		$output = '<span class="onedrivefile-error">'.__('Can not load file!', 'pwebonedrive').(is_string($file) ? ' '.$file : '').'</span>';
	}
	
	return $output;
}


function pweb_onedrive_get_gallery($resource_id)
{
	static $galleries = array();
	
	if (isset($galleries[$resource_id])) {
		return $galleries[$resource_id];
	}
	
	$client = LiveConnectClient::getInstance();
	$client->setOption('usecookie', false);
	
	$client->log(__FUNCTION__.'. Get images by Folder ID: '.$resource_id);
	
	// Get photos
	$response = $client->queryByRersourceId($resource_id, $resource_id.'/files?filter=photos');
	if (is_wp_error($response)) 
	{
		return __('Can not load data!', 'pwebonedrive').' '.$response->get_error_message();
	}
	
	$data = $response['body'];
	if (!$data) return false;
	
	if (isset($data->data))
	{
		$client->log(__FUNCTION__.'. Images loaded');
		
		// Access Id
		$data->access_id = $client->getAccessId();
		
		$galleries[$resource_id] = $data;
		return $galleries[$resource_id];
	}
	elseif (isset($data->error) AND isset($data->error->message))
	{
		$client->log(__FUNCTION__.'. Get images REST error: '.$data->error->message, E_USER_ERROR);
		return $data->error->message;
	}
	
	return false;
}
function pweb_onedrive_get_file($resource_id)
{
	static $files = array();
	
	if (isset($files[$resource_id])) {
		return $files[$resource_id];
	}
	
	$client = LiveConnectClient::getInstance();
	$client->setOption('usecookie', false);
	
	$client->log(__FUNCTION__.'. Get file by ID: '.$resource_id);
	
	// Get File
	$response = $client->queryByRersourceId($resource_id);
	if (is_wp_error($response)) 
	{
		return __('Can not load data!', 'pwebonedrive').' '.$response->get_error_message();
	}
	
	$data = $response['body'];
	if (!$data) return false;
	
	if (isset($data->id))
	{
		$client->log(__FUNCTION__.'. File loaded');
		
		// File extension
		$dot = strrpos($data->name, '.') + 1;
		$data->ext = substr($data->name, $dot);
		
		// Access Id
		$data->access_id = $client->getAccessId();
		
		// Formatted file size
		$data->size = pweb_onedrive_file_format_size($data->size);
		
		$files[$resource_id] = $data;
		return $files[$resource_id];
	}
	elseif (isset($data->error) AND isset($data->error->message))
	{
		$client->log(__FUNCTION__.'. Get file REST error: '.$data->error->message, E_USER_ERROR);
		return $data->error->message;
	}
	
	return false;
}
function pweb_onedrive_file_format_size($size)
{
    $base = log($size, 2);
	
	if ($base >= 30) {
		$div = 1024*1024*1024;
		$sufix = ' GB';
	}
	elseif ($base >= 20) {
		$div = 1024*1024;
		$sufix = ' MB';
	}
	elseif ($base >= 10) {
		$div = 1024;
		$sufix = ' KB';
	}
	else {
		return $size.' B';
	}
	
	$size = $size / $div;
	return round($size, $size < 50 ? 1 : 0) . $sufix;
}


add_action('wp_ajax_nopriv_pweb_onedrive_download_file', 'pweb_onedrive_download_file');
function pweb_onedrive_download_file() 
{
	$client = LiveConnectClient::getInstance();
	$client->setOption('usecookie', false);
	
	$client->log(__FUNCTION__);
	
	$access_id 	= isset($_GET['aid']) ? (int)$_GET['aid'] : 0;
	$url 		= isset($_GET['code']) ? base64_decode($_GET['code']) : null;
	
	if (!$url OR !$access_id) die();
			
	// Get File
	$response = $client->queryByAccessId($access_id, $url);
	if (is_wp_error($response))
	{
		die(__('Can not load data!', 'pwebonedrive').' Request error: '.$response->get_error_message());
	}
	
	// Follow location returned by request
	if (headers_sent() AND isset($response['headers']['location']))
	{
		echo "<script>document.location.href='" . htmlspecialchars($response['headers']['location']) . "';</script>\n";
	}
	else 
	{
		unset($response['headers']['keep-alive']);
		
		foreach ($response['headers'] as $name => $value)
		{
			header($name.': '.$value);
		}
		echo $response['body'];
	}

	die();
}


add_action('wp_ajax_nopriv_pweb_onedrive_display_photo', 'pweb_onedrive_display_photo');
function pweb_onedrive_display_photo() 
{
	$client = LiveConnectClient::getInstance();
	$client->setOption('usecookie', false);
	
	$client->log(__FUNCTION__);
	
	$access_id 	= isset($_GET['aid']) ? (int)$_GET['aid'] : 0;
	$url 		= isset($_GET['code']) ? base64_decode($_GET['code']) : null;
	
	if (!$url OR !$access_id) die();
			
	// Get File
	$response = $client->queryByAccessId($access_id, $url);
	if (is_wp_error($response))
	{
		die(__('Can not load data!', 'pwebonedrive').' Request error: '.$response->get_error_message());
	}
	
	// Follow location returned by request
	if (headers_sent() AND isset($response['headers']['location']))
	{
		echo "<script>document.location.href='" . htmlspecialchars($response['headers']['location']) . "';</script>\n";
	}
	else 
	{
		if ($response['body']) 
		{
			unset($response['headers']['location'], $response['headers']['keep-alive']);
		}
		elseif (false) //TODO option: image_redirect
		{
			// Get image from location and output to the browser instead of redirecting to that location
			$url = $response['headers']['location'];
			unset($response['headers']['location'], $response['headers']['keep-alive']);
			
			$response = $client->request($url, $response['headers']);
			if (is_wp_error($response))
			{
				die(__('Can not load data!', 'pwebonedrive').' Request error: '.$response->get_error_message());
			}
		}
		
		foreach ($response['headers'] as $name => $value)
		{
			header($name.': '.$value);
		}
		echo $response['body'];
	}

	die();
}


register_activation_hook( __FILE__, 'pweb_onedrive_install' );
function pweb_onedrive_install()
{
	global $wpdb;
	global $charset_collate;
	
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
	$sql = 
	"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}onedrive_access` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `user_id` varchar(1024) DEFAULT NULL,
	  `access_token` varchar(1024) DEFAULT NULL,
	  `refresh_token` varchar(1024) DEFAULT NULL,
	  `created` int(11) unsigned DEFAULT NULL,
	  `expires_in` int(6) DEFAULT '3600',
	  PRIMARY KEY (`id`),
	  KEY `user` (`user_id`(333))
	) $charset_collate AUTO_INCREMENT=1;";
	
	dbDelta( $sql );
	
	$sql = 
	"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}onedrive_storage` (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `resource_id` varchar(1024) NOT NULL,
	  `access_id` int(11) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`id`),
	  KEY `resource` (`resource_id`(333)),
	  KEY `idx_access_id` (`access_id`)
	) $charset_collate AUTO_INCREMENT=1;";
	
	dbDelta( $sql );
}
