<?php
date_default_timezone_set('Australia/Melbourne');

// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
remove_filter( 'wp_mail_content_type', 'set_html_content_type' );

function set_html_content_type() {

	return 'text/html';
}


define("STILLBEAUTY_THEME_URL", get_site_url() . '/wp-content/themes/stillbeauty');

define("STILLBEAUTY_THEME_DIR", dirname(__FILE__));
define("STILLBEAUTY_THEME_TPL_DIR", dirname(__FILE__) . '/templates/');

include(STILLBEAUTY_THEME_DIR . '/lib/config.php');
include(STILLBEAUTY_THEME_DIR . '/lib/utils.php');
include(STILLBEAUTY_THEME_DIR . '/lib/catalogue.php');
include(STILLBEAUTY_THEME_DIR . '/lib/cart.php');
include(STILLBEAUTY_THEME_DIR . '/lib/admin.php');

$appdata = array();

$appdata['nav_menus'] = array('mainnav' => 'Main navigation');

$appdata['css'] = array( 
					 array('handle' => 'bootstrap', 'src' => STILLBEAUTY_THEME_URL . '/assets/css/bootstrap.min.css', 'media' => 'screen'),
					 array('handle' => 'oswald', 'src' => 'http://fonts.googleapis.com/css?family=Oswald:300', 'media' => 'screen'),
					 array('handle' => 'fontawesome', 'src' => 'http://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.min.css', 'media' => 'screen'),
					 array('handle' => 'ionicons', 'src' => 'http://code.ionicframework.com/ionicons/1.4.1/css/ionicons.min.css', 'media' => 'screen'),
					 array('handle' => 'datepicker-style', 'src' => STILLBEAUTY_THEME_URL . '/assets/css/datepicker.css', 'media' => 'screen'),
					 array('handle' => 'timepicker-style', 'src' => STILLBEAUTY_THEME_URL . '/assets/css/bootstrap-timepicker.min.css', 'media' => 'screen'),
					 array('handle' => 'validation-style', 'src' => STILLBEAUTY_THEME_URL . '/assets/css/validationEngine.css', 'media' => 'screen'),
					 array('handle' => 'stillbeauty-style', 'src' => STILLBEAUTY_THEME_URL . '/assets/css/style.css', 'media' => 'screen')
				    );

$appdata['js'] = array( 
					array('handle' => 'bootstrap', 'src' => 'http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js'),
					array('handle' => 'validation-en', 'src' => STILLBEAUTY_THEME_URL . '/assets/js/jquery.validationEngine-en.js'),
					array('handle' => 'validation', 'src' => STILLBEAUTY_THEME_URL . '/assets/js/jquery.validationEngine.js'),
					array('handle' => 'datepicker', 'src' => STILLBEAUTY_THEME_URL . '/assets/js/bootstrap-datepicker.js'),
					array('handle' => 'timepicker', 'src' => STILLBEAUTY_THEME_URL . '/assets/js/bootstrap-timepicker.min.js'),
					array('handle' => 'infotip', 'src' => STILLBEAUTY_THEME_URL . '/assets/js/infotip.js'),
					array('handle' => 'modal', 'src' => STILLBEAUTY_THEME_URL . '/assets/js/modal.js'),
					array('handle' => 'stillbeauty', 'src' => STILLBEAUTY_THEME_URL . '/assets/js/stillbeauty.js')

				  );

$appdata['remove_header_actions'] = array('wp_generator', 
										  'rsd_link', 
										  'wlwmanifest_link', 
										  'index_rel_link', 
										  'parent_post_rel_link', 
										  'adjacent_posts_rel_link_wp_head');

$appdata['editor_style'] = 'assets/css/editor.css';


Class StillBeautyApp {
	private $config;

	public function __construct() {
		$this->config = new Config($appdata);
	}

	public function put_header($data) {

		// add css
		foreach($data['css'] as $stylesheet) {
			wp_enqueue_style($stylesheet['handle'], $stylesheet['src'], NULL, false, $stylesheet['media']);
		}

		// deregister wp jquery
		wp_deregister_script('jquery');

		// load scripts
		foreach($data['js'] as $js) {
			wp_enqueue_script($js['handle'], $js['src'], NULL, '', true);
		}
	}

	public function getPageId() {
		$s = Utils::get_uri_segments($_SERVER['REQUEST_URI']);

		if (!empty($s))
			return($s[count($s)-1]);
		else
			return 'home';
	} 

	public function getVoucherHeader() {
		return STILLBEAUTY_THEME_URL . '/assets/images/still_voucher_header.jpg';
	}

	public function getPayPalUrl() {
		return 'https://www.paypal.com/cgi-bin/webscr';
	}

	public function getThankyouUrl() {
		return site_url('/checkout/thankyou/');
	}

	public function getIpnUrl() {
		return site_url('/checkout/paypalipn');
	}

	public function saveVoucher($data) {
		$content = serialize($data);
		$fp = fopen(STILLBEAUTY_THEME_DIR."/tmp/".$data['voucher_code'].".txt","wb");
		fwrite($fp,$content);
		fclose($fp);
	}

	public function render($tpl, $params = NULL) {

		if (file_exists(STILLBEAUTY_THEME_TPL_DIR . $tpl)) {
			$html = file_get_contents( STILLBEAUTY_THEME_TPL_DIR . $tpl );


			if (!empty($params)) {
				foreach($params as $search => $replace) {
					$html = str_replace('%'.$search.'%', $replace, $html);
				}
			}
		} else {
			$html = "File not found: " . STILLBEAUTY_THEME_TPL_DIR . $tpl;
		}


		return $html;

	}

	public function recordTransaction() {
		session_start();

		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

			if (array_key_exists('transaction', $_POST)) {
				global $wpdb;

				$wpdb->insert(  'sb_transactions', 
							    array('tx_ref' => $_POST['custom'],
							  		  'tx_details' => serialize($_POST['transaction']),
							  		  'status' => 'Pending',
							  		  'type' => 'Voucher'),
							    array('%s', '%s', '%s', '%s')
							  );
			}

			echo json_encode(array('status' => '1'));

		}
		else {
			header("Location: ".$_SERVER["HTTP_REFERER"]);
		}

		die();
	}
	
	public function rmFromCart() {
		session_start();

		$catalogue = new StillBeautyCatalogue();

		if (!array_key_exists('cart', $_SESSION)) {
			$_SESSION['cart'] = new StillBeautyCart();
		}

		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

			$_SESSION['cart']->rm($_POST['id']);

			$cart = $_SESSION['cart']->getItems();
			$total = $_SESSION['cart']->getTotal();
			$items = $_SESSION['cart']->getTotalItems();

			echo json_encode(array('total' => $total, 'items' => $items, 'cart' => $cart));

		}
		else {
			header("Location: ".$_SERVER["HTTP_REFERER"]);
		}

		die();
	}

	public function initCart() {
		session_start();

		$catalogue = new StillBeautyCatalogue();

		if (!array_key_exists('cart', $_SESSION)) {
			$_SESSION['cart'] = new StillBeautyCart();
		}

		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

			$cart = $_SESSION['cart']->getItems();
			$total = $_SESSION['cart']->getTotal();
			$items = $_SESSION['cart']->getTotalItems();

			echo json_encode(array('total' => $total, 'items' => $items, 'cart' => $cart));

		}
		else {
			header("Location: ".$_SERVER["HTTP_REFERER"]);
		}

		die();
	}

	public function addToCart() {
		session_start();

		$catalogue = new StillBeautyCatalogue();

		if (!array_key_exists('cart', $_SESSION)) {
			$_SESSION['cart'] = new StillBeautyCart();
		}

		if ( !wp_verify_nonce( $_REQUEST['nonce'], "sb_add_to_cart_nonce")) {
		 	exit("No monkey business please");
		} 

		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			
			$product = $catalogue->getProduct($_POST['id']);
			$_SESSION['cart']->push($product);

			$cart = $_SESSION['cart']->getItems();
			$total = $_SESSION['cart']->getTotal();
			$items = $_SESSION['cart']->getTotalItems();

			echo json_encode(array('total' => $total, 'items' => $items, 'cart' => $cart));

		}
		else {
			header("Location: ".$_SERVER["HTTP_REFERER"]);
		}

		die();
	}

	public function renderHtml( $atts, $content = "" ) {
		$nonce = (array_key_exists('nonce', $atts)) ? wp_create_nonce($atts['nonce']) : "";
		$posturl = (array_key_exists('posturi', $atts)) ? site_url($atts['posturi']) : "";
		return self::render($atts['src'], array('today' => date('Y-M-d'), 'voucher' => substr(md5(rand()), 0, 7), 'nonce' => $nonce, 'posturl' => $posturl));
	}

	public function sendBooking() {
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "sb_send_booking_nonce")) {
		 	exit("No monkey business please");
		} 

		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

			$mailcontent = '<table width="100%" cellpadding="10" style="border-collapse: collapse; border: 1px solid #dddddd">';
		
			$mailcontent .= '<tr style="border-bottom:1px solid #dddddd;" align="left"><td style="background:#f5f5f5; border-right:1px solid #dddddd;">First Name</td><td>'.$_POST['fname'].'</td></tr>';
			$mailcontent .= '<tr style="border-bottom:1px solid #dddddd;" align="left"><td style="background:#f5f5f5; border-right:1px solid #dddddd;">Last Name</td><td>'.$_POST['lname'].'</td></tr>';
			$mailcontent .= '<tr style="border-bottom:1px solid #dddddd;" align="left"><td style="background:#f5f5f5; border-right:1px solid #dddddd;">Gender</td><td>'.$_POST['gender'].'</td></tr>';
			$mailcontent .= '<tr style="border-bottom:1px solid #dddddd;" align="left"><td style="background:#f5f5f5; border-right:1px solid #dddddd;">Telephone Number</td><td>'.$_POST['tel'].'</td></tr>';
			$mailcontent .= '<tr style="border-bottom:1px solid #dddddd;" align="left"><td style="background:#f5f5f5; border-right:1px solid #dddddd;">Mobile Number</td><td>'.$_POST['mobile'].'</td></tr>';
			$mailcontent .= '<tr style="border-bottom:1px solid #dddddd;" align="left"><td style="background:#f5f5f5; border-right:1px solid #dddddd;">Email</td><td>'.$_POST['email'].'</td></tr>';
			$mailcontent .= '<tr style="border-bottom:1px solid #dddddd;" align="left"><td style="background:#f5f5f5; border-right:1px solid #dddddd;">Address</td><td>'.$_POST['address'].'</td></tr>';
			$mailcontent .= '<tr style="border-bottom:1px solid #dddddd;" align="left"><td style="background:#f5f5f5; border-right:1px solid #dddddd;">Favourite Treatment</td><td>'.implode(', ', $_POST['likes']).'</td></tr>';
			$mailcontent .= '<tr style="border-bottom:1px solid #dddddd;" align="left"><td style="background:#f5f5f5; border-right:1px solid #dddddd;">Subscribe</td><td>'.$_POST['newsletter'].'</td></tr>';
			$mailcontent .= '<tr style="border-bottom:1px solid #dddddd;" align="left"><td style="background:#f5f5f5; border-right:1px solid #dddddd;">Date</td><td>'.$_POST['date'].'</td></tr>';
			$mailcontent .= '<tr style="border-bottom:1px solid #dddddd;" align="left"><td style="background:#f5f5f5; border-right:1px solid #dddddd;">Start</td><td>'.$_POST['start'].'</td></tr>';
			$mailcontent .= '<tr style="border-bottom:1px solid #dddddd;" align="left"><td style="background:#f5f5f5; border-right:1px solid #dddddd;">End</td><td>'.$_POST['end'].'</td></tr>';
			$mailcontent .= '<tr style="border-bottom:1px solid #dddddd;" align="left"><td style="background:#f5f5f5; border-right:1px solid #dddddd;">Message</td><td>'.$_POST['additional'].'</td></tr>';

			$mailcontent .= '</table>';

			$filepath = STILLBEAUTY_THEME_DIR.'/assets/json/email.json';
			$string = file_get_contents($filepath);
			$record = json_decode($string,true);

			$headers =  "From: " . $record['email']['fromName'] . " <" . $record['email']['from'] . ">\r\n". 
						"Cc: " . $record['email']['cc'] . "\r\n" .
						"MIME-Version: 1.0" . "\r\n" . 
						"Content-type: text/html; charset=UTF-8" . "\r\n";

			wp_mail($record['email']['to'], "Booking Request", $mailcontent, $headers);

			echo json_encode(array('status' => 'OK'));

		} else {
			header("Location: ".$_SERVER["HTTP_REFERER"]);
		}

		die();
	}


	public function sendContact() {
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "sb_send_contact_nonce")) {
		 	exit("No monkey business please");
		} 

		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

			$mailcontent = '<table width="100%" cellpadding="10" style="border-collapse: collapse; border: 1px solid #dddddd">';
		
			$mailcontent .= '<tr style="border-bottom:1px solid #dddddd;" align="left"><td style="background:#f5f5f5; border-right:1px solid #dddddd;">First Name</td><td>'.$_POST['fname'].'</td></tr>';
			$mailcontent .= '<tr style="border-bottom:1px solid #dddddd;" align="left"><td style="background:#f5f5f5; border-right:1px solid #dddddd;">Last Name</td><td>'.$_POST['lname'].'</td></tr>';
			$mailcontent .= '<tr style="border-bottom:1px solid #dddddd;" align="left"><td style="background:#f5f5f5; border-right:1px solid #dddddd;">Gender</td><td>'.$_POST['gender'].'</td></tr>';
			$mailcontent .= '<tr style="border-bottom:1px solid #dddddd;" align="left"><td style="background:#f5f5f5; border-right:1px solid #dddddd;">Telephone Number</td><td>'.$_POST['tel'].'</td></tr>';
			$mailcontent .= '<tr style="border-bottom:1px solid #dddddd;" align="left"><td style="background:#f5f5f5; border-right:1px solid #dddddd;">Email</td><td>'.$_POST['email'].'</td></tr>';
			$mailcontent .= '<tr style="border-bottom:1px solid #dddddd;" align="left"><td style="background:#f5f5f5; border-right:1px solid #dddddd;">Address</td><td>'.$_POST['address'].'</td></tr>';
			$mailcontent .= '<tr style="border-bottom:1px solid #dddddd;" align="left"><td style="background:#f5f5f5; border-right:1px solid #dddddd;">Source</td><td>'.$_POST['source'].'</td></tr>';
			$mailcontent .= '<tr style="border-bottom:1px solid #dddddd;" align="left"><td style="background:#f5f5f5; border-right:1px solid #dddddd;">Favourite Treatment</td><td>'.$_POST['favourite'].'</td></tr>';
			$mailcontent .= '<tr style="border-bottom:1px solid #dddddd;" align="left"><td style="background:#f5f5f5; border-right:1px solid #dddddd;">Mailing List Status</td><td>'.$_POST['mail'].'</td></tr>';
			$mailcontent .= '<tr style="border-bottom:1px solid #dddddd;" align="left"><td style="background:#f5f5f5; border-right:1px solid #dddddd;">Message</td><td>'.$_POST['message'].'</td></tr>';

			$mailcontent .= '</table>';


			$filepath = STILLBEAUTY_THEME_DIR.'/assets/json/email.json';
			$string = file_get_contents($filepath);
			$record = json_decode($string,true);

			$headers =  "From: " . $record['email']['fromName'] . " <" . $record['email']['from'] . ">\r\n". 
						"Cc: " . $record['email']['cc'] . "\r\n" .
						"MIME-Version: 1.0" . "\r\n" . 
						"Content-type: text/html; charset=UTF-8" . "\r\n";

			wp_mail($record['email']['to'], "Contact Message", $mailcontent, $headers);

			echo json_encode(array('status' => 'OK'));

		} else {
			header("Location: ".$_SERVER["HTTP_REFERER"]);
		}

		die();
	}

	public function mail($message) {
		if (!array_key_exists('to', $message)) $message['to'] = "hypervisual.media@gmail.com";
		if (!array_key_exists('subject', $message)) $message['subject'] = "Still Beauty Website - No Subject";

		if (!array_key_exists('content', $message)) $message['content'] = "&lt; Empty &gt;";

		if (!array_key_exists('headers', $message)) 
			$message['headers'] = "From: Still Beauty Website <website@stillbeauty.com.au>\r\n". 
								"MIME-Version: 1.0" . "\r\n" . 
								"Content-type: text/html; charset=UTF-8" . "\r\n";

		return wp_mail($message['to'], $message['subject'], $message['content'], $message['headers']);
	}

	public function processPayment() {
        $ppAcc = "joanna@stillbeauty.com.au";
        $at = "svEAa3xnr7x2KC8dsBrdbe-8H1tcEq47uthV1OccdTLI0htOeKny78JSZuG"; 
        $url = "https://www.paypal.com/cgi-bin/webscr";

        $tx = $_REQUEST["tx"];
        $cmd = "_notify-synch";
        $post = "tx=$tx&at=$at&cmd=$cmd";

        $ch = curl_init ($url);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt ($ch, CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);

        $result = curl_exec ($ch);
        $error = curl_error($ch);

        if (curl_errno($ch) != 0) return NULL;

        $lines = explode("\n", $result);

        if (strcmp ($lines[0], "SUCCESS") == 0) {

        	if (array_key_exists('cart', $_SESSION)) {
        		$_SESSION['cart']->reset();
        	}

            return $result;
           
        } else {

            return NULL;

        }
	}

	public function renderCheckout() {
		$params = array('cmd' => '_cart',
						'upload' => '1',
						'currency_code' => 'AUD',
						'business' => 'joanna@stillbeauty.com.au',
						'return' => site_url('/checkout/thankyou/'))
?> 
		<form id="checkout-form" action="https://www.paypal.com/cgi-bin/webscr" method="post" style="visibility: hidden;">
<?php
		
		foreach($params as $name => $value) :
?>
			<input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>" />		
<?php
		endforeach;
		if (array_key_exists('cart', $_SESSION)) :
			$cart = $_SESSION['cart']->getItems();

			for($i=1;  $i <= count($cart); ++$i) {
?>
				<input type="hidden" name="item_name_<?php echo $i; ?>" value="<?php echo $cart[$i-1]['product']['type'] . ' - ' . $cart[$i-1]['product']['name']; ?>" />
				<input type="hidden" name="amount_<?php echo $i; ?>" value="<?php echo number_format($cart[$i-1]['product']['price'], 2); ?>" />
				<input type="hidden" name="quantity_<?php echo $i; ?>" value="<?php echo $cart[$i-1]['quantity']; ?>" />

<?php
			}

			global $wpdb;

			$wpdb->insert(  'sb_transactions', 
						    array('tx_ref' => substr(md5(rand()), 0, 7),
						  		  'tx_details' => serialize($cart),
						  		  'status' => 'Pending',
						  		  'type' => 'Products'),
						    array('%s', '%s', '%s', '%s')
						  );
		endif;
?>

		</form>
<?php
	}

	public function showProducts( $atts, $content="" ) {
		$category = get_page_by_title($atts['name']);
		$nonce = wp_create_nonce("sb_add_to_cart_nonce");

		if (!empty($category)) :
?>
		<section id="<?php echo $category->post_name; ?>" class="product-block <?php echo $atts['class']; ?>">
			<h4><a  href="#<?php echo $category->post_name; ?>"><span class="ion-ios7-plus-outline"></span><span class="ion-ios7-minus-outline"></span><?php echo $category->post_title; ?></a></h4>
			<div>
				<?php echo apply_filters( 'the_content', $category->post_content ); ?>
				<ul>
					<?php 
					$products = get_pages(array(
							'parent' => $category->ID,
							'child_of' => $category->ID,
							'sort_column' => 'menu_order'
						));

					foreach($products as $product) :
					?>
						<li>
							<ul>
								<li>
									<?php
									$image = get_the_post_thumbnail( $product->ID, 'full' );
									echo $image;
									?>
									<form action="" method="">
										<input type="hidden" name="nonce" value="<?php echo $nonce; ?>" />
										<input type="hidden" name="id" value="<?php echo get_post_meta( $product->ID, "product_id", true ); ?>" />
										<input type="hidden" name="price" value="<?php echo $product_price; ?>" />
										<input class="addToCart bg-<?php echo strtolower($product->post_name); ?>" data-product="<?php echo $product->post_title; ?>" data-category="<?php echo $category->post_title; ?>" type="submit" value="Add to cart" />
									</form>
								</li>
								<li>
									<div>
										<h4>
											<span price>
											<?php
											$product_price = get_post_meta( $product->ID, "product_price", true );
											echo $product_price;
											?>
											</span>
											<?php echo $product->post_title; ?>
										</h4>


										<?php
										$product->post_content = str_replace('<li>', '<li class="ion-ios7-checkmark-empty">&nbsp;', $product->post_content);
										echo apply_filters('the_content', $product->post_content);
										?>




									</div>
								</li>

							</ul>
						</li>
					<?php
					endforeach;
					?>
				</ul>
			</div>
			<hr />
		</section>
<?php
		endif;
	}

}

/*** CREATE APP INSTANCE ***/
$app = new StillBeautyApp($appdata);

add_shortcode('sbProduct', array('StillBeautyApp', 'showProducts'));
add_shortcode('sbInclude', array('StillBeautyApp', 'renderHtml'));
add_shortcode('sbCheckout', array('StillBeautyApp', 'renderCheckout'));

/*** Ajax services ***/
add_action('wp_ajax_sb_add_to_cart', array('StillBeautyApp', 'addToCart'));
add_action('wp_ajax_nopriv_sb_add_to_cart', array('StillBeautyApp', 'addToCart'));
add_action('wp_ajax_sb_init_cart', array('StillBeautyApp', 'initCart'));
add_action('wp_ajax_nopriv_sb_init_cart', array('StillBeautyApp', 'initCart'));
add_action('wp_ajax_sb_rm_from_cart', array('StillBeautyApp', 'rmFromCart'));
add_action('wp_ajax_nopriv_sb_rm_from_cart', array('StillBeautyApp', 'rmFromCart'));
add_action('wp_ajax_sb_send_booking', array('StillBeautyApp', 'sendBooking'));
add_action('wp_ajax_nopriv_sb_send_booking', array('StillBeautyApp', 'sendBooking'));
add_action('wp_ajax_sb_send_contact', array('StillBeautyApp', 'sendContact'));
add_action('wp_ajax_nopriv_sb_send_contact', array('StillBeautyApp', 'sendContact'));
add_action('wp_ajax_sb_record_tx', array('StillBeautyApp', 'recordTransaction'));
add_action('wp_ajax_nopriv_sb_record_tx', array('StillBeautyApp', 'recordTransaction'));

if (is_admin()) {
	add_action('wp_dashboard_setup', 'stillbeauty_setup_dashboard');

	function stillbeauty_setup_dashboard() {
		// Globalize the metaboxes array, this holds all the widgets for wp-admin
	 	global $wp_meta_boxes;

		// Remove the incomming links widget
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);	

		// Remove right now
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
		
		// et al
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_quick_press']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);

	}
}

?>
