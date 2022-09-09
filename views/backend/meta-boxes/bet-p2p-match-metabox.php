<?php 

global $wpdb;

$query = $wpdb->prepare(
    "SELECT * FROM $wpdb->matchesmeta
    WHERE match_id = %d",
    $post->ID 
);

$results = $wpdb->get_results( $query, ARRAY_A );

?>

<table class="form-table betp2p-match-metabox">
    <!-- nonce -->
    <input type="hidden" name="betp2p_match_nonce" value="<?php echo wp_create_nonce( "betp2p_match_nonce" ); ?>">

    <!-- to check if the dataare stored for the first time -->
    <input 
    type="hidden" 
    name="betp2p_match_action" 
    value="<?php echo ( 
        empty( $results[0]['meta_value'] ) ||
        empty( $results[1]['meta_value'] ) ||
        empty( $results[2]['meta_value'] ) ||
        empty( $results[3]['meta_value'] ) ||
        empty( $results[4]['meta_value'] ) ||
        empty( $results[5]['meta_value'] ) ? 'save' : 'update'
    ); ?>">

    <tr>
        <th>
            <label for="_betp2p_match_sport_key"><?php esc_html_e( 'Sport', 'betp2p' ); ?></label>
        </th>
        <td>
            <select name="_betp2p_match_sport_key" id="_betp2p_match_sport_key">

                <option value="baseball_mlb" <?php if ( isset( $results[0]['meta_value'] ) ) selected( $results[0]['meta_value'], 'baseball_mlb' ); ?>><?php esc_html_e( 'MLB', 'betp2p' ); ?></option>

                <option value="americanfootball_nfl_preseason" <?php if ( isset( $results[0]['meta_value'] ) ) selected( $results[0]['meta_value'], 'americanfootball_nfl_preseason' ); ?>><?php esc_html_e( 'NFL Preseason', 'betp2p' ); ?></option>

                <option value="soccer_brazil_campeonato" <?php if ( isset( $results[0]['meta_value'] ) ) selected( $results[0]['meta_value'], 'soccer_brazil_campeonato' ); ?>><?php esc_html_e( 'Brazil Série A', 'betp2p' ); ?></option>

                <option value="basketball_wnba" <?php if ( isset( $results[0]['meta_value'] ) ) selected( $results[0]['meta_value'], 'basketball_wnba' ); ?>><?php esc_html_e( 'WNBA', 'betp2p' ); ?></option>

            </select>
        </td>
    </tr>

    <tr>
        <th>
            <label for="_betp2p_match_sport_title"><?php esc_html_e( 'Sport Title', 'betp2p' ) ?></label>
        </th>
        <td>
            <input 
                type="text" 
                name="_betp2p_match_sport_title" 
                id="_betp2p_match_sport_title" 
                class="regular-text betp2p-match_sport_title"
                value="<?php echo ( isset( $results[1]['meta_value'] ) ) ? esc_html( $results[1]['meta_value'] ) : ''; ?>"
                required
            >
        </td>
    </tr>

    <tr>
        <th>
            <label for="_betp2p_match_commence_time"><?php esc_html_e( 'Commence Time', 'betp2p' ) ?></label>
        </th>
        <td>
            <input 
                type="text" 
                name="_betp2p_match_commence_time" 
                id="_betp2p_match_commence_time" 
                class="regular-text betp2p-match_commence_time"
                value="<?php echo ( isset( $results[2]['meta_value'] ) ) ? esc_html( $results[2]['meta_value'] ) : ''; ?>"
                required
            >
        </td>
    </tr>
 
    <tr><!-- _betp2p_both_teams_runs_scored-->
        <th>
            <label for="_betp2p_match_home_team"><?php esc_html_e( 'Home Team', 'betp2p' ) ?></label>
        </th>
        <td>
            <input 
                type="text" 
                name="_betp2p_match_home_team" 
                id="_betp2p_match_home_team" 
                class="regular-text betp2p-_betp2p_match_home_team"
                value="<?php echo ( isset( $results[3]['meta_value'] ) ) ? esc_html( $results[3]['meta_value'] ) : ''; ?>"
                required
            >
        </td>
    </tr>

    <tr><!-- _betp2p_selected_teams_runs_scored-->
        <th>
            <label for="_betp2p_match_away_team"><?php esc_html_e( 'Away Team', 'betp2p' ) ?></label>
        </th>
        <td>
            <input 
                type="text" 
                name="_betp2p_match_away_team" 
                id="_betp2p_match_away_team" 
                class="regular-text betp2p-_betp2p_match_away_team"
                value="<?php echo ( isset( $results[4]['meta_value'] ) ) ? esc_html( $results[4]['meta_value'] ) : ''; ?>"
                required
            >
        </td>
    </tr>   
    
    <tr><!-- _betp2p_selected_teams_runs_scored-->
        <th>
            <label for="_betp2p_odd_api_match_id"><?php esc_html_e( 'Odd API Match ID', 'betp2p' ) ?></label>
        </th>
        <td>
            <input 
                type="text" 
                name="_betp2p_odd_api_match_id" 
                id="_betp2p_odd_api_match_id" 
                class="regular-text betp2p-odd_api_match_id"
                value="<?php echo ( isset( $results[5]['meta_value'] ) ) ? esc_html( $results[5]['meta_value'] ) : ''; ?>"
                disabled
            >
        </td>
    </tr>  

</table>