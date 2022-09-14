<?php get_header(); ?>

    <div class="row col-12">

        <!-- page-navigation -->
        <aside class="sidebar col-12 col-md-2">
            <?php
                $args = array(
                    'theme_location' => 'sidebar-menu',
                    'container' => 'nav',
                    'container_class' => 'ibetcha-sidebar-menu list-group',
                    'container_id' => 'ibetcha-sidebar-menu'
                );
                wp_nav_menu( $args );
            ?>
        </aside>

        <main class="main-content col-12 col-md-7">

            <?php dynamic_sidebar( 'matches_content_sidebar' ); ?>

        </main>

    </div>    

<?php get_footer(); ?>