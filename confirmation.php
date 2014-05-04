<?php
/*
 * Template name: Confirmation
 */

get_header();

?>
	<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
        <div class="span10 page">
            <h1><?php the_title(); ?></h1>
            <?php

            the_content();

            global $app;


            $message = (!empty($_POST['message'])) ? '<p><strong>Message</strong></p><p>' . $_POST['message'] . '</p>' : '';

            switch ($_POST['delivery']) {
                case 'email-sender':
                    $_POST['delivery-short'] = "email-sender";
                    $delivery = "This Voucher will be <strong>emailed</strong> to <strong>you</strong>.";
                    break;
                case 'email-receiver':
                    $_POST['delivery-short'] = "email-receiver";
                    $delivery = "This Voucher will be <strong>emailed</strong> to <strong>".$_POST['fname_receiver']."</strong>.";
                    break;
                case 'post-sender':
                    $_POST['delivery-short'] = "post-sender";
                    $delivery = "This Voucher will be <strong>posted</strong> to <strong>".$_POST['fname_sender']."</strong> of <strong>".$_POST['address_sender']."</strong>";
                    break;
                case 'post-receiver':
                    $_POST['delivery-short'] = "post-receiver";
                    $delivery = "This Voucher will be <strong>posted</strong> to <strong>".$_POST['fname_receiver']."</strong> of <strong>".$_POST['address_receiver']."</strong>";
                    break;
            }

            $expresspost = (isset($_POST['express_post'])) ? '<p>* You have chosen Express post. An additional $6 will be added to the total.</p>' : '';
            $expresspostfields = (isset($_POST['express_post']) && $deliveryshort == "post-sender") ? '<input type="hidden" name="item_name_2" value="Express Post"><input type="hidden" name="amount_2" value="6.00">' : '';
            $total = (isset($_POST['express_post']) && $_POST['delivery-short'] == "post-sender") ? 6.0 + floatval($_POST['value']) : floatval($_POST['value']);

            if (stripslashes($_POST['promo']) == "Mother's Day") {
                $intro = "<p>This voucher is a gift to you from <strong>" . $_POST['fname_sender'] . " " . $_POST['lname_sender'] . "</strong> and entitles you to: 1 hour Massage of your choice, a 30 minute customised facial and a box of Still Beauty tea.</p>";
                $redeem = "<p>Either jump onto our website and request a booking, or call Joanna 0488 416 555.</p>";
            } else {
                $intro = "<p>This voucher is a <strong>$" . number_format($_POST['value'], 2) . "</strong> gift to you from <strong>" . $_POST['fname_sender'] . " " . $_POST['lname_sender'] . "</strong> that may be put toward any product or treatment from Still Beauty!</p>";
                $redeem = "<p>Visit www.stillbeauty.com.au choose what you'd like to do with your voucher.<br>You can make an online booking request for a treatment, or call Joanna on 0488 416 555.<br>To arrange product purchases using this voucher, please call Joanna.</p>";
            }

            $params = array(
                    'fname_sender' => $_POST['fname_sender'],
                    'lname_sender' => $_POST['lname_sender'],
                    'value' => number_format($_POST['value'], 2),
                    'tel_sender' => $_POST['tel_sender'],
                    'email_sender' => $_POST['email_sender'],
                    'address_sender' => $_POST['address_sender'],
                    'fname_receiver' => $_POST['fname_receiver'],
                    'lname_receiver' => $_POST['lname_receiver'],
                    'gender' => $_POST['gender'],
                    'tel_receiver' => $_POST['tel_receiver'],
                    'email_receiver' => $_POST['email_receiver'],
                    'address_receiver' => $_POST['address_receiver'],
                    'message' => $message,
                    'expresspost' => $expresspost,
                    'expresspost_fields' => $expresspostfields,
                    'delivery' => $delivery,
                    'promo' => stripslashes($_POST['promo']),
                    'custom' => $_POST['voucher_code'],
                    'still_voucher_header' => $app->getVoucherHeader($_POST['promo']),
                    'enddate' => date('jS \of F, Y', strtotime('+1 year')),
                    'startdate' => date("F j, Y, g:i a"),
                    'paypalurl' => $app->getPayPalUrl(),
                    'thankyouurl' => $app->getThankyouUrl(),
                    'total' => $total,
                    'intro' => $intro,
                    'redeem' => $redeem
                );

            //$app->saveVoucher($_POST);


            $html = $app->render('confirmation.html', $params);
            echo $html;

            ?>
        </div>
    <?php endwhile; ?>
    <?php endif; ?>
<?php
wp_localize_script( 'stillbeauty', 'confirmation', array(
                    'tx' => array( 
                                    'fname_sender' => $_POST['fname_sender'],
                                    'lname_sender' => $_POST['lname_sender'],
                                    'value' => $_POST['value'],
                                    'tel_sender' => $_POST['tel_sender'],
                                    'email_sender' => $_POST['email_sender'],
                                    'address_sender' => $_POST['address_sender'],
                                    'lname_receiver' => $_POST['lname_receiver'],
                                    'fname_receiver' => $_POST['fname_receiver'],
                                    'gender' => $_POST['gender'],
                                    'tel_receiver' => $_POST['tel_receiver'],
                                    'email_receiver' => $_POST['email_receiver'],
                                    'address_receiver' => $_POST['address_receiver'],
                                    'message' => $_POST['message'],
                                    'express_post' => $expresspost,
                                    'expresspost_fields' => $expresspostfields,
                                    'delivery' => $_POST['delivery'],
                                    'startdate' => date("F j, Y, g:i a"),
                                    'enddate' => date('jS \of F, Y', strtotime('+1 year'))
                                ),
                    'promo'  => stripslashes($_POST['promo']),
                    'custom' => $_POST['voucher_code']));  
get_footer();
?>