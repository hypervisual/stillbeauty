<?php
/*
 * Template name: SB Default
 */

get_header();
?>
	<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
        <div class="span10 page">
            <h1><?php the_title(); ?></h1>

            <?php the_content(); ?>
        </div>
    <?php endwhile; ?>
    <?php endif; ?>
<?php
get_footer();
?>