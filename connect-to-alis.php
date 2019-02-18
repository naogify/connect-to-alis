<?php
/**
 * Plugin Name:     Connect To Alis
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          YOUR NAME HERE
 * Author URI:      YOUR SITE HERE
 * Text Domain:     connect-to-alis
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Connect_To_Alis
 */


add_action( 'admin_init', 'add_general_custom_sections' );
function add_general_custom_sections() {
	// register_setting( 'general', 'キー' )で値を保存
	register_setting( 'general', 'cta_alis_api_key' );
	// add_settings_field( 'キー', 'ラベル', 'コールバック関数', 'general' )で項目を追加
	add_settings_field( 'cta_alis_api_key', 'Alis API key', 'cta_alis_api_key', 'general' );
}

function cta_alis_api_key( $args ) {
	$alis_api_key = get_option( 'cta_alis_api_key' );
	?>
    <input type="text" name="cta_alis_api_key" id="cta_alis_api_key" size="30"
           value="<?php echo esc_attr( $alis_api_key ); ?>"/>
	<?php
}

//If post published -> get the post id -> get title,content,thumbnail_url,overview?.