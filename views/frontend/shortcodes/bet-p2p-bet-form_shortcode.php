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

    if ( isset( $_POST['betp2p_form_submitted'] ) ) {
        $title                                  = $_POST['title'];
        $maker_selected_team                    = $_POST['_betp2p_maker_selected_team'];
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
            BetP2P_Post_Type::save_post( $post_id, $post );
        }
    }

    # METHOD 1 :

    // global $post;
    // $post = get_post( $ID, OBJECT );
    // setup_postdata( $post );

    // $post->post_type = 'bet-p2p';

    // wp_reset_postdata();

    # METHOD 2 :

    $post = get_post( get_the_ID() );
    $post->post_type = 'bet-p2p';
    
    // var_dump($post);

    global $wpdb;

    $query = $wpdb->prepare(
        "SELECT * FROM $wpdb->matchesmeta
        WHERE match_id = %d",
        get_the_ID()
    );

    $results = $wpdb->get_results( $query, ARRAY_A );

    // var_dump($results);

?>

<div class="betp2p-bet">
    <form action="" method="POST" id="betp2p-bet-form">

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
                        <h2><?php the_title(); ?></h2>
                        <p><b><?php esc_html_e( $results[2]['meta_value'] ); ?></b></p>
                        <input 
                            type="hidden" 
                            name="title" 
                            value="<?php esc_html_e( $results[4]['meta_value'] . ' @ ' . $results[3]['meta_value'] ); ?>" 
                        />
                    </div>
                    <div id="general-information" class="banner absolute">                        
                        <img src="src/img/lidom.png" alt="Logo LIDOM"/>
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
                                                <p><strong><?php esc_html_e( $results[4]['meta_value'] ); ?></strong></p>
                                            </div>
                                            <input 
                                                type="radio" 
                                                name="_betp2p_maker_selected_team"
                                                class="betp2p_maker_selected_team" 
                                                value="1"
                                            >
                                        </li>
                                        <li class="team">
                                            <div class="team-tagline">
                                                <!-- <img src="src/img/teams/aguilas.png" alt="Aguilas Cibao"> -->
                                                <p><strong><?php esc_html_e( $results[3]['meta_value'] ) ?></strong></p>
                                            </div>
                                            <input 
                                                type="radio" 
                                                name="_betp2p_maker_selected_team"
                                                class="betp2p_maker_selected_team" 
                                                value="2"
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
                                            <input type="radio" id="moneyline" name="_betp2p_bet_type" value="moneyline">
                                            <label for="moneyline"><?php esc_html_e( 'Straight Up Win (Moneyline)', 'betp2p' ); ?></label>
                                        </li>
                                        <li>
                                            <div class="flex space-between">
                                                <div class="input-group">
                                                    <input type="radio" id="point-spread" name="_betp2p_bet_type" value="point-spread">
                                                    <label for="point-spread"><?php esc_html_e( 'Point Spread', 'betp2p' ); ?></label>
                                                </div>    
                                                <input 
                                                    name="_betp2p_bet_value" 
                                                    id="_betp2p_bet_value" 
                                                    type="number"
                                                    step="1"
                                                    min="1"
                                                    size="3"
                                                    value="<?php if( isset( $bet_value ) ) echo $bet_value; ?>"
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
                                <label for="betp2p_vs_mode_option"><?php esc_html_e( 'Share type', 'betp2p' ); ?></label>
                                <select name="_betp2p_vs_mode_option" id="betp2p_vs_mode_option">
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
                    <input type="hidden" name="_betp2p_share_type" value="open">
                    <input type="hidden" name="match_id" value="<?php esc_html_e( $results[0]['match_id'] ); ?>" />
                    <input type="hidden" name="betp2p_action" value="save" />
                    <input type="hidden" name="action" value="editpost" />
                    <input type="hidden" name="betp2p_nonce" value="<?php echo wp_create_nonce( 'betp2p_nonce' ); ?>" />
                    <input type="hidden" name="betp2p_form_submitted" id="betp2p_form_submitted" value="true" />
                    <input class="btn btn-primary" type="submit" name="submit_betp2p_bet_form" value="<?php esc_attr_e( 'Make Bet', 'betp2p' ); ?>" />
                    <a href="<?php echo esc_url( home_url( '/matches' ) ); ?>" class="btn btn-secondary" ><?php esc_html_e( 'Go Back', 'betp2p' ); ?></a>
                </div>
        </section>

    </form>     
</div>




<div class="user-bets-list">
        <?php 

        global $current_user;
        global $wpdb;
        $q = $wpdb->prepare(
            "SELECT ID, post_author, post_date, post_title, post_status, meta_key, meta_value
            FROM $wpdb->posts AS p
            INNER JOIN $wpdb->betp2pmeta AS bm
            ON p.ID = bm.bet_id
            WHERE p.post_author = %d
            AND bm.meta_key = '_betp2p_share_type'
            AND p.post_status IN ( 'publish', 'pending' )
            ORDER BY p.post_date DESC
            ",
            $current_user->ID
        );

        $results = $wpdb->get_results( $q );

        // var_dump($results);

        ?>
        <?php if ( $wpdb->num_rows ) : ?>

            <aside>
                <table>
                    <caption><?php esc_html_e( 'These are the last bets you have created', 'betp2p' ); ?></caption>
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Date', 'betp2p' ); ?></th>
                            <th><?php esc_html_e( 'Bet ID', 'betp2p' ); ?></th>
                            <th><?php esc_html_e( 'Match', 'betp2p' ); ?></th>
                            <th><?php esc_html_e( 'Type', 'betp2p' ); ?></th>
                            <th><?php esc_html_e( 'Edit?', 'betp2p' ); ?></th>
                            <th><?php esc_html_e( 'Delete?', 'betp2p' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach( $results as $result ) : ?>

                            <tr>
                                <td><?php echo esc_html( date( 'd-m-Y', strtotime( $result->post_date ) ) ) ; ?></td>
                                <td><?php echo esc_html( $result->ID ); ?></td>
                                <td><?php echo esc_html( $result->post_title ); ?></td>
                                <td><?php echo esc_html( $result->meta_value == 'open' ? esc_html__( 'Open', 'betp2p' ) : esc_html__( 'Closed', 'betp2p' ) ); ?></td>
                                <td>Edit</td>
                                <td>
                                    <a 
                                    onclick="return confirm( 'Are you sure you want to delete bet: <?php echo $result->post_title ?>?' )"
                                    href="<?php echo get_delete_post_link( $result->ID, "", true ); ?>"
                                    >
                                        <?php esc_html_e( 'Delete', 'betp2p' ); ?>
                                    </a>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </aside>

        <?php endif; ?>
    </div>

<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>