$ = jQuery.noConflict();

$(document).ready( function() {

    $("#betp2p-cash-in-form").validate({
        rules: {
            balance: {
                required: true,
                number: true,
                min: 2
            }
        },
        // messages: {
        //     stake_amount: "",
        // }
    });

});



$("#betp2p_payment_method").change( function() {

    paymentMethod = $("#betp2p_payment_method").val();

    switch (paymentMethod) {
        case 'debit-credit':
            paymentMethodSelected = 'Debit/Credit Card';
            $("#betp2p_account_balance").attr( 'disabled', false );
            $("#betp2p_account_balance").focus();

            break;
        case 'paypal':
            paymentMethodSelected = 'PayPal';
            $("#betp2p_account_balance").attr( 'disabled', false );
            $("#betp2p_account_balance").focus();
            break;
        
        default:
            paymentMethodSelected = '';
            break;
    }

    $("#betp2p-payment-method-checkout").val( paymentMethodSelected );

})



$("#betp2p_account_balance").change( function() {

    dollarAmount = $("#betp2p_account_balance").val();

    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();

    today = mm + '/' + dd + '/' + yyyy;

    $("#betp2p-buying-usd-amount").val( dollarAmount );
    $("#betp2p-payment-date-checkout").val( today );
    $("#betp2p-payment-destiny").val( 'Ibetcha Wallet' );

    // alert( dollarAmount );

    if( dollarAmount ) {

        dollarToExchange = $("#betp2p-buying-usd-amount").val();
        coinValue = $("#betp2p-coin-value").val();

        fDollarToExchange = parseFloat( dollarToExchange );
        fCoinValue = parseFloat( coinValue );
        // alert( typeof(coinValue));


        $dollarToCoin = fDollarToExchange * fCoinValue;

        $("#betp2p-buying-coin-exchange").val( $dollarToCoin.toFixed(2) )

        $("#betp2p-dollar-amount-checkout").html( 'US$ ' + dollarAmount )

    }

    
    
})

    
