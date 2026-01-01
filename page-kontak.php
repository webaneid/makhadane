<?php
/*
 * Template Name: Contact Us
 */
get_header();
?>

<main id="site-content">
    <?php
    if (have_posts()) :
        while (have_posts()) :
            the_post();
            get_template_part('tp/content', 'page-kontak');
        endwhile;
    endif;
    ?>
</main>

<?php get_footer(); ?>
