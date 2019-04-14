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


	public function test_Sample_create_user( $role ) {
		$user_id = self::factory()->user->create( array(
			'role' => $role,
		) );

		return $user_id;
	}

	/**
	 * A single example test.
	 */
	public function test_sample() {

		update_option( 'cta_alis_username', 'username' );
		update_option( 'cta_alis_password','password' );

		$actual   = cta_alis_load_api_scripts( 'post.php' );
		$expected = 'test';

		$this->assertSame( $actual, $expected );

	}
}
