<?php
/*
 * Template name: Home
 */

get_header();
?>
	<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
  			<div class="desktop">
				<?php echo get_the_post_thumbnail( get_the_ID(), 'full' ); ?> 
    		</div>

            <div class="mobile banner">
                <section id="logo" class="mobile"><a href="/"><img src="<?php echo site_url() . '/wp-content/themes/stillbeauty/assets/images/still-blue.png'; ?>" alt="Still Beauty" /></a></section>

                <h1><?php bloginfo('description'); ?></h1>
            </div>

    		<?php
    		echo the_content(); 
    		?>

            <div id="home-nav" class="mobile">
                <ul>
                    <li><a href="/about">About</a></li>
                    <li><a href="/treatments">Treatments</a></li>
                    <li><a href="/products">Products</a></li>
                    <li><a href="/bookings">Bookings</a></li>
                    <li><a href="/vouchers">Vouchers</a></li>
                    <li><a href="/contact">Contact</a></li>
                </ul>
            </div>
    <?php endwhile; ?>
    <?php endif; ?>
<?php
get_footer();
?>