<?php 

if ( ! class_exists( 'BetP2P_Matches_Shortcode' ) ) {

    class BetP2P_Matches_Shortcode {

        public function __construct() {

            add_shortcode( 'betp2p_odds', array( $this, 'add_shortcode' ) );
            
        }

        public function add_shortcode() {

            ob_start();
            require( BETP2P_DIR_PATH . 'views/frontend/shortcodes/bet-p2p-matches_shortcode.php' );
            return ob_get_clean();

        }

    }
}