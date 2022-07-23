<?php

use boctulus\BzzExport\libs\Strings;
use boctulus\BzzExport\libs\Mail;
use boctulus\BzzExport\libs\Files;
use boctulus\BzzExport\libs\Products;

/*
	@author Pablo Bozzolo (2022)
*/

#if (defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY){
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
#}

$config = include __DIR__ . '/config/config.php';

require_once __DIR__ . '/libs/Strings.php';
require_once __DIR__ . '/libs/Debug.php';

require_once __DIR__ . '/helpers/config.php';
require_once __DIR__ . '/helpers/debug.php';
require_once __DIR__ . '/helpers/system.php';


if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', realpath(__DIR__ . '/../../..') . DIRECTORY_SEPARATOR);

	require_once ABSPATH . 'wp-config.php';
	require_once ABSPATH . 'wp-load.php';
}

require_once __DIR__ . '/libs/Files.php';
require_once __DIR__ . '/libs/Products.php';


Products::exportProducts();