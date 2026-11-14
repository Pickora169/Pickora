<?php
/**
 * Newsletter form.
 *
 * @package pickora
 */
$status = isset( $_GET['pk_sub'] ) ? sanitize_key( wp_unslash( $_GET['pk_sub'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$msgs   = array(
	'ok'      => array( 'Thanks for subscribing.', '' ),
	'invalid' => array( 'Please enter a valid email address.', 'err' ),
	'wait'    => array( 'Please wait a few seconds before trying again.', 'err' ),
	'error'   => array( 'Something went wrong. Please try again.', 'err' ),
);
?>
<section class="sec news" id="newsletter">
	<div class="wrap">
		<h2>Make Smarter Shopping Decisions.</h2>
		<p>Get useful product discoveries, buying guides and carefully selected recommendations.</p>
		<?php if ( isset( $msgs[ $status ] ) ) : ?>
			<div class="note <?php echo esc_attr( $msgs[ $status ][1] ); ?>" role="status"><?php echo esc_html( $msgs[ $status ][0] ); ?></div>
		<?php endif; ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="pickora_subscribe">
			<?php wp_nonce_field( 'pickora_subscribe', 'pk_nonce' ); ?>
			<input class="hp" type="text" name="pk_website" tabindex="-1" autocomplete="off" aria-hidden="true">
			<label class="sr" for="pk_email">Email address</label>
			<input type="email" id="pk_email" name="pk_email" placeholder="you@example.com" required>
			<button class="btn" type="submit">Subscribe</button>
		</form>
	</div>
</section>
