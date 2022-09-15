<?php 

/**
 * Class responsible for displaying the user's bets
 */

if ( ! class_exists( 'BetP2P_User_Bets' ) ) {

    class BetP2P_User_Bets {

        public function __construct() {

            add_shortcode( 'betp2p_user_bets', array( $this, 'add_shortcode' ) );
            
        }

        public function add_shortcode() {

            ob_start();

            require( BETP2P_DIR_PATH . 'views/frontend/shortcodes/betp2p-user_bets_shortcode.php' );

            return ob_get_clean();

        }

    }

}