<?php 

global $wpdb;

// esc_sql method
// $query = "SELECT * FROM $wpdb->betp2pmeta
//     WHERE bet_id = '" . esc_sql($post->ID) . "'";

/**
 * PREPARE METHOD
 * 
 * use %d for integer values
 * use %f for float values
 * use %s for string values
 * use %% for porcents
 */
$query = $wpdb->prepare(
    "SELECT * FROM $wpdb->betp2pmeta
    WHERE bet_id = %d",
    $post->ID 
);

$results = $wpdb->get_results( $query, ARRAY_A ); // object, object_k, array_n, array_a

// var_dump( $results );

?>

<table class="form-table betp2p-metabox">
    <!-- nonce -->
    <input type="hidden" name="betp2p_nonce" value="<?php echo wp_create_nonce( "betp2p_nonce" ); ?>">

    <!-- to check if the dataare stored for the first time -->
    <input 
    type="hidden" 
    name="betp2p_action" 
    value="<?php echo ( 
        empty( $results[0]['meta_value'] ) ||
        empty( $results[1]['meta_value'] ) ||
        empty( $results[2]['meta_value'] ) ||
        empty( $results[3]['meta_value'] ) ||
        empty( $results[4]['meta_value'] ) ||
        empty( $results[5]['meta_value'] ) ||
        empty( $results[6]['meta_value'] ) ||
        empty( $results[7]['meta_value'] ) ||
        empty( $results[8]['meta_value'] ) ||
        empty( $results[9]['meta_value'] ) ? 'save' : 'update'
    ); ?>">

    <tr>
        <th>
            <label for="_betp2p_bet_value"><?php esc_html_e( 'Bet Vaue', 'betp2p' ) ?></label>
        </th>
        <td>
            <input 
                type="text" 
                name="_betp2p_bet_value" 
                id="betp2p_bet_value" 
                class="regular-text betp2p-bet-value"
                value="<?php echo ( isset( $results[0]['meta_value'] ) ) ? esc_html( $results[0]['meta_value'] ) : ''; ?>"
                disabled
            >
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <fieldset>
                <legend class="screen-reader-text"><span><?php esc_html_e( 'Point Spread type minus', 'betp2p' ); ?></span></legend>
                <label title='<?php esc_html_e( 'Minus', 'betp2p' ); ?>'>
                    <input type="radio" name="_betp2p_point_spread_type" value="minus" 
                    <?php 
                        if ( isset( $results[1]['meta_value'] ) ) {
                            checked( 'minus', $results[1]['meta_value'] );
                        }
                    ?>
                    />
                    <span><?php esc_attr_e( 'Minus', 'betp2p' ); ?></span>
                </label>
            </fieldset>  
            <fieldset>
                <legend class="screen-reader-text"><span><?php esc_html_e( 'Point Spread type plus', 'betp2p' ); ?></span></legend>
                <label title='<?php esc_html_e( 'Plus', 'betp2p' ); ?>'>
                    <input type="radio" name="_betp2p_point_spread_type" value="plus" 
                    <?php 
                        if ( isset( $results[1]['meta_value'] ) ) {
                            checked( 'plus', $results[1]['meta_value'] );
                        }
                    ?>
                    />
                    <span><?php esc_attr_e( 'Plus', 'betp2p' ); ?></span>
                </label>
            </fieldset>  
            <fieldset>
                <legend class="screen-reader-text"><span><?php esc_html_e( 'Point Spread type exactly', 'betp2p' ); ?></span></legend>
                <label title='<?php esc_html_e( 'Exactly', 'betp2p' ); ?>'>
                    <input type="radio" name="_betp2p_point_spread_type" value="exactly" 
                    <?php 
                        if ( isset( $results[1]['meta_value'] ) ) {
                            checked( 'exactly', $results[1]['meta_value'] );
                        }
                    ?>
                    />
                    <span><?php esc_attr_e( 'Exactly', 'betp2p' ); ?></span>
                </label>
            </fieldset>  
        </td>     
    </tr>
    <tr><!-- _betp2p_both_teams_runs_scored-->
        <th>
            <label for="_betp2p_both_teams_runs_scored"><?php esc_html_e( 'Runs Scored (Both Teams)', 'betp2p' ) ?></label>
        </th>
        <td>
            <input 
                type="text" 
                name="_betp2p_both_teams_runs_scored" 
                id="_betp2p_both_teams_runs_scored" 
                class="regular-text betp2p-runs-scored"
                value="<?php echo ( isset( $results[2]['meta_value'] ) ) ? esc_html( $results[2]['meta_value'] ) : ''; ?>"
                required
            >
        </td>
    </tr>  
    <tr><!-- _betp2p_selected_teams_runs_scored-->
        <th>
            <label for="_betp2p_selected_teams_runs_scored"><?php esc_html_e( 'Runs Scored (Selected Teams)', 'betp2p' ) ?></label>
        </th>
        <td>
            <input 
                type="text" 
                name="_betp2p_selected_teams_runs_scored" 
                id="_betp2p_selected_teams_runs_scored" 
                class="regular-text betp2p-runs-scored"
                value="<?php echo ( isset( $results[4]['meta_value'] ) ) ? esc_html( $results[4]['meta_value'] ) : ''; ?>"
                required
            >
        </td>
    </tr>
    <tr><!-- _betp2p_run_scored_option-->
        <td></td>
        <td>
            <fieldset>
                <legend class="screen-reader-text"><span><?php esc_html_e( 'Run scored option', 'betp2p' ); ?></span></legend>
                <label title='<?php esc_html_e( 'Over', 'betp2p' ); ?>'>
                    <input type="radio" name="_betp2p_run_scored_option" value="over" 
                    <?php 
                        if ( isset( $results[5]['meta_value'] ) ) {
                            checked( 'over', $results[5]['meta_value'] );
                        }
                    ?>
                    />
                    <span><?php esc_attr_e( 'Over', 'betp2p' ); ?></span>
                </label>
           
                <label title='<?php esc_html_e( 'Under', 'betp2p' ); ?>'>
                    <input type="radio" name="_betp2p_run_scored_option" value="under" 
                    <?php 
                        if ( isset( $results[5]['meta_value'] ) ) {
                            checked( 'under', $results[5]['meta_value'] );
                        }
                    ?>
                    />
                    <span><?php esc_attr_e( 'Under', 'betp2p' ); ?></span>
                </label>           
               
                <label title='<?php esc_html_e( 'Exactly', 'betp2p' ); ?>'>
                    <input type="radio" name="_betp2p_run_scored_option" value="exactly" 
                    <?php 
                        if ( isset( $results[5]['meta_value'] ) ) {
                            checked( 'exactly', $results[5]['meta_value'] );
                        }
                    ?>
                    />
                    <span><?php esc_attr_e( 'Exactly', 'betp2p' ); ?></span>
                </label>
            </fieldset>   
        </td>
    </tr>
    <tr>
        <th>
            <label for="_betp2p_stake_amount"><?php esc_html_e( 'Stake Amount', 'betp2p' ) ?></label>
        </th>
        <td>
            <input 
                type="text" 
                name="_betp2p_stake_amount" 
                id="_betp2p_stake_amount" 
                class="regular-text betp2p-stake-amount"
                value="<?php echo ( isset( $results[6]['meta_value'] ) ) ? esc_html( $results[6]['meta_value'] ) : ''; ?>"
                required
            >
        </td>
    </tr>
    <tr>
        <th>
            <label for="_betp2p_rate"><?php esc_html_e( 'Rate', 'betp2p' ) ?></label>
        </th>
        <td>
            <input 
                type="text" 
                name="_betp2p_rate" 
                id="_betp2p_rate" 
                class="regular-text betp2p-rate"
                value="<?php echo ( isset( $results[7]['meta_value'] ) ) ? esc_html( $results[7]['meta_value'] ) : ''; ?>"
                required
            >
        </td>
    </tr>
    <tr>
        <th>
            <label for="_betp2p_subtotal"><?php esc_html_e( 'Subtotal', 'betp2p' ) ?></label>
        </th>
        <td>
            <input 
                type="text" 
                name="_betp2p_subtotal" 
                id="_betp2p_subtotal" 
                class="regular-text betp2p-subtotal"
                value="<?php echo ( isset( $results[8]['meta_value'] ) ) ? esc_html( $results[8]['meta_value'] ) : ''; ?>"
                required
            >
        </td>
    </tr>
    <tr>
        <th>
            <label for="_betp2p_share_type"><?php esc_html_e( 'Share Type', 'betp2p' ); ?></label>
        </th>
        <td>
            <select name="_betp2p_share_type" id="_betp2p_share_type">
                <option value="closed" <?php if ( isset( $results[9]['meta_value'] ) ) selected( $results[9]['meta_value'], 'closed' ); ?>><?php esc_html_e( 'Closed', 'betp2p' ); ?></option>
                <option value="open" <?php if ( isset( $results[9]['meta_value'] ) ) selected( $results[9]['meta_value'], 'open' ); ?>><?php esc_html_e( 'Open', 'betp2p' ); ?></option>
            </select>
        </td>
    </tr>

</table>