<h3><?php esc_html_e( 'Create your account', 'betp2p' ); ?></h3>

<form id="betp2p_registration_form" class="betp2p_form" action="" method="POST" autocomplete="off">
    <fieldset>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="_betp2p_user_document_id"><?php esc_html_e( 'Document ID', 'betp2p' ); ?><span class="required"><?php esc_html_e( '(Required)', 'betp2p') ?></span></label>
                <input type="text" name="_betp2p_user_document_id" id="_betp2p_user_document_id" class="form-control" required />
            </div>
            <div class="form-group col-md-6">
                <label for="_betp2p_user_login"><?php esc_html_e( 'Username', 'betp2p' ); ?><span class="required"><?php esc_html_e( '(Required)', 'betp2p') ?></span></label>
                <input type="text" name="_betp2p_user_login" id="_betp2p_user_login" class="form-control" required />
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="_betp2p_user_email"><?php esc_html_e( 'Email', 'betp2p' ); ?><span class="required"><?php esc_html_e( '(Required)', 'betp2p') ?></span></label>
                <input type="email" name="_betp2p_user_email" id="_betp2p_user_email" class="form-control" required  />
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">                     
                <label for="_betp2p_user_first_name"><?php esc_html_e( 'First Name', 'betp2p' ); ?></label>
                <input type="text" name="_betp2p_user_first_name" id="_betp2p_user_first_name" class="form-control" required  />                                     
            </div>
            <div class="form-group col-md-6">
                <label for="_betp2p_user_last_name"><?php esc_html_e( 'Last Name', 'betp2p' ); ?></label>
                <input type="text" name="_betp2p_user_last_name" id="_betp2p_user_last_name" class="form-control" required  />
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="_betp2p_user_birthday"><?php esc_html_e( 'Birthday', 'betp2p' ); ?></label>
                <input type="text" name="_betp2p_user_birthday" id="_betp2p_user_birthday" class="form-control"  />
            </div>
            <div class="form-group col-md-6">
                <label for='_betp2p_user_gender'><?php esc_html_e( 'Select a gender:', 'betp2p' ); ?></label>
                <select name="_betp2p_user_gender" class="form-control">
                    <option value="M"><?php esc_html_e( 'Male', 'betp2p' ); ?></option>
                    <option value="F"><?php esc_html_e( 'Female', 'betp2p' ); ?></option>
                    <option value="N/S"><?php esc_html_e( 'Not Specified', 'betp2p' ); ?></option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="_betp2p_user_password"><?php esc_html_e( 'Password', 'betp2p' ); ?><span class="required"><?php esc_html_e( '(Required)', 'betp2p') ?></span></label>
                <input type="password" name="_betp2p_user_password" id="_betp2p_user_password" class="form-control" required  />
            </div>
            <div class="form-group col-md-6">
                <label for="_betp2p_user_password_confirm"><?php esc_html_e( 'Confirm Password', 'betp2p' ); ?></label>
                <input type="password" name="_betp2p_user_password_confirm" id="_betp2p_user_password_confirm" class="form-control" required />
            </div>
        </div>
        <div class="form-group col-md-12">
            <label for="betp2p_privacy_policy_acceptance">
                <input 
                    type="checkbox" 
                    name="_betp2p_privacy_policy_acceptance" 
                    id="betp2p_privacy_policy_acceptance" 
                    value="yes"                                
                />
                <?php
                $privacy_policy_page = get_page_by_path( 'privacy-policy' );
                $privacy_policy_page_title = get_the_title( $privacy_policy_page );
                $privacy_policy_page_url = get_the_permalink( $privacy_policy_page );
                ?>
                <small><?php echo sprintf( __( 'Please confirm that you agree with our %s', 'betp2p' ), '<a href="'.$privacy_policy_page_url.'">'.$privacy_policy_page_title.'</a>' ); ?></small>
            </label>
            <input type="hidden" name="_betp2p_privacy_policy_version" id="_betp2p_privacy_policy_version" value="<?php echo esc_attr__( BETP2P_PRIVACY_POLICY ); ?>" />
        </div>
        <div class="form-group col-md-12">
            <label for="betp2p_terms_and_conditions_acceptance">
                <input 
                    type="checkbox" 
                    name="_betp2p_terms_and_conditions_acceptance" 
                    id="betp2p_terms_and_conditions_acceptance" 
                    value="yes"                                
                />
                <?php
                $terms_and_conditions_page = get_page_by_path( 'terms-and-conditions' );
                $terms_and_conditions_page_title = get_the_title( $terms_and_conditions_page );
                $terms_and_conditions_page_url = get_the_permalink( $terms_and_conditions_page );
                ?>
                <small><?php echo sprintf( __( 'By submitting my information I agree to the %s.', 'betp2p' ), '<a href="'.$terms_and_conditions_page_url.'">'.$terms_and_conditions_page_title.'</a>' ); ?></small>
            </label>
            <input type="hidden" name="_betp2p_terms_and_conditions_version" id="_betp2p_terms_and_conditions_version" value="<?php echo esc_attr__( BETP2P_TERMS_AND_CONDITIONS_VERSION ); ?>" />
        </div>
        <p>
            <input type="hidden" name="betp2p_register_nonce" value="<?php echo wp_create_nonce( 'betp2p-register-u-m-nonce' ); ?>" />
            <input type="hidden" name="betp2p_register_submitted" id="betp2p_register_submitted" value="true" />
        </p>
        <p>
            <input type="submit" name="betp2p_register_submit" class="btn btn-primary" value="<?php echo esc_attr__( 'Sign Up', 'betp2p' ); ?>" />
        </p>
    </fieldset>
</form>

<h3><?php esc_html_e( 'Or login', 'betp2p' ); ?></h3>

<?php wp_login_form(); ?>