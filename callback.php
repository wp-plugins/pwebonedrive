<?php
/**
 * @version 1.2.0
 * @package OneDrive
 * @copyright © 2014 Perfect Web sp. z o.o., All rights reserved. http://www.perfect-web.co
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @author Piotr Moćko
 *
 * @deprecated Enable permalinks in WordPress settings to stop using this file
 */

define( 'DOING_AJAX', true );

// Load WordPress
require_once( dirname(dirname(dirname( dirname( __FILE__ ) ))) . '/wp-load.php' );

pweb_onedrive_callback();

die();