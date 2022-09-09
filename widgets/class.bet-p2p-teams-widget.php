<?php 

class BetP2P_Teams_Widget extends WP_Widget {

    public function __construct() {

        $widget_options = array(
            'description' => __( 'Shows a list of teams with their name, logo, etc.', 'betp2p' ),

        );

        parent::__construct(
            'betp2p-teams',
            'BetP2P Teams',
            $widget_options
        );

        add_action( 'widgets_init', function() {
            register_widget(
                'BetP2P_Teams_Widget'
            );
        });
        
    }

    public function form( $instance ) {

        $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $show_logo = isset( $instance['show_logo'] ) ? (bool) $instance['show_logo'] : false;

        require( BETP2P_DIR_PATH . 'views/backend/widgets/betp2p-teams-widget-form.php' );

    }

    public function widget( $args, $instance ) {

        $default_title = 'BetP2P Teams';
        $title = ! empty( $instance['title'] ) ? $instance['title'] : $default_title;
        $show_logo = isset( $instance['show_logo'] ) ? $instance['show_logo'] : false;

        echo $args['before_widget'];
        echo $args['before_title'] . $title . $args['after_title'];
        require( BETP2P_DIR_PATH . 'views/frontend/widgets/betp2p-teams-widget.php' );
        echo $args['after_widget'];

    }

    public function update( $new_instance, $old_instance ) {

        $instance = $old_instance;

        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['show_logo'] = ! empty( $new_instance['title'] ) ? 1 : 0;

        return $instance;

    }

}