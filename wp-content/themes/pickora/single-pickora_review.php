<?php
/**
 * Single review template. Sections appear only when their fields are filled.
 *
 * @package pickora
 */
get_header();
while ( have_posts() ) :
	the_post();
	$id     = get_the_ID();
	$term   = pickora_primary_term( $id );
	$m      = function ( $k ) use ( $id ) { return get_post_meta( $id, $k, true ); };
	$pros   = pickora_lines( $m( '_pk_pros' ) );
	$cons   = pickora_lines( $m( '_pk_cons' ) );
	$specs  = pickora_pairs( $m( '_pk_specs' ) );
	$faqs   = pickora_pairs( $m( '_pk_faqs' ) );
	$buy    = pickora_lines( $m( '_pk_who_buy' ) );
	$avoid  = pickora_lines( $m( '_pk_who_avoid' ) );
	$tested = 'tested' === $m( '_pk_method' );
	$link   = rawurlencode( get_permalink() );
	$ttl    = rawurlencode( get_the_title() );
	$alts   = array_filter( array_map( 'absint', explode( ',', (string) $m( '_pk_alt_ids' ) ) ) );
	?>
<article class="article">
	<div class="wrap narrow">
		<nav class="crumbs" aria-label="Breadcrumb">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> /
			<a href="<?php echo esc_url( get_post_type_archive_link( 'pickora_review' ) ); ?>">Reviews</a>
			<?php if ( $term ) : ?>/ <a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a><?php endif; ?>
		</nav>
		<span class="badge <?php echo $tested ? 'badge--sage' : ''; ?>"><?php echo $tested ? 'Hands-on tested' : 'Research-based review'; ?></span>
		<h1 style="margin-top:12px"><?php the_title(); ?></h1>
		<p class="meta">
			By <?php the_author(); ?> &middot; Published <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
			<?php if ( pickora_updated_timestamp() - get_the_date( 'U' ) > DAY_IN_SECONDS ) : ?>&middot; Updated <?php echo esc_html( wp_date( get_option( 'date_format' ), pickora_updated_timestamp() ) ); ?><?php endif; ?>
		</p>
		<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'large', array( 'style' => 'border-radius:12px;margin:18px 0' ) ); } ?>
		<p class="disclose">This article may contain affiliate links. If you buy through them, PICKORA may earn a commission at no extra cost to you. <a href="<?php echo esc_url( pickora_page_url( 'affiliate-disclosure' ) ); ?>">Learn more</a>.</p>

		<?php if ( has_excerpt() ) : ?><p style="font-size:1.15rem"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>

		<?php if ( $m( '_pk_verdict' ) || $pros || $cons ) : ?>
		<div class="verdict">
			<h2 style="margin-top:0;font-size:1.3rem">Quick Verdict</h2>
			<p><?php echo esc_html( $m( '_pk_verdict' ) ); ?></p>
			<?php if ( $m( '_pk_best_for' ) ) : ?><p><strong>Best suited for:</strong> <?php echo esc_html( $m( '_pk_best_for' ) ); ?></p><?php endif; ?>
			<?php if ( $pros ) : ?><p><strong>Main strength:</strong> <?php echo esc_html( $pros[0] ); ?></p><?php endif; ?>
			<?php if ( $cons ) : ?><p><strong>Main limitation:</strong> <?php echo esc_html( $cons[0] ); ?></p><?php endif; ?>
			<?php pickora_affiliate_button( $id ); ?>
		</div>
		<?php endif; ?>

		<?php if ( $specs ) : ?>
		<h2>Product Specifications</h2>
		<table class="specs"><tbody>
			<?php foreach ( $specs as $row ) : ?><tr><th scope="row"><?php echo esc_html( $row[0] ); ?></th><td><?php echo esc_html( $row[1] ); ?></td></tr><?php endforeach; ?>
		</tbody></table>
		<?php endif; ?>

		<div class="body"><?php the_content(); ?></div>

		<?php if ( $pros || $cons ) : ?>
		<h2>Pros and Cons</h2>
		<div class="two">
			<div class="pc pc--pro"><h3>Pros</h3><?php pickora_list( $pros ); ?></div>
			<div class="pc pc--con"><h3>Cons</h3><?php pickora_list( $cons ); ?></div>
		</div>
		<?php endif; ?>

		<?php if ( $m( '_pk_experience' ) ) : ?><h2>Performance and Experience</h2><p><?php echo nl2br( esc_html( $m( '_pk_experience' ) ) ); ?></p><?php endif; ?>

		<?php if ( $m( '_pk_price_note' ) || pickora_affiliate_url( $id ) ) : ?>
		<h2>Price and Value</h2>
		<?php if ( $m( '_pk_price_note' ) ) : ?><p><?php echo esc_html( $m( '_pk_price_note' ) ); ?></p><?php endif; ?>
		<p><?php pickora_affiliate_button( $id ); ?></p>
		<?php endif; ?>

		<?php if ( $buy ) : ?><h2>Who Should Buy This Product?</h2><?php pickora_list( $buy ); endif; ?>
		<?php if ( $avoid ) : ?><h2>Who Should Avoid It?</h2><?php pickora_list( $avoid ); endif; ?>

		<?php if ( $alts ) : ?>
		<h2>Alternatives</h2>
		<ul>
			<?php foreach ( $alts as $aid ) : if ( 'publish' === get_post_status( $aid ) ) : ?><li><a href="<?php echo esc_url( get_permalink( $aid ) ); ?>"><?php echo esc_html( get_the_title( $aid ) ); ?></a></li><?php endif; endforeach; ?>
		</ul>
		<?php endif; ?>

		<?php if ( $faqs ) : ?>
		<h2>Frequently Asked Questions</h2>
		<?php foreach ( $faqs as $f ) : ?><details><summary><?php echo esc_html( $f[0] ); ?></summary><p><?php echo esc_html( $f[1] ); ?></p></details><?php endforeach; ?>
		<?php endif; ?>

		<?php if ( pickora_affiliate_url( $id ) ) : ?>
		<h2>Final Verdict</h2>
		<p><?php echo esc_html( $m( '_pk_verdict' ) ); ?></p>
		<p><?php pickora_affiliate_button( $id ); ?></p>
		<?php endif; ?>

		<div class="share">
			<strong>Share:</strong>
			<a href="https://wa.me/?text=<?php echo $ttl . '%20' . $link; // phpcs:ignore WordPress.Security.EscapeOutput ?>" target="_blank" rel="noopener">WhatsApp</a>
			<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $link; // phpcs:ignore WordPress.Security.EscapeOutput ?>" target="_blank" rel="noopener">Facebook</a>
			<a href="https://twitter.com/intent/tweet?url=<?php echo $link; // phpcs:ignore WordPress.Security.EscapeOutput ?>&text=<?php echo $ttl; // phpcs:ignore WordPress.Security.EscapeOutput ?>" target="_blank" rel="noopener">X</a>
		</div>
	</div>

	<?php $rel = pickora_related( $id, 4 ); if ( $rel->have_posts() ) : ?>
	<section class="sec sec--ivory"><div class="wrap">
		<h2>Related Articles</h2>
		<div class="grid"><?php while ( $rel->have_posts() ) : $rel->the_post(); get_template_part( 'template-parts/review-card' ); endwhile; wp_reset_postdata(); ?></div>
	</div></section>
	<?php endif; ?>
</article>
<?php
endwhile;
get_footer();
