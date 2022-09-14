<?php 

/**
 * Class responsible for displaying the user's electronic wallet
 */

if ( ! class_exists( 'BetP2P_Wallet' ) ) {

    class BetP2P_Wallet {

        public function __construct() {

            add_shortcode( 'betp2p_wallet', array( $this, 'add_shortcode' ) );
            
        }

        public function add_shortcode() {

            ob_start();

            require( BETP2P_DIR_PATH . 'views/frontend/shortcodes/betp2p-wallet_shortcode.php' );

            return ob_get_clean();

        }

    }

}