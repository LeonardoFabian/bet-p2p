<?php get_header(); ?>

<div>
    <?php while ( have_posts() ) : the_post(); ?>

    <div class="section container-fluid">
        <main class="main-content col-12">     

            <?php the_content(); ?>
            
        </main>
    </div>  
        
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>