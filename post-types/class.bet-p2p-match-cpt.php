<?php 

if ( ! class_exists( 'BetP2P_Match_Post_Type' ) ) {

    class BetP2P_Match_Post_Type {

        public function __construct() {
            
            // cpt
            add_action( 'init', array( $this, 'create_post_type' ) );

            // taxonomies
            add_action( 'init', array( $this, 'create_taxonomy' ) );

            // metadata table
            add_action( 'init', array( $this, 'register_metadata_table' ) );

            // cpt metaboxes
            add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

            // insert and update metatable
            add_action( 'wp_insert_post', array( $this, 'save_post' ), 10, 2 );

            // delete data in metatable
            add_action( 'delete_post', array( $this, 'delete_post' ) );
        }

        public function create_post_type() {

            register_post_type(
                'betp2p-match',
                array(
                    'label' => esc_html__('Match', 'betp2p'),
                    'description' => esc_html__('Matches', 'betp2p'),
                    'labels' => array(
                        'name' => esc_html__('Matches', 'betp2p'),
                        'singular_name' => esc_html__('Match', 'betp2p'),
                    ),
                    'public' => true,
                    'supports' => array( 'title', 'author', 'content' ), // set 'page-attributes' if 'hierarchical' => true
                    'rewrite' => array( 'slug' => 'matches' ),
                    'menu_icon' => 'dashicons-calendar-alt',
                    'hierarchical' => false,
                    'show_ui' => true,
                    'show_in_menu' => true, // set false if the plugin using a custom add_menu_page
                    'menu_position' => 5,
                    'show_in_admin_bar' => false,
                    'show_in_nav_menus' => true,
                    'can_export' => true,
                    'has_archive' => true,
                    'exclude_from_search' => false,
                    'publicly_queryable' => true,
                    'show_in_rest' => true,
                    // 'capability_type' => 'manage_options'
                    // 'register_meta_box_cb' => array( $this, 'add_meta_boxes' )
                )
            );

        }

        public function create_taxonomy() {

            register_taxonomy(
                'betp2p-match-sport',
                'betp2p-match',
                array(
                    'labels' => array(
                        'name' => __( 'Sports', 'betp2p' ),
                        'singular_name' => __( 'Sport', 'betp2p' )
                    ),
                    'hierarchical' => false,
                    'show_in_rest' => true,
                    'public' => true,
                    'show_admin_column' => true
                )
            );

        }

        public function register_metadata_table() {

            global $wpdb;

            $wpdb->matchesmeta = $wpdb->prefix . 'betp2p_matchesmeta';

        }

        public function add_meta_boxes() {

            add_meta_box(
                'betp2p_match_metabox',
                esc_html__( 'Details', 'betp2p' ),
                array( $this, 'add_inner_meta_boxes'),
                'betp2p-match',
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

            require_once( BETP2P_DIR_PATH . 'views/backend/meta-boxes/bet-p2p-match-metabox.php' );

        }

        /**
         * Stores and updates match metadata in custom metadata table
         *
         * @param [type] $post_id
         * @param [type] $post
         * @return void
         */
        public function save_post( $post_id, $post ) {

            // verify nonce
            if ( isset( $_POST['betp2p_match_nonce'] ) ) {
                if ( ! wp_verify_nonce( $_POST['betp2p_match_nonce'], 'betp2p_match_nonce' ) ) {
                    return;
                }
            }

            // save data only if the update button is pressed and the browser has not been closed
            if ( defined( DOING_AUTOSAVE ) && DOING_AUTOSAVE ) {
                return;
            }

            // check user capabilities
            if ( isset( $_POST['post_type'] ) && $_POST['post_type'] === 'betp2p-match' ) {
                if ( ! current_user_can( 'edit_page', $post_id ) ) {
                    return;
                } elseif( ! current_user_can( 'edit_post', $post_id ) ) {
                    return;
                }
            }

            if ( isset( $_POST['action']) && $_POST['action'] == 'editpost' ) {

                $sport_key = sanitize_text_field( $_POST['_betp2p_match_sport_key'] );
                $sport_title = sanitize_text_field( $_POST['_betp2p_match_sport_title'] );
                $commence_time = sanitize_text_field( $_POST['_betp2p_match_commence_time'] );
                $home_team = sanitize_text_field( $_POST['_betp2p_match_home_team'] );
                $away_team = sanitize_text_field( $_POST['_betp2p_match_away_team'] );
                $odd_api_match_id = sanitize_text_field( $_POST['_betp2p_odd_api_match_id'] );
                
                global $wpdb;

                // save if metadata table is empty
                if ( $_POST['betp2p_match_action'] == 'save' ) {

                    if ( get_post_type( $post ) == 'betp2p-match' &&
                    $post->post_status != 'trash' &&
                    $post->post_status != 'auto-draft' &&
                    $post->post_status != 'draft' &&
                    $wpdb->get_var(
                        $wpdb->prepare(
                            "SELECT match_id
                            FROM $wpdb->matchesmeta
                            WHERE match_id = %d", 
                            $post_id
                        )
                    ) == null
                    ) {
                        $wpdb->insert(
                            $wpdb->matchesmeta,
                            array(
                                'match_id' => $post_id,
                                'meta_key' => '_betp2p_match_sport_key',
                                'meta_value' => $sport_key                           
                            ),
                            array(
                                '%d', '%s', '%s'
                            )
                        );
                        $wpdb->insert(
                            $wpdb->matchesmeta,
                            array(
                                'match_id' => $post_id,
                                'meta_key' => '_betp2p_match_sport_title',
                                'meta_value' => $sport_title                           
                            ),
                            array(
                                '%d', '%s', '%s'
                            )
                        );
                        $wpdb->insert(
                            $wpdb->matchesmeta,
                            array(
                                'match_id' => $post_id,
                                'meta_key' => '_betp2p_match_commence_time',
                                'meta_value' => $commence_time                            
                            ),
                            array(
                                '%d', '%s', '%s'
                            )
                        );
                        $wpdb->insert(
                            $wpdb->matchesmeta,
                            array(
                                'match_id' => $post_id,
                                'meta_key' => '_betp2p_match_home_team',
                                'meta_value' => $home_team                         
                            ),
                            array(
                                '%d', '%s', '%s'
                            )
                        );
                        $wpdb->insert(
                            $wpdb->matchesmeta,
                            array(
                                'match_id' => $post_id,
                                'meta_key' => '_betp2p_match_away_team',
                                'meta_value' => $away_team                       
                            ),
                            array(
                                '%d', '%s', '%s'
                            )
                        );
                        $wpdb->insert(
                            $wpdb->matchesmeta,
                            array(
                                'match_id' => $post_id,
                                'meta_key' => '_betp2p_odd_api_match_id',
                                'meta_value' => $odd_api_match_id                      
                            ),
                            array(
                                '%d', '%s', '%s'
                            )
                        );                        
                    }
                } else {
                    if ( get_post_type( $post ) == 'betp2p-match' ){
                        $wpdb->update(
                            $wpdb->matchesmeta,
                            array(
                                'meta_value' => $sport_key
                            ),
                            array(
                                'match_id' => $post_id,
                                'meta_key' => '_betp2p_match_sport_key'
                            ),
                            array( '%s' ), // set format
                            array( '%d', '%s' ) // where formats
                        );
                        $wpdb->update(
                            $wpdb->matchesmeta,
                            array(
                                'meta_value' => $sport_title
                            ),
                            array(
                                'match_id' => $post_id,
                                'meta_key' => '_betp2p_match_sport_title'
                            ),
                            array( '%s' ), // set format
                            array( '%d', '%s' ) // where formats
                        );
                        $wpdb->update(
                            $wpdb->matchesmeta,
                            array(
                                'meta_value' => $commence_time 
                            ),
                            array(
                                'match_id' => $post_id,
                                'meta_key' => '_betp2p_match_commence_time'
                            ),
                            array( '%s' ), // set format
                            array( '%d', '%s' ) // where formats
                        );
                        $wpdb->update(
                            $wpdb->matchesmeta,
                            array(
                                'meta_value' => $home_team
                            ),
                            array(
                                'match_id' => $post_id,
                                'meta_key' => '_betp2p_match_home_team'
                            ),
                            array( '%s' ), // set format
                            array( '%d', '%s' ) // where formats
                        );
                        $wpdb->update(
                            $wpdb->matchesmeta,
                            array(
                                'meta_value' =>  $away_team
                            ),
                            array(
                                'match_id' => $post_id,
                                'meta_key' => '_betp2p_match_away_team'
                            ),
                            array( '%s' ), // set format
                            array( '%d', '%s' ) // where formats
                        );
                        $wpdb->update(
                            $wpdb->matchesmeta,
                            array(
                                'meta_value' =>  $odd_api_match_id
                            ),
                            array(
                                'match_id' => $post_id,
                                'meta_key' => '_betp2p_odd_api_match_id'
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

            if ( get_post_type( $post ) == 'betp2p-match' ) {

                global $wpdb;

                $wpdb->delete(
                    $wpdb->matchesmeta,
                    array( 'match_id' => $post_id ),
                    array( '%d' )
                );

            }

        }


    }

}