<?php
/**
 * Site footer.
 *
 * @package pickora
 */
$legal = array(
	'Contact Us'          => 'contact-us',
	'About Us'            => 'about-us',
	'Privacy Policy'      => 'privacy-policy',
	'Terms & Conditions'  => 'terms-and-conditions',
	'Affiliate Disclosure'=> 'affiliate-disclosure',
	'Editorial Policy'    => 'editorial-policy',
	'Our Review Methodology' => 'our-review-methodology',
);
$cats = get_terms( array( 'taxonomy' => 'review_category', 'hide_empty' => false ) );
?>
</main>
<footer class="site-footer">
	<div class="wrap">
		<div class="fcols">
			<div>
				<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo pickora_logo_icon(); // phpcs:ignore WordPress.Security.EscapeOutput ?><span>PICKORA</span></a>
				<p><strong>Better Picks. Smarter Living.</strong></p>
				<p>Researched product reviews, honest comparisons and practical buying guides for Indian shoppers.</p>
				<div class="social"><?php echo pickora_social_links(); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			</div>
			<div>
				<h4>Explore</h4>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
					<li><a href="<?php echo esc_url( get_post_type_archive_link( 'pickora_review' ) ); ?>">Reviews</a></li>
					<li><a href="<?php echo esc_url( pickora_type_url( 'best-picks' ) ); ?>">Best Picks</a></li>
					<li><a href="<?php echo esc_url( pickora_type_url( 'comparisons' ) ); ?>">Comparisons</a></li>
					<li><a href="<?php echo esc_url( pickora_type_url( 'buying-guides' ) ); ?>">Buying Guides</a></li>
					<li><a href="<?php echo esc_url( pickora_type_url( 'deals' ) ); ?>">Deals</a></li>
				</ul>
			</div>
			<div>
				<h4>Categories</h4>
				<ul>
				<?php
				if ( $cats && ! is_wp_error( $cats ) ) {
					foreach ( $cats as $c ) {
						printf( '<li><a href="%s">%s</a></li>', esc_url( get_term_link( $c ) ), esc_html( $c->name ) );
					}
				}
				?>
				</ul>
			</div>
			<div>
				<h4>PICKORA</h4>
				<ul>
				<?php foreach ( $legal as $label => $slug ) : ?>
					<li><a href="<?php echo esc_url( pickora_page_url( $slug ) ); ?>"><?php echo esc_html( $label ); ?></a></li>
				<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<div class="legal">
			<p>As an Amazon Associate I earn from qualifying purchases.</p>
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> PICKORA. All rights reserved. PICKORA is not affiliated with Amazon.</p>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
