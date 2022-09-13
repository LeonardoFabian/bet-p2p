<?php 

if ( ! class_exists( 'BetP2P_Wallets' ) ) {

    class BetP2P_Wallets {

        protected $wallets;

        public function __construct() {

            global $wpdb;

            $wpdb->wallets = $wpdb->prefix . 'betp2p_wallet';

            $this->wallets = "SELECT * FROM {$wpdb->wallets}";
            
        }       

        public function get_wallets() {

            global $wpdb;

            $sql = $this->wallets;
            $result = $wpdb->get_results( $sql, ARRAY_A );
            var_dump( $result );
            
        }

    }

}