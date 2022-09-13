<?php 

if ( ! class_exists( 'BetP2P_Users' ) ) {

    class BetP2P_Users {

        protected $users;

        public function __construct() {

            global $wpdb;

            $this->users = "SELECT * FROM {$wpdb->prefix}users";
            
        }

        public function get_users() {

            global $wpdb;

            $sql = $this->users;
            $result = $wpdb->get_results( $sql, ARRAY_A );
            var_dump( $result );
            
        }

    }

}