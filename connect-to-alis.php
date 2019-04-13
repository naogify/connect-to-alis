<?php
/**
 * Plugin Name:     Connect To Alis
 * Plugin URI:      https://github.com/naogify/connect-to-alis
 * Description:     This is the WordPress plugin to share your post in Alis.
 * Author:          Naoki Ohashi
 * Author URI:      https://naoki-is.me/
 * Text Domain:     connect-to-alis
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Connect_To_Alis
 */
require_once dirname(__FILE__) . '/CTA_Alis.php';
$CTA_Alis = new CTA_Alis;

add_action( 'transition_post_status', 'cta_alis_post_published', 10, 3 );
add_action( 'admin_enqueue_scripts', 'cta_alis_load_api_scripts' );
