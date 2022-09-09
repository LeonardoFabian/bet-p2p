jQuery(document).ready(function(){

    jQuery("#betp2p_registration_form").validate({
        rules: {
            _betp2p_user_document_id: {
                
                number: true,
                minlength: 11
            },
            _betp2p_user_login: {
                
                minlength: 6
            },
            _betp2p_user_email: {
                
                email: true
            },
            _betp2p_user_first_name: {
                
                number: false,
                minlength: 2
            },
            _betp2p_user_last_name: {
                
                number: false,
                minlength: 2
            },
            _betp2p_user_password: {
                
                minlength: 8
            }
        },
        // messages: {
        //     stake_amount: "",
        // }
    });

});