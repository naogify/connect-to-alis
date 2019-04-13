<?php

class CTA_Alis {
	/**
	 * Access to Alis api, when post is published.
	 * @param $new_status
	 * @param $old_status
	 * @param $post
	 */
	public function call_publish_api( $new_status, $old_status, $post ) {

		$post_thumbnail_id = get_post_thumbnail_id( $post );

		if ( $old_status == 'new'  &&  $new_status == 'publish' || $old_status == 'pending' && $new_status == 'publish' || $old_status == 'draft' && $new_status == 'publish'|| $old_status == 'future' && $new_status == 'publish' || $old_status == 'auto-draft' && $new_status == 'publish' ) {


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

	/**
	 * Load Alis api scripts.
	 * @param $hook
	 */
	public function load_api_scripts( $hook ) {

		if ( 'post-new.php' == $hook && current_user_can( 'administrator' ) ) {

			wp_enqueue_script( 'alis_api_scripts', plugin_dir_url( __FILE__ ) . '/dist/my-app.js', [
				'wp-blocks',
				'wp-element',
				'wp-i18n'
			], false, true );

			$ajax_nonce = wp_create_nonce( "my-special-string" );

			$data_array = array(
				'nonce'    => $ajax_nonce
			);
			wp_localize_script( 'alis_api_scripts', 'cta_alis_user_info', $data_array );
		}

	}
}
