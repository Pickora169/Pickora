<?php
/**
 * Review and category archives, and search results.
 *
 * @package pickora
 */
get_header();
?>
<section class="sec"><div class="wrap">
	<div class="sec-head"><h1>
	<?php
	if ( is_search() ) {
		printf( 'Search results for &ldquo;%s&rdquo;', esc_html( get_search_query() ) );
	} elseif ( is_tax() ) {
		single_term_title();
	} else {
		post_type_archive_title();
	}
	?>
	</h1></div>
	<?php if ( is_tax() && term_description() ) : ?><div class="meta" style="margin-bottom:24px"><?php echo wp_kses_post( term_description() ); ?></div><?php endif; ?>
	<?php if ( have_posts() ) : ?>
	<div class="grid"><?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/review-card' ); endwhile; ?></div>
	<div class="pagination"><?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?></div>
	<?php else : ?>
	<p>Nothing found. Try a different search.</p><?php get_search_form(); ?>
	<?php endif; ?>
</div></section>
<?php get_footer(); ?>
