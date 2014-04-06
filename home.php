<?php
/*
 * Template name: Home
 */

get_header();
?>
	<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
  			<div>
				<?php echo get_the_post_thumbnail( get_the_ID(), 'full' ); ?> 
    		</div>

    		<?php
    		echo the_content(); 
    		?>
    <?php endwhile; ?>
    <?php endif; ?>
<?php
get_footer();
?>