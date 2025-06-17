<?php
get_header();
?>

<main id="primary" class="site-main" role="main">
    <?php
    while ( have_posts() ) :
        the_post();
        echo apply_filters('the_content', get_the_content());
    endwhile;
    ?>
</main>

<?php
get_footer();
