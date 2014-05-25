<!DOCTYPE html>
<html>
<head>
	<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( 'Page %s', max( $paged, $page ) );

	?></title>

	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<link href="favicon.ico" rel="shortcut icon" />

	<?php
	global $app, $appdata;

	session_start();
	$app->put_header($appdata);
	wp_head();

	?>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">


	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.7.1/modernizr.min.js"></script>

	<script type="text/javascript">
	var StillBeauty = StillBeauty || {};
	StillBeauty.ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';
	StillBeauty.checkouturl = '<?php echo site_url("/checkout/"); ?>';
	</script>


<!--[if lt IE 9]>
	<script src="http://cdnjs.cloudflare.com/ajax/libs/selectivizr/1.0.2/selectivizr-min.js"></script>
<![endif]-->



</head>

<body id="<?php echo $app->getPageId(); ?>"  class="push wrap">
	<div class="container">

		<a href="#main-menu" class="mobile menu-link"><span class="ion-navicon-round"></span> Menu</a>

		<nav class="text-center desktop" id="menu-holder">
			<ul class="inline" id="main-menu">
				<li><a href="/about">About</a></li>
				<li><a href="/treatments">Treatments</a></li>
				<li><a href="/products">Products</a></li>
				<li><a href="/"><img src="http://www.stillbeauty.com.au/wp-content/themes/stillbeauty/assets/images/still-blue.png" width="158" style="max-width:158px"></a></li>
				<li><a href="/bookings">Bookings</a></li>
				<li><a href="/vouchers">Vouchers</a></li>
				<li><a href="/contact">Contact</a></li>
  			</ul>
  		</nav>  <!-- #menu-holder -->

  		
