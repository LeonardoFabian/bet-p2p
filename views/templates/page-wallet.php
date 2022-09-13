<?php 
/**
 * Template Name: Page Wallet
 */
?>

<?php get_header(); ?>

    <div class="container">

        Wallet

        <?php 

            $users = new BetP2P_Users;
            $users->get_users();

            $wallets = new BetP2P_Wallets;
            $wallets->get_wallets();

        ?>

    </div>

<?php get_footer(); ?>