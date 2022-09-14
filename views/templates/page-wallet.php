<?php 
/**
 * Template Name: Page Wallet
 */
?>

<?php get_header(); ?>

    <div class="container-fluid">

        Wallet

        <?php 

            $users = new BetP2P_Users;
            $users->get_users();

            $wallets = new BetP2P_Wallets;
            $wallets->get_wallets();

        ?>

        <?php echo do_shortcode( '[betp2p_wallet]' ); ?>

    </div>

<?php get_footer(); ?>