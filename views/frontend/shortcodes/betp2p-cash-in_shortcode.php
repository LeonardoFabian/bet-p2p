<?php 
$coin_options = get_option( 'ibetcha_coin_options' );
?>

<div class="card">
    <div class="card-header">
        <?php echo esc_html( 'Cash In', 'betp2p' ) ?>
    </div>
    <div class="card-body p-5">
        <h5 class="card-title">Cash In</h5>

        <div class="row d-flex items-center mb-4">
            <p>Adquiere <?php echo esc_html( $coin_options['ibetcha_coin_name']  ); ?> para realizar tus apuestas.</p>
        </div>

        <div class="gap-3 d-flex align-items-center justify-content-between">

            <div class="col-12 col-md-6">                
                <div class="card p-5 shadow">
                    <form id="betp2p-cash-in-form">
                        <div class="card-header">
                            <?php esc_html_e( 'Buy', 'betp2p' ); ?>
                        </div>
                        <div class="card-body p-5">      
                            <div class="form-group mb-4">
                                <select id="betp2p_payment_method" class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
                                    <option>Selecciona...</option>
                                    <option value="debit-credit">Pay with Debit/Credit Card</option>
                                    <option value="paypal">Pay with PayPal</option>
                                </select>
                            </div>
                            <div class="text-left">
                                <label><?php esc_html_e( 'Amount', 'betp2p' ); ?></label>
                            </div>                  
                            <div class="input-group input-group-lg mb-4">                  
                                <span class="input-group-text">$</span>
                                <input type="text" id="betp2p_account_balance" name="balance" class="form-control" aria-label="Amount (to the nearest dollar)" disabled>
                                <span class="input-group-text">USD</span>
                            </div>     
                            
                            <div class="d-flex gap-3 align-items-center justify-content-around mb-4">
                                <div class="input-group">
                                    <input type="text" id="betp2p-buying-usd-amount" class="form-control" readonly>
                                    <span class="input-group-text">USD</span>
                                </div>
                                <i class="fas fa-exchange-alt fa-2x text-secondary"></i>
                                <div class="input-group">
                                    <input type="text" id="betp2p-buying-coin-exchange" class="form-control" readonly>
                                    <input type="hidden" id="betp2p-coin-value" value="<?php echo esc_html( $coin_options['ibetcha_coin_value'] ); ?>" />
                                    <span class="input-group-text">CHA</span>
                                </div>
                            </div>

                            <hr class="mb-4 mt-4">

                            <div class="d-grid d-md-block">
                                <button type="submit" class="btn btn-info text-uppercase"><?php printf( __( 'Buy %s', 'betp2p' ), esc_html( $coin_options['ibetcha_coin_name'] ) ); ?></button>
                            </div>
                        </div>
                    </form>
                </div>   
            </div>

            <div class="col-12 col-md-5">
                <div class="card p-4 shadow">
                    <div class="card-body">
                        <div class="text-uppercase text-center text-info">
                            <?php esc_html_e( 'You are buying', 'betp2p' ); ?>
                            <h3 class="text-info" id="coin-buying-amount">0.00 <?php echo esc_html( $coin_options['ibetcha_coin_name']  ); ?></h3>
                            <span id="betp2p-dollar-amount-checkout">US$ 0.00</span> <span><?php printf( __( 'per %s' ), esc_html( $coin_options['ibetcha_coin_name'] ) ); ?></span>
                        </div>

                        <hr class="mb-4 mt-4">

                        <div class="payment-checkout text-info">
                            <div class="mb-3">
                                <label for="betp2p-payment-method-checkout"><?php esc_html_e( 'Payment Method', 'betp2p' ); ?></label>
                                <input type="text" id="betp2p-payment-method-checkout" class="form-control" readonly />
                            </div>

                            <hr class="mb-2 mt-2">

                            <div class="mb-3">
                                <label for="betp2p-payment-date-checkout"><?php esc_html_e( 'Trade Date', 'betp2p' ); ?></label>
                                <input type="text" id="betp2p-payment-date-checkout" class="form-control" readonly />
                            </div>

                            <hr class="mb-2 mt-2">

                            <div class="mb-3">
                                <label for="betp2p-payment-exchange-checkout"><?php esc_html_e( 'Deposit To', 'betp2p' ); ?></label>
                                <input type="text" id="betp2p-payment-destiny" class="form-control" readonly />
                            </div>
                        </div>

                        
                        <hr class="mb-4 mt-4">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>