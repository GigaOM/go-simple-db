<?php

/**
 * Plugin name: GO Simple DB
 * Description:	Establish a connection to Amazon Simple DB
 * Author: 		GigaOM 
 * Author URI: 	http://gigaom.com/
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

require_once __DIR__ . '/components/class-go-simple-db.php';

// is this a wp-cli call?
if ( defined( 'WP_CLI' ) && WP_CLI )
{
	include __DIR__ . '/components/class-go-simple-db-wp-cli.php';
}
