<?php 

if ( ! class_exists( 'BetP2P_Post_Type' ) ) {

    class BetP2P_Post_Type {

        public function __construct() {

            // custom post type
            add_action( 'init', array( $this, 'create_post_type' ) );

            // taxonomy
            add_action( 'init', array( $this, 'create_taxonomy' ) );

            // metadata table
            add_action( 'init', array( $this, 'register_metadata_table' ) );

            // cpt metaboxes
            add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

            // insert and update data in metadata table
            add_action( 'wp_insert_post', array( $this, 'save_post' ), 10, 2 );

            add_action( 'wp_insert_post', array( $this, 'take_bet' ), 10, 2 );

            // delete data in metadata table
            add_action( 'delete_post', array( $this, 'delete_post' ) );
            
        }        

        public function create_post_type() {

            register_post_type(
                'bet-p2p',
                array(
                    'label' => esc_html__('Bet', 'betp2p'),
                    'description' => esc_html__('Bets', 'betp2p'),
                    'labels' => array(
                        'name' => esc_html__('Bets', 'betp2p'),
                        'singular_name' => esc_html__('Bet', 'betp2p'),
                    ),
                    'public' => true,
                    'supports' => array( 'title', 'editor', 'author' ), // set 'page-attributes' if 'hierarchical' => true
                    'rewrite' => array( 'slug' => 'bets' ),
                    'menu_icon' => 'dashicons-money-alt',
                    'hierarchical' => false,
                    'show_ui' => true,
                    'show_in_menu' => true, // set false if the plugin using a custom add_menu_page
                    'menu_position' => 5,
                    'show_in_admin_bar' => true,
                    'show_in_nav_menus' => true,
                    'can_export' => true,
                    'has_archive' => true,
                    'exclude_from_search' => true,
                    'publicly_queryable' => true,
                    'show_in_rest' => true,
                    
                    // 'register_meta_box_cb' => array( $this, 'add_meta_boxes' )
                )
            );
        }          
        
        public function create_taxonomy() {

            register_taxonomy(
                'bet-type',
                'bet-p2p',
                array(
                    'labels' => array(
                        'name' => __( 'Bet Types', 'betp2p' ),
                        'singular_name' => __( 'Bet Type', 'betp2p' )
                    ),
                    'hierarchical' => false,
                    'show_in_rest' => true,
                    'public' => true,
                    'show_admin_column' => true
                ),
            );

        }

        public function register_metadata_table() {

            global $wpdb;

            $wpdb->betp2pmeta = $wpdb->prefix . 'betp2p_betsmeta';
            $wpdb->betsmatched = $wpdb->prefix . 'betp2p_bets_matched';

        }

        public function add_meta_boxes() {

            add_meta_box(
                'bet-p2p_meta_box',
                esc_html__( 'Bet Details', 'betp2p' ),
                array( $this, 'add_inner_meta_boxes' ),
                'bet-p2p',
                'normal',
                'high'
            );

        }

        /**
         * Display Post Type metabox 
         *
         * @param [type] $post
         * @return void
         */
        public function add_inner_meta_boxes( $post ) {

            require_once( BETP2P_DIR_PATH . 'views/backend/meta-boxes/bet-p2p_metabox.php' );

        }

        /**
         * Stores and updates bet metadata in custom metadata table
         *
         * @param [type] $post_id
         * @param [type] $post
         * @return void
         */
        public static function save_post( $post_id, $post ) {

            // verify nonce
            if ( isset( $_POST['betp2p_nonce'] ) ) {
                if ( ! wp_verify_nonce( $_POST['betp2p_nonce'], 'betp2p_nonce' ) ) {
                    return;
                }
            }

            // save data only if the update button is pressed and the browser has not been closed
            if ( defined( DOING_AUTOSAVE ) && DOING_AUTOSAVE ) {
                return;
            }

            // check user capabilities
            if ( isset( $_POST['post_type'] ) && $_POST['post_type'] === 'bet-p2p' ) {
                if ( ! current_user_can( 'edit_page', $post_id ) ) {
                    return;
                } elseif( ! current_user_can( 'edit_post', $post_id ) ) {
                    return;
                }
            }

            if ( isset( $_POST['action']) && $_POST['action'] == 'editpost' ) {

                $maker_selected_team = sanitize_text_field( $_POST['_betp2p_maker_selected_team'] );
                $bet_type = sanitize_text_field( $_POST['_betp2p_bet_type'] );
                $bet_value = sanitize_text_field( $_POST['_betp2p_bet_value'] );
                $point_spread_type = sanitize_text_field( $_POST['_betp2p_point_spread_type'] );
                $run_scored_type = sanitize_text_field( $_POST['_betp2p_run_scored_type'] );
                $both_teams_runs_scored = sanitize_text_field( $_POST['_betp2p_both_teams_runs_scored'] );
              
                $selected_teams_runs_scored = sanitize_text_field( $_POST['_betp2p_selected_teams_runs_scored'] );
                $run_scored_option = sanitize_text_field( $_POST['_betp2p_run_scored_option'] );

                $vs_mode_option = sanitize_text_field( $_POST['_betp2p_vs_mode_option'] );
             
                $stake_amount = sanitize_text_field( $_POST['_betp2p_stake_amount'] );
                $rate = sanitize_text_field( $_POST['_betp2p_rate_amount'] ); // TODO: Set Rate to options
                $subtotal = sanitize_text_field( $_POST['_betp2p_subtotal'] );
                $share_type = sanitize_text_field( $_POST['_betp2p_share_type'] );
                $match_id = sanitize_text_field( $_POST['match_id'] );

                global $wpdb;

                // save bets if metadata table is empty and update if not empty
                if ( $_POST['betp2p_action'] == 'save' ) {

                    if ( get_post_type( $post ) == 'bet-p2p' &&
                    $post->post_status != 'trash' &&
                    $post->post_status != 'auto-draft' &&
                    $post->post_status != 'draft' &&
                    $wpdb->get_var(
                        $wpdb->prepare(
                            "SELECT bet_id
                            FROM $wpdb->betp2pmeta
                            WHERE bet_id = %d", 
                            $post_id
                        )
                    ) == null
                    ) {
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_maker_selected_team',
                                'meta_value' => $maker_selected_team                            
                            ),
                            array(
                                '%d', '%s', '%d'
                            )
                        );
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_bet_type',
                                'meta_value' => $bet_type                            
                            ),
                            array(
                                '%d', '%s', '%s'
                            )
                        );
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_bet_value',
                                'meta_value' => $bet_value                            
                            ),
                            array(
                                '%d', '%s', '%d'
                            )
                        );
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_point_spread_type',
                                'meta_value' => $point_spread_type                          
                            ),
                            array(
                                '%d', '%s', '%s'
                            )
                        );
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_run_scored_type',
                                'meta_value' => $run_scored_type
                            ),
                            array(
                                '%d', '%s', '%s'
                            )
                        );
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_both_teams_runs_scored',
                                'meta_value' => $both_teams_runs_scored                        
                            ),
                            array(
                                '%d', '%s', '%d'
                            )
                        );                       
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_selected_teams_runs_scored',
                                'meta_value' => $selected_teams_runs_scored                      
                            ),
                            array(
                                '%d', '%s', '%d'
                            )
                        );
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_run_scored_option',
                                'meta_value' => $run_scored_option                  
                            ),
                            array( '%d', '%s', '%s' )
                        );
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_vs_mode_option',
                                'meta_value' => $vs_mode_option
                            ),
                            array( '%d', '%s', '%s' )
                        );                       
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_stake_amount',
                                'meta_value' => $stake_amount                
                            ),
                            array(
                                '%d', '%s', '%f'
                            )
                        );
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_rate',
                                'meta_value' => $rate             
                            ),
                            array(
                                '%d', '%s', '%f'
                            )
                        );
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_subtotal',
                                'meta_value' => $subtotal           
                            ),
                            array(
                                '%d', '%s', '%f'
                            )
                        );
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_share_type',
                                'meta_value' => $share_type         
                            ),
                            array(
                                '%d', '%s', '%s'
                            )
                        );
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => 'match_id',
                                'meta_value' => $match_id
                            ),
                            array( '%d', '%s', '%d' )
                        );
                    }
                } else {
                    if ( get_post_type( $post ) == 'bet-p2p' ){
                        $wpdb->update(
                            $wpdb->betp2pmeta,
                            array(
                                'meta_value' => $maker_selected_team
                            ),
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_maker_selected_team'
                            ),
                            array( '%d' ), // set format
                            array( '%d', '%s' ) // where formats
                        );
                        $wpdb->update(
                            $wpdb->betp2pmeta,
                            array(
                                'meta_value' => $bet_type
                            ),
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_bet_type'
                            ),
                            array( '%s' ), // set format
                            array( '%d', '%s' ) // where formats
                        );
                        $wpdb->update(
                            $wpdb->betp2pmeta,
                            array(
                                'meta_value' => $bet_value
                            ),
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_bet_value'
                            ),
                            array( '%d' ), // set format
                            array( '%d', '%s' ) // where formats
                        );
                        $wpdb->update(
                            $wpdb->betp2pmeta,
                            array(
                                'meta_value' => $point_spread_type
                            ),
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_point_spread_type'
                            ),
                            array( '%s' ), // set format
                            array( '%d', '%s' ) // where formats
                        );
                        $wpdb->update(
                            $wpdb->betp2pmeta,
                            array(
                                'meta_value' => $run_scored_type
                            ),
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_run_scored_type'
                            ),
                            array( '%s' ),
                            array( '%d', '%s' ),
                        );  
                        $wpdb->update(
                            $wpdb->betp2pmeta,
                            array(
                                'meta_value' =>  $both_teams_runs_scored
                            ),
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_both_teams_runs_scored'
                            ),
                            array( '%d' ), // set format
                            array( '%d', '%s' ) // where formats
                        );                        
                        $wpdb->update(
                            $wpdb->betp2pmeta,
                            array(
                                'meta_value' =>  $selected_teams_runs_scored
                            ),
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_selected_teams_runs_scored'
                            ),
                            array( '%d' ), // set format
                            array( '%d', '%s' ) // where formats
                        );
                        $wpdb->update(
                            $wpdb->betp2pmeta,
                            array(
                                'meta_value' =>  $run_scored_option
                            ),
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_run_scored_option'
                            ),
                            array( '%s' ), // set format
                            array( '%d', '%s' ) // where formats
                        );
                        $wpdb->update(
                            $wpdb->betp2pmeta,
                            array(
                                'meta_value' => $vs_mode_option
                            ),
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_vs_mode_option'
                            ),
                            array( '%s' ),
                            array( '%d', '%s' )
                        );                        
                        $wpdb->update(
                            $wpdb->betp2pmeta,
                            array(
                                'meta_value' =>  $stake_amount
                            ),
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_stake_amount'
                            ),
                            array( '%f' ), // set format
                            array( '%d', '%s' ) // where formats
                        );
                        $wpdb->update(
                            $wpdb->betp2pmeta,
                            array(
                                'meta_value' =>  $rate
                            ),
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_rate'
                            ),
                            array( '%f' ), // set format
                            array( '%d', '%s' ) // where formats
                        );
                        $wpdb->update(
                            $wpdb->betp2pmeta,
                            array(
                                'meta_value' =>  $subtotal
                            ),
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_subtotal'
                            ),
                            array( '%f' ), // set format
                            array( '%d', '%s' ) // where formats
                        );
                        $wpdb->update(
                            $wpdb->betp2pmeta,
                            array(
                                'meta_value' =>  $share_type
                            ),
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_share_type'
                            ),
                            array( '%s' ), // set format
                            array( '%d', '%s' ) // where formats
                        );
                        $wpdb->update(
                            $wpdb->betp2pmeta,
                            array(
                                'meta_value' => $match_id
                            ),
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => 'match_id'
                            ),
                            array( '%d' ),
                            array( '%d', '%s' )
                        );
                    }
                }

            }

        }

        public static function take_bet( $post_id, $post ) {

            // verify nonce
            if ( isset( $_POST['betp2p_taker_nonce'] ) ) {
                if ( ! wp_verify_nonce( $_POST['betp2p_taker_nonce'], 'betp2p_taker_nonce' ) ) {
                    return;
                }
            }

            if ( defined( DOING_AUTOSAVE ) && DOING_AUTOSAVE ) {
                return;
            }

            // check user capabilities
            if ( isset( $_POST['post_type'] ) && $_POST['post_type'] === 'bet-p2p' ) {
                if ( ! current_user_can( 'edit_page', $post_id ) ) {
                    return;
                } elseif( ! current_user_can( 'edit_post', $post_id ) ) {
                    return;
                }
            }

            if ( isset( $_POST['take-action'] ) && $_POST['take-action'] == 'closebet' ) {

                $taker_selected_team = sanitize_text_field( $_POST['_betp2p_taker_selected_team'] );
                $bet_type = sanitize_text_field( $_POST['_betp2p_bet_type'] );
                $bet_value = sanitize_text_field( $_POST['_betp2p_bet_value'] );
                $point_spread_type = sanitize_text_field( $_POST['_betp2p_point_spread_type'] );
                $run_scored_type = sanitize_text_field( $_POST['_betp2p_run_scored_type'] );
                $both_teams_runs_scored = sanitize_text_field( $_POST['_betp2p_both_teams_runs_scored'] );
              
                $selected_teams_runs_scored = sanitize_text_field( $_POST['_betp2p_selected_teams_runs_scored'] );
                $run_scored_option = sanitize_text_field( $_POST['_betp2p_run_scored_option'] );

                $vs_mode_option = sanitize_text_field( $_POST['_betp2p_vs_mode_option'] );
             
                $stake_amount = sanitize_text_field( $_POST['_betp2p_stake_amount'] );
                $rate = sanitize_text_field( $_POST['_betp2p_rate_amount'] ); // TODO: Set Rate to options
                $subtotal = sanitize_text_field( $_POST['_betp2p_subtotal'] );
                $share_type = sanitize_text_field( $_POST['_betp2p_share_type'] );
                $match_id = sanitize_text_field( $_POST['match_id'] );
                $maker_id = sanitize_text_field( $_POST['maker_id'] );
                $taker_id = sanitize_text_field( $_POST['taker_id'] );


                global $wpdb;
                global $inserted_id;

                if ( $_POST['betp2p_take_action'] == 'close-bet-matched' ) {

                    if ( get_post_type( $post )  == 'bet-p2p' &&
                    $post->post_status != 'trash' &&
                    $post->post_status != 'auto-draft' &&
                    $post->post_status != 'draft' &&
                    $wpdb->get_var(
                        $wpdb->prepare(
                            "SELECT bet_id
                            FROM $wpdb->betp2pmeta
                            WHERE bet_id = %d", 
                            $post_id
                        )
                    ) == null ) {
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_taker_selected_team',
                                'meta_value' => $taker_selected_team                            
                            ),
                            array(
                                '%d', '%s', '%d'
                            )
                        );
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_bet_type',
                                'meta_value' => $bet_type                            
                            ),
                            array(
                                '%d', '%s', '%s'
                            )
                        );
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_bet_value',
                                'meta_value' => $bet_value                            
                            ),
                            array(
                                '%d', '%s', '%d'
                            )
                        );
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_point_spread_type',
                                'meta_value' => $point_spread_type                          
                            ),
                            array(
                                '%d', '%s', '%s'
                            )
                        );
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_run_scored_type',
                                'meta_value' => $run_scored_type
                            ),
                            array(
                                '%d', '%s', '%s'
                            )
                        );
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_both_teams_runs_scored',
                                'meta_value' => $both_teams_runs_scored                        
                            ),
                            array(
                                '%d', '%s', '%d'
                            )
                        );                       
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_selected_teams_runs_scored',
                                'meta_value' => $selected_teams_runs_scored                      
                            ),
                            array(
                                '%d', '%s', '%d'
                            )
                        );
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_run_scored_option',
                                'meta_value' => $run_scored_option                  
                            ),
                            array( '%d', '%s', '%s' )
                        );
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_vs_mode_option',
                                'meta_value' => $vs_mode_option
                            ),
                            array( '%d', '%s', '%s' )
                        );                       
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_stake_amount',
                                'meta_value' => $stake_amount                
                            ),
                            array(
                                '%d', '%s', '%f'
                            )
                        );
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_rate',
                                'meta_value' => $rate             
                            ),
                            array(
                                '%d', '%s', '%f'
                            )
                        );
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_subtotal',
                                'meta_value' => $subtotal           
                            ),
                            array(
                                '%d', '%s', '%f'
                            )
                        );                       
                        $wpdb->insert(
                            $wpdb->betp2pmeta,
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => 'match_id',
                                'meta_value' => $match_id
                            ),
                            array( '%d', '%s', '%d' )
                        );

                        $inserted_id = $wpdb->insert_id;

                        $taker_bet_id = $wpdb->get_var(
                            $wpdb->prepare(
                                "SELECT bet_id
                                FROM $wpdb->betp2pmeta
                                WHERE meta_id = %d", 
                                $inserted_id
                            )
                        );

                        // var_dump( $taker_bet_id );

                        /**
                         * Insert bet matched data
                         */
                        $wpdb->insert(
                            $wpdb->betsmatched,
                            array(
                                'maker_bet_id' => $post_id,
                                'taker_bet_id' => $taker_bet_id,
                                'match_id' => $match_id,
                                'maker_id' => $maker_id,
                                'taker_id' => $taker_id,
                                'has_maker_won' => null,
                                'has_taker_won' => null,
                                'status' => $share_type
                            )
                        );

                        /**
                         * close bet
                         *
                         */
                        $wpdb->update(
                            $wpdb->betp2pmeta,
                            array(
                                'meta_value' => $share_type
                            ),
                            array(
                                'bet_id' => $post_id,
                                'meta_key' => '_betp2p_share_type'
                            ),
                            array( '%s' ), // set format
                            array( '%d', '%s' ) // where formats
                        );
                    }                    

                }

            }

        }

        public function delete_post( $post_id ) {

            if ( ! current_user_can( 'delete_posts' ) ) {
                return;
            }

            if ( get_post_type( $post ) == 'bet-p2p' ) {

                global $wpdb;

                $wpdb->delete(
                    $wpdb->betp2pmeta,
                    array( 'bet_id' => $post_id ),
                    array( '%d' )
                );

            }

        }

    }

}