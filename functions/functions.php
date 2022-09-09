<?php 


function betp2p_register() {

    if ( isset( $_POST['betp2p_register_submitted'] ) ) {

        if ( isset( $_POST['betp2p_register_nonce'] ) ) {
            if ( ! wp_verify_nonce( $_POST['betp2p_register_nonce'], 'betp2p-register-u-m-nonce' ) ) {
                return;
            }
        }

        global $reg_errors;
        $reg_errors = new WP_Error();

        $document_id = sanitize_text_field( $_POST['_betp2p_user_document_id'] );
        $document_id = str_replace( '-', '', $document_id );
        $username = sanitize_user( $_POST['_betp2p_user_login'] );
        $email = sanitize_email( $_POST['_betp2p_user_email'] );
        $firstname = sanitize_text_field( $_POST['_betp2p_user_first_name'] );
        $lastname = sanitize_text_field( $_POST['_betp2p_user_last_name'] );
        $birthday = sanitize_text_field( $_POST['_betp2p_user_birthday'] );
        $gender = sanitize_text_field( $_POST['_betp2p_user_gender'] );
        $password = $_POST['_betp2p_user_password'];
        $password_confirm = $_POST['_betp2p_user_password_confirm'];
        $privacy_acceptance = $_POST['_betp2p_privacy_policy_acceptance'];
        $privacy_acceptance_version = sanitize_text_field( $_POST['_betp2p_privacy_policy_version'] );
        $terms_acceptance = $_POST['_betp2p_terms_and_conditions_acceptance'];
        $terms_acceptance_version = sanitize_text_field( $_POST['_betp2p_terms_and_conditions_version'] );

        if ( empty( $document_id ) ) {
            $reg_errors->add( 'document-empty', esc_html__('The document ID is required', 'betp2p' ) );
        }

        if ( empty( $username ) ) {
            $reg_errors->add( 'username-empty', esc_html__( 'Username is required', 'betp2p' ) );
        }

        if ( strlen( $username ) < 6 ) {
            $reg_errors->add( 'username-length', esc_html__( 'Username is too short. At least 6 characters is required', 'betp2p' ) );
        }

        if ( username_exists( $username ) ) {
            $reg_errors->add( 'username-exists', esc_html__( 'Invalid credentials', 'betp2p' ) );
        }

        if ( ! validate_username( $username ) ) {
            $reg_errors->add( 'invalid-username', esc_html__( 'Invalid credentials', 'betp2p' ) );
        }

        if ( ! is_email( $email ) ) {
            $reg_errors->add( 'invalid-email', esc_html__( 'Invalid email', 'betp2p' ) );
        }

        if ( email_exists( $email ) ) {
            $reg_errors->add( 'email-exists', esc_html__( 'Invalid email', 'betp2p' ) );
        }

        if ( empty( $password ) ) {
            $reg_errors->add( 'password-empty', esc_html__( 'Password is required', 'betp2p' ) );
        }
        
        if ( strlen( $password ) < 8 ) {
            $reg_errors->add( 'password-length', esc_html__( 'Password length must be greater than 8', 'betp2p' ) );
        }

        $password_regex = "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/";

        if ( ! preg_match( $password_regex, $password ) ) {
            $reg_errors->add( 'invalid-password', esc_html__( 'Invalid format, the password must contain a minimum of eight (8) characters, one (1) uppercase letter, one (1) digit and one (1) special character', 'betp2p' ) );
        }

        if ( $password != $password_confirm ) {
            $reg_errors->add( 'password-mismatch', esc_html__( 'The password confirmation does not match', 'betp2p' ) );
        }

        if ( ! isset( $privacy_acceptance ) ) {
            $reg_errors->add( 'unchecked-policy', esc_html__( 'You must accept our privacy policy', 'betp2p' ) );
        }

        if ( ! isset( $terms_acceptance ) ) {
            $reg_errors->add( 'unchecked-terms', esc_html__( 'You must accept our terms and conditions', 'betp2p' ) );
        }

        if ( is_wp_error( $reg_errors ) ) : ?>
            <div class="alert alert-danger">
                <?php foreach( $reg_errors->get_error_messages() as $error ) : ?>
                    <span class="error">
                        <?php echo $error; ?>
                    </span>
                <?php endforeach; ?>
            </div>
        <?php endif;

        if ( count( $reg_errors->get_error_messages() ) < 1 ) {

            $user_data = array(
                'user_login' => $username,
                'first_name' => $firstname,
                'last_name' => $lastname,
                'user_email' => $email,
                'user_pass' => $password,
                'role' => 'contributor'
            );
    
            $user = wp_insert_user( $user_data );

            add_user_meta( $user, '_betp2p_user_document_id', $document_id );
            add_user_meta( $user, '_betp2p_user_birthday', $birthday );
            add_user_meta( $user, '_betp2p_user_gender', $gender );
            add_user_meta( $user, '_betp2p_privacy_policy_acceptance', $privacy_acceptance );
            add_user_meta( $user, '_betp2p_privacy_policy_version', $privacy_acceptance_version );
            add_user_meta( $user, '_betp2p_terms_and_conditions_acceptance', $terms_acceptance );
            add_user_meta( $user, '_betp2p_terms_and_conditions_version', $terms_acceptance_version );

            // wp_new_user_notification( $user );

            wp_login_form();

        }        

    }

    if ( ! isset( $user ) ) {

        require( BETP2P_DIR_PATH . 'views/frontend/betp2p-register.php' );

    }

}

add_filter( 'display_post_states', 'betp2p_add_post_states', 10, 2 );

/**
 * Add post state to the plugin pages
 */
function betp2p_add_post_states( $post_states, $post ) {

    if( $post->post_name == 'take-bet' ) {
        $post_states[] = __( 'Take bet page', 'betp2p' );
    }

    if( $post->post_name == 'submit-bet' ) {
        $post_states[] = __( 'Make bet page', 'betp2p' );
    }

    if( $post->post_name == 'edit-bet' ) {
        $post_states[] = __( 'Bet edit page', 'betp2p' );
    }

    return $post_states;
}


add_action( 'admin_menu', 'betp2p_remove_menu_items' );
/**
 * Remove admin menu items for non-admin users
 */
function betp2p_remove_menu_items() {
    if ( ! current_user_can( 'administrator' ) ) {
        remove_menu_page( 'edit.php' );
        remove_menu_page( 'edit.php?post_type=bet-p2p' );
        remove_menu_page( 'edit.php?post_type=betp2p-match' );
        remove_menu_page( 'edit.php?post_type=betp2p-team' );
        remove_menu_page( 'edit-comments.php' );
    }
}

function betp2p_dashicon( $class ) {
    return '<span class="dashicons '. $class .'"></span>';
}