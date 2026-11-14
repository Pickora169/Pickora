<?php
/**
 * Fallback template for posts and pages.
 *
 * @package pickora
 */
get_header();
?>
<div class="sec"><div class="narrow article">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<h1><?php the_title(); ?></h1>
	<div class="body"><?php the_content(); ?></div>
<?php endwhile; else : ?>
	<h1>Page not found</h1><p>Try searching instead.</p><?php get_search_form(); ?>
<?php endif; ?>
</div></div>
<?php get_footer(); ?>
