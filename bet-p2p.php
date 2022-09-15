<?php

/**
 * Plugin Name: Bet P2P
 * Plugin URI:
 * Description: Bet with your friends with this wonderful P2P betting system
 * Version: 1.0
 * Requires at least: 6.0
 * Author: Leonardo Fabian
 * Author URI: https://leonardofabian.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: betp2p
 * Domain Path: /languages
 */

/*
BET P2P is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

BET P2P is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with BET P2P. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/



if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'BetP2P' ) ) {

    class BetP2P {

        public $options = array();        

        function __construct() {

            $this->define_constants();

            $this->load_dependencies();            

            $this->load_textdomain();

            // functions
            require_once( BETP2P_DIR_PATH . 'functions/functions.php' );

            // plugin menu
            add_action( 'admin_menu', array( $this, 'add_menu' ) );

            // admin scripts
            add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

            // frontend scripts
            add_action( 'wp_enqueue_scripts', array( $this, 'register_public_scripts' ) );

            add_filter( 'template_include', array( $this, 'include_templates' ) );

        }

        /**
         * Define all plugin constants
         *
         * @return void
         */
        public function define_constants() {
            define( 'BETP2P_DIR_PATH', plugin_dir_path( __FILE__ ) );
            define( 'BETP2P_DIR_URL', plugin_dir_url( __FILE__ ) );
            define( 'BETP2P_VERSION', '1.0.0' );
            define( 'BETP2P_TERMS_AND_CONDITIONS_VERSION', '1.0' );            
            define( 'BETP2P_PRIVACY_POLICY', '1.0' );            
        }

        public function load_dependencies() {

            // Custom Post Types
            require_once( BETP2P_DIR_PATH . 'post-types/class.bet-p2p-cpt.php' );
            $BetP2P_PostType = new BetP2P_Post_Type();

            require_once( BETP2P_DIR_PATH . 'post-types/class.bet-p2p-match-cpt.php' );
            $BetP2P_MatchPostType = new BetP2P_Match_Post_Type();   
            
            require_once( BETP2P_DIR_PATH . 'post-types/class.bet-p2p-team-cpt.php' );
            $BetP2P_TeamPostType = new BetP2P_Team_Post_Type();

            require_once( BETP2P_DIR_PATH . 'bet-p2p-odds.php');

            // shortcodes           

            require_once( BETP2P_DIR_PATH . 'shortcodes/class.bet-p2p-bets-shortcode.php' );
            $BetP2P_BetsShortcode = new BetP2P_Bets_Shortcode();

            require_once( BETP2P_DIR_PATH . 'shortcodes/class.betp2p-take-bet-shortcode.php' );
            $BetP2P_TakeBetShortcode = new BetP2P_TakeBet_Shortcode();

            require_once( BETP2P_DIR_PATH . 'shortcodes/class.bet-p2p-matches-shortcode.php' );
            $BetP2P_MatchesShortcode = new BetP2P_Matches_Shortcode();

            require_once( BETP2P_DIR_PATH . 'shortcodes/class.betp2p-account-shortcode.php' );
            $BetP2P_Account = new BetP2P_Account();

            require_once( BETP2P_DIR_PATH . 'shortcodes/class.betp2p-cash-in-shortcode.php' );
            $BetP2P_WalletCashIn = new BetP2P_Wallet_Cashin();

            require_once( BETP2P_DIR_PATH . 'shortcodes/class.betp2p-cash-out-shortcode.php' );
            $BetP2P_WalletCashOut = new BetP2P_Wallet_Cashout();

            require_once( BETP2P_DIR_PATH . 'shortcodes/class.betp2p-transactions-shortcode.php' );
            $BetP2P_Transactions = new BetP2P_Transactions();

            require_once( BETP2P_DIR_PATH . 'shortcodes/class.betp2p-wallet-shortcode.php' );
            $BetP2P_Wallet = new BetP2P_Wallet();

            require_once( BETP2P_DIR_PATH . 'shortcodes/class.betp2p-profile-shortcode.php' );
            $BetP2P_Profile = new BetP2P_Profile();

            require_once( BETP2P_DIR_PATH . 'shortcodes/class.betp2p-user-bets-shortcode.php' );
            $BetP2P_UserBets = new BetP2P_User_Bets();

            // widgets

            require_once( BETP2P_DIR_PATH . 'widgets/class.bet-p2p-matches-widget.php' );
            $BetP2P_MatchesWidget = new BetP2P_Matches_Widget();

            require_once( BETP2P_DIR_PATH . 'widgets/class.bet-p2p-bets-widget.php' );
            $BetP2P_BetsWidget = new BetP2P_Bets_Widget();

            require_once( BETP2P_DIR_PATH . 'widgets/class.bet-p2p-teams-widget.php' );
            $BetP2P_TeamsWidget = new BetP2P_Teams_Widget();

            // models
            require( BETP2P_DIR_PATH . 'models/class.users.php' );
            $BetP2P_Users = new BetP2P_Users();

            require( BETP2P_DIR_PATH . 'models/class.wallets.php' );
            $BetP2P_Wallets = new BetP2P_Wallets();

        }

        /**
         * Activate the plugin
         */
        public static function activate() {
            update_option( 'rewrite_rules', '' );

            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();

            // custom tables
            $tableprefix = $wpdb->prefix . "betp2p_";

            $betp2p_db_version = get_option( 'betp2p_db_version' );

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            if ( empty( $betp2p_db_version ) ) {

                $betsmetatable = $tableprefix . 'betsmeta'; 
                $matchesmetatable = $tableprefix . 'matchesmeta';
                $betsmatchedtable = $tableprefix . 'bets_matched';
                $wallettable = $tableprefix . 'wallet';
                $accounttable = $tableprefix . 'account';
                $transactionstable = $tableprefix . 'transactions';

                $sql = "CREATE TABLE " . $betsmetatable . " (
                        meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                        bet_id bigint(20) NOT NULL DEFAULT '0',
                        meta_key varchar(255) DEFAULT NULL,
                        meta_value longtext,
                        PRIMARY KEY  (meta_id),                      
                        KEY bet_id (bet_id),
                        KEY meta_key (meta_key)                       
                    ) $charset_collate;"; 
                    dbDelta($sql);      
                
                $sql = "CREATE TABLE " . $matchesmetatable . " (
                        meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                        match_id bigint(20) NOT NULL DEFAULT '0',
                        meta_key varchar(255) DEFAULT NULL,
                        meta_value longtext,
                        PRIMARY KEY  (meta_id),
                        KEY match_id (match_id),
                        KEY meta_key (meta_key)                       
                    ) $charset_collate;";
                    dbDelta($sql); 

                $sql = "CREATE TABLE " . $betsmatchedtable . " (
                    ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    maker_bet_id bigint(20) unsigned NOT NULL,
                    taker_bet_id bigint(20) unsigned NOT NULL,
                    match_id bigint(20) unsigned NOT NULL,
                    maker_id bigint(20) unsigned NOT NULL,
                    taker_id bigint(20) unsigned NOT NULL,
                    has_maker_won boolean DEFAULT false,
                    has_taker_won boolean DEFAULT false,
                    status enum('open', 'closed') DEFAULT 'open',
                    PRIMARY KEY  (ID),
                    FOREIGN KEY  (maker_bet_id) REFERENCES " . $wpdb->prefix . "posts(ID),
                    FOREIGN KEY  (taker_bet_id) REFERENCES " . $wpdb->prefix . "posts(ID),
                    FOREIGN KEY  (maker_id) REFERENCES " . $wpdb->prefix . "users(ID),
                    FOREIGN KEY  (taker_id) REFERENCES " . $wpdb->prefix . "users(ID)
                ) $charset_collate;";
                dbDelta($sql);

                $sql = "CREATE TABLE " . $wallettable . " (
                    ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    user_id bigint(20) unsigned NOT NULL,
                    amount decimal(10,2),
                    status enum('private', 'public') DEFAULT 'private',
                    created_at TIMESTAMP,
                    updated_at DATETIME,
                    active boolean DEFAULT true,
                    PRIMARY KEY  (ID),
                    FOREIGN KEY  (user_id) REFERENCES " . $wpdb->prefix . "users(ID),
                    UNIQUE KEY user_id (user_id)
                ) $charset_collate;";
                dbDelta($sql);

                $sql = "CREATE TABLE " . $accounttable . " (
                    ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    account_number varchar(10) NOT NULL,
                    user_id bigint(20) unsigned NOT NULL,
                    balance decimal(10,2),
                    opening_date TIMESTAMP,
                    status enum('0', '1') DEFAULT '1',
                    PRIMARY KEY  (ID),
                    FOREIGN KEY  (user_id) REFERENCES " . $wpdb->prefix . "users(ID), 
                    UNIQUE KEY account_number (account_number)
                ) $charset_collate;";
                dbDelta($sql);

                $sql = "CREATE TABLE " . $transactionstable . " (
                    ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    type enum('credito', 'debito') DEFAULT 'credito',
                    user_id bigint(20) unsigned NOT NULL,
                    account_id bigint(10) unsigned NOT NULL,
                    amount decimal(10,2),
                    details longtext,
                    created_at TIMESTAMP,
                    PRIMARY KEY  (ID),
                    FOREIGN KEY  (user_id) REFERENCES " . $wpdb->prefix . "users(ID),
                    FOREIGN KEY  (account_id) REFERENCES " . $wpdb->prefix . "betp2p_account(ID)
                ) $charset_collate;";
                dbDelta($sql);
                
                $betp2p_db_version = '1.0';
                add_option( 'betp2p_db_version', $betp2p_db_version );

            }

            // create submit-bet page if not exists
            if ( $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'submit-bet'" ) === null ) {

                $current_user = wp_get_current_user();
                
                $page = array(
                    'post_title' => __('Submit Bet', 'betp2p' ),
                    'post_name' => 'submit-bet',
                    'post_status' => 'publish',
                    'post_author' => $current_user->ID,
                    'post_type' => 'page',
                    'post_content' => '<!-- wp:shortcode -->[betp2p_bet_form]<!-- /wp:shortcode -->'
                );

                wp_insert_post( $page );

            }

            // create wallet page if not exists
            if ( $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'wallet'" ) === null ) {

                $current_user = wp_get_current_user();
                
                $page = array(
                    'post_title' => __('Wallet', 'betp2p' ),
                    'post_name' => 'wallet',
                    'post_status' => 'publish',
                    'post_author' => $current_user->ID,
                    'post_type' => 'page',
                    'post_content' => '<!-- wp:shortcode -->[betp2p_wallet]<!-- /wp:shortcode -->'
                );

                $wallet_page_id = wp_insert_post( $page );

            }

            // create account page if not exists
            if ( $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'account'" ) === null ) {

                $current_user = wp_get_current_user();
                
                $page = array(
                    'post_title' => __('Account', 'betp2p' ),
                    'post_name' => 'account',
                    'post_status' => 'publish',
                    'post_author' => $current_user->ID,
                    'post_type' => 'page',
                    'post_content' => '<!-- wp:shortcode -->[betp2p_account]<!-- /wp:shortcode -->'
                );

                wp_insert_post( $page );

            }

            // create transactions page if not exists
            if ( $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'transactions'" ) === null ) {

                $current_user = wp_get_current_user();
                
                $page = array(
                    'post_title' => __('Transactions', 'betp2p' ),
                    'post_name' => 'transactions',
                    'post_status' => 'publish',
                    'post_author' => $current_user->ID,
                    'post_type' => 'page',
                    'post_content' => '<!-- wp:shortcode -->[betp2p_transactions]<!-- /wp:shortcode -->'
                );

                wp_insert_post( $page );

            }

            // create cash in page if not exists
            if ( $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'cash-in'" ) === null ) {

                $current_user = wp_get_current_user();
                
                $page = array(
                    'post_title' => __('Cash in', 'betp2p' ),
                    'post_name' => 'cash-in',
                    'post_status' => 'publish',
                    'post_parent' => $wallet_page_id,
                    'post_author' => $current_user->ID,
                    'post_type' => 'page',
                    'post_content' => '<!-- wp:shortcode -->[betp2p_cashin]<!-- /wp:shortcode -->'
                );

                wp_insert_post( $page );

            }

            // create cash out page if not exists
            if ( $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'cash-out'" ) === null ) {

                $current_user = wp_get_current_user();
                
                $page = array(
                    'post_title' => __('Cash out', 'betp2p' ),
                    'post_name' => 'cash-out',
                    'post_status' => 'publish',
                    'post_parent' => $wallet_page_id,
                    'post_author' => $current_user->ID,
                    'post_type' => 'page',
                    'post_content' => '<!-- wp:shortcode -->[betp2p_cashout]<!-- /wp:shortcode -->'
                );

                wp_insert_post( $page );

            }

            if ( $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'take-bet'" ) === null ) {

                $current_user = wp_get_current_user();

                $page = array(
                    'post_title' => __( 'Take Bet', 'betp2p' ),
                    'post_name' => 'take-bet',
                    'post_status' => 'publish',
                    'post_author' => $current_user->ID,
                    'post_type' => 'page',
                    'post_content' => '<!-- wp:shortcode -->[betp2p_take_bet_form]<!-- /wp:shortcode -->'
                );

                wp_insert_post( $page );

            }

            // create edit-bet page if not exists
            if ( $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'edit-bet'" ) === null ) {

                $current_user = wp_get_current_user();
                
                $page = array(
                    'post_title' => __('Edit Bet', 'betp2p' ),
                    'post_name' => 'edit-bet',
                    'post_status' => 'publish',
                    'post_author' => $current_user->ID,
                    'post_type' => 'page',
                    'post_content' => '<!-- wp:shortcode -->[betp2p_edit]<!-- /wp:shortcode -->'
                );

                wp_insert_post( $page );

            }

            // create results page if not exists
            if ( $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'results'" ) === null ) {

                $current_user = wp_get_current_user();
                
                $page = array(
                    'post_title' => __('Results', 'betp2p' ),
                    'post_name' => 'results',
                    'post_status' => 'publish',
                    'post_author' => $current_user->ID,
                    'post_type' => 'page',
                    'post_content' => '<!-- wp:shortcode -->[betp2p_results]<!-- /wp:shortcode -->'
                );

                wp_insert_post( $page );

            }

            // create profile page if not exists
            if ( $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'profile'" ) === null ) {

                $current_user = wp_get_current_user();
                
                $page = array(
                    'post_title' => __('Profile', 'betp2p' ),
                    'post_name' => 'profile',
                    'post_status' => 'publish',
                    'post_author' => $current_user->ID,
                    'post_type' => 'page',
                    'post_content' => '<!-- wp:shortcode -->[betp2p_profile]<!-- /wp:shortcode -->'
                );

                wp_insert_post( $page );

            }

            // create user-bets page if not exists
            if ( $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'user-bets'" ) === null ) {

                $current_user = wp_get_current_user();
                
                $page = array(
                    'post_title' => __('User Bets', 'betp2p' ),
                    'post_name' => 'user-bets',
                    'post_status' => 'publish',
                    'post_author' => $current_user->ID,
                    'post_type' => 'page',
                    'post_content' => '<!-- wp:shortcode -->[betp2p_user_bets]<!-- /wp:shortcode -->'
                );

                wp_insert_post( $page );

            }
        }

        /**
         * Deactivate the plugin
         *
         * @return void
         */
        public static function deactivate() {

            flush_rewrite_rules();
            unregister_post_type( 'bet-p2p' );
            unregister_post_type( 'betp2p-match' );
            unregister_post_type( 'betp2p-team' );
            delete_option( 'betp2p_db_version' );

            global $wpdb;

            // PLUGIN TABLES
            $tables = array(
                "betp2p_betsmeta",
                "betp2p_matchesmeta",
                "betp2p_bets_matched",
                "betp2p_wallet",
                "betp2p_account",
                "betp2p_transactions"
            );

            foreach ( $tables as $table ) {
                $wpdb->query("DROP TABLE IF EXISTS " . self::betp2p_get_table_name( $table ) );
            }

            $posts = $wpdb->get_results(
                "SELECT * FROM " . $wpdb->prefix . "posts WHERE post_type = 'betp2p-match'"
            );

            foreach ( $posts as $post ) {
                wp_delete_post( $post->ID, true );
            }
            
        }

        public function betp2p_get_table_name( $table ) {
            global $wpdb;
            return $wpdb->prefix . $table;
        }

        /**
         * Uninstall the plugin
         *
         * @return void
         */
        public static function uninstall() {

            delete_option( 'betp2p_odds_api_options' );
            delete_option( 'betp2p_db_version' );
            
        }

        /**
         * Load plugin translations
         */
        public function load_textdomain() {

            load_plugin_textdomain(
                'betp2p',
                false,
                dirname( plugin_basename( __FILE__ ) ) . '/languages/'
            );

        }

        /**
         * Register plugin options page
         *
         * @return void
         */
        public function add_menu() {

            add_options_page(
                esc_html__( 'BetP2P Settings', 'betp2p' ),
                esc_html__( 'BetP2P', 'betp2p' ),
                'manage_options',
                'betp2p_options',
                array( $this, 'betp2p_options_page' )
            );

        }     

        /**
         * Display Plugin Options page
         *
         * @return void
         */
        public function betp2p_options_page() {

            if ( ! current_user_can( 'manage_options' ) ) {
                wp_die( 'You do not have permission to view this page' );
            }

            global $options;            

            if ( isset( $_POST['betp2p-odds-api-form-submitted'] ) ) {
                
                $form_submitted = esc_html( $_POST['betp2p-odds-api-form-submitted'] );

                if ( $form_submitted == "yes" ) {

                    $sports = esc_html( $_POST['_betp2p_odds_api_sport_parameter'] );
                    $regions = esc_html( $_POST['_betp2p_odds_api_regions_parameter'] );
                    $apikey = esc_html( $_POST['_betp2p_odds_api_apikey'] );

                    $BetP2P_Odds = new BetP2P_Odds( $apikey );
                    $oddsapi_results = $BetP2P_Odds->get( $sports, $regions );

                    // convert date format fromm 2022-08-16T01:37:15Z to 2022-07-06 13:21:47               

                    global $wpdb;
                    $wpdb->matchesmeta = $wpdb->prefix . 'betp2p_matchesmeta';

                    // Create a match post type for each match of the API result
                    foreach($oddsapi_results as $data) {

                        $post_name = $data->{'away_team'} . '-' . $data->{'home_team'} . '-' . $data->{'id'};
                        $sport = $data->{'sport_title'};
                        $current_user = wp_get_current_user();

                        if ($wpdb->get_row("SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = '$post_name'") === null) {                            

                            // var_dump( $data->{'home_team'} );

                            $match_id = wp_insert_post(array(
                                'post_type' => 'betp2p-match',
                                'post_title' => $data->{'away_team'} . ' @ ' . $data->{'home_team'},
                                'post_name' => $post_name,
                                'post_status' => 'publish',
                                'post_content' => '<!-- wp:shortcode -->[betp2p_bet_form]<!-- /wp:shortcode -->',
                                'tax_input' => array(
                                    'betp2p-match-sport' => sanitize_text_field( $sport )
                                ),
                                'post_author' => $current_user->ID,
                                'comment_status' => 'closed',
                                'ping_status' => 'closed'
                            ));

                            if( $match_id ) {

                                $wpdb->insert(
                                    $wpdb->matchesmeta,
                                    array(
                                        'match_id' => $match_id,
                                        'meta_key' => '_betp2p_match_sport_key',
                                        'meta_value' => $data->{'sport_key'}                          
                                    ),
                                    array(
                                        '%d', '%s', '%s'
                                    )
                                );
                                $wpdb->insert(
                                    $wpdb->matchesmeta,
                                    array(
                                        'match_id' => $match_id,
                                        'meta_key' => '_betp2p_match_sport_title',
                                        'meta_value' => $data->{'sport_title'}                          
                                    ),
                                    array(
                                        '%d', '%s', '%s'
                                    )
                                );
                                $wpdb->insert(
                                    $wpdb->matchesmeta,
                                    array(
                                        'match_id' => $match_id,
                                        'meta_key' => '_betp2p_match_commence_time',
                                        'meta_value' => $data->{'commence_time'}                          
                                    ),
                                    array(
                                        '%d', '%s', '%s'
                                    )
                                );
                                $wpdb->insert(
                                    $wpdb->matchesmeta,
                                    array(
                                        'match_id' => $match_id,
                                        'meta_key' => '_betp2p_match_home_team',
                                        'meta_value' => $data->{'home_team'}                          
                                    ),
                                    array(
                                        '%d', '%s', '%s'
                                    )
                                );
                                $wpdb->insert(
                                    $wpdb->matchesmeta,
                                    array(
                                        'match_id' => $match_id,
                                        'meta_key' => '_betp2p_match_away_team',
                                        'meta_value' => $data->{'away_team'}                          
                                    ),
                                    array(
                                        '%d', '%s', '%s'
                                    )
                                );
                                $wpdb->insert(
                                    $wpdb->matchesmeta,
                                    array(
                                        'match_id' => $match_id,
                                        'meta_key' => '_betp2p_odd_api_match_id',
                                        'meta_value' => $data->{'id'}                          
                                    ),
                                    array(
                                        '%d', '%s', '%s'
                                    )
                                );

                            }
                        }
                    }

                    $options['_betp2p_odds_api_sport_parameter'] = $sports;
                    $options['_betp2p_odds_api_regions_parameter'] = $regions;
                    $options['_betp2p_odds_api_apikey'] = $apikey;
                    $options['_betp2p_odds_api_last_updated'] = time();

                    $options['_betp2p_odds_api_results'] = $oddsapi_results;

                    update_option( 'betp2p_odds_api_options', $options );

                }

            }

            $options = get_option( 'betp2p_odds_api_options' );

            if ( ! empty( $options ) ) {
                $sports =  $options['_betp2p_odds_api_sport_parameter'];
                $regions = $options['_betp2p_odds_api_regions_parameter'];
                $apikey =  $options['_betp2p_odds_api_apikey'];
                $oddsapi_results = $options['_betp2p_odds_api_results'];
                $last_updated = $options['_betp2p_odds_api_last_updated'];
            }

            require( BETP2P_DIR_PATH . 'views/bet-p2p-options-page.php' );

        }

        public function register_admin_scripts() {

            $current_screen = get_current_screen();

            // var_dump( $current_screen );

            if ( $current_screen->id == 'bet-p2p' ) {

                wp_enqueue_style(
                    'betp2p-admin-css',
                    BETP2P_DIR_URL . 'assets/css/admin.css'
                );

            }

            if ( $current_screen->id == 'settings_page_betp2p_options' ) {

                wp_enqueue_style(
                    'betp2p-options-page-css',
                    BETP2P_DIR_URL . 'assets/css/options-page.css'
                );

            }
        }

        public function register_public_scripts() {

            // Fontawesome
            wp_enqueue_style( 
                'betp2p-fontawesome', 
                BETP2P_DIR_URL .'helpers/fontawesome-5.15.4/css/all.min.css', 
                array(), 
                '5.15.4',
                'all'
            );
            wp_enqueue_style( 
                'betp2p-fontawesome-brands', 
                BETP2P_DIR_URL .'helpers/fontawesome-5.15.4/css/brands.min.css', 
                array(), 
                '5.15.4',
                'all'
            );

            wp_enqueue_style(
                'betp2p-public-bet-form-css',
                BETP2P_DIR_URL . 'assets/css/bet-form.css',
                array(),
                BETP2P_VERSION,
                'all'
            );

            wp_enqueue_style(
                'betp2p-public-widgets-css',
                BETP2P_DIR_URL . 'assets/public/css/widgets.css',
                array(),
                BETP2P_VERSION,
                'all'
            );

            wp_enqueue_style(
                'betp2p-public-bets-css',
                BETP2P_DIR_URL . 'assets/public/css/bets.css',
                array(),
                BETP2P_VERSION,
                'all'
            );

            wp_enqueue_style( 
                'datepicker-ui-css', 
                BETP2P_DIR_URL .'assets/public/css/jquery-ui.css', 
                array(), 
                '1.13.2',
                'all'
            );
    
            wp_enqueue_style( 
                'datepicker-theme', 
                BETP2P_DIR_URL .'assets/public/css/jquery-ui.theme.css', 
                array(), 
                '1.13.2',
                'all'
            );
    
            wp_enqueue_style( 
                'datepicker-structure', 
                BETP2P_DIR_URL .'assets/public/css/jquery-ui.structure.css', 
                array(), 
                '1.13.2',
                'all'
            );

            if ( is_singular( 'betp2p-match' ) ) {
                wp_enqueue_script(
                    'betp2p-bet-form-validate-js',
                    BETP2P_DIR_URL . 'assets/public/js/bet-form.validate.js',
                    array( 'jquery' ),
                    BETP2P_VERSION,
                    true
                );
            }

            if( is_page( 'take-bet' ) ) {
                wp_enqueue_script(
                    'betp2p-take-bet-form-validate-js',
                    BETP2P_DIR_URL . 'assets/public/js/take-bet-form.validate.js',
                    array( 'jquery' ),
                    BETP2P_VERSION,
                    true
                );
            }

          
            // wp_enqueue_script(
            //     'betp2p-popper',
            //     BETP2P_DIR_URL . 'assets/public/js/popper.min.js',
            //     array( 'jquery' ),
            //     '1.0.0',
            //     true
            // );
        

            if ( is_post_type_archive( 'bet-p2p' ) ) {
                wp_enqueue_script(
                    'betp2p-twitter-bootstrap-countdown',
                    BETP2P_DIR_URL . 'assets/public/js/countdown.jquery.js',
                    array( 'jquery' ),
                    '1.0.0',
                    true
                );
                wp_enqueue_script(
                    'betp2p-bets-archive',
                    BETP2P_DIR_URL . 'assets/public/js/bets-archive.js',
                    array( 'jquery' ),
                    BETP2P_VERSION,
                    true
                );
            }

            wp_enqueue_script(
                'betp2p-register-form-validate-js',
                BETP2P_DIR_URL . 'assets/public/js/register-form.validate.js',
                array( 'jquery' ),
                BETP2P_VERSION,
                true
            );

            wp_enqueue_script(
                'jquery-validate-min-js',
                BETP2P_DIR_URL . 'assets/public/js/jquery.validate.min.js',
                array( 'jquery' ),
                '1.19.5',
                true
            );

            wp_enqueue_script( 
                'datepicker-ui-js', 
                BETP2P_DIR_URL . 'assets/public/js/jquery-ui.js', 
                array( 'jquery' ), 
                '1.13.2' 
            );
    
            wp_enqueue_script( 
                'datepicker-custom-js', 
                BETP2P_DIR_URL . 'assets/public/js/custom-datepicker.js', 
                array( 'jquery' ), 
                '1.13.2' 
            );

        }

        public function include_templates( $template ) {

            # bet-p2p

            if ( is_post_type_archive( 'bet-p2p' ) ) {
                $template = BETP2P_DIR_PATH . 'views/templates/archive-bet-p2p.php';
            }

            # betp2p-match

            if ( is_post_type_archive( 'betp2p-match' ) ) {
                $template = BETP2P_DIR_PATH . 'views/templates/archive-betp2p-match.php';
            }

            if ( is_singular( 'betp2p-match' ) ) {
                $template = BETP2P_DIR_PATH . 'views/templates/single-betp2p-match.php';
            }

            if ( is_page( 'results' ) ) {
                $template = BETP2P_DIR_PATH . 'views/templates/page-results.php';
            }

            if ( is_page( 'wallet' ) ) {
                $template = BETP2P_DIR_PATH . 'views/templates/page-wallet.php';
            }

            if ( is_page( 'account' ) ) {
                $template = BETP2P_DIR_PATH . 'views/templates/page-account.php';
            }

            if ( is_page( 'cash-in' ) ) {
                $template = BETP2P_DIR_PATH . 'views/templates/page-cash-in.php';
            }

            if ( is_page( 'cash-out' ) ) {
                $template = BETP2P_DIR_PATH . 'views/templates/page-cash-out.php';
            }

            if ( is_page( 'profile' ) ) {
                $template = BETP2P_DIR_PATH . 'views/templates/page-profile.php';
            }

            if ( is_page( 'user-bets' ) ) {
                $template = BETP2P_DIR_PATH . 'views/templates/page-user-bets.php';
            }

            if ( is_page( 'transactions' ) ) {
                $template = BETP2P_DIR_PATH . 'views/templates/page-transactions.php';
            }


            return $template;
        }
    

    }
}

if ( class_exists( 'BetP2P' ) ) {

    // Installation an uninstallation hooks
    register_activation_hook( __FILE__, array( 'BetP2P', 'activate' ) );
    register_deactivation_hook( __FILE__, array( 'BetP2P', 'deactivate' ) );
    register_uninstall_hook( __FILE__, array( 'BetP2P', 'uninstall' ) );

    // Instatiate the plugin class
    $BetP2P = new BetP2P();
}