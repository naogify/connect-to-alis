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

require './vendor/autoload.php';
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;

$CTA_Alis = new CTA_Alis();


class CTA_Alis {

	public function __construct() {

		remove_filter( 'authenticate', 'wp_authenticate_username_password', 20 );
		add_filter( 'authenticate', array( $this, 'authenticate_via_cognito' ), 20, 3 );

		add_action( 'admin_enqueue_scripts', array( $this, 'load_api_scripts' ), 10, 1 );
		add_action( 'wp_ajax_get_ajax_data', array( $this, 'get_ajax_data' ), 10, 0 );
		add_action( 'transition_post_status', array( $this, 'call_draft_api' ), 10, 3 );

	}


	/**
	 * TODO
	 * aws-sdk-phpで、cognitoのクライアントを作成、usernameとpasswordをセットして、アクセストークンを取得。
	 * まだこの関数は動作していない。
	 *
	 * https://github.com/aws/aws-sdk-php
	 * https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-cognito-idp-2016-04-18.html
	 *
	 */
	public function view() {
		$client = new CognitoIdentityProviderClient([
			'profile' => 'default',
			'region'  => 'ap-northeast-1',
			'version' => 'latest'
		]);
		$params = [
			'Username' => 'naogifydev',
			'UserPoolId' => 'ap-northeast-1_HNT0fUj4J'
		];
		$user = $client->adminGetUser($params);
		return $user;
//		return view('user', compact('user'));
	}

	/**
	 * @param $user
	 * @param $username
	 * @param $password
	 *
	 * @return bool|WP_Error|WP_User
	 */
	public function authenticate_via_cognito( $user, $username, $password ) {



		if(empty($username) || empty($password)) {
			return false;
		}else{

			//アクセストークンを取得
		}

		/**
		 * TODO
		 * アクセストークンが返ってきた + ユーザー名が既存のユーザーになければ作成。
		 * tokenを update_option( 'cta_alis_token' )で保存。
		 */

		/**
		 * TODO
		 * 既存のユーザ名として存在すればログイン処理
		 */

		/**
		 * TODO
		 * アクセストークンが返ってこなければ、通常のログイン処理。
		 */


//		$login_info = 'admin11';
//
//		if ( $username == $login_info && $password == $login_info ) {
//
//			$user_id = wp_create_user( $username, $password, $username . '@gmail.com' );
//			$creds   = array(
//				'user_login'    => $username,
//				'user_password' => $password,
//				'remember'      => false
//			);
//
//			wp_signon( $creds, false );
//		}


//			if ( $user_id ) {
//
//				$creds = array(
//					'user_login'    => $login_info,
//					'user_password' => $login_info,
//					'remember'      => true
//				);
//
//				$user = wp_signon( $creds, true );
//
//				if ( is_wp_error( $user ) ) {
//					echo $user->get_error_message();
//				} else {
//					return $user;
//				}
//			}

//		} else {
//			return new WP_Error( 'broke', __( "Failed to login", "my_textdomain" ) );
//		}
	}


	/**
	 * Create http request.
	 *
	 * @param $request_type
	 * @param $base_url
	 * @param $data
	 *
	 * @return array|mixed|object
	 */
	public function send_http_request( $request_type, $base_url, $data ) {


		$token = get_option( 'cta_alis_token' );
		if ( isset( $token ) ) {
			esc_html( $token );
		}

		$header = [
			'accept: application/json',
			'Authorization: ' . esc_html( $token ),
			'Content-Type: application/json',
		];

		$curl = curl_init();

		curl_setopt( $curl, CURLOPT_URL, $base_url );
		curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, $request_type );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, json_encode( $data ) );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, $header );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_HEADER, true );

		$response    = curl_exec( $curl );
		$header_size = curl_getinfo( $curl, CURLINFO_HEADER_SIZE );
		$header      = substr( $response, 0, $header_size );
		$body        = substr( $response, $header_size );
		$result      = json_decode( $body, true );

		curl_close( $curl );

		update_option( 'cta_alis_result', $response );

		return $result;

	}

	/**
	 * Access to Alis api to create draft.
	 *
	 * @param $new_status
	 * @param $old_status
	 * @param $post
	 */
	public function call_draft_api( $new_status, $old_status, $post ) {

		$post_thumbnail_id = get_post_thumbnail_id( $post );

		if ( $old_status == 'new' && $new_status == 'publish' || $old_status == 'pending' && $new_status == 'publish' || $old_status == 'draft' && $new_status == 'publish' || $old_status == 'future' && $new_status == 'publish' || $old_status == 'auto-draft' && $new_status == 'publish' ) {


			$base_url = 'https://alis.to/api/me/articles/drafts';

			$title    = esc_html( $post->post_title );

			if ( has_post_thumbnail() ) {
				$thumbnail = get_the_post_thumbnail_url( get_the_ID(), 'full' );
				die();
			} else {
				$thumbnail = esc_url( content_url() ) . '/plugins/connect-to-alis/assets/thumbnail.jpg';
			}

			$data = [
				'title'         => $title,
				'body'          => wp_strip_all_tags( $post->post_content ),
				'eye_catch_url' => esc_url( $thumbnail ),
				'overview'      => $title,
			];

			$alis_article = self::send_http_request( 'POST', $base_url, $data );
			self::call_publish_api( $alis_article['article_id'] );

		}
	}


	/**
	 * Access to Alis api to make draft public.
	 *
	 * @param $article_id
	 */
	public function call_publish_api( $article_id ) {

		$base_url = 'https://alis.to/api/me/articles/' . (string)$article_id . '/drafts/publish';
		$data     = [
			'topic' => 'others',
			'tgas'  => [ 'others' ]
		];

		self::send_http_request( 'PUT', $base_url, $data );
	}

	/**
	 * Load Alis api scripts.
	 *
	 * @param $hook
	 */
	public function load_api_scripts( $hook ) {

		if ( 'post-new.php' == $hook && current_user_can( 'administrator' ) ) {

			wp_enqueue_script( 'alis_api_scripts', plugin_dir_url( __FILE__ ) . '/dist/my-app.js', [
				'jquery',
				'wp-blocks',
				'wp-element',
				'wp-i18n'
			], false, true );

			$ajax_nonce = wp_create_nonce( "my-special-string" );

			$data_array = array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => $ajax_nonce

			);
			wp_localize_script( 'alis_api_scripts', 'cta_alis_user_info', $data_array );
		}

	}

	/**
	 * Get api token from javascript.
	 */
	public function get_ajax_data() {
		check_ajax_referer( 'my-special-string', 'security' );
		$token = sanitize_text_field( $_POST['token'] );
		update_option('cta_alis_token',$token);
	}
}
