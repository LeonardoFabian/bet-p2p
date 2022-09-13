<?php get_header(); ?>

    <div class="container-fluid bets-page"> 

        <!-- bets-list -->
        <div class="betp2p-bets-list">

            <?php

            /*
            SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
            */
            
            global $current_user;
            global $wpdb;
            $match_date_filter = substr( date('Y'), 0, 2);

            $q = $wpdb->prepare(
                "SELECT 
                    b.bet_id AS bet_id,         
                    mode.meta_value AS bet_mode,        
                    m.meta_value AS commence_time,
                    amount.meta_value AS stake_amount,
                    p.post_author, 
                    p.post_date, 
                    p.post_title, 
                    p.post_status, 
                    b.meta_key, 
                    b.meta_value AS bet_status
                FROM $wpdb->betp2pmeta AS b 
                JOIN $wpdb->posts AS p ON p.ID = b.bet_id
                LEFT JOIN $wpdb->betp2pmeta AS bm ON bm.bet_id = b.bet_id
                    AND bm.meta_key = 'match_id'
                LEFT JOIN $wpdb->betp2pmeta AS mode ON mode.bet_id = b.bet_id
                    AND mode.meta_key = '_betp2p_vs_mode_option'
                LEFT JOIN $wpdb->betp2pmeta AS amount ON amount.bet_id = b.bet_id
                    AND amount.meta_key = '_betp2p_stake_amount'
                JOIN $wpdb->matchesmeta AS m ON m.match_id IN (SELECT DISTINCT(bm.meta_value) FROM $wpdb->betp2pmeta )
                WHERE bm.meta_key = 'match_id'
                AND p.post_author != %d
                AND p.post_status  = 'publish'
                AND b.meta_value = 'open'
                AND m.meta_value LIKE '%s'
                GROUP BY b.bet_id
                ORDER BY p.post_date DESC
                ",
                array(
                    $current_user->ID,
                    $match_date_filter . '%' 
                )
            );

            // echo $q;

            /*

            SELECT 
                b.bet_id, 
                bm.meta_value AS bet_id, 
                m.match_id, 
                m.meta_value as fecha 

            FROM wp_betp2p_betsmeta b 
            JOIN wp_posts AS p ON p.ID = b.bet_id
            LEFT JOIN wp_betp2p_betsmeta bm ON bm.bet_id = b.bet_id AND bm.meta_key = 'match_id' 

            JOIN wp_betp2p_matchesmeta m ON m.match_id in (select distinct(bm.meta_value) FROM wp_betp2p_betsmeta ) 
            WHERE bm.meta_key = 'match_id' 
            AND m.meta_value LIKE '20%' 
            GROUP BY b.bet_id;

            */

            $bets = $wpdb->get_results( $q );
            
            // var_dump( $bets );

            ?>

            <?php if ( $wpdb->num_rows ) : ?>
                <table id="betp2p-bets-table" cellspacing="0" cellpadding="0">

                    <caption>Bet List</caption>

                    <thead>

                        <tr>

                            <th><?php esc_html_e( 'Date', 'betp2p' ); ?></th>
                            <th><?php esc_html_e( 'Bets', 'betp2p' ); ?></th>
                            <th><?php esc_html_e( 'Status', 'betp2p' ); ?></th>
                            <th><?php esc_html_e( 'Options', 'betp2p' ); ?></th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach( $bets as $key => $bet ) : ?>       
                            
                            <?php 
                            $commence_time = date( 'd-m-Y h:i:s a', strtotime( $bet->commence_time ) );
                            $current_date = date('d-m-Y h:i:s a');
                            $is_match_in_progress = ( $current_date >= $commence_time ) ? true : false;
                            // echo $current_date;
                            ?>

                            <tr class="betp2p-open-bets <?php echo $is_match_in_progress == true ?  'disabled' : ''; ?>">

                                <td class="betp2p-bet-meta">
                                    
                                    <p>
                                        <?php esc_html_e( $commence_time ); ?> | <?php printf( esc_html__( 'Bet ID: %d', 'betp2p'), $bet->bet_id ) ?>
                                    </p>
                                    <div class="betp2p-countdown-<?php echo $key; ?>"></div>

                                    <input class="match-commence-time" type="hidden" value="<?php echo esc_html( $bet->commence_time ); ?>" />

                                </td>

                                <td>

                                    <p class="betp2p-bet-title">
                                        <?php esc_html_e( $bet->post_title ); ?>
                                    </p>

                                    <?php
                                    $maker = get_userdata( $bet->post_author );
                                    ?>
                                    <p>
                                        <?php esc_html_e( $maker->display_name ); ?>
                                    </p>        

                                    <?php 
                                    $amount = number_format( $bet->stake_amount, 2, '.', '' );
                                    ?>
                                    <p>
                                        <?php echo esc_html__( 'Total Amount', 'betp2p' ) . ': ' . $amount;  ?>
                                    </p>               

                                </td>

                                <td>

                                    <p>5</p>
                                    <?php 

                                        if ( $is_match_in_progress) {
                                            esc_html_e( 'In progress', 'betp2p' );
                                        } else {
                                            switch( $bet->post_status ) {
                                                case 'private': 
                                                    $bet->post_status = __( 'Finished', 'betp2p' );
                                                break;
                                                default: 
                                                    $bet->post_status = __( 'Available', 'betp2p' );
                                                break;
                                            }
                                            ?>
                                            <p><?php echo esc_html( $bet->post_status );  ?></p>
                                            <?php
                                        }
                                        
                                    ?>
                                    

                                </td>

                                <?php $take_bet = add_query_arg( 'post', $bet->bet_id, home_url( '/take-bet' ) ); ?>
                                <td>
                                    
                                        <?php if ( ! $is_match_in_progress ) : ?>
                                            <?php if ( $bet->bet_status == 'open' ) : ?>
                                                <a href="<?php echo esc_url( $take_bet ); ?>" class="btn btn-accent">
                                                    <?php esc_html_e( 'Take Bet', 'betp2p' ); ?>
                                                </a>
                                            <?php else: ?>
                                                <?php echo betp2p_dashicon('dashicons-lock'); ?>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php echo betp2p_dashicon( 'dashicons-video-alt' ); ?>
                                        <?php endif; ?>
                                   
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            <?php else: ?>

                <div class="alert alert-info">    
                    <div class="flex items-center">
                        <span class="dashicons dashicons-info-outline"></span>                
                        <span class="info">
                            <?php esc_html_e( 'No bets have been generated by other users, visit the Matches section to see the matches of the day.', 'betp2p' ); ?>
                        </span> 
                    </div>                   
                </div>

            <?php endif; ?>

        </div>

        <!-- bets info -->
        <div class="betp2p-bets-selected">

            <!-- selected bets -->
            <table id="betp2p-bets-selected-table" cellspacing="0" cellpadding="0">

                <caption>Selected Bets</caption>

                <thead>

                    <tr>

                        <th><?php esc_html_e( 'Selections', 'betp2p' ); ?></th>
                        <th><?php esc_html_e( 'Odds', 'betp2p' ); ?></th>
                        <th><?php esc_html_e( 'Options', 'betp2p' ); ?></th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ( have_posts() ) : the_post(); ?>       
                    
                        <tr>
                            <td colspan="3">
                                <p class="betp2p-selected-bet-title">
                                    <?php esc_html_e( get_the_title() ); ?>
                                </p>
                            </td>
                        </tr>

                        <tr>

                            <td class="betp2p-selected-bet-meta">
                                <p>
                                    <?php the_date(); ?> | <?php printf( esc_html__( 'Bet ID: %d', 'betp2p'), get_the_ID() ) ?>
                                </p>
                                <p>
                                    <?php esc_html_e( get_the_author_meta( 'display_name' ) ); ?>
                                </p>
                            </td>

                            <td>
                                <p>
                                    <?php printf( 
                                        esc_html__( 'Total Amount: %f', 'betp2p' ), 1000.00 
                                    ); ?>
                                </p>
                            </td>

                            <td>

                                Button

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

            <!-- selected bets check-out -->
            <table id="betp2p-bets-checkout-table" cellspacing="0" cellpadding="0">

                <caption>Check-Out</caption>

                <thead>

                    <tr>

                        <th><?php esc_html_e( 'Details', 'betp2p' ); ?></th>
                        <th><?php esc_html_e( 'Amount', 'betp2p' ); ?></th>
                        <th><?php esc_html_e( 'Profit', 'betp2p' ); ?></th>

                    </tr>

                </thead>

                <tbody>       
                    

                    <tr>

                        <td class="betp2p-bet-checkout-details">
                            <p>
                                2 Bets
                            </p>
                            
                        </td>

                        <td>
                            <input type="text">
                        </td>
                        

                        <td>

                            US$1000

                        </td>

                    </tr>

                    <tr>
                        <td colspan="3">
                            <button class="btn btn-accent"><?php esc_html_e( 'Place Bets', 'betp2p' ); ?></button>
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>
    </div>   

<?php get_footer(); ?>