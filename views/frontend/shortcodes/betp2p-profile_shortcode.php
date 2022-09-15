<div class="card">
    <div class="card-header">
        <?php echo esc_html( 'Profile', 'betp2p' ) ?>
    </div>
    <div class="card-body">
        <h5 class="card-title">Your Profile</h5>
        <p>Información general de su perfil</p>

        <div class="row col-12">

            <?php $account = ibetcha_get_current_user_page( 'account' ); ?>
            <div class="col-12 col-md-3">
                <div class="card shadow mb-4">
                    <div class="card-body p-4">
                        <a href="<?php echo $account; ?>">
                            <i class="fas fa-piggy-bank fa-2x text-info mb-4"></i>
                        </a>
                        <a href="<?php echo $account; ?>">
                            <h3 class="card-title text-info"><?php echo esc_html( 'Account', 'betp2p' ); ?></h3>
                        </a>
                        <p><?php esc_html_e( 'Access your account to check your balance and see the movements that have been made', 'betp2p' ); ?></p>
                    </div>
                </div>
            </div>

            <?php $wallet = ibetcha_get_current_user_page( 'wallet' ); ?>
            <div class="col-12 col-md-3">
                <div class="card shadow mb-4">
                    <div class="card-body p-4">
                        <a href="<?php echo $wallet; ?>">
                            <i class="fas fa-wallet fa-2x text-info mb-4"></i>
                        </a>
                        <a href="<?php echo $wallet; ?>">
                            <h3 class="card-title text-info"><?php echo esc_html( 'Wallet', 'betp2p' ); ?></h3>
                        </a>
                        <p>
                            <?php esc_html_e( 'Check the balance of your wallet, these funds will be used to make and take bets', 'betp2p' ); ?>
                        </p>
                    </div>
                </div>
            </div>

            <?php $bets = ibetcha_get_current_user_page( 'user-bets' ); ?>
            <div class="col-12 col-md-3">
                <div class="card shadow mb-4">
                    <div class="card-body p-4">
                        <a href="<?php echo $bets; ?>">
                            <i class="fas fa-football-ball fa-2x text-info mb-4"></i>
                        </a>
                        <a href="<?php echo $bets; ?>">
                            <h3 class="card-title text-info"><?php echo esc_html( 'Bets', 'betp2p' ); ?></h3>
                        </a>
                        <p>
                            <?php esc_html_e( 'Check the history of the bets you have made and the bets you have taken', 'betp2p' ); ?>
                        </p>
                    </div>
                </div>
            </div>

            <?php $transactions = ibetcha_get_current_user_page( 'transactions' ); ?>
            <div class="col-12 col-md-3">
                <div class="card shadow mb-4">
                    <div class="card-body p-4">
                        <a href="<?php echo $transactions; ?>">
                            <i class="fas fa-exchange-alt fa-2x text-info mb-4"></i>
                        </a>
                        <a href="<?php echo $transactions; ?>">
                            <h3 class="card-title text-info"><?php echo esc_html( 'Transactions', 'betp2p' ); ?></h3>
                        </a>
                        <p>
                            <?php esc_html_e( 'Here you can find the history of the transactions you have made', 'betp2p' ); ?>
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>