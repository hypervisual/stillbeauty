<?php
/*
Template Name: PayPalIpn
*/
?>
<!doctype html>
<html>
<head>
	<title>Still Beauty &raquo; Access Denied</title>
	<link href="<?php bloginfo('template_url'); ?>/assets/css/404.css" rel="stylesheet" />
</head>

<body>
	<div>
		<div>
			<h4>Access<br />denied</h4>
			<h5><a href="/">Home</a></h5>

			<?php

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				$data = array();
        		$data['cmd'] = "_notify-validate";
	 
		        foreach($_POST as $key => $value) {
		            $data[$key] = $value;
		        }

		        $url = "https://www.paypal.com/cgi-bin/webscr";

		        $ch = curl_init ($url);
		        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		        curl_setopt ($ch, CURLOPT_HEADER, 0);
		        curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
		        curl_setopt ($ch, CURLOPT_POST, 1);
		        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
		        curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);

		        $result = curl_exec ($ch);
		        $error = curl_error($ch);

		        $baseurl = dirname(__FILE__);
	  
	        	if($result == "VERIFIED") {
		            $trandetails = file_get_contents($baseurl.'/tmp/'.$data['custom'].'.txt');
		            $custom = unserialize($trandetails);

		            $filepath = $baseurl.'/assets/json/email.json';
		            $string = file_get_contents($filepath);
		            $record = json_decode($string,true);

            		if($custom['delivery-short']=="email-sender" || $custom['delivery-short']=="email-receiver") {

                		if($custom['delivery-short']=="email-sender" && isset($custom['email_sender'])) {
                    		$email = $custom['email_sender'];
                    		$messagetoadmin = "This Voucher has been delivered via email to the Sender (".$email.")";
                		} elseif($custom['delivery-short']=="email-receiver" && isset($custom['email_receiver'])) {
                    		$email = $custom['email_receiver'];
                    		$messagetoadmin = "This voucher has been delivered via email to the Receiver (".$email.")";
                		}

                		$mailcontent = '<div style="border:2px dashed #ddd; padding:25px"><table width="100%"><tr><td width="20%"><img src="'.base_url().'img/still_voucher_header.jpeg" width="100%"></td>';
                		$mailcontent .= '</tr><tr><td colspan="2">&nbsp;</td></tr><tr><td colspan="2">';
                		$mailcontent .= '<p>'.$custom['fname_receiver'].',</p>';
                		$mailcontent .= '<p>This voucher is a <strong>$'.number_format($custom['value'],2,'.',',').'</strong> gift to you from <strong>'.$custom['fname_sender'].' '.$custom['lname_sender'].'</strong> that may be put toward any product or treatment from Still Beauty!</p>';
                
                		if(isset($custom['message']) && $custom['message']!='') :
                    		$mailcontent .= '<p><strong>Message</strong></p><p>'.$custom['message'].'</p><br>';
                		endif;

                		$mailcontent .= '<p><strong>To Redeem</strong></p><p>Visit www.stillbeauty.com.au choose what you\'d like to do with your voucher.<br>You can make an online booking request for a treatment, or call Joanna on 0488 416 555.<br>To arrange product purchases using this voucher, please call Joanna.</p><p><strong>Validity</strong></p><p>This voucher remains valid until <strong>'.date('jS \of F, Y', strtotime('+1 year')).'</strong>.</p></td></tr><tr><td colspan="2">&nbsp;</td></tr><tr><td colspan="2">';
                		$mailcontent .= '<p>From '.$custom['fname_sender'].' '.$custom['lname_sender'].' / '.$custom['tel_sender'].'</p><p>To '.$custom['fname_receiver'].' '.$custom['lname_receiver'].' / '.$custom['gender'].'</p>
                   						 <p>Issued '.date("F j, Y, g:i a").'</p></td></tr></table></div>';

		                // 		$this->load->helper('phpmailer');
		                // send_email($record['email']['fromName'], $email, $record['email']['from'], $custom['fname_receiver'].', You have got a new Gift Voucher from Still Beauty!', $mailcontent);
						$headers =  "From: " . $record['email']['fromName'] . " <" . $record['email']['from'] . ">\r\n". 
									"Bcc: " . $record['email']['cc'] . "\r\n" .
									"MIME-Version: 1.0" . "\r\n" . 
									"Content-type: text/html; charset=UTF-8" . "\r\n";

						wp_mail($email, $custom['fname_receiver'].", you have got a new gift voucher from Still Beauty!", $mailcontent, $headers);

		                $mailcontent = "<br><br><strong>".$messagetoadmin."</strong><br><br><hr>".$mailcontent;
						wp_mail($record['email']['to'], $custom['fname_receiver'].", you have got a new gift voucher from Still Beauty!", $mailcontent, $headers);
	            	} elseif($custom['delivery-short']=="post-sender" || $custom['delivery-short']=="post-receiver") {

	                	if($custom['delivery-short']=="post-sender" && isset($custom['address_sender'])) {
	                    	$messagetoadmin = "This voucher must be posted to the sender (".$custom['fname_sender'].' '.$custom['lname_sender'].") at ".$custom['address_sender'];
	                	} elseif($custom['delivery-short']=="post-receiver" && isset($custom['address_receiver'])) {
	                    	$messagetoadmin = "This voucher must be posted to the Receiver (".$custom['fname_receiver'].' '.$custom['lname_receiver'].") at ".$custom['address_receiver'];
	                	}

		                if($custom['express_post']=="6") {
		                    $messagetoadmin .= " via Express Post";
		                }

		                $mailcontent = '<div style="border:2px dashed #ddd; padding:25px"><table width="100%"><tr><td width="20%"><img src="'.base_url().'img/Still-Beauty-Clouds-Logo.png" width="140"></td>';
		                $mailcontent .= '<td><h3>Gift Voucher</h3></td></tr><tr><td colspan="2">&nbsp;</td></tr><tr><td colspan="2">';
		                $mailcontent .= '<p>'.$custom['fname_receiver'].',</p>';
		                $mailcontent .= '<p>This voucher is a <strong>$'.number_format($custom['value'],2,'.',',').'</strong> gift to you from <strong>'.$custom['fname_sender'].' '.$custom['lname_sender'].'</strong> that may be put toward any product or treatment from Still Beauty!</p>';
		                $mailcontent .= '<p><strong>Message</strong></p><p>'.$custom['message'].'</p><br>';
		                $mailcontent .= '<p><strong>To Redeem</strong></p><p>Visit www.stillbeauty.com.au choose what you\'d like to do with your voucher.<br>You can make an online booking request for a treatment, or call Joanna on 0488 416 555.<br>To arrange product purchases using this voucher, please call Joanna.</p><p><strong>Validity</strong></p><p>This voucher remains valid until <strong>'.date('jS \of F, Y', strtotime('+1 year')).'</strong>.</p></td></tr><tr><td colspan="2">&nbsp;</td></tr><tr><td colspan="2">';
		                $mailcontent .= '<p>From '.$custom['fname_sender'].' '.$custom['lname_sender'].' / '.$custom['tel_sender'].'</p><p>To '.$custom['fname_receiver'].' '.$custom['lname_receiver'].' / '.$custom['gender'].'</p>
		                    <p>Issued '.date("F j, Y, g:i a").'</p></td></tr></table></div>';

		                $mailcontent = "<br><br><strong>".$messagetoadmin."</strong><br><br><hr>".$mailcontent;

						$headers =  "From: " . $record['email']['fromName'] . " <" . $record['email']['from'] . ">\r\n". 
									"Cc: " . $record['email']['cc'] . "\r\n" .
									"MIME-Version: 1.0" . "\r\n" . 
									"Content-type: text/html; charset=UTF-8" . "\r\n";

						wp_mail($record['email']['to'], "Voucher Send Request", $mailcontent, $headers);
	            	}

	            	unlink($baseurl.'/tmp/'.$data['custom'].'.txt');
	        	} elseif($result == "INVALID") {
	            	$message = "There was an issue with your IPN. Log the data to research this further.".$result;
	            	$message .= "<br>".serialize($data)."<br><br>";
	            	$message .= serialize($this->input->post())."<br><br>";

					$headers =  "From: " . $record['email']['fromName'] . " <" . $record['email']['from'] . ">\r\n". 
								"MIME-Version: 1.0" . "\r\n" . 
								"Content-type: text/html; charset=UTF-8" . "\r\n";

					wp_mail("hyperviusal.media@gmail.com", "Still Beauty IPN Failure", $message, $headers);

	        	}


			}


        	?>
		</div>
	</div>
</body>
</html>