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

    <div class="card mb-4 shadow">
        <form action="" method="POST">
            <div class="card-header d-flex justify-content-between">
                <div>
                    <span class="badge rounded-pill bg-light text-dark"><?php esc_html_e( $results[1]['meta_value'] ); ?></span>
                </div>
                <?php if ( $show_date ): ?>
                    <div class="betp2p-match-item-date">
                        <time><?php esc_html_e( $results[2]['meta_value'] ); ?></time>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-body">   
                <div class="betp2p-match-item-away-team">
                    <p class="betp2p-match-team"><strong><?php esc_html_e( $results[4]['meta_value'] ); ?></strong></p>
                    <small><?php esc_html_e( 'Away', 'betp2p' ); ?></small>
                </div>
                <div class="betp2p-match-item-away-team">
                    <p class="betp2p-match-team"><strong><?php esc_html_e( $results[3]['meta_value'] ); ?></strong></p>
                    <small><?php esc_html_e( 'Home', 'betp2p' ); ?></small>
                </div>            
            </div>
            <?php if ( ! $is_match_in_progress ) : ?>    
                <div class="card-footer d-flex justify-content-end bg-secondary">   
                    <a class="btn btn-info" href="<?php echo esc_url( the_permalink() ); ?>">
                        <?php esc_html_e( 'Bet this Match', 'betp2p' ); ?>               
                        <span class="dashicons dashicons-arrow-right-alt"></span>     
                    </a>  
                </div>
            <?php endif; ?>
        </form>
    </div>

    <?php endwhile; wp_reset_postdata(); ?>
<?php endif; ?>