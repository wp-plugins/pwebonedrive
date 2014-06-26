<?php
/**
 * @version 1.1.0
 * @package OneDrive
 * @copyright © 2014 Perfect Web sp. z o.o., All rights reserved. http://www.perfect-web.co
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @author Piotr Moćko
 */

define( 'DOING_AJAX', true );

// Load WordPress
require_once( dirname(dirname(dirname( dirname( __FILE__ ) ))) . '/wp-load.php' );


$client = LiveConnectClient::getInstance();
$client->log(__FUNCTION__);

echo $client->handlePageRequest();

$client->log(__FUNCTION__.'. Die');

die();