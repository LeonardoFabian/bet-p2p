<?php 

if ( ! class_exists( 'BetP2P_TakeBet_Shortcode' ) ) {

    class BetP2P_TakeBet_Shortcode {

        public function __construct() {
            
            add_shortcode( 'betp2p_take_bet_form', array( $this, 'add_shortcode' ) );
        }

        public function add_shortcode() {

            ob_start();
            require( BETP2P_DIR_PATH . 'views/frontend/shortcodes/bet-p2p-take-bet-form_shortcode.php' );
            wp_enqueue_script( 'betp2p-take-bet-form-validate-js' );
            wp_enqueue_script( 'jquery-validate-min-js' );
            return ob_get_clean();

        }

    }
}