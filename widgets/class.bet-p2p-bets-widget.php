<?php 

class BetP2P_Bets_Widget extends WP_Widget {

    public function __construct() {

        $widget_options = array(
            'description' => __( 'Shows a list of available bets (open or closed)', 'betp2p' ),
        );

        parent::__construct(
            'betp2p-bets',
            'BetP2P Bets',
            $widget_options
        );

        add_action( 'widgets_init', function() {
            register_widget(
                'BetP2P_Bets_Widget'
            );
        });
        
    }

    public function form( $instance ) {

        $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $share_type = isset( $instance['share_type'] ) ? esc_attr( $instance['share_type'] ) : 'open';

        require( BETP2P_DIR_PATH . 'views/backend/widgets/betp2p-bets-widget-form.php' ); 

    }

    public function widget( $args, $instance ) {

        $default_title = 'BetP2P Matches';
        $title = ! empty( $instance['title'] ) ? $instance['title'] : $default_title;
        $share_type = ! empty( $instance['share_type'] ) ? $instance['share_type'] : 'open';

        echo $args['before_widget'];
        echo $args['before_title'] . $title . $args['after_title'];
        require( BETP2P_DIR_PATH . 'views/frontend/widgets/betp2p-bets-widget.php' );
        echo $args['after_widget'];

    }

    public function update( $new_instance, $old_instance ) {

        $instance = $old_instance;

        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['share_type'] = sanitize_text_field( $new_instance['share_type'] );

        return $instance;

    }

}