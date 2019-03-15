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

add_theme_support('post-thumbnails');
//
add_action( 'admin_init', 'add_general_custom_sections' );
function add_general_custom_sections() {
	// register_setting( 'general', 'キー' )で値を保存
	register_setting( 'general', 'cta_alis_api_key' );
	// add_settings_field( 'キー', 'ラベル', 'コールバック関数', 'general' )で項目を追加
	add_settings_field( 'cta_alis_api_key', 'Alis API key', 'cta_alis_api_key', 'general' );

	var_dump( get_option( 'alis-dev-value' ) );
}

function cta_alis_api_key( $args ) {
	$alis_api_key = get_option( 'cta_alis_api_key' );
	?>
    <input type="text" name="cta_alis_api_key" id="cta_alis_api_key" size="30"
           value="<?php echo esc_attr( $alis_api_key ); ?>"/>
	<?php
}

//If post published -> get the post id -> get title,content,thumbnail_url,overview?.
add_action( 'transition_post_status', 'post_unpublished', 10, 3 );
function post_unpublished( $new_status, $old_status, $post ) {

	if ( $old_status == 'new'  &&  $new_status == 'publish' || $old_status == 'pending' && $new_status == 'publish' || $old_status == 'draft' && $new_status == 'publish'|| $old_status == 'future' && $new_status == 'publish' || $old_status == 'auto-draft' && $new_status == 'publish' ) {

		$token     = '';
		$base_url  = 'https://alis.to/api/me/articles/drafts';
		$title     = esc_html( $post->post_title );
		$thumbnail = get_the_post_thumbnail_url( $post, 'full' );

//		If there is No thumbnail, it will use default one.
		if ( ! isset( $thumbnail ) ) {
			$thumbnail = esc_url( content_url() ) . '/plugins/connect-to-alis/assets/thumbnail.jpg';
		}

		$data = [
			'title'         => $title,
			'body'          => wp_strip_all_tags( $post->post_content ),
			'eye_catch_url' => esc_url( $thumbnail ),
			'overview'      => $title,
		];

		$header = [
			'accept: application/json',
			'Authorization: ' . esc_html( $token ),
			'Content-Type: application/json',
		];

		$curl = curl_init();

		curl_setopt( $curl, CURLOPT_URL, $base_url );
		curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'POST' ); // post
		curl_setopt( $curl, CURLOPT_POSTFIELDS, json_encode( $data ) ); // jsonデータを送信
		curl_setopt( $curl, CURLOPT_HTTPHEADER, $header ); // リクエストにヘッダーを含める
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_HEADER, true );

		$response    = curl_exec( $curl );
		$header_size = curl_getinfo( $curl, CURLINFO_HEADER_SIZE );
		$header      = substr( $response, 0, $header_size );
		$body        = substr( $response, $header_size );
		$result      = json_decode( $body, true );

		curl_close( $curl );

		update_option( 'alis-dev-value', $result );
	}
}
