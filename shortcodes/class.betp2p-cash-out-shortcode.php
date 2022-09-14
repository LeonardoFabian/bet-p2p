<?php 

/**
 * Class responsible for displaying the form that records the output of money from the wallet
 */

if ( ! class_exists( 'BetP2P_Wallet_Cashout' ) ) {

    class BetP2P_Wallet_Cashout {

        public function __construct() {

            add_shortcode( 'betp2p_cashout', array( $this, 'add_shortcode' ) );
            
        }

        public function add_shortcode() {

            ob_start();

            return ob_get_clean();
            
        }

    }

}