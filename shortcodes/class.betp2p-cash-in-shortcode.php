<?php 

/**
 * Class responsible for displaying the form that records the entry of money into the wallet
 */

if ( ! class_exists( 'BetP2P_Wallet_Cashin' ) ) {

    class BetP2P_Wallet_Cashin {

        public function __construct() {

            add_shortcode( 'betp2p_cashin', array( $this, 'add_shortcode' ) );
            
        }

        public function add_shortcode() {

            ob_start();

            return ob_get_clean();
            
        }

    }

}