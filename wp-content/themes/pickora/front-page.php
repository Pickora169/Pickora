<?php
/**
 * Homepage. Every section pulls from the database; nothing is hardcoded.
 *
 * @package pickora
 */
get_header();

$feat = pickora_featured_review();
$cats = get_terms( array( 'taxonomy' => 'review_category', 'hide_empty' => false ) );
$latest = new WP_Query( array( 'post_type' => 'pickora_review', 'posts_per_page' => 8, 'ignore_sticky_posts' => true ) );
$picks  = new WP_Query( array( 'post_type' => 'pickora_review', 'posts_per_page' => 4, 'meta_key' => '_pk_editors_pick', 'meta_value' => '1' ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
$reviews_url = get_post_type_archive_link( 'pickora_review' );
?>
<section class="hero">
	<div class="wrap">
		<h1>Find Better Products. Make Smarter Choices.</h1>
		<p>Discover thoughtfully researched product reviews, honest comparisons and practical buying guides to help you choose products worth your money.</p>
		<?php get_search_form(); ?>
		<div class="btns">
			<a class="btn btn--gold" href="<?php echo esc_url( $reviews_url ); ?>">Explore Reviews</a>
			<a class="btn btn--ghost" href="<?php echo esc_url( pickora_type_url( 'buying-guides' ) ); ?>">Browse Buying Guides</a>
		</div>
	</div>
</section>

<?php if ( $feat ) :
	$fid   = $feat->ID;
	$fterm = pickora_primary_term( $fid );
	$fexc  = get_the_excerpt( $feat );
	$verd  = get_post_meta( $fid, '_pk_verdict', true );
	$pros  = pickora_lines( get_post_meta( $fid, '_pk_pros', true ) );
	$cons  = pickora_lines( get_post_meta( $fid, '_pk_cons', true ) );
	$high  = pickora_lines( get_post_meta( $fid, '_pk_highlights', true ) );
	$buy   = pickora_lines( get_post_meta( $fid, '_pk_who_buy', true ) );
	$avoid = pickora_lines( get_post_meta( $fid, '_pk_who_avoid', true ) );
	$tested = 'tested' === get_post_meta( $fid, '_pk_method', true );
	?>
<section class="sec sec--ivory" aria-labelledby="feat-h">
	<div class="wrap">
		<div class="sec-head"><h2 id="feat-h">Featured Review</h2></div>
		<div class="feat">
			<div class="ph"><?php echo get_the_post_thumbnail( $fid, 'large' ); ?></div>
			<div class="bd">
				<span class="badge <?php echo $tested ? 'badge--sage' : ''; ?>"><?php echo $tested ? 'Hands-on tested' : 'Research-based review'; ?></span>
				<div class="meta"><?php if ( $fterm ) : ?><span class="cat"><?php echo esc_html( $fterm->name ); ?></span> &middot; <?php endif; ?><?php echo esc_html( get_the_date( '', $fid ) ); ?></div>
				<h3 style="font-size:1.8rem"><?php echo esc_html( get_the_title( $fid ) ); ?></h3>
				<p><?php echo esc_html( wp_trim_words( $fexc, 36 ) ); ?></p>
				<button type="button" class="btn" data-pk-open>View Review</button>
			</div>
		</div>
	</div>
</section>

<div class="lb" id="pk-lightbox" role="dialog" aria-modal="true" aria-labelledby="lb-title">
	<div class="lb-box">
		<button type="button" class="lb-x" aria-label="Close">&times;</button>
		<div class="lb-top">
			<div class="ph"><?php echo get_the_post_thumbnail( $fid, 'large' ); ?></div>
			<div class="bd">
				<div class="meta"><?php if ( $fterm ) : ?><span class="cat"><?php echo esc_html( $fterm->name ); ?></span><?php endif; ?></div>
				<h2 id="lb-title" style="margin:0"><?php echo esc_html( get_the_title( $fid ) ); ?></h2>
				<p><?php echo esc_html( wp_trim_words( $fexc, 40 ) ); ?></p>
				<?php if ( $verd ) : ?><div class="verdict"><strong>Quick verdict.</strong> <?php echo esc_html( $verd ); ?></div><?php endif; ?>
				<a class="btn" href="<?php echo esc_url( get_permalink( $fid ) ); ?>">Read Full Review</a>
			</div>
		</div>
		<div class="lb-body">
			<?php if ( $pros || $cons ) : ?>
			<div class="two">
				<div class="pc pc--pro"><h3>Pros</h3><?php pickora_list( $pros ); ?></div>
				<div class="pc pc--con"><h3>Cons</h3><?php pickora_list( $cons ); ?></div>
			</div>
			<?php endif; ?>
			<?php if ( $high ) : ?><h3>Highlights</h3><?php pickora_list( $high ); endif; ?>
			<div class="two" style="margin-top:16px">
				<?php if ( $buy ) : ?><div><h3>Who should consider it</h3><?php pickora_list( $buy ); ?></div><?php endif; ?>
				<?php if ( $avoid ) : ?><div><h3>Who might prefer alternatives</h3><?php pickora_list( $avoid ); ?></div><?php endif; ?>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<section class="sec">
	<div class="wrap">
		<div class="sec-head"><h2>Explore Categories</h2></div>
		<div class="cats">
		<?php
		if ( $cats && ! is_wp_error( $cats ) ) :
			foreach ( $cats as $c ) :
				?>
			<a class="cat-card" href="<?php echo esc_url( get_term_link( $c ) ); ?>"><?php echo esc_html( $c->name ); ?><small><?php echo esc_html( sprintf( _n( '%d review', '%d reviews', $c->count, 'pickora' ), $c->count ) ); ?></small></a>
				<?php
			endforeach;
		endif;
		?>
		</div>
	</div>
</section>

<section class="sec sec--ivory">
	<div class="wrap">
		<div class="sec-head"><h2>Latest Product Reviews</h2><a class="btn btn--ghost" href="<?php echo esc_url( $reviews_url ); ?>">View All Reviews</a></div>
		<?php if ( $latest->have_posts() ) : ?>
		<div class="grid">
			<?php
			while ( $latest->have_posts() ) :
				$latest->the_post();
				get_template_part( 'template-parts/review-card' );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
		<?php else : ?>
		<p>No reviews published yet. Go to Reviews &rarr; Add New Review in the dashboard.</p>
		<?php endif; ?>
	</div>
</section>

<?php if ( $picks->have_posts() ) : ?>
<section class="sec">
	<div class="wrap">
		<div class="sec-head"><h2>Editor&rsquo;s Picks &ndash; Products Worth Exploring</h2></div>
		<div class="grid">
			<?php
			while ( $picks->have_posts() ) :
				$picks->the_post();
				get_template_part( 'template-parts/review-card' );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php
$budgets = get_terms( array( 'taxonomy' => 'budget', 'hide_empty' => true ) );
if ( $budgets && ! is_wp_error( $budgets ) ) :
	?>
<section class="sec">
	<div class="wrap">
		<div class="sec-head"><h2>Best Products by Budget</h2></div>
		<div class="cats">
			<?php foreach ( $budgets as $b ) : ?>
			<a class="cat-card" href="<?php echo esc_url( get_term_link( $b ) ); ?>">Best Products <?php echo esc_html( $b->name ); ?><small><?php echo esc_html( sprintf( _n( '%d guide', '%d guides', $b->count, 'pickora' ), $b->count ) ); ?></small></a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php
$home_blocks = array(
	array( 'comparisons', 'Confused Between Two Products? Let&rsquo;s Compare.', 'sec sec--ivory' ),
	array( 'buying-guides', 'Buying Guides', 'sec' ),
);
foreach ( $home_blocks as $hb ) :
	$gq = new WP_Query( array( 'post_type' => 'pickora_guide', 'posts_per_page' => 4, 'ignore_sticky_posts' => true, 'tax_query' => array( array( 'taxonomy' => 'guide_type', 'field' => 'slug', 'terms' => $hb[0] ) ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	if ( $gq->have_posts() ) :
		?>
<section class="<?php echo esc_attr( $hb[2] ); ?>">
	<div class="wrap">
		<div class="sec-head"><h2><?php echo wp_kses_post( $hb[1] ); ?></h2><a class="btn btn--ghost" href="<?php echo esc_url( pickora_type_url( $hb[0] ) ); ?>">View all</a></div>
		<div class="grid">
			<?php
			while ( $gq->have_posts() ) :
				$gq->the_post();
				get_template_part( 'template-parts/review-card' );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
		<?php
	endif;
endforeach;
?>

<section class="sec sec--navy">
	<div class="wrap">
		<h2>Our Promise: Better Information. Smarter Decisions.</h2>
		<div class="trust">
			<div><h3>Research Before Recommendations</h3><p>We focus on understanding product specifications, features and available evidence before recommending products.</p></div>
			<div><h3>Honest Pros and Cons</h3><p>Every review should explain both the strengths and limitations of a product.</p></div>
			<div><h3>Transparent Recommendations</h3><p>We clearly disclose affiliate relationships and explain how the website earns commissions.</p></div>
			<div><h3>Consumer-First Approach</h3><p>Our objective is to help readers choose products based on their requirements and budgets, not simply push products.</p></div>
		</div>
		<p style="margin-top:28px"><a class="btn btn--gold" href="<?php echo esc_url( pickora_page_url( 'our-review-methodology' ) ); ?>">Our Review Methodology</a></p>
	</div>
</section>

<section class="sec sec--ivory pledge">
	<div class="wrap">
		<h2>Your Trust Matters More Than a Sale.</h2>
		<p>At PICKORA, we believe a good buying decision starts with good information. Our goal is to simplify product research through thoughtful reviews, useful comparisons and transparent recommendations. We aim to explain what makes a product worth considering, where it may fall short and who it is best suited for. We believe informed buyers make better decisions.</p>
		<a class="btn btn--ghost" href="<?php echo esc_url( pickora_page_url( 'editorial-policy' ) ); ?>">Read our Editorial Policy</a>
	</div>
</section>

<?php get_template_part( 'template-parts/newsletter' ); ?>
<?php get_footer(); ?>
