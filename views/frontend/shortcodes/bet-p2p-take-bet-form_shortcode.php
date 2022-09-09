<div class="bets-page"> 

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
                bm.meta_value AS match_id,
                mode.meta_value AS bet_mode,
                m.meta_value AS commence_time,
                amount.meta_value AS stake_amount,
                p.post_author, 
                p.post_date, 
                p.post_title, 
                hometeam.meta_value AS home_team,
                awayteam.meta_value AS away_team,
                teamselected.meta_value AS maker_team,
                league.meta_value AS league,
                p.post_status, 
                b.meta_key, 
                b.meta_value AS bet_status
            FROM $wpdb->betp2pmeta AS b 
            JOIN $wpdb->posts AS p ON p.ID = b.bet_id
            LEFT JOIN $wpdb->betp2pmeta AS bm ON bm.bet_id = b.bet_id
                AND bm.meta_key = 'match_id'
            LEFT JOIN $wpdb->betp2pmeta AS amount ON amount.bet_id = b.bet_id
                AND amount.meta_key = '_betp2p_stake_amount'
            LEFT JOIN $wpdb->betp2pmeta AS mode ON mode.bet_id = b.bet_id 
                AND mode.meta_key = '_betp2p_vs_mode_option'
            LEFT JOIN $wpdb->betp2pmeta AS teamselected ON teamselected.bet_id = b.bet_id 
                AND teamselected.meta_key = '_betp2p_maker_selected_team'
            JOIN $wpdb->matchesmeta AS m ON m.match_id IN (SELECT DISTINCT(bm.meta_value) FROM $wpdb->betp2pmeta )
            LEFT JOIN $wpdb->matchesmeta AS hometeam ON hometeam.match_id IN (SELECT bm.meta_value FROM $wpdb->betp2pmeta WHERE bm.meta_key = 'match_id' )
                AND hometeam.meta_key = '_betp2p_match_home_team'
            LEFT JOIN $wpdb->matchesmeta AS awayteam ON awayteam.match_id IN (SELECT bm.meta_value FROM $wpdb->betp2pmeta WHERE bm.meta_key = 'match_id' )
                AND awayteam.meta_key = '_betp2p_match_away_team'
            LEFT JOIN $wpdb->matchesmeta AS league ON league.match_id IN (SELECT bm.meta_value FROM $wpdb->betp2pmeta WHERE bm.meta_key = 'match_id' )
                AND league.meta_key = '_betp2p_match_sport_title'
            WHERE b.bet_id = %d
            AND p.post_author != %d
            AND p.post_status  = 'publish'
            AND b.meta_value = 'open'
            AND m.meta_value LIKE '%s'
            GROUP BY b.bet_id
            ORDER BY p.post_date DESC
            ",
            array( 
                $_GET['post'], 
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
        
       var_dump( $bets );

        ?>

        <?php if ( $wpdb->num_rows ) : ?>
            <table id="betp2p-bets-table" cellspacing="0" cellpadding="0">

                <caption><?php esc_html_e( 'Maker Bet', 'betp2p' ); ?></caption>

                <thead>

                    <tr>

                        <th><?php esc_html_e( 'Date', 'betp2p' ); ?></th>
                        <th><?php esc_html_e( 'Bets', 'betp2p' ); ?></th>
                        <th><?php esc_html_e( 'Takers', 'betp2p' ); ?></th>
                        <th><?php esc_html_e( 'Options', 'betp2p' ); ?></th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach( $bets as $bet ) : ?>                        

                        <?php 
                        $maker = get_userdata( $bet->post_author );
                        $commence_time = date( 'd-m-Y h:i:s a', strtotime( $bet->commence_time ) );
                        $current_date = date('d-m-Y h:i:s a');
                        $is_bet_finished = ( $current_date >= $commence_time ) ? true : false;
                        // echo $current_date;
                        ?>

                        <tr class="betp2p-open-bets <?php echo $is_bet_finished == true ?  'disabled' : ''; ?>">

                            <td class="betp2p-bet-meta">
                                
                                <p>
                                    <?php esc_html_e( $commence_time ); ?> | <?php printf( esc_html__( 'Bet ID: %d', 'betp2p'), $bet->bet_id ) ?>
                                </p>
                            </td>

                            <td>

                                <p class="betp2p-bet-title">
                                    <?php esc_html_e( $bet->post_title ); ?>
                                </p>

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

                            </td>

                            <?php $take_bet = add_query_arg( 'post', $bet->bet_id, home_url( '/take-bet' ) ); ?>
                            <td>

                                <a href="<?php echo esc_url( $take_bet ); ?>">
                                    <?php esc_html_e( 'Take Bet', 'betp2p' ); ?>
                                </a>

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

<?php

    if ( ! is_user_logged_in() ) {
        betp2p_register();
        return;
    }

    if ( isset( $_POST['betp2p_nonce'] ) ) {
        if ( ! wp_verify_nonce( $_POST['betp2p_nonce'], 'betp2p_nonce' ) ) {
            return;
        }
    }

    $errors = array();
    $hasError = false;

    if ( isset( $_POST['betp2p_taker_form_submitted'] ) ) {
        $title                                  = $_POST['title'];
        $taker_selected_team                    = $_POST['_betp2p_taker_selected_team'];
        $bet_type                               = $_POST['_betp2p_bet_type'];
        $bet_value                              = $_POST['_betp2p_bet_value'];
        $point_spread_type                      = $_POST['_betp2p_point_spread_type'];
        $run_scored_type                        = $_POST['_betp2p_run_scored_type'];
        $run_scored_by_both_teams               = $_POST['_betp2p_both_teams_runs_scored'];   
        $run_scored_by_selected_team            = $_POST['_betp2p_selected_teams_runs_scored'];
        $run_scored_option                      = $_POST['_betp2p_run_scored_option'];

        $vs_mode_option                         = $_POST['_betp2p_vs_mode_option'];

        $stake_amount                           = $_POST['_betp2p_stake_amount'];
        $rate                                   = $_POST['_betp2p_rate_amount'];
        $subtotal                               = $_POST['_betp2p_subtotal'];
        $share_type                             = $_POST['_betp2p_share_type'];

        if ( trim( $title ) === '' ) {
            $errors[] = esc_html__( 'Please, select a match', 'betp2p' );
            $hasError = true;
        }
        if ( trim( $bet_type ) === '' ) {
            $errors[] = esc_html__( 'Plese, choose a bet type', 'betp2p' );
            $hasError = true;
        }

        if ( $hasError === false ) {
            $bet_data = array(
                'post_type' => 'bet-p2p',
                'post_title' => sanitize_text_field( $title ),
                'post_content' => '',
                'tax_input' => array(
                    'bet-type' => sanitize_text_field( $bet_type )
                ),
                'post_status' => 'publish'
            );
    
            $post_id = wp_insert_post( $bet_data );
    
            global $post;
            BetP2P_Post_Type::take_bet( $post_id, $post );


           // var_dump( $inserted_bet );







            // save bets matched
            // if ( $_POST['betp2p_action'] == 'close-bet-matched' ) {

            //     if ( get_post_type( $post ) == 'bet-p2p' &&
            //     $post->post_status != 'trash' &&
            //     $post->post_status != 'auto-draft' &&
            //     $post->post_status != 'draft' &&
            //     $wpdb->get_var(
            //         $wpdb->prepare(
            //             "SELECT bet_id
            //             FROM $wpdb->betp2pmeta
            //             WHERE bet_id = %d", 
            //             $post_id
            //         )
            //     ) == null
            //     ) {
            //         $wpdb->insert(
            //             $wpdb->betsmatched,
            //             array(
            //                 'maker_bet_id' => $post_id,
            //                 'taker_bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_maker_selected_team',
            //                 'meta_value' => $maker_selected_team                            
            //             ),
            //             array(
            //                 '%d', '%s', '%d'
            //             )
            //         );
            //         $wpdb->insert(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_taker_selected_team',
            //                 'meta_value' => $taker_selected_team
            //             ),
            //             array( '%d', '%s', '%d' )
            //         );
            //         $wpdb->insert(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_bet_type',
            //                 'meta_value' => $bet_type                            
            //             ),
            //             array(
            //                 '%d', '%s', '%s'
            //             )
            //         );
            //         $wpdb->insert(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_bet_value',
            //                 'meta_value' => $bet_value                            
            //             ),
            //             array(
            //                 '%d', '%s', '%d'
            //             )
            //         );
            //         $wpdb->insert(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_point_spread_type',
            //                 'meta_value' => $point_spread_type                          
            //             ),
            //             array(
            //                 '%d', '%s', '%s'
            //             )
            //         );
            //         $wpdb->insert(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_run_scored_type',
            //                 'meta_value' => $run_scored_type
            //             ),
            //             array(
            //                 '%d', '%s', '%s'
            //             )
            //         );
            //         $wpdb->insert(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_both_teams_runs_scored',
            //                 'meta_value' => $both_teams_runs_scored                        
            //             ),
            //             array(
            //                 '%d', '%s', '%d'
            //             )
            //         );                       
            //         $wpdb->insert(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_selected_teams_runs_scored',
            //                 'meta_value' => $selected_teams_runs_scored                      
            //             ),
            //             array(
            //                 '%d', '%s', '%d'
            //             )
            //         );
            //         $wpdb->insert(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_run_scored_option',
            //                 'meta_value' => $run_scored_option                  
            //             ),
            //             array( '%d', '%s', '%s' )
            //         );
            //         $wpdb->insert(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_vs_mode_option',
            //                 'meta_value' => $vs_mode_option
            //             ),
            //             array( '%d', '%s', '%s' )
            //         );                       
            //         $wpdb->insert(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_stake_amount',
            //                 'meta_value' => $stake_amount                
            //             ),
            //             array(
            //                 '%d', '%s', '%f'
            //             )
            //         );
            //         $wpdb->insert(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_rate',
            //                 'meta_value' => $rate             
            //             ),
            //             array(
            //                 '%d', '%s', '%f'
            //             )
            //         );
            //         $wpdb->insert(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_subtotal',
            //                 'meta_value' => $subtotal           
            //             ),
            //             array(
            //                 '%d', '%s', '%f'
            //             )
            //         );
            //         $wpdb->insert(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_share_type',
            //                 'meta_value' => $share_type         
            //             ),
            //             array(
            //                 '%d', '%s', '%s'
            //             )
            //         );
            //         $wpdb->insert(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => 'match_id',
            //                 'meta_value' => $match_id
            //             ),
            //             array( '%d', '%s', '%d' )
            //         );
            //     }
            // } else {
            //     if ( get_post_type( $post ) == 'bet-p2p' ){
            //         $wpdb->update(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'meta_value' => $maker_selected_team
            //             ),
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_maker_selected_team'
            //             ),
            //             array( '%d' ), // set format
            //             array( '%d', '%s' ) // where formats
            //         );
            //         $wpdb->update(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'meta_value' => $taker_selected_team
            //             ),
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_taker_selected_team'
            //             ),
            //             array( '%d' ), // set format
            //             array( '%d', '%s' ) // where formats
            //         );
            //         $wpdb->update(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'meta_value' => $bet_type
            //             ),
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_bet_type'
            //             ),
            //             array( '%s' ), // set format
            //             array( '%d', '%s' ) // where formats
            //         );
            //         $wpdb->update(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'meta_value' => $bet_value
            //             ),
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_bet_value'
            //             ),
            //             array( '%d' ), // set format
            //             array( '%d', '%s' ) // where formats
            //         );
            //         $wpdb->update(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'meta_value' => $point_spread_type
            //             ),
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_point_spread_type'
            //             ),
            //             array( '%s' ), // set format
            //             array( '%d', '%s' ) // where formats
            //         );
            //         $wpdb->update(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'meta_value' => $run_scored_type
            //             ),
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_run_scored_type'
            //             ),
            //             array( '%s' ),
            //             array( '%d', '%s' ),
            //         );  
            //         $wpdb->update(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'meta_value' =>  $both_teams_runs_scored
            //             ),
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_both_teams_runs_scored'
            //             ),
            //             array( '%d' ), // set format
            //             array( '%d', '%s' ) // where formats
            //         );                        
            //         $wpdb->update(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'meta_value' =>  $selected_teams_runs_scored
            //             ),
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_selected_teams_runs_scored'
            //             ),
            //             array( '%d' ), // set format
            //             array( '%d', '%s' ) // where formats
            //         );
            //         $wpdb->update(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'meta_value' =>  $run_scored_option
            //             ),
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_run_scored_option'
            //             ),
            //             array( '%s' ), // set format
            //             array( '%d', '%s' ) // where formats
            //         );
            //         $wpdb->update(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'meta_value' => $vs_mode_option
            //             ),
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_vs_mode_option'
            //             ),
            //             array( '%s' ),
            //             array( '%d', '%s' )
            //         );                        
            //         $wpdb->update(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'meta_value' =>  $stake_amount
            //             ),
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_stake_amount'
            //             ),
            //             array( '%f' ), // set format
            //             array( '%d', '%s' ) // where formats
            //         );
            //         $wpdb->update(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'meta_value' =>  $rate
            //             ),
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_rate'
            //             ),
            //             array( '%f' ), // set format
            //             array( '%d', '%s' ) // where formats
            //         );
            //         $wpdb->update(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'meta_value' =>  $subtotal
            //             ),
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_subtotal'
            //             ),
            //             array( '%f' ), // set format
            //             array( '%d', '%s' ) // where formats
            //         );
            //         $wpdb->update(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'meta_value' =>  $share_type
            //             ),
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => '_betp2p_share_type'
            //             ),
            //             array( '%s' ), // set format
            //             array( '%d', '%s' ) // where formats
            //         );
            //         $wpdb->update(
            //             $wpdb->betp2pmeta,
            //             array(
            //                 'meta_value' => $match_id
            //             ),
            //             array(
            //                 'bet_id' => $post_id,
            //                 'meta_key' => 'match_id'
            //             ),
            //             array( '%d' ),
            //             array( '%d', '%s' )
            //         );
            //     }
            // }












        }
    }


?>

<div class="betp2p-bet">
    <form action="" method="POST" id="betp2p-take-bet-form">

        <?php if ( ! empty( $errors ) ) : ?>
            <div class="alert alert-danger">
                <?php foreach ( $errors as $error ) : ?>
                    <span class="error">
                        <?php echo $error; ?>
                    </span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div id="hero">
            <div class="container">
                <div class="relative">
                    <div class="hero-tagline text-center">
                        <h2><?php esc_html_e( $bet->post_title ); ?></h2>
                        <p><b><?php esc_html_e( $commence_time ); ?></b></p>
                        <input 
                            type="hidden" 
                            name="title" 
                            value="<?php esc_html_e( $bet->post_title ); ?>" 
                        />
                    </div>
                    <div id="general-information" class="banner absolute">    
                        <?php if( isset( $bet->league ) ) : ?>       
                            <p><?php esc_html_e( $bet->league ); ?></p>
                        <?php else : ?>
                            <img src="src/img/lidom.png" alt="Logo LIDOM"/>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <section id="bet-section">
        
                <div class="bet-form-top">
                    <div class="bet-form-top-grid">
                        <div class="team-meta">
                            <div class="teams">
                                <fieldset id="team-selection-fields">
                                    <legend><?php esc_html_e( 'Select Team', 'betp2p' ); ?>:</legend>
                                    <ul id="team-selection" class="teams-wrapper">
                                        <li class="team">
                                            <div class="team-tagline">
                                                <!-- <img src="src/img/teams/estrellas.png" alt="Estrellas Orientales"> -->
                                                <p><strong><?php esc_html_e( $bet->away_team ); ?></strong></p>
                                                <?php if( $bet->maker_team == '1' ) : ?>
                                                    <p class="betp2p-team-selected-info">
                                                        <?php printf(
                                                            esc_html__( 'Selected by Maker %s', 'betp2p' ),
                                                            $maker->display_name
                                                        ); ?>
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                            <input 
                                                type="radio" 
                                                name="_betp2p_taker_selected_team"
                                                class="betp2p_taker_selected_team" 
                                                value="1"

                                                <?php
                                                checked( '2', $bet->maker_team );
                                                ?>

                                                <?php 
                                                disabled( '1', $bet->maker_team );
                                                ?>
                                            >
                                        </li>
                                        <li class="team">
                                            <div class="team-tagline">
                                                <!-- <img src="src/img/teams/aguilas.png" alt="Aguilas Cibao"> -->
                                                <p><strong><?php esc_html_e( $bet->home_team ) ?></strong></p>
                                                <?php if( $bet->maker_team == '2' ) : ?>
                                                    <p class="betp2p-team-selected-info">
                                                        <?php printf(
                                                            esc_html__( 'Selected by Maker %s', 'betp2p' ),
                                                            $maker->display_name
                                                        ); ?>
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                            <input 
                                                type="radio" 
                                                name="_betp2p_taker_selected_team"
                                                class="betp2p_taker_selected_team" 
                                                value="2"

                                                <?php
                                                checked( '1', $bet->maker_team );
                                                ?>

                                                <?php
                                                disabled( '2', $bet->maker_team );
                                                ?>
                                            >
                                        </li>
                                    </ul>                
                                </fieldset>                
                            </div><!--.teams-->
                            <hr>
                            <div id="bet" class="bet">
                                <fieldset id="type-of-bet">
                                    <ul id="bet-type">
                                        <li>
                                            <input 
                                            type="radio" 
                                            id="moneyline" 
                                            name="_betp2p_bet_type" 
                                            value="moneyline">
                                            <label for="moneyline"><?php esc_html_e( 'Straight Up Win (Moneyline)', 'betp2p' ); ?></label>
                                        </li>
                                        <li>
                                            <div class="flex space-between">
                                                <div class="input-group">
                                                    <input 
                                                    type="radio" 
                                                    id="point-spread" name="_betp2p_bet_type" 
                                                    value="point-spread">
                                                    <label for="point-spread"><?php esc_html_e( 'Point Spread', 'betp2p' ); ?></label>
                                                </div>    
                                                <input 
                                                    name="_betp2p_bet_value" 
                                                    id="_betp2p_bet_value" 
                                                    type="number"
                                                    step="1"
                                                    min="1"
                                                    size="3"
                                                    value=""
                                                >                          
                                            </div>  
                                            <ul class="point-type-wrapper">
                                                <li>
                                                    <input type="radio" id="point_spread_minus" name="_betp2p_point_spread_type" value="minus" >
                                                    <label for="point_spread_minus"><?php esc_html_e( 'Minus', 'betp2p' ); ?></label>
                                                </li>
                                                <li>
                                                    <input type="radio" id="point_spread_plus" name="_betp2p_point_spread_type" value="plus">
                                                    <label for="point_spread_plus"><?php esc_html_e( 'Plus', 'betp2p' ); ?></label>
                                                </li>
                                                <li>
                                                    <input type="radio" id="point_spread_exactly" name="_betp2p_point_spread_type" value="exactly">
                                                    <label for="point_spread_exactly"><?php esc_html_e( 'Exactly', 'betp2p' ); ?></label>
                                                </li>
                                            </ul>                                          
                                        </li>
                                    
                                    </ul>
                                </fieldset>
                            </div><!--.bet-->
                            <hr>
                            <div id="runs">
                                <fieldset id="run-scored">
                                    <ul id="runs-wrapper">
                                        <li class="run">
                                            <div class="flex space-between">
                                                <div class="input-group">
                                                    <input type="radio" id="both-teams-runs" name="_betp2p_run_scored_type" value="both-team">
                                                    <label for="both-teams-runs">Run Scored <small>(for both Teams)</small></label>
                                                </div>
                                                <input 
                                                    name="_betp2p_both_teams_runs_scored" 
                                                    id="both-teams-points"             
                                                    type="number"
                                                    step="1"
                                                    min="0"
                                                    size="3"
                                                    value="<?php if( isset( $run_scored_by_both_teams ) ) echo $run_scored_by_both_teams; ?>"
                                                >
                                            </div>
                                            
                                        </li>

                                        <li class="run">
                                            <div class="flex space-between">
                                                <div class="input-group">
                                                    <input type="radio" id="selected-teams-runs" name="_betp2p_run_scored_type" value="selected-team">
                                                    <label for="selected-teams-runs">Run Scored <small>(for selected Teams)</small></label>
                                                </div>
                                                <input 
                                                    name="_betp2p_selected_teams_runs_scored" 
                                                    id="selected-teams-points"
                                                    type="number"
                                                    step="1"
                                                    min="0"
                                                    size="3"
                                                    value="<?php if( isset( $run_scored_by_selected_team ) ) echo $run_scored_by_selected_team; ?>"
                                                >
                                            </div>
                                            <ul class="selected-teams-point-wrapper">
                                                <li>
                                                    <input type="radio" id="points-over"  name="_betp2p_run_scored_option" value="points-over">
                                                    <label for="points-over"><?php esc_html_e( 'Over', 'betp2p' ); ?></label>
                                                </li>
                                                <li>
                                                    <input type="radio" id="points-under"  name="_betp2p_run_scored_option" value="points-under">
                                                    <label for="points-under"><?php esc_html_e( 'Under', 'betp2p' ); ?></label>
                                                </li>
                                                <li>
                                                    <input type="radio" id="points-exactly"  name="_betp2p_run_scored_option" value="points-exactly">
                                                    <label for="points-exactly"><?php esc_html_e( 'Exactly', 'betp2p' ); ?></label>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </fieldset>
                            </div><!--.bet-->
                        </div>
                        <div class="score-section">                           
                            <div class="score-tagline">
                                <table>
                                    <thead>
                                        <th></th>
                                        <th>1</th>
                                        <th>2</th>
                                        <th>3</th>
                                        <th>4</th>
                                        <th>5</th>
                                        <th>6</th>
                                        <th>7</th>
                                        <th>8</th>
                                        <th>9</th>
                                        <th>10</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th class="score-runs">R</th>
                                        <th class="score-hits">H</th>
                                        <th class="score-errors">E</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><img src="src/img/teams/estrellas.png" alt="Estrellas Orientales"></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="score-runs">0</td>
                                            <td class="score-hits">0</td>
                                            <td class="score-errors">0</td>
                                        </tr>
                                        <tr>
                                            <td><img src="src/img/teams/aguilas.png" alt="Aguilas Cibao"></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="score-runs">0</td>
                                            <td class="score-hits">0</td>
                                            <td class="score-errors">0</td>
                                        </tr>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="bet-form-middle">
                        <div class="bet-form-middle-flex">
                            <div class="bet-form-vs">
                                <select name="_betp2p_vs_mode_option" id="betp2p_vs_mode_option" disabled>
                                    <option value="1-vs-1" <?php if( isset( $vs_mode_option ) ) selected( $vs_mode_option, '1-vs-1' ); ?>><?php esc_html_e( '1 vs 1', 'betp2p' ); ?></option>
                                    <option value="team-vs-team-multiple-betors" <?php if( isset( $vs_mode_option ) ) selected( $vs_mode_option, 'team-vs-team-multiple-betors'); ?>><?php esc_html_e( 'Team vs Team (multiple betors)', 'betp2p' ); ?></option>
                                </select>
                            </div>
                            <div class="bet-form-datetime">
                                <input type="checkbox" id="bet-datetime" name="bet-datetime" >
                                <label for="bet-datetime">Specific Date & Time</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bet-form-checkout">
                    <table style="background-color: #ffffff;">
                        <thead>
                            <th>Bet Details</th>
                            <th>Balance</th>
                            <th>Disclosure</th>
                        </thead>
                        <tbody>
                            <td>
                                <tr>
                                    <td style="padding: 2rem;">
                                        <input 
                                            id="stake_amount"
                                            name="_betp2p_stake_amount"
                                            class="block"
                                            type="number"
                                            step="1"
                                            size="3"
                                            value="<?php if( isset( $stake_amount ) ) echo $stake_amount; ?>"                              />
                                        <label for="stake_amount"><?php esc_html_e( 'Place Stake Amount', 'betp2p' ); ?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 2rem;">
                                        <input 
                                            type="text" 
                                            name="_betp2p_rate_amount"
                                            id="betp2p_rate_amount" 
                                            class="block"
                                            disabled
                                        >
                                        <label for="_betp2p_rate_amount"><?php esc_html_e( 'Rate', 'betp2p' ); ?></label>
                                    </td>
                                </tr style="display:block;">
                                <tr>
                                    <td style="padding: 2rem;">
                                        <input 
                                            type="text" 
                                            name="_betp2p_subtotal"
                                            id="betp2p_bet_subtotal" 
                                            class="block"
                                            disabled
                                        >
                                        <label for="_betp2p_subtotal"><?php esc_html_e( 'Subtotal', 'betp2p' ); ?></label>
                                    </td>
                                </tr>
                            </td>
                            <td>
                                <div></div>
                            </td>
                            <td>
                                <span>
                                <!-- If bet is not accepted by takers, the funds will be returned to maker within 24 hours since the game started.

                                If balance does not reach the amount you want to bet, or such amount is not sufficient, a refill can take place, by clicking the icons in the middle.  

                                Once the bet is accepted by taker the bet cannot be edited or cancelled.

                                Winning bets will be deposit to i-betU Account or Affiliate Bookie Account accordingly within 24hours since the official result has been delivered.

                                When sports event has been Canceled the money will be returned to the account where the bet fund was originated (i-betU Account, Affiliate Bookie Account or Bitcoin Account).  

                                Postponed or rescheduled games will remain in full effect until the date.  -->

                                </span>
                            </td>
                        </tbody>
                    </table>
                </div>          

                <div style="margin: 4rem; text-align:center;">
                    <input type="hidden" name="_betp2p_share_type" value="<?php echo ($bet->bet_mode == '1-vs-1') ? 'closed' : 'open' ?>">
                    <input type="hidden" name="match_id" value="<?php esc_html_e( $bet->match_id ); ?>" />
                    <input type="hidden" name="maker_id" value="<?php esc_html_e( $bet->post_author ); ?>" />
                    <input type="hidden" name="taker_id" value="<?php esc_html_e( $current_user->ID ) ?>" />
                    <input type="hidden" name="betp2p_take_action" value="close-bet-matched" />
                    <input type="hidden" name="take-action" value="closebet" />
                    <input type="hidden" name="betp2p_taker_nonce" value="<?php echo wp_create_nonce( 'betp2p_taker_nonce' ); ?>" />
                    <input type="hidden" name="betp2p_taker_form_submitted" id="betp2p_taker_form_submitted" value="true" />
                    <input class="btn btn-primary" type="submit" name="submit_betp2p_bet_form" value="<?php esc_attr_e( 'Take Bet', 'betp2p' ); ?>" />
                    <a href="<?php echo esc_url( home_url( '/bets' ) ); ?>" class="btn btn-secondary" ><?php esc_html_e( 'Go Back', 'betp2p' ); ?></a>
                </div>
        </section>

    </form>     
</div>

<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>



