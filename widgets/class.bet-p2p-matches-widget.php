<?php 

class BetP2P_Matches_Widget extends WP_Widget {

    public function __construct() {

        $widget_options = array(
            'description' => __( 'Shows a list of the matches of the day or of the previous matches', 'betp2p' ),

        );

        parent::__construct(
            'betp2p-matches',
            'BetP2P Matches',
            $widget_options
        );

        add_action( 'widgets_init', function() {
            register_widget(
                'BetP2P_Matches_Widget'
            );
        });
        
    }

    /**
     * Display widget instance form in the widgets area
     *
     * @param array $instance
     * @return void
     */
    public function form( $instance ) {

        $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $number = isset( $instance['number'] ) ? (int) $instance['number'] : 999;
        $sports = isset( $instance['sports'] ) ? esc_attr( $instance['sports'] ) : 'all';
        $show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;

        global $wpdb;
        $wpdb->matchesmeta = $wpdb->prefix . 'betp2p_matchesmeta';

        $sports_list = $wpdb->get_results(
            "SELECT DISTINCT meta_value 
            FROM {$wpdb->matchesmeta}
            WHERE meta_key = '_betp2p_match_sport_title'"
        );

        require( BETP2P_DIR_PATH . 'views/backend/widgets/betp2p-matches-widget-form.php' );

    }

    /**
     * Display the html content of the widget in the frontend
     *
     * @param array $args
     * @param array $instance
     * @return void
     */
    public function widget( $args, $instance ) {

        $default_title = 'BetP2P Matches';
        $title = ! empty( $instance['title'] ) ? $instance['title'] : $default_title;
        $number = ! empty( $instance['number'] ) ? $instance['number'] : 999;
        $sports = ! empty( $instance['sports'] ) ? $instance['sports'] : 'all';
        $show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

        echo $args['before_widget'];
        echo $args['before_title'] . $title . $args['after_title'];
        require( BETP2P_DIR_PATH . 'views/frontend/widgets/betp2p-matches-widget.php' );
        echo $args['after_widget'];

    }

    /**
     * Store and update all widget data
     *
     * @param array $new_instance
     * @param array $old_instance
     * @return void
     */
    public function update( $new_instance, $old_instance ) {

        $instance = $old_instance;

        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['number'] = (int) $new_instance['number'];
        $instance['sports'] = sanitize_text_field( $new_instance['sports'] );
        $instance['show_date'] = ! empty( $new_instance['show_date'] ) ? 1 : 0;

        return $instance;

    }

}