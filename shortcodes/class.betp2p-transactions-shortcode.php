<?php 

/**
 * Class responsible for displaying the table where the transactions made by the user are recorded
 */

if ( ! class_exists( 'BetP2P_Transactions' ) ) {

    class BetP2P_Transactions {

        public function __construct() {

            add_shortcode( 'betp2p_transactions', array( $this, 'add_shortcode' ) );
            
        }

        public function add_shortcode() {

            ob_start();

            require( BETP2P_DIR_PATH . 'views/frontend/shortcodes/betp2p-transactions_shortcode.php' );

            return ob_get_clean();

        }

    }

}