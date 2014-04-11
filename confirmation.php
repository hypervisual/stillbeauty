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
            $total = (isset($_POST['express_post']) && $deliveryshort == "post-sender") ? 6.0 + floatval($_POST['value']) : floatval($_POST['value']);

            $params = array(
                    '%fname_sender%' => $_POST['fname_sender'],
                    '%lname_sender%' => $_POST['lname_sender'],
                    '%value%' => number_format($_POST['value'], 2),
                    '%tel_sender%' => $_POST['tel_sender'],
                    '%email_sender%' => $_POST['email_sender'],
                    '%address_sender%' => $_POST['address_sender'],
                    '%fname_receiver%' => $_POST['fname_receiver'],
                    '%lname_receiver%' => $_POST['lname_receiver'],
                    '%gender%' => $_POST['gender'],
                    '%tel_receiver%' => $_POST['tel_receiver'],
                    '%email_receiver%' => $_POST['email_receiver'],
                    '%address_receiver%' => $_POST['address_receiver'],
                    '%message%' => $message,
                    '%expresspost%' => $expresspost,
                    '%expresspost_fields%' => $expresspostfields,
                    '%delivery%' => $delivery,
                    '%custom%' => $_POST['voucher_code'],
                    '%still_voucher_header%' => $app->getVoucherHeader(),
                    '%enddate%' => date('jS \of F, Y', strtotime('+1 year')),
                    '%startdate%' => date("F j, Y, g:i a"),
                    '%paypalurl%' => $app->getPayPalUrl(),
                    '%thankyouurl%' => $app->getThankyouUrl(),
                    '%ipnurl%' => $app->getIpnUrl(),
                    '%total%' => $total
                );

            $app->saveVoucher($_POST);

            $html = $app->render('/templates/confirmation.html', $params);
            echo $html;

            ?>
        </div>
    <?php endwhile; ?>
    <?php endif; ?>
<?php
get_footer();
?>