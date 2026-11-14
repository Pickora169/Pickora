<?php
/**
 * PICKORA theme functions.
 *
 * @package pickora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function pickora_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'responsive-embeds' );
	register_nav_menus( array( 'primary' => 'Primary menu' ) );
	add_image_size( 'pickora-card', 640, 480, true );
}
add_action( 'after_setup_theme', 'pickora_setup' );

function pickora_assets() {
	wp_enqueue_style( 'pickora-fonts', 'https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Fraunces:opsz,wght@9..144,500;9..144,600&display=swap', array(), null );
	wp_enqueue_style( 'pickora', get_stylesheet_uri(), array( 'pickora-fonts' ), wp_get_theme()->get( 'Version' ) );
	wp_enqueue_script( 'pickora', get_template_directory_uri() . '/assets/js/main.js', array(), wp_get_theme()->get( 'Version' ), true );
}
add_action( 'wp_enqueue_scripts', 'pickora_assets' );

function pickora_core_missing_notice() {
	if ( ! post_type_exists( 'pickora_review' ) && current_user_can( 'activate_plugins' ) ) {
		echo '<div class="notice notice-warning"><p>The PICKORA theme needs the <strong>PICKORA Core</strong> plugin to be activated.</p></div>';
	}
}
add_action( 'admin_notices', 'pickora_core_missing_notice' );

/* Fallbacks so the theme never fatals if the plugin is off. */
if ( ! function_exists( 'pickora_opt' ) ) {
	function pickora_opt( $k, $d = '' ) { return $d; }
}
if ( ! function_exists( 'pickora_lines' ) ) {
	function pickora_lines( $t ) { return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $t ) ), 'strlen' ) ); }
}
if ( ! function_exists( 'pickora_pairs' ) ) {
	function pickora_pairs( $t ) { return array(); }
}
if ( ! function_exists( 'pickora_updated_timestamp' ) ) {
	function pickora_updated_timestamp( $id = 0 ) { return (int) get_post_modified_time( 'U', true, $id ? $id : get_the_ID() ); }
}
if ( ! function_exists( 'pickora_affiliate_url' ) ) {
	function pickora_affiliate_url( $id = 0 ) { return ''; }
}

function pickora_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

function pickora_featured_review() {
	$id = (int) pickora_opt( 'featured_id', 0 );
	if ( $id && 'publish' === get_post_status( $id ) && 'pickora_review' === get_post_type( $id ) ) {
		return get_post( $id );
	}
	$q = get_posts( array( 'post_type' => 'pickora_review', 'numberposts' => 1, 'post_status' => 'publish' ) );
	return $q ? $q[0] : null;
}

function pickora_primary_term( $id ) {
	foreach ( array( 'review_category', 'guide_type' ) as $tax ) {
		$t = get_the_terms( $id, $tax );
		if ( $t && ! is_wp_error( $t ) ) {
			return $t[0];
		}
	}
	return null;
}

function pickora_type_url( $slug ) {
	$t = get_term_by( 'slug', $slug, 'guide_type' );
	if ( $t ) {
		$l = get_term_link( $t );
		if ( ! is_wp_error( $l ) ) {
			return $l;
		}
	}
	return home_url( '/' );
}

function pickora_related( $id, $n = 4 ) {
	$manual = array_filter( array_map( 'absint', explode( ',', (string) get_post_meta( $id, '_pk_related_ids', true ) ) ) );
	$manual = array_values( array_diff( $manual, array( $id ) ) );
	$manual = array_slice( $manual, 0, $n );
	$ids    = $manual;
	if ( count( $ids ) < $n ) {
		$term = pickora_primary_term( $id );
		$args = array( 'post_type' => 'pickora_review', 'posts_per_page' => $n - count( $ids ), 'post__not_in' => array_merge( array( $id ), $ids ), 'ignore_sticky_posts' => true, 'fields' => 'ids' );
		if ( $term ) {
			$args['tax_query'] = array( array( 'taxonomy' => 'review_category', 'field' => 'term_id', 'terms' => $term->term_id ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
		}
		$auto = get_posts( $args );
		$ids  = array_merge( $ids, $auto );
		if ( count( $ids ) < $n ) {
			$more = get_posts( array( 'post_type' => 'pickora_review', 'posts_per_page' => $n - count( $ids ), 'post__not_in' => array_merge( array( $id ), $ids ), 'fields' => 'ids' ) );
			$ids  = array_merge( $ids, $more );
		}
	}
	return new WP_Query( array( 'post_type' => 'pickora_review', 'post__in' => $ids ? $ids : array( 0 ), 'orderby' => 'post__in', 'posts_per_page' => $n, 'ignore_sticky_posts' => true ) );
}

function pickora_logo_icon( $size = 34 ) {
	return '<svg width="' . (int) $size . '" height="' . (int) $size . '" viewBox="0 0 40 40" aria-hidden="true"><rect width="40" height="40" rx="9" fill="#172D3C"/><path d="M14 30V10h8.5a6 6 0 010 12H14" fill="none" stroke="#D8C7A5" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M17.8 16.3l2.1 2.1 4.1-4.6" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}

function pickora_favicon() {
	if ( has_site_icon() ) {
		return;
	}
	$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40"><rect width="40" height="40" rx="9" fill="#172D3C"/><path d="M14 30V10h8.5a6 6 0 010 12H14" fill="none" stroke="#D8C7A5" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
	echo '<link rel="icon" href="data:image/svg+xml,' . rawurlencode( $svg ) . '">' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
}
add_action( 'wp_head', 'pickora_favicon' );

function pickora_fallback_menu() {
	$items = array(
		'Home'           => home_url( '/' ),
		'Reviews'        => get_post_type_archive_link( 'pickora_review' ) ? get_post_type_archive_link( 'pickora_review' ) : home_url( '/reviews/' ),
		'Best Picks'     => pickora_type_url( 'best-picks' ),
		'Comparisons'    => pickora_type_url( 'comparisons' ),
		'Buying Guides'  => pickora_type_url( 'buying-guides' ),
		'Deals'          => pickora_type_url( 'deals' ),
		'About Us'       => pickora_page_url( 'about-us' ),
	);
	echo '<ul>';
	foreach ( $items as $label => $url ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
}

function pickora_social_links() {
	$icons = array(
		'instagram' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg>',
		'facebook'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M14 8h3V4h-3a4 4 0 00-4 4v2H7v4h3v6h4v-6h3l1-4h-4V8z"/></svg>',
		'youtube'   => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="4"/><path d="M10 9l5 3-5 3z" fill="currentColor"/></svg>',
	);
	$out = '';
	foreach ( $icons as $k => $svg ) {
		$u = pickora_opt( $k );
		if ( $u ) {
			$out .= '<a href="' . esc_url( $u ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr( ucfirst( $k ) ) . '">' . $svg . '</a>';
		}
	}
	return $out; // Icons are static markup; URLs are escaped above.
}

function pickora_affiliate_button( $id = 0, $label = 'Check Price on Amazon', $class = 'btn btn--gold' ) {
	$url = pickora_affiliate_url( $id );
	if ( ! $url ) {
		return;
	}
	printf( '<a class="%s" href="%s" target="_blank" rel="sponsored nofollow noopener">%s</a>', esc_attr( $class ), $url, esc_html( $label ) ); // phpcs:ignore WordPress.Security.EscapeOutput
}

function pickora_list( $items, $class = '' ) {
	if ( ! $items ) {
		return;
	}
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $items as $i ) {
		echo '<li>' . esc_html( $i ) . '</li>';
	}
	echo '</ul>';
}
