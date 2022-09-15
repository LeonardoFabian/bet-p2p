<?php 

if ( ! class_exists( 'BetP2P_Profile' ) ) {

    class BetP2P_Profile {

        public function __construct() {
            
            add_shortcode( 'betp2p_profile', array( $this, 'add_shortcode' ) );
        }

        public function add_shortcode() {

            ob_start();

            require( BETP2P_DIR_PATH . 'views/frontend/shortcodes/betp2p-profile_shortcode.php' );
            wp_enqueue_style( 'betp2p-fontawesome' );
            wp_enqueue_style( 'betp2p-fontawesome-brands' );
            
            return ob_get_clean();

        }

    }
}