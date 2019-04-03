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

add_theme_support( 'post-thumbnails' );

add_action( 'wp_ajax_getToken', 'getToken' );
function getToken() {


	check_ajax_referer( 'my-special-string', 'security' );

	global $wpdb; // this is how you get access to the database

	$token = esc_html( $_POST['idToken'] );

	if ( isset( $token ) ) {
		update_option( 'cta_token', $token );
	}

	wp_die(); // this is required to terminate immediately and return a proper response
}

/**
 * Access to Alis api, when post is published.
 * @param $new_status
 * @param $old_status
 * @param $post
 */
function cta_alis_post_published( $new_status, $old_status, $post ) {

	$post_thumbnail_id = get_post_thumbnail_id( $post );

	if ( $old_status == 'new'  &&  $new_status == 'publish' || $old_status == 'pending' && $new_status == 'publish' || $old_status == 'draft' && $new_status == 'publish'|| $old_status == 'future' && $new_status == 'publish' || $old_status == 'auto-draft' && $new_status == 'publish' ) {


		wp_add_inline_script( 'alis_api_scripts', 'alert("hello world");' );

		$token = get_option( 'cta_token' );
		if ( isset( $token ) ) {
			esc_html( $token );
		}
		$base_url = 'https://alis.to/api/me/articles/drafts';
		$title    = esc_html( $post->post_title );

		if ( has_post_thumbnail() ) {
			$thumbnail = get_the_post_thumbnail_url( (int) $post->ID, 'full' );
		} else {
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
		curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'POST' );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, json_encode( $data ) );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, $header );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_HEADER, true );

//		$response    = curl_exec( $curl );
//		$header_size = curl_getinfo( $curl, CURLINFO_HEADER_SIZE );
//		$header      = substr( $response, 0, $header_size );
//		$body        = substr( $response, $header_size );
//		$result      = json_decode( $body, true );

		curl_close( $curl );

	}
}
add_action( 'transition_post_status', 'cta_alis_post_published', 10, 3 );

/**
 * Load Alis api scripts.
 * @param $hook
 */
function cta_alis_load_api_scripts( $hook ) {

	if ( 'post-new.php' == $hook || 'post.php' == $hook ) {

		if ( current_user_can( 'administrator' ) ) {

			wp_register_script( 'alis_api_scripts', plugin_dir_url( __FILE__ ) . '/dist/my-app.js' );
			wp_enqueue_script( 'alis_api_scripts' );

				$ajax_nonce = wp_create_nonce( "my-special-string" );

				$data_array = array(
					'nonce'    => $ajax_nonce
				);
				wp_localize_script( 'alis_api_scripts', 'cta_alis_user_info', $data_array );


		}
	}

}
add_action( 'admin_enqueue_scripts', 'cta_alis_load_api_scripts' );
