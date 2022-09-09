<div class="wrap">

	<div id="icon-options-general" class="icon32"></div>
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

    <div id="poststuff">

    
        <div id="post-body" class="metabox-holder columns-2">

            <!-- main content -->
            <div id="post-body-content">

                <div class="col-wrap meta-box-sortables ui-sortable">

                    <?php if ( ! isset( $sports ) || empty( $sports ) ) : ?>

                        <div class="postbox">

                            <div class="handlediv" title="Click to toggle"><br></div>
                            <!-- Toggle -->

                            <h2 class="hndle"><span><?php esc_attr_e( 'Get odds from Odds API', 'betp2p' ); ?></span>
                            </h2>

                            <div class="inside">
                                <form method="post" action="">

                                    <!-- hidden value to check if form is submitted -->
                                    <input type="hidden" name="betp2p-odds-api-form-submitted" value="yes" >

                                    <table class="form-table">

                                        <tr valign="top">
                                            <td scope="row">
                                                <label for="_betp2p_odds_api_sport_parameter">
                                                    <?php esc_attr_e( 'Sport parameter'); ?>
                                                </label>                                          
                                            </td>
                                            <td>
                                                <select name="_betp2p_odds_api_sport_parameter" id="_betp2p_odds_api_sport_parameter">

                                                    <option value="upcoming" <?php if ( isset($sports)) selected( $sports, 'upcoming'); ?>><?php esc_html_e( 'Any live games', 'betp2p'); ?></option>

                                                    <option value="basketball_nba" <?php if ( isset($sports)) selected( $sports, 'basketball_nba'); ?>><?php esc_html_e('NBA', 'betp2p'); ?></option>

                                                    <option value="americanfootball_ncaaf" <?php if ( isset($sports)) selected( $sports, 'americanfootball_ncaaf'); ?>><?php esc_html_e('NCAAF', 'betp2p'); ?></option>

                                                    <option value="americanfootball_nfl" <?php if ( isset($sports)) selected( $sports, 'americanfootball_nfl'); ?>><?php esc_html_e('NFL', 'betp2p'); ?></option>

                                                </select>
                                            </td>
                                        </tr>

                                        <tr valign="top">
                                            <td scope="row">
                                                <label for="_betp2p_odds_api_regions_parameter">
                                                    <?php esc_attr_e( 'Regions parameter'); ?>
                                                </label>                                          
                                            </td>
                                            <td>
                                                <select name="_betp2p_odds_api_regions_parameter" id="_betp2p_odds_api_regions_parameter">

                                                    <option value="us" <?php if ( isset($regions)) selected( $regions, 'us'); ?>><?php esc_html_e( 'United States', 'betp2p'); ?></option>

                                                    <option value="uk" <?php if ( isset($regions)) selected( $regions, 'uk'); ?>><?php esc_html_e('United Kingdom', 'betp2p'); ?></option>

                                                    <option value="au" <?php if ( isset($regions)) selected( $regions, 'au'); ?>><?php esc_html_e('Australia', 'betp2p'); ?></option>

                                                    <option value="eu" <?php if ( isset($regions)) selected( $regions, 'eu'); ?>><?php esc_html_e('Europe', 'betp2p'); ?></option>

                                                </select>
                                            </td>
                                        </tr>
                                        
                                        <tr valign="top">
                                            <td scope="row">
                                                <label for="_betp2p_odds_api_apikey">
                                                    <?php esc_attr_e( 'API Key', 'betp2p' ); ?>
                                                </label>
                                            </td>
                                            <td>
                                                <input 
                                                    name="_betp2p_odds_api_apikey"
                                                    id="_betp2p_odds_api_apikey"
                                                    type="text" 
                                                    value="<?php echo $apikey; ?>" 
                                                    class="regular-text" 
                                                /><br>
                                            </td>
                                        </tr>       
                                    </table>
                                    <p>
                                        <input 
                                            class="button-primary" 
                                            type="submit" 
                                            name="betp2p_odds_api_form_submit" 
                                            value="<?php esc_attr_e( 'Get Odds', 'betp2p' ); ?>" 
                                        />     
                                    </p>
                                </form>

                            </div>
                            <!-- .inside -->

                        </div> <!-- .postbox -->

                    <?php else : ?>

                        <div class="postbox">

                            <div class="handlediv" title="Click to toggle"><br></div>
                            <!-- Toggle -->

                            <h2 class="hndle"><span><?php esc_attr_e( 'Odds API today matches', 'betp2p' ); ?></span>
                            </h2>

                            <div class="inside odds-api-today-odds-table-wrap">
                            
                                <p><?php echo esc_html__( sprintf('Showing %d matches remaining on this day', count($oddsapi_results), 'betp2p' )  ); ?></p>

                                <br class="clear" />
                                
                                <table class="widefat odds-api-today-odds-table">
                                    <thead>
                                        <tr>
                                            <th class="row-title"><?php esc_attr_e( 'Commence Time', 'betp2p' ); ?></th>                 
                                            <th class="row-title"><?php esc_attr_e( 'Sport', 'betp2p' ); ?></th>                                 
                                            <th><?php esc_attr_e( 'Home Team', 'betp2p' ); ?></th>
                                            <th><?php esc_attr_e( 'Away Team', 'betp2p' ); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php for( $i = 0; $i < count($oddsapi_results); $i++ ): ?>
                                            <tr>
                                                <td><?php echo $oddsapi_results[$i]->{'commence_time'}; ?></td>                                                         
                                                <td><?php echo $oddsapi_results[$i]->{'sport_title'}; ?></td>
                                                <td class="row-title"><label for="tablecell"><?php echo $oddsapi_results[$i]->{'home_team'}; ?></label></td>
                                                <td class="row-title"><label for="tablecell"><?php echo $oddsapi_results[$i]->{'away_team'}; ?></label></td>
                                            </tr> 
                                        <?php endfor; ?>                                    
                                    </tbody>
                                    <tfoot>
                                        <!-- _betp2p_odds_api_last_updated -->
                                        <tr>
                                            <th class="row-title"><?php esc_attr_e( 'Latest update', 'betp2p' ); ?></th>
                                            <th><?php echo esc_attr( date('d-m-Y H:i:s', $last_updated) ); ?></th>
                                        </tr>
                                    </tfoot>
                                </table>


                            </div>
                            <!-- .inside -->                            

                        </div> <!-- .postbox -->                        

                    <?php endif; ?>

                  

                </div> <!-- .meta-box-sortables .ui-sortable -->

            </div> <!-- #postbox-content -->

            <!-- sidebar -->
            <div id="postbox-container-1" class="postbox-container">

                <div class="col-wrap meta-box-sortables">

                    <?php if ( isset( $sports ) || ! empty( $sports ) ) : ?>

                        <div class="postbox">

                            <div class="handlediv" title="Click to toggle"><br></div>
                            <!-- Toggle -->

                            <h2 class="hndle"><span><?php esc_attr_e( 'Get odds from Odds API', 'betp2p' ); ?></span>
                            </h2>

                            <div class="inside">
                                <form method="post" action="">

                                    <input type="hidden" name="betp2p-odds-api-form-submitted" value="yes" >

                                    <table class="form-table">                        
                                        
                                        <p><strong><?php esc_html_e( 'Sports', 'betp2p'); ?></strong></p>
                                        <p>
                                            <select name="_betp2p_odds_api_sport_parameter" id="_betp2p_odds_api_sport_parameter" style="width: 65%;">

                                                <option value="upcoming" <?php if ( isset($sports)) selected( $sports, 'upcoming'); ?>><?php esc_html_e( 'Any live games', 'betp2p'); ?></option>

                                                <option value="basketball_nba" <?php if ( isset($sports)) selected( $sports, 'basketball_nba'); ?>><?php esc_html_e('NBA', 'betp2p'); ?></option>

                                                <option value="americanfootball_ncaaf" <?php if ( isset($sports)) selected( $sports, 'americanfootball_ncaaf'); ?>><?php esc_html_e('NCAAF', 'betp2p'); ?></option>

                                                <option value="americanfootball_nfl" <?php if ( isset($sports)) selected( $sports, 'americanfootball_nfl'); ?>><?php esc_html_e('NFL', 'betp2p'); ?></option>

                                            </select>
                                        </p>  
                                        
                                        <p><strong><?php esc_html_e( 'Region', 'betp2p'); ?></strong></p>
                                        <p>
                                            <select name="_betp2p_odds_api_regions_parameter" id="_betp2p_odds_api_regions_parameter" style="width: 65%;">

                                                <option value="us" <?php if ( isset($regions)) selected( $regions, 'us'); ?>><?php esc_html_e( 'United States', 'betp2p'); ?></option>

                                                <option value="uk" <?php if ( isset($regions)) selected( $regions, 'uk'); ?>><?php esc_html_e('United Kingdom', 'betp2p'); ?></option>

                                                <option value="au" <?php if ( isset($regions)) selected( $regions, 'au'); ?>><?php esc_html_e('Australia', 'betp2p'); ?></option>

                                                <option value="eu" <?php if ( isset($regions)) selected( $regions, 'eu'); ?>><?php esc_html_e('Europe', 'betp2p'); ?></option>

                                            </select>
                                        </p>
                                        
                                        <p><strong><?php esc_html_e( 'API Key', 'betp2p'); ?></strong></p>
                                        <p>
                                            <input 
                                                name="_betp2p_odds_api_apikey"
                                                id="_betp2p_odds_api_apikey"
                                                type="text" 
                                                value="<?php echo $apikey; ?>" 
                                                class="regular-text" 
                                                style="max-width: 65%;"
                                            /><br>
                                        </p>
                                        <span>
                                            <?php echo esc_html__( 'Default API Key:', 'betp2p'); ?>
                                            
                                        </span>
                                        <p>
                                            <input                                              
                                                type="text" 
                                                value="0e4e75792dcf23e6594d67f7a00be73d" 
                                                class="regular-text" 
                                                style="max-width: 65%;"
                                                disabled
                                            /><br>
                                        </p>

                                    </table>
                                    <p>
                                        <input 
                                            class="button-primary" 
                                            type="submit" 
                                            name="betp2p_odds_api_form_submit" 
                                            value="<?php esc_attr_e( 'Update Odds', 'betp2p' ); ?>" 
                                        />     
                                    </p>
                                </form>

                            </div>
                            <!-- .inside -->

                        </div>
                        <!-- .postbox -->

                    <?php endif; ?>

                </div>
                <!-- .meta-box-sortables -->

            </div> <!-- #postbox-container-1 -->

        </div> <!-- post-body -->       


    </div> <!-- #poststuff -->

</div> <!-- .wrap -->
