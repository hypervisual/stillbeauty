<?php
/*
 * Template name: Treatments
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

            <?php
            $categories = get_pages(array(
                    'child_of' => get_the_ID(),
                    'parent' => get_the_ID(),
                    'sort_column' => 'menu_order'
                ));

            ?>

            <nav>
                <ul id="treatments-menu" class="inline desktop">
                <?php
                foreach($categories as $category) :
                ?>
                    <li><a href="#<?php echo $category->post_name; ?>"><?php echo $category->post_title; ?></a></li>
                <?php
                endforeach;
                ?>
                </ul>
            </nav>

            <?php 
            $firstcat = 1;
            $i = 0;
            foreach($categories as $category) :
                $style = ($firstcat) ? '' : 'style="display: none;"';
                $classname = ($firstcat) ? 'active' : '';
                $arrow = ($firstcat) ? 'ion-ios7-arrow-up' : 'ion-ios7-arrow-down';
                $firstcat = 0;
            ?>
                <h4 class="mobile accordion accordion-<?php echo ++$i; ?>"><a href="#<?php echo $category->post_name; ?>" class="js-accordion-toggle"><span class="<?php echo $arrow; ?>"></span><?php echo $category->post_name; ?></a></h4>
                <div id="<?php echo $category->post_name; ?>" class="category <?php echo $classname; ?>" <?php echo $style; ?>>
            <?php
                $products = get_pages(array(
                        'child_of' => $category->ID,
                        'parent' => $category->ID,
                        'sort_column' => 'menu_order'
                    ));

                $first = 1;

                foreach($products as $product) :
                    if (!$first) :
            ?>
                    <hr />
            <?php
                    else :
                        $first = 0;
                    endif;
            ?>

                    <div class="product">
                        <h4><?php echo $product->post_title; ?></h4>
            <?php
                    $image = get_the_post_thumbnail( $product->ID, 'full' );

                    if (!empty($image)) :
            ?>
                    <div>
                        <?php echo $image; ?>
                    </div>
            <?php
                    endif;
            ?>

                        <?php echo apply_filters('the_content', $product->post_content); ?>
                    </div>
            <?php
                endforeach;
            ?>
                </div>
            <?php
            endforeach;
            ?>

            <aside>
                <?php
                $cancellation = get_page_by_title('Cancellation Policy');
                ?>
                <h6><?php echo $cancellation->post_title; ?></h6>
                <?php echo apply_filters('the_content', $cancellation->post_content); ?>
            </aside>
        </div>
    <?php endwhile; ?>
    <?php endif; ?>
<?php
get_footer();
?>