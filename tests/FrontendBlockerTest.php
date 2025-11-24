<?php
/**
 * Class Frontend_Blocker_Test
 *
 * @package HeadlessLockPro
 */

use HeadlessLockPro\Frontend_Blocker;

class Frontend_Blocker_Test extends WP_UnitTestCase {

    public function test_block_frontend_access_allows_admin() {
        // Simulate admin area
        set_current_screen( 'dashboard' );

        // This should not die or redirect
        ob_start();
        Frontend_Blocker::block_frontend_access();
        $output = ob_get_clean();

        $this->assertEmpty( $output );
    }

    public function test_block_frontend_access_allows_rest_api() {
        // Simulate REST API request
        $_SERVER['REQUEST_URI'] = '/wp-json/wp/v2/posts';

        ob_start();
        Frontend_Blocker::block_frontend_access();
        $output = ob_get_clean();

        $this->assertEmpty( $output );
    }
}