<?php
/*
 * Template name: SB Default
 */

get_header();
?>
	<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
        <div class="mobile banner">
                <section id="logo" class="mobile"><a href="/"><img src="<?php echo site_url() . '/wp-content/themes/stillbeauty/assets/images/still-blue.png'; ?>" alt="Still Beauty" /></a></section>
        </div>
        <div class="span10 page">
            <h1><?php the_title(); ?></h1>

            <?php the_content(); ?>
        </div>
    <?php endwhile; ?>
    <?php endif; ?>
<?php
get_footer();
?>