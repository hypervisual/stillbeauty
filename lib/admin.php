<?php
if( ! class_exists( 'WP_List_Table' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

require_once( STILLBEAUTY_THEME_DIR . '/lib/transactions_list.php' );

define("STILLBEAUTY_LOGO_URL", get_site_url() . '/wp-content/themes/stillbeauty/assets/images/still-blue.png');
define("STILLBEAUTY_HEADER_URL", get_site_url() . '/wp-content/themes/stillbeauty/assets/images/still_voucher_header.jpg');


class StillBeautyAdminPage {
	public function __construct() {
		return;
	}


	private function get_options($name, $list, $selected) {

		$select = '<select name="%name%">%options%</select>';
		$opt = '<option value="%key%" %selected%>%value%</option>';
		$options = '';

		foreach ($list as $key => $value) {
			$selected = ($key == $selected) ? 'selected="selected"' : '';
			$options .= str_replace('%key%', $key,
				        str_replace('%value%', $value,
				        str_replace('%selected%', $selected, $opt)));
		}

		$html = str_replace('%name%', $name,
			   	str_replace('%options%', $options, $select));

		return $html;

	}

	public function put_overview($tx) {

		global $app;

		$status = self::get_options(
						'status',
						array('Pending' => 'Pending', 
							  'Processing' => 'Processing', 
							  'Dispatched' => 'Dispatched', 
							  'Refunded' => 'Refunded', 
							  'Cancelled' => 'Cancelled'),
						$tx->status
					);

		$params = array(
					'reference' => $tx->tx_ref,
					'date' => $tx->tx_date,
					'type' => $tx->type,
					'status' => $status
				  );

		$html = $app->render("overview.html", $params);
		echo $html;

	}

	public function put_summary($tx) {
		global $app;


		$params = array(
					'reference' => $tx->tx_ref,
					'date' => $tx->tx_date,
					'type' => $tx->type,
					'status' => $tx->status
				  );

		$html = $app->render("overview.html", $params);
		echo $html;
	}

	public function put_voucher($data) {
		global $app;

		$data['value']  = number_format($data['value'], 2);
		$data['notes']  = stripslashes($data['notes']);
		$data['message'] = stripslashes(stripslashes($data['message']));
		$html = $app->render('voucher_details.html', $data);
		echo $html;
	}

	public function put_actions($params) {
		global $app;

		$html = $app->render('actions.html', $params);
		echo $html;
	}

	public function put_product($params) {
		global $app;

		$html = $app->render('product.html', $params);
		echo $html;
	}

	public function put_total($params) {
		global $app;

		$html = $app->render('total.html', $params);
		echo $html;
	}

	public function put_preview($data) {
		global $app;

		$data['still_voucher_header'] = $app->getVoucherHeader($data['promo']);

        if (stripslashes($data['promo']) == "Mother's Day") {
            $data['intro'] = "<p style='margin: 1em 0; line-height: 2;'>This voucher is a gift to you from <strong>" . $data['fname_sender'] . " " . $data['lname_sender'] . "</strong> and entitles you to: 1 hour Massage of your choice, a 30 minute customised facial and a box of Still Beauty tea.</p>";
            $data['redeem'] = "<p style='margin: 1em 0; line-height: 2;'>Either jump onto our website and request a booking, or call Joanna 0488 416 555.</p>";

        } else {
            $data['intro'] = "<p style='margin: 1em 0; line-height: 2;'>This voucher is a <strong>$" . number_format($data['value'], 2) . "</strong> gift to you from <strong>" . $data['fname_sender'] . " " . $data['lname_sender'] . "</strong> that may be put toward any product or treatment from Still Beauty!</p>";
            $data['redeem'] = "<p style='margin: 1em 0; line-height: 2;'>Visit www.stillbeauty.com.au choose what you'd like to do with your voucher.<br>You can make an online booking request for a treatment, or call Joanna on 0488 416 555.<br>To arrange product purchases using this voucher, please call Joanna.</p><p style='margin: 1em 0; line-height: 2;'>To arrange product purchases using this voucher, please call Joanna.</p>";
        }

		if ($data['delivery'] == 'email-sender') {
			$data['fname'] = $data['fname_sender'];
			$data['lname'] = $data['lname_sender'];
			$data['email'] = $data['email_sender'];
		} else {
			$data['fname'] = $data['fname_receiver'];
			$data['lname'] = $data['lname_receiver'];
			$data['email'] = $data['email_receiver'];
		}

		$data['message'] = stripslashes(stripslashes($data['message']));

		$html = $app->render('preview.html', $data);
		echo $html;
	}

	public function send_voucher($data, $promo) {
		global $app;

		$data['still_voucher_header'] = $app->getVoucherHeader($promo);

		$baseurl = dirname(__FILE__);
        $filepath = $baseurl.'/../assets/json/email.json';
        $string = file_get_contents($filepath);
        $record = json_decode($string,true);

        if (stripslashes($promo) == "Mother's Day") {
            $data['intro'] = "<p style='margin: 1em 0; line-height: 2;'>This voucher is a gift to you from <strong>" . $data['fname_sender'] . " " . $data['lname_sender'] . "</strong> and entitles you to: 1 hour Massage of your choice, a 30 minute customised facial and a box of Still Beauty tea.</p>";
            $data['redeem'] = "<p style='margin: 1em 0; line-height: 2;'>Either jump onto our website and request a booking, or call Joanna 0488 416 555.</p>";

        } else {
            $data['intro'] = "<p class='margin: 1em 0; line-height: 2;'>This voucher is a <strong>$" . number_format($data['value'], 2) . "</strong> gift to you from <strong>" . $data['fname_sender'] . " " . $data['lname_sender'] . "</strong> that may be put toward any product or treatment from Still Beauty!</p>";
            $data['redeem'] = "<p class='margin: 1em 0; line-height: 2;'>Visit www.stillbeauty.com.au choose what you'd like to do with your voucher.<br>You can make an online booking request for a treatment, or call Joanna on 0488 416 555.<br>To arrange product purchases using this voucher, please call Joanna.</p><p style='margin: 1em 0; line-height: 2;'>To arrange product purchases using this voucher, please call Joanna.</p>";
        }


		if ($data['delivery'] == 'email-sender') {
			$fname = $data['fname_sender'];
			$lname = $data['lname_sender'];
			$email = $data['email_sender'];
		} else {
			$fname = $data['fname_receiver'];
			$lname = $data['lname_receiver'];
			$email = $data['email_receiver'];
		}

		$data['message'] = stripslashes(stripslashes($data['message']));

		$html = $app->render('voucher_email.html', $data);

		$headers =  "From: " . $record['email']['fromName'] . " <" . $record['email']['from'] . ">\r\n". 
					"Bcc: " . $record['email']['cc'] . "\r\n" .
					"MIME-Version: 1.0" . "\r\n" . 
					"Content-type: text/html; charset=UTF-8" . "\r\n";
		
		$app->mail(array(
				'to' => $lname . ' ' . $fname . '<' . $email . '>',
				'headers' => $headers,
				'subject' => $data['fname_receiver'].', you have got a new gift voucher from Still Beauty!',
				'content' => $html
			));

        /*		*/
	}

	public function admin_page() {
		global $wpdb, $app;
		$list = new TransactionsList();
		$flash   = "";
		$error   = "";

		echo '<div class="wrap">';
		echo '<div id="icon-index"  class="icon32"><br /></div>';
		echo '<h2><img src="' . STILLBEAUTY_LOGO_URL . '" style="vertical-align: middle; height: 50px;" />&nbsp;Transactions</h2>';


		if ($_REQUEST['action'] == 'update-transaction') {
			$wpdb->update('sb_transactions', 
						  array('notes' => $_REQUEST['notes'], 'status' => $_REQUEST['status']),
						  array('id' => $_REQUEST['id']),
						  array('%s', '%s'),
						  array('%d'));

		} elseif ($_REQUEST['action'] == 'delete-transaction') {
			$wpdb->delete('sb_transactions',
						  array('id' => $_REQUEST['id']),
						  array('%d'));
		} elseif ($_REQUEST['action'] == 'send-voucher') {
			$wpdb->update('sb_transactions', 
						  array('notes' => $_REQUEST['notes'], 'status' => 'Dispatched'),
						  array('id' => $_REQUEST['id']),
						  array('%s', '%s'),
						  array('%d'));

			$tx = $wpdb->get_row('SELECT * FROM sb_transactions WHERE id = '.$_REQUEST['id'].' LIMIT 1');
			$data = unserialize($tx->tx_details);

			self::send_voucher($data, $tx->promo);
		} 

		if ($_REQUEST['action'] == 'view-transaction') {

			$tx = $wpdb->get_row('SELECT * FROM sb_transactions WHERE id = '.$_REQUEST['id'].' LIMIT 1');
			$data = unserialize($tx->tx_details);

			echo "<form action='' method='post'>";

			self::put_overview($tx);

			if ($tx->type == 'Voucher') {

				self::put_voucher($data);

				$sendit = '';
				if (strncmp($data['delivery'], 'email', 5) == 0) {
					$sendit = '&nbsp;&nbsp;&nbsp;<a href="?page=' . $_REQUEST['page'] . '&action=preview-voucher&id=' . $_REQUEST['id'] . '" class="button button-secondary">Send it</a>';
				}

				self::put_actions(array(
					'notes' => stripslashes($tx->notes),
					'page'  => $_REQUEST['page'],
					'id'    => $_REQUEST['id'],
					'action'=> 'update-transaction',
					'label' => 'Update',
					'display-notes' => 'block',
					'sendit'=> $sendit
				));

			} elseif ($tx->type == 'Products') {

				echo "<h3>Products</h3>";

				$total = 0.0;
				foreach($data as $d) {
					$price = intval($d['quantity']) * floatval($d['product']['price']);
					$total += $price;
					self::put_product(array(
						'src'   => $d['product']['src'],
						'type'  => $d['product']['type'],
						'name'  => $d['product']['name'],
						'qty'   => $d['quantity'],
						'price' => '$' . number_format($price, 2)
					));
				}

				self::put_total(array('total' => '$' . number_format($total, 2)));
				self::put_actions(array(
					'notes' => stripslashes($tx->notes),
					'page'  => $_REQUEST['page'],
					'id'    => $_REQUEST['id'],
					'action'=> 'update-transaction',
					'label' => 'Update',
					'display-notes' => 'block',
					'sendit'=> ''
				));
			}

			echo "</form>";

		} elseif ($_REQUEST['action'] == 'remove-transaction') {
			$tx = $wpdb->get_row('SELECT * FROM sb_transactions WHERE id = '.$_REQUEST['id'].' LIMIT 1');
			$data = unserialize($tx->tx_details);

			echo "<form action='' method='post'>";

			self::put_summary($tx);

			self::put_actions(array(
				'notes' => '',
				'page'  => $_REQUEST['page'],
				'id'    => $_REQUEST['id'],
				'action'=> 'delete-transaction',
				'label' => 'Delete',
				'display-notes' => 'none',
				'sendit'=> ''
			));			

			echo "</form>";
		} elseif ($_REQUEST['action'] == 'preview-voucher') {

			$tx = $wpdb->get_row('SELECT * FROM sb_transactions WHERE id = '.$_REQUEST['id'].' LIMIT 1');
			$data = unserialize($tx->tx_details);

			echo "<h3>Preview</h3>";
			
			echo "<form action='' method='post'>";

			$data['promo'] = $tx->promo;

			self::put_preview($data);

			self::put_actions(array(
				'notes' => stripslashes($tx->notes),
				'page'  => $_REQUEST['page'],
				'id'    => $_REQUEST['id'],
				'action'=> 'send-voucher',
				'label' => 'Send voucher',
				'display-notes' => 'block',
				'sendit'=> ''
			));			

			echo "</form>";	
		} else {
			echo '<form id="tx-filter" method="get">';
			echo '<input type="hidden" name="page" value="' . $_REQUEST['page'] . '" />';
			$list->prepare_items();
			$list->search_box( '', '');
			$list->display();
			echo '</form>';			
		}


		echo '</div> <!-- .wrap -->';
	}

	public function add_admin_menu() {

		add_menu_page ('Still Beauty', 'Still Beauty', 'activate_plugins', 'stillbeauty', array('StillBeautyAdminPage', 'admin_page'), null );

	}



}

add_action('admin_menu', array('StillBeautyAdminPage', 'add_admin_menu'));
?>