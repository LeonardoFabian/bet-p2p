<?php 

if ( ! class_exists( 'BetP2P_Team_Post_Type' ) ) {

    class BetP2P_Team_Post_Type {

        public function __construct() {

            add_action( 'init', array( $this, 'create_post_type' ) );

            // taxonomy
            add_action( 'init', array( $this, 'create_taxonomy' ) );
            
        }

        public function create_post_type() {

            register_post_type(
                'betp2p-team',
                array(
                    'label' => esc_html__('Team', 'betp2p'),
                    'description' => esc_html__('Teams', 'betp2p'),
                    'labels' => array(
                        'name' => esc_html__('Teams', 'betp2p'),
                        'singular_name' => esc_html__('Team', 'betp2p'),
                    ),
                    'public' => true,
                    'supports' => array( 'title', 'author', 'thumbnail' ), // set 'page-attributes' if 'hierarchical' => true
                    'rewrite' => array( 'slug' => 'teams' ),
                    'menu_icon' => 'dashicons-flag',
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
                'betp2p-team-league',
                'betp2p-team',
                array(
                    'labels' => array(
                        'name' => __( 'League', 'betp2p' ),
                        'singular_name' => __( 'League', 'betp2p' )
                    ),
                    'hierarchical' => true,
                    'show_in_rest' => true,
                    'public' => true,
                    'show_admin_column' => true
                ),
            );

            register_taxonomy(
                'betp2p-team-category',
                'betp2p-team',
                array(
                    'labels' => array(
                        'name' => __( 'Category', 'betp2p' ),
                        'singular_name' => __( 'Category', 'betp2p' )
                    ),
                    'hierarchical' => false,
                    'show_in_rest' => true,
                    'public' => true,
                    'show_admin_column' => true
                ),
            );

        }      

    }

}