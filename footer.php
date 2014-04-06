<?php
global $app;
?>
		<footer class="text-center">
			<?php
			global $app;
			if ($app->getPageId() != 'home') :
			?>
			<h3>
				<span>MASSAGE</span><span>BEAUTY</span><span>ANYWHERE</span>
			</h3>
			<?php
			endif;
			?>

			<ul id="social-links" class="inline">
				<li>
					<a href="http://www.facebook.com/StillBeautyAU" target="_blank"><span class="ion-social-facebook"></span></a>
				</li>

				<li>
					<a href="https://twitter.com/stillbeautyau" target="_blank"><span class="ion-social-twitter"></span></a>
				</li>

				<li>
					<a href="http://instagram.com/stillbeautyau" target="_blank"><span class="icon-instagram"></span></a>
				</li>

			</ul>


			<div id="copyright" class="text-center uppercase muted">&copy; <?php echo date('Y'); ?> Still Beauty </div>
		</footer>

		<a class="cart" href="#"><span class="cart-total">$0.00</span><i class="cart-items">0</i></a>

		<div class="shopping-cart hide">
			<div class="shopping-cart-top"></div>

			<div class="shopping-cart-list">
				<a href="#" class="close-cart">Close</a>
				<h4>Shopping cart</h4>

				<div class="shopping-cart-content">

					
				</div>
			</div>
		</div>

	</div>  <!-- .container -->


	<div class="modal-cover"></div>
	<div id="modal-message" class="modal-popup modal-message"></div>


<?php
	wp_footer();
?>

</body>
</html>
