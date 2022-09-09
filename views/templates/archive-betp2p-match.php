<?php get_header(); ?>

    <div class="flex items-center">
        <?php foreach ( $sports as $sport ) : ?>
            <a href="<?php echo esc_url( get_term_link($sport) ) ?>"><?php echo esc_html($port->name); ?></a>
        <?php endforeach ?>
    </div>

    <div class="section container with-two-sidebar">

        <!-- page-navigation -->
        <aside class="sidebar">
            <?php
                $args = array(
                    'theme_location' => 'sidebar-menu',
                    'container' => 'nav',
                    'container_class' => 'ibetcha-sidebar-menu',
                    'container_id' => 'ibetcha-sidebar-menu'
                );
                wp_nav_menu( $args );
            ?>
        </aside>

        <main class="main-content">

            <?php dynamic_sidebar( 'matches_content_sidebar' ); ?>

        </main>

    </div>    

<?php get_footer(); ?>