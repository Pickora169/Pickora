<?php
/**
 * Single guide / comparison / best picks template.
 *
 * @package pickora
 */
get_header();
while ( have_posts() ) :
	the_post();
	$id   = get_the_ID();
	$term = pickora_primary_term( $id );
	$link = rawurlencode( get_permalink() );
	$ttl  = rawurlencode( get_the_title() );
	?>
<article class="article">
	<div class="wrap narrow">
		<nav class="crumbs" aria-label="Breadcrumb">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
			<?php if ( $term ) : ?>/ <a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a><?php endif; ?>
		</nav>
		<h1><?php the_title(); ?></h1>
		<p class="meta">By <?php the_author(); ?> &middot; Published <?php echo esc_html( get_the_date() ); ?><?php if ( pickora_updated_timestamp() - get_the_date( 'U' ) > DAY_IN_SECONDS ) : ?> &middot; Updated <?php echo esc_html( wp_date( get_option( 'date_format' ), pickora_updated_timestamp() ) ); ?><?php endif; ?></p>
		<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'large', array( 'style' => 'border-radius:12px;margin:18px 0' ) ); } ?>
		<p class="disclose">This article may contain affiliate links. If you buy through them, PICKORA may earn a commission at no extra cost to you. <a href="<?php echo esc_url( pickora_page_url( 'affiliate-disclosure' ) ); ?>">Learn more</a>.</p>
		<div class="body"><?php the_content(); ?></div>
		<div class="share"><strong>Share:</strong>
			<a href="https://wa.me/?text=<?php echo $ttl . '%20' . $link; // phpcs:ignore WordPress.Security.EscapeOutput ?>" target="_blank" rel="noopener">WhatsApp</a>
			<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $link; // phpcs:ignore WordPress.Security.EscapeOutput ?>" target="_blank" rel="noopener">Facebook</a>
		</div>
	</div>
	<?php
	$args = array( 'post_type' => 'pickora_guide', 'posts_per_page' => 3, 'post__not_in' => array( $id ) );
	if ( $term ) {
		$args['tax_query'] = array( array( 'taxonomy' => 'guide_type', 'field' => 'term_id', 'terms' => $term->term_id ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	}
	$rel = new WP_Query( $args );
	if ( $rel->have_posts() ) :
		?>
	<section class="sec sec--ivory"><div class="wrap"><h2>Related Articles</h2>
		<div class="grid"><?php while ( $rel->have_posts() ) : $rel->the_post(); get_template_part( 'template-parts/review-card' ); endwhile; wp_reset_postdata(); ?></div>
	</div></section>
	<?php endif; ?>
</article>
	<?php
endwhile;
get_footer();
