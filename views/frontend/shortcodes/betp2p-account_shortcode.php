<?php 
$coin_options = get_option( 'ibetcha_coin_options' );
?>

<div class="card">
    <div class="card-header">
        <?php echo esc_html( 'Account', 'betp2p' ) ?>
    </div>
    <div class="card-body">
        <h5 class="card-title">Your Account</h5>
        <p>Información general de tu cuenta</p>

        <div class="row col-12 mb-4">
            <div class="col-12 col-md-4 d-flex align-items-center justify-content-between">
                <span>Balance actual: <span class="text-success">0.00 <?php echo esc_html( $coin_options['ibetcha_coin_name'] ); ?></span></span>

                <?php $cashin = ibetcha_get_current_user_page( 'cash-in' ); ?>
                <a href="<?php echo $cashin  ?>" class="btn btn-info"><?php printf( __( 'Buy %s', 'betp2p' ), esc_html( $coin_options['ibetcha_coin_name'] ) ); ?></a>
            </div>
        </div>

        <table class="table table-hover">
            <thead>
                <tr>
                <th scope="col">#</th>
                <th scope="col">First</th>
                <th scope="col">Last</th>
                <th scope="col">Handle</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <th scope="row">1</th>
                <td>Mark</td>
                <td>Otto</td>
                <td>@mdo</td>
                </tr>
                <tr>
                <th scope="row">2</th>
                <td>Jacob</td>
                <td>Thornton</td>
                <td>@fat</td>
                </tr>
                <tr>
                <th scope="row">3</th>
                <td colspan="2">Larry the Bird</td>
                <td>@twitter</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>