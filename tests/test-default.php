<?php
/**
 * Class SampleTest
 *
 * @package Connect_To_Alis
 */

require_once dirname(dirname(__FILE__)).'/connect-to-alis.php';

/**
 * Sample test case.
 */
class SampleTest extends WP_UnitTestCase {


	/**
	 * Load Alis api scripts test.
	 */
//	public function test_load_api_js(){
//
//		$tinymce_templates = new TinyMCE_Templates();
//
//		$tinymce_templates->admin_enqueue_scripts( '' );
//		$this->assertFalse( wp_script_is( 'alis_api_scripts' ) );
//
//		$tinymce_templates->admin_enqueue_scripts( 'post-new.php' );
//		$this->assertTrue( wp_script_is( 'alis_api_scripts' ) );
//
//		$tinymce_templates->admin_enqueue_scripts( 'post.php' );
//		$this->assertTrue( wp_script_is( 'alis_api_scripts' ) );
//
//	}

	/**
	 * A single example test.
	 */
	public function test_sample() {
		// Replace this with some actual testing code.
		$this->assertTrue( true );

		update_option( 'cta_alis_username', 'username' );
		update_option( 'cta_alis_password','password' );

		cta_alis_load_api_scripts('edit.php');




	}
}
