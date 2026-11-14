<?php
/**
 * Review card.
 *
 * @package pickora
 */
$term = pickora_primary_term( get_the_ID() );
?>
<article class="card">
	<a class="ph" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'pickora-card', array( 'loading' => 'lazy' ) ); } ?>
	</a>
	<div class="bd">
		<div class="meta"><?php if ( $term ) : ?><span class="cat"><?php echo esc_html( $term->name ); ?></span> &middot; <?php endif; ?><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></div>
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
		<a class="more" href="<?php the_permalink(); ?>">Read more</a>
	</div>
</article>
