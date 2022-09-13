jQuery(document).ready(function(){

    jQuery("#betp2p-bet-form").validate({
        rules: {
            _betp2p_stake_amount: {
                required: true,
                number: true,
                min: 10
            }
        },
        // messages: {
        //     stake_amount: "",
        // }
    });

    jQuery("#moneyline").click(disablePointSpreadOptions);

    function disablePointSpreadOptions() {
        jQuery("#type-of-bet #_betp2p_bet_value").attr('disabled',true);
        jQuery("#type-of-bet #_betp2p_bet_value").val('');
        jQuery("#type-of-bet #_betp2p_bet_value").css("background-color", "#e2e2e2");

        jQuery("#type-of-bet #point_spread_minus").attr('disabled',true);
        jQuery("#type-of-bet #point_spread_minus").siblings().css('opacity','0.4');
        jQuery("#type-of-bet #point_spread_plus").attr('disabled',true);
        jQuery("#type-of-bet #point_spread_plus").siblings().css('opacity','0.4');
        jQuery("#type-of-bet #point_spread_exactly").attr('disabled',true);
        jQuery("#type-of-bet #point_spread_exactly").siblings().css('opacity','0.4');
    }

    jQuery("#point-spread").click(enablePointSpreadOptions);

    function enablePointSpreadOptions() {
        jQuery("#type-of-bet #_betp2p_bet_value").attr('disabled',false);
        jQuery("#type-of-bet #_betp2p_bet_value").css("background-color", "#e5effd");
        jQuery("#type-of-bet #_betp2p_bet_value").focus();

        jQuery("#type-of-bet #point_spread_minus").attr('disabled',false);
        jQuery("#type-of-bet #point_spread_minus").siblings().css('opacity','1');
        jQuery("#type-of-bet #point_spread_plus").attr('disabled',false);
        jQuery("#type-of-bet #point_spread_plus").siblings().css('opacity','1');
        jQuery("#type-of-bet #point_spread_exactly").attr('disabled',false);
        jQuery("#type-of-bet #point_spread_exactly").siblings().css('opacity','1');
    }

    jQuery("#both-teams-runs").click(selectBothTeamsRunsScored);

    function selectBothTeamsRunsScored() {
        jQuery("#run-scored #both-teams-points").attr('disabled', false);
        jQuery("#run-scored #both-teams-points").css("background-color", "#e5effd");
        jQuery("#run-scored #both-teams-points").focus();       

        jQuery("#run-scored #selected-teams-points").attr('disabled', true);
        jQuery("#run-scored #selected-teams-points").css("background-color", "#e2e2e2");
    }

    jQuery("#selected-teams-runs").click(selectedTeamRunsScored);

    function selectedTeamRunsScored() {
        jQuery("#run-scored #both-teams-points").attr('disabled', true);
        jQuery("#run-scored #both-teams-points").css("background-color","#e2e2e2");


        jQuery("#run-scored #selected-teams-points").attr('disabled', false);
        jQuery("#run-scored #selected-teams-points").css("background-color", "#e5effd");
        jQuery("#run-scored #selected-teams-points").focus();
    }

    /**
     * Checkout validate
     */

    $("#stake_amount").change(function() {

        stakeAmount = $("#stake_amount").val();
        ratePercent = $("#ibetcha_payment_rate").val();        

        if ( ratePercent ) {
            fStakeAmount = parseFloat( stakeAmount );
            fRatePercent = parseFloat(ratePercent );
            $totalRate = fStakeAmount * fRatePercent
            $subTotal = fStakeAmount + $totalRate

            $("#betp2p_rate_amount").val( $totalRate.toFixed(2) )
            $("#betp2p_bet_subtotal").val( $subTotal.toFixed(2) )
            // alert($totalRate)
        } else {
            $subTotal = parseFloat( stakeAmount );
            $("#betp2p_bet_subtotal").val( $subTotal.toFixed(2) )

            alert( $subTotal )
        }      
        

        
    })
});