<?php 

/**
 * Class responsible for displaying the user's account
 */

if ( ! class_exists( 'BetP2P_Account' ) ) {

    class BetP2P_Account {

        public function __construct() {

            add_shortcode( 'betp2p_account', array( $this, 'add_shortcode' ) );
            
        }

        public function add_shortcode() {

            ob_start();

            require( BETP2P_DIR_PATH . 'views/frontend/shortcodes/betp2p-account_shortcode.php');

            return ob_get_clean();

        }

    }

}