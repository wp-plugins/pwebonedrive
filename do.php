<?php
/**
 * @version 1.0.0
 * @package OneDrive
 * @copyright © 2014 Perfect Web sp. z o.o., All rights reserved. http://www.perfect-web.co
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @author Piotr Moćko
 */

define( 'DOING_AJAX', true );

// Load WordPress
require_once( dirname(dirname(dirname( dirname( __FILE__ ) ))) . '/wp-load.php' );

if (isset($_GET['action']) AND in_array($_GET['action'], array('download_file', 'display_photo')) AND function_exists('pweb_onedrive_'.$_GET['action']))
{
	call_user_func('pweb_onedrive_'.$_GET['action']);
}

// Default status
die('0');
