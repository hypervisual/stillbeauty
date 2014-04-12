<?php
/*
 * Template name: Thankyou
 */

get_header();

?>
	<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
        <div class="span10 page">
            <?php
            global $app;
            if (($result = $app->processPayment()) != NULL) {
            	// success
            	$page = get_page_by_title('Success');
            	echo apply_filters( 'the_content', $page->post_content );

            } else {
            	// failure
            	$page = get_page_by_title('Failure');

            	echo apply_filters( 'the_content', $page->post_content );
            }
            ?>
        </div>
    <?php endwhile; ?>
    <?php endif; ?>
<?php
get_footer();
?>