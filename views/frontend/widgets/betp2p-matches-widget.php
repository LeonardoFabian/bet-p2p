<?php 

$matches = new WP_Query(
    array(
        'post_type' => 'betp2p-match',
        'posts_per_page' => $number,
        'post_status' => 'publish'
    )
);

?>

<?php if( $matches->have_posts() ) : ?>
    <?php while( $matches->have_posts() ) : $matches->the_post(); ?>

    <!-- metadata -->
    <?php 

    global $wpdb;

    $query = $wpdb->prepare(
        "SELECT * FROM $wpdb->matchesmeta
        WHERE match_id = %d",
        get_the_ID()
    );

    $results = $wpdb->get_results( $query, ARRAY_A );

    // var_dump($results);

    ?>

    <?php 
    $commence_time = date( 'd-m-Y h:i:s a', strtotime( $results[2]['meta_value'] ) );
    $current_date = date( 'd-m-Y h:i:s a' );
    $is_match_in_progress = ( $current_date >= $commence_time ) ? true : false;
    ?>

    <div class="betp2p-match-item">
        <form action="" method="POST">
            <div class="betp2p-match-item-header">
                <div>
                    <span><?php esc_html_e( $results[1]['meta_value'] ); ?></span>
                </div>
                <?php if ( $show_date ): ?>
                    <div class="betp2p-match-item-date">
                        <time><?php esc_html_e( $results[2]['meta_value'] ); ?></time>
                    </div>
                <?php endif; ?>
            </div>
            <div class="betp2p-match-item-content">   
                <div class="betp2p-match-item-away-team">
                    <p class="betp2p-match-team"><strong><?php esc_html_e( $results[4]['meta_value'] ); ?></strong></p>
                    <small><?php esc_html_e( 'Away', 'betp2p' ); ?></small>
                </div>
                <div class="betp2p-match-item-away-team">
                    <p class="betp2p-match-team"><strong><?php esc_html_e( $results[3]['meta_value'] ); ?></strong></p>
                    <small><?php esc_html_e( 'Home', 'betp2p' ); ?></small>
                </div>            
            </div>
            <div class="betp2p-match-item-footer">   
                <?php if ( ! $is_match_in_progress ) : ?>    
                    <a class="betp2p-match-item-submit-bet" href="<?php echo esc_url( the_permalink() ); ?>">
                        <?php esc_html_e( 'Bet this Match', 'betp2p' ); ?>               
                        <span class="dashicons dashicons-arrow-right-alt"></span>     
                    </a>  
                <?php else: ?>

                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php endwhile; wp_reset_postdata(); ?>
<?php endif; ?>