<?php
/**
 * Plugin Name: PICKORA Core
 * Description: Review post type, structured review fields, featured review, affiliate links, SEO fields and newsletter for the PICKORA theme.
 * Version: 1.0.0
 * Author: PICKORA
 * License: GPL-2.0-or-later
 * Text Domain: pickora-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PICKORA_CORE_VERSION', '1.0.0' );

/* ---------- Content types ---------- */

function pickora_core_register_types() {
	register_post_type( 'pickora_review', array(
		'labels'        => array(
			'name'               => 'Reviews',
			'singular_name'      => 'Review',
			'add_new'            => 'Add New Review',
			'add_new_item'       => 'Add New Review',
			'edit_item'          => 'Edit Review',
			'all_items'          => 'All Reviews',
			'search_items'       => 'Search Reviews',
			'menu_name'          => 'Reviews',
		),
		'public'        => true,
		'has_archive'   => 'reviews',
		'rewrite'       => array( 'slug' => 'reviews', 'with_front' => false ),
		'menu_icon'     => 'dashicons-star-filled',
		'menu_position' => 5,
		'show_in_rest'  => true,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'revisions' ),
		'taxonomies'    => array( 'post_tag' ),
	) );

	register_taxonomy( 'review_category', 'pickora_review', array(
		'labels'            => array( 'name' => 'Review Categories', 'singular_name' => 'Review Category', 'menu_name' => 'Categories' ),
		'hierarchical'      => true,
		'public'            => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'topic', 'with_front' => false ),
	) );
}
add_action( 'init', 'pickora_core_register_types' );

function pickora_core_activate() {
	pickora_core_register_types();
	pickora_core_register_guides();
	pickora_core_seed_terms();
	pickora_core_create_pages();
	$defaults = array( 'Electronics & Gadgets', 'Home & Kitchen', 'Beauty & Grooming', 'Fashion & Lifestyle', 'Home & Living', 'Fitness & Wellness', 'Everyday Essentials' );
	foreach ( $defaults as $name ) {
		if ( ! term_exists( $name, 'review_category' ) ) {
			wp_insert_term( $name, 'review_category' );
		}
	}
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'pickora_core_activate' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

/* ---------- Helpers ---------- */

function pickora_opt( $key, $default = '' ) {
	$opts = get_option( 'pickora_options', array() );
	return isset( $opts[ $key ] ) && '' !== $opts[ $key ] ? $opts[ $key ] : $default;
}

function pickora_lines( $text ) {
	$lines = preg_split( '/\r\n|\r|\n/', (string) $text );
	return array_values( array_filter( array_map( 'trim', $lines ), 'strlen' ) );
}

function pickora_pairs( $text ) {
	$out = array();
	foreach ( pickora_lines( $text ) as $line ) {
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		if ( 2 === count( $parts ) && '' !== $parts[0] ) {
			$out[] = $parts;
		}
	}
	return $out;
}

/** Last updated date: manual override if set, otherwise the WordPress modified date. */
function pickora_updated_timestamp( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$manual  = get_post_meta( $post_id, '_pk_updated', true );
	if ( $manual && strtotime( $manual ) ) {
		return strtotime( $manual );
	}
	return (int) get_post_modified_time( 'U', true, $post_id );
}

/** Affiliate URL with your tracking tag applied to Amazon.in links only. */
function pickora_affiliate_url( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$url     = get_post_meta( $post_id, '_pk_affiliate', true );
	if ( ! $url ) {
		return '';
	}
	$tag  = pickora_opt( 'affiliate_tag' );
	$host = wp_parse_url( $url, PHP_URL_HOST );
	if ( $tag && $host && preg_match( '/(^|\.)amazon\.in$/i', $host ) ) {
		$url = add_query_arg( 'tag', rawurlencode( $tag ), remove_query_arg( 'tag', $url ) );
	}
	return esc_url( $url );
}

/* ---------- Review fields (meta box) ---------- */

function pickora_core_fields() {
	return array(
		'_pk_method'    => array( 'Review type', 'select', array( 'research' => 'Research-based (not hands-on tested)', 'tested' => 'Hands-on tested' ) ),
		'_pk_verdict'   => array( 'Quick verdict', 'textarea', 'Two or three sentences summing up the product.' ),
		'_pk_best_for'  => array( 'Best suited for', 'text', 'e.g. Daily commuters on a budget' ),
		'_pk_pros'      => array( 'Pros (one per line)', 'textarea', '' ),
		'_pk_cons'      => array( 'Cons (one per line)', 'textarea', '' ),
		'_pk_highlights'=> array( 'Product highlights (one per line)', 'textarea', '' ),
		'_pk_specs'     => array( 'Specifications (one per line, as Label | Value)', 'textarea', "Battery life | Up to 30 hours\nWeight | 55 g" ),
		'_pk_experience'=> array( 'Performance and experience notes', 'textarea', 'State clearly what you tested yourself and what comes from research.' ),
		'_pk_price_note'=> array( 'Verified price / value note', 'text', 'Only enter a price you have verified, with the date. Leave blank if unsure.' ),
		'_pk_who_buy'   => array( 'Who should buy it (one per line)', 'textarea', '' ),
		'_pk_who_avoid' => array( 'Who should consider alternatives (one per line)', 'textarea', '' ),
		'_pk_faqs'      => array( 'FAQs (one per line, as Question | Answer)', 'textarea', '' ),
		'_pk_affiliate' => array( 'Amazon.in product link', 'url', 'https://www.amazon.in/dp/...' ),
		'_pk_alt_ids'   => array( 'Alternatives to show (hold Ctrl or Cmd to pick several)', 'reviews', '' ),
		'_pk_related_ids' => array( 'Related articles to show first (hold Ctrl or Cmd to pick several; the rest fill automatically)', 'reviews', '' ),
		'_pk_updated'   => array( 'Last updated date (leave blank to use the date you last saved)', 'date', '' ),
		'_pk_seo_title' => array( 'SEO title (optional)', 'text', '' ),
		'_pk_seo_desc'  => array( 'SEO meta description (optional)', 'textarea', '' ),
		'_pk_editors_pick' => array( 'Show in Editor\'s Picks on the homepage', 'checkbox', '' ),
	);
}

function pickora_core_add_box() {
	add_meta_box( 'pickora_review_fields', 'Review details', 'pickora_core_render_box', 'pickora_review', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'pickora_core_add_box' );

function pickora_core_render_box( $post ) {
	wp_nonce_field( 'pickora_save_review', 'pickora_review_nonce' );
	echo '<style>.pk-f{margin:0 0 16px}.pk-f label{display:block;font-weight:600;margin-bottom:4px}.pk-f input[type=text],.pk-f input[type=url],.pk-f textarea,.pk-f select{width:100%}</style>';
	foreach ( pickora_core_fields() as $key => $f ) {
		$val = get_post_meta( $post->ID, $key, true );
		echo '<div class="pk-f">';
		if ( 'checkbox' === $f[1] ) {
			printf( '<label><input type="checkbox" name="%1$s" value="1" %2$s> %3$s</label>', esc_attr( $key ), checked( $val, '1', false ), esc_html( $f[0] ) );
		} else {
			printf( '<label for="%1$s">%2$s</label>', esc_attr( $key ), esc_html( $f[0] ) );
			if ( 'textarea' === $f[1] ) {
				printf( '<textarea id="%1$s" name="%1$s" rows="4" placeholder="%3$s">%2$s</textarea>', esc_attr( $key ), esc_textarea( $val ), esc_attr( $f[2] ) );
			} elseif ( 'reviews' === $f[1] ) {
				$chosen = array_map( 'absint', explode( ',', (string) $val ) );
				$others = get_posts( array( 'post_type' => 'pickora_review', 'post_status' => 'publish', 'numberposts' => 300, 'post__not_in' => array( $post->ID ), 'orderby' => 'title', 'order' => 'ASC' ) );
				echo '<select multiple size="6" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '[]">';
				foreach ( $others as $o ) {
					printf( '<option value="%d" %s>%s</option>', (int) $o->ID, selected( in_array( (int) $o->ID, $chosen, true ), true, false ), esc_html( $o->post_title ) );
				}
				echo '</select>';
			} elseif ( 'date' === $f[1] ) {
				printf( '<input type="date" id="%1$s" name="%1$s" value="%2$s" style="width:auto">', esc_attr( $key ), esc_attr( $val ) );
			} elseif ( 'select' === $f[1] ) {
				echo '<select id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '">';
				foreach ( $f[2] as $v => $label ) {
					printf( '<option value="%s" %s>%s</option>', esc_attr( $v ), selected( $val, $v, false ), esc_html( $label ) );
				}
				echo '</select>';
			} else {
				printf( '<input type="%1$s" id="%2$s" name="%2$s" value="%3$s" placeholder="%4$s">', 'url' === $f[1] ? 'url' : 'text', esc_attr( $key ), esc_attr( $val ), esc_attr( $f[2] ) );
			}
		}
		echo '</div>';
	}
	echo '<p class="description">Use the main editor above for the detailed review body, the Excerpt box for the short introduction, and the Featured image box for the product photo.</p>';
}

function pickora_core_save_box( $post_id ) {
	if ( ! isset( $_POST['pickora_review_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['pickora_review_nonce'] ) ), 'pickora_save_review' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( pickora_core_fields() as $key => $f ) {
		$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		switch ( $f[1] ) {
			case 'reviews':
				$ids = is_array( $raw ) ? array_filter( array_map( 'absint', $raw ) ) : array();
				$val = implode( ',', $ids );
				break;
			case 'date':
				$val = ( is_string( $raw ) && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $raw ) && strtotime( $raw ) ) ? $raw : '';
				break;
			case 'checkbox':
				$val = $raw ? '1' : '';
				break;
			case 'url':
				$val = esc_url_raw( $raw );
				break;
			case 'textarea':
				$val = sanitize_textarea_field( $raw );
				break;
			case 'select':
				$val = array_key_exists( $raw, $f[2] ) ? $raw : 'research';
				break;
			default:
				$val = sanitize_text_field( $raw );
		}
		if ( '' === $val ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $val );
		}
	}
}
add_action( 'save_post_pickora_review', 'pickora_core_save_box' );

/* ---------- Settings page ---------- */

function pickora_core_settings_menu() {
	add_options_page( 'PICKORA Settings', 'PICKORA', 'manage_options', 'pickora-settings', 'pickora_core_settings_page' );
}
add_action( 'admin_menu', 'pickora_core_settings_menu' );

function pickora_core_register_settings() {
	register_setting( 'pickora_options_group', 'pickora_options', array( 'sanitize_callback' => 'pickora_core_sanitize_options' ) );
}
add_action( 'admin_init', 'pickora_core_register_settings' );

function pickora_core_sanitize_options( $in ) {
	return array(
		'featured_id'   => isset( $in['featured_id'] ) ? absint( $in['featured_id'] ) : 0,
		'affiliate_tag' => isset( $in['affiliate_tag'] ) ? sanitize_text_field( $in['affiliate_tag'] ) : '',
		'instagram'     => isset( $in['instagram'] ) ? esc_url_raw( $in['instagram'] ) : '',
		'facebook'      => isset( $in['facebook'] ) ? esc_url_raw( $in['facebook'] ) : '',
		'youtube'       => isset( $in['youtube'] ) ? esc_url_raw( $in['youtube'] ) : '',
		'contact_email' => isset( $in['contact_email'] ) ? sanitize_email( $in['contact_email'] ) : '',
	);
}

function pickora_core_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$o        = get_option( 'pickora_options', array() );
	$reviews  = get_posts( array( 'post_type' => 'pickora_review', 'post_status' => 'publish', 'numberposts' => 200, 'orderby' => 'date', 'order' => 'DESC' ) );
	$subs     = get_option( 'pickora_subscribers', array() );
	$fields   = array(
		'affiliate_tag' => 'Amazon Associates tracking ID (e.g. yourname-21)',
		'instagram'     => 'Instagram URL',
		'facebook'      => 'Facebook URL',
		'youtube'       => 'YouTube URL',
		'contact_email' => 'Contact form recipient email',
	);
	echo '<div class="wrap"><h1>PICKORA Settings</h1><form method="post" action="options.php">';
	settings_fields( 'pickora_options_group' );
	echo '<table class="form-table"><tr><th><label for="featured_id">Homepage featured review</label></th><td><select id="featured_id" name="pickora_options[featured_id]"><option value="0">Latest review (automatic)</option>';
	foreach ( $reviews as $r ) {
		printf( '<option value="%d" %s>%s</option>', (int) $r->ID, selected( isset( $o['featured_id'] ) ? (int) $o['featured_id'] : 0, $r->ID, false ), esc_html( $r->post_title ) );
	}
	echo '</select></td></tr>';
	foreach ( $fields as $k => $label ) {
		printf( '<tr><th><label for="%1$s">%2$s</label></th><td><input class="regular-text" type="text" id="%1$s" name="pickora_options[%1$s]" value="%3$s"></td></tr>', esc_attr( $k ), esc_html( $label ), esc_attr( isset( $o[ $k ] ) ? $o[ $k ] : '' ) );
	}
	echo '</table>';
	submit_button();
	echo '</form><h2>Starter pages</h2><p>Creates About, Methodology, Editorial Policy, Affiliate Disclosure, Contact (published) and Privacy / Terms (drafts, templates only). Existing pages are never changed.</p><p><a class="button" href="' . esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=pickora_pages' ), 'pickora_pages' ) ) . '">Create starter pages</a></p><h2>Newsletter subscribers (' . count( (array) $subs ) . ')</h2><textarea readonly rows="8" class="large-text code">' . esc_textarea( implode( "\n", (array) $subs ) ) . '</textarea></div>';
}

/* ---------- Newsletter (nonce, honeypot, rate limit) ---------- */

function pickora_core_subscribe() {
	$back = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$back = remove_query_arg( 'pk_sub', $back );
	$ok   = isset( $_POST['pk_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['pk_nonce'] ) ), 'pickora_subscribe' );
	$bot  = ! empty( $_POST['pk_website'] );
	$mail = isset( $_POST['pk_email'] ) ? sanitize_email( wp_unslash( $_POST['pk_email'] ) ) : '';
	$ip   = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key  = 'pk_rl_' . md5( $ip );

	if ( ! $ok || $bot ) {
		$status = 'error';
	} elseif ( ! is_email( $mail ) ) {
		$status = 'invalid';
	} elseif ( get_transient( $key ) ) {
		$status = 'wait';
	} else {
		set_transient( $key, 1, 30 );
		$subs = (array) get_option( 'pickora_subscribers', array() );
		if ( ! in_array( strtolower( $mail ), array_map( 'strtolower', $subs ), true ) ) {
			$subs[] = $mail;
			update_option( 'pickora_subscribers', $subs, false );
		}
		$status = 'ok';
	}
	wp_safe_redirect( add_query_arg( 'pk_sub', $status, $back ) . '#newsletter' );
	exit;
}
add_action( 'admin_post_nopriv_pickora_subscribe', 'pickora_core_subscribe' );
add_action( 'admin_post_pickora_subscribe', 'pickora_core_subscribe' );

/* ---------- SEO fields output (skipped if an SEO plugin is active) ---------- */

function pickora_core_seo_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' );
}

function pickora_core_title( $title ) {
	if ( is_singular( 'pickora_review' ) && ! pickora_core_seo_active() ) {
		$custom = get_post_meta( get_the_ID(), '_pk_seo_title', true );
		if ( $custom ) {
			return $custom;
		}
	}
	return $title;
}
add_filter( 'pre_get_document_title', 'pickora_core_title' );

function pickora_core_head() {
	if ( pickora_core_seo_active() ) {
		return;
	}
	$desc = ''; $img = ''; $url = ''; $title = wp_get_document_title(); $type = 'website';
	if ( is_singular() ) {
		$id   = get_the_ID();
		$desc = get_post_meta( $id, '_pk_seo_desc', true );
		$desc = $desc ? $desc : wp_trim_words( wp_strip_all_tags( get_the_excerpt( $id ) ), 28, '' );
		$img  = get_the_post_thumbnail_url( $id, 'large' );
		$url  = get_permalink( $id );
		$type = 'article';
	} elseif ( is_front_page() ) {
		$desc = get_bloginfo( 'description' );
		$url  = home_url( '/' );
	}
	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	if ( $url ) {
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
		echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
		echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
		echo '<meta property="og:site_name" content="PICKORA">' . "\n";
		if ( $desc ) {
			echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
		}
		echo '<meta name="twitter:card" content="' . ( $img ? 'summary_large_image' : 'summary' ) . '">' . "\n";
		if ( $img ) {
			echo '<meta property="og:image" content="' . esc_url( $img ) . '">' . "\n";
			echo '<meta name="twitter:image" content="' . esc_url( $img ) . '">' . "\n";
		}
	}
}
add_action( 'wp_head', 'pickora_core_head', 5 );

/* ---------- Guides, comparisons, best picks, deals + budgets ---------- */

function pickora_core_register_guides() {
	register_post_type( 'pickora_guide', array(
		'labels'        => array( 'name' => 'Guides', 'singular_name' => 'Guide', 'add_new_item' => 'Add New Guide', 'edit_item' => 'Edit Guide', 'all_items' => 'All Guides', 'menu_name' => 'Guides' ),
		'public'        => true,
		'has_archive'   => false,
		'rewrite'       => array( 'slug' => 'guide', 'with_front' => false ),
		'menu_icon'     => 'dashicons-book-alt',
		'menu_position' => 6,
		'show_in_rest'  => true,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'revisions' ),
	) );
	register_taxonomy( 'guide_type', 'pickora_guide', array(
		'labels'            => array( 'name' => 'Guide Types', 'singular_name' => 'Guide Type', 'menu_name' => 'Guide Types' ),
		'hierarchical'      => true,
		'public'            => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'guides', 'with_front' => false ),
	) );
	register_taxonomy( 'budget', 'pickora_guide', array(
		'labels'            => array( 'name' => 'Budgets', 'singular_name' => 'Budget', 'menu_name' => 'Budgets' ),
		'hierarchical'      => true,
		'public'            => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'budget', 'with_front' => false ),
	) );
}
add_action( 'init', 'pickora_core_register_guides' );

function pickora_core_seed_terms() {
	$types   = array( 'buying-guides' => 'Buying Guides', 'comparisons' => 'Comparisons', 'best-picks' => 'Best Picks', 'deals' => 'Deals' );
	$budgets = array( 'under-500' => 'Under ₹500', 'under-1000' => 'Under ₹1,000', 'under-2000' => 'Under ₹2,000', 'under-5000' => 'Under ₹5,000' );
	foreach ( $types as $slug => $name ) {
		if ( ! term_exists( $slug, 'guide_type' ) ) {
			wp_insert_term( $name, 'guide_type', array( 'slug' => $slug ) );
		}
	}
	foreach ( $budgets as $slug => $name ) {
		if ( ! term_exists( $slug, 'budget' ) ) {
			wp_insert_term( $name, 'budget', array( 'slug' => $slug ) );
		}
	}
}

/* ---------- Structured data (only what the page visibly shows) ---------- */

function pickora_core_schema() {
	if ( pickora_core_seo_active() ) {
		return;
	}
	$data = array();
	if ( is_front_page() ) {
		$same = array_values( array_filter( array( pickora_opt( 'instagram' ), pickora_opt( 'facebook' ), pickora_opt( 'youtube' ) ) ) );
		$data = array( '@context' => 'https://schema.org', '@type' => 'Organization', 'name' => 'PICKORA', 'url' => home_url( '/' ), 'sameAs' => $same );
	} elseif ( is_singular( array( 'pickora_review', 'pickora_guide' ) ) ) {
		$id      = get_the_ID();
		$img     = get_the_post_thumbnail_url( $id, 'large' );
		$article = array(
			'@type'         => 'Article',
			'headline'      => get_the_title( $id ),
			'datePublished' => get_the_date( 'c', $id ),
			'dateModified'  => gmdate( 'c', pickora_updated_timestamp( $id ) ),
			'author'        => array( '@type' => 'Person', 'name' => get_the_author_meta( 'display_name', get_post_field( 'post_author', $id ) ) ),
			'mainEntityOfPage' => get_permalink( $id ),
		);
		if ( $img ) {
			$article['image'] = $img;
		}
		$crumbs = array( array( 'Home', home_url( '/' ) ) );
		if ( 'pickora_review' === get_post_type( $id ) ) {
			$crumbs[] = array( 'Reviews', get_post_type_archive_link( 'pickora_review' ) );
		}
		$crumbs[] = array( get_the_title( $id ), get_permalink( $id ) );
		$list = array();
		foreach ( $crumbs as $i => $c ) {
			$list[] = array( '@type' => 'ListItem', 'position' => $i + 1, 'name' => $c[0], 'item' => $c[1] );
		}
		$data = array( '@context' => 'https://schema.org', '@graph' => array( $article, array( '@type' => 'BreadcrumbList', 'itemListElement' => $list ) ) );
	}
	if ( $data ) {
		echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
add_action( 'wp_head', 'pickora_core_schema', 6 );

/* ---------- Contact form ---------- */

function pickora_core_contact_form() {
	$s    = isset( $_GET['pk_contact'] ) ? sanitize_key( wp_unslash( $_GET['pk_contact'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$msgs = array(
		'ok'      => array( 'Thanks. Your message has been sent.', '' ),
		'invalid' => array( 'Please fill in your name, a valid email and a message.', 'err' ),
		'wait'    => array( 'Please wait a minute before sending another message.', 'err' ),
		'error'   => array( 'Something went wrong. Please try again.', 'err' ),
	);
	ob_start();
	echo '<div id="contact">';
	if ( isset( $msgs[ $s ] ) ) {
		printf( '<div class="note %s" role="status">%s</div>', esc_attr( $msgs[ $s ][1] ), esc_html( $msgs[ $s ][0] ) );
	}
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	echo '<input type="hidden" name="action" value="pickora_contact">';
	wp_nonce_field( 'pickora_contact', 'pk_nonce' );
	echo '<input class="hp" type="text" name="pk_website" tabindex="-1" autocomplete="off" aria-hidden="true">';
	echo '<p><label for="pk_name">Name</label><br><input class="sform-input" style="width:100%;padding:.8em;border:1px solid #e3ded3;border-radius:8px" type="text" id="pk_name" name="pk_name" required></p>';
	echo '<p><label for="pk_cemail">Email</label><br><input style="width:100%;padding:.8em;border:1px solid #e3ded3;border-radius:8px" type="email" id="pk_cemail" name="pk_cemail" required></p>';
	echo '<p><label for="pk_msg">Message</label><br><textarea style="width:100%;padding:.8em;border:1px solid #e3ded3;border-radius:8px" id="pk_msg" name="pk_msg" rows="6" required></textarea></p>';
	echo '<p><button class="btn" type="submit">Send message</button></p></form></div>';
	return ob_get_clean();
}
add_shortcode( 'pickora_contact', 'pickora_core_contact_form' );

function pickora_core_contact_submit() {
	$back = wp_get_referer() ? remove_query_arg( 'pk_contact', wp_get_referer() ) : home_url( '/' );
	$ok   = isset( $_POST['pk_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['pk_nonce'] ) ), 'pickora_contact' );
	$name = isset( $_POST['pk_name'] ) ? sanitize_text_field( wp_unslash( $_POST['pk_name'] ) ) : '';
	$mail = isset( $_POST['pk_cemail'] ) ? sanitize_email( wp_unslash( $_POST['pk_cemail'] ) ) : '';
	$msg  = isset( $_POST['pk_msg'] ) ? sanitize_textarea_field( wp_unslash( $_POST['pk_msg'] ) ) : '';
	$ip   = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key  = 'pk_ct_' . md5( $ip );

	if ( ! $ok || ! empty( $_POST['pk_website'] ) ) {
		$status = 'error';
	} elseif ( '' === $name || ! is_email( $mail ) || strlen( $msg ) < 5 ) {
		$status = 'invalid';
	} elseif ( get_transient( $key ) ) {
		$status = 'wait';
	} else {
		set_transient( $key, 1, 60 );
		$to      = pickora_opt( 'contact_email', get_option( 'admin_email' ) );
		$headers = array( 'Reply-To: ' . $name . ' <' . $mail . '>' );
		$sent    = wp_mail( $to, 'PICKORA contact form: ' . $name, "From: $name <$mail>\n\n$msg", $headers );
		$status  = $sent ? 'ok' : 'error';
	}
	wp_safe_redirect( add_query_arg( 'pk_contact', $status, $back ) . '#contact' );
	exit;
}
add_action( 'admin_post_nopriv_pickora_contact', 'pickora_core_contact_submit' );
add_action( 'admin_post_pickora_contact', 'pickora_core_contact_submit' );

/* ---------- Starter pages (created once; never overwrites existing pages) ---------- */

function pickora_core_pages() {
	$notice = '<p><strong>Template only.</strong> Replace this text with your own policy and have it reviewed by a qualified legal professional before publishing. Delete this line when done.</p>';
	return array(
		'about-us' => array( 'About PICKORA', 'publish', '<p>PICKORA publishes product reviews, comparisons and buying guides for shoppers in India. Our aim is to explain what a product does, where it falls short and who it suits, so you can choose based on your needs and budget.</p><p>Add your own story, who is behind PICKORA and how you choose what to cover.</p>' ),
		'our-review-methodology' => array( 'Our Review Methodology', 'publish', '<h2>How we choose products</h2><p>Describe how you select products to cover.</p><h2>Research-based reviews</h2><p>These reviews are based on manufacturer specifications, published information and available user and expert feedback. We have not used the product ourselves, and each article says so.</p><h2>Hands-on tested reviews</h2><p>These are only used when we have actually used the product. Each article carries a Hands-on tested label and describes what we did.</p><h2>Ratings</h2><p>We do not publish star ratings. If we introduce them, we will document exactly how they are calculated here first.</p>' ),
		'editorial-policy' => array( 'Editorial Policy', 'publish', '<h2>Accuracy</h2><p>We aim to check specifications and claims against reliable sources before publishing.</p><h2>Corrections</h2><p>If you spot an error, contact us. We will correct it and update the article date.</p><h2>Affiliate transparency</h2><p>Commissions do not decide what we recommend. Affiliate links are always disclosed. See our Affiliate Disclosure.</p>' ),
		'affiliate-disclosure' => array( 'Affiliate Disclosure', 'publish', '<p>PICKORA participates in the Amazon Associates programme. Some links on this site are affiliate links. If you click one and make a qualifying purchase, we may earn a commission at no extra cost to you.</p><p>As an Amazon Associate I earn from qualifying purchases.</p><p>Prices and availability change often, so always check the current price on the retailer\'s site.</p>' ),
		'contact-us' => array( 'Contact Us', 'publish', '<p>Questions, corrections or suggestions? Send us a message.</p>[pickora_contact]' ),
		'privacy-policy' => array( 'Privacy Policy', 'draft', $notice ),
		'terms-and-conditions' => array( 'Terms & Conditions', 'draft', $notice ),
	);
}

function pickora_core_create_pages() {
	foreach ( pickora_core_pages() as $slug => $p ) {
		if ( ! get_page_by_path( $slug ) ) {
			wp_insert_post( array( 'post_type' => 'page', 'post_name' => $slug, 'post_title' => $p[0], 'post_status' => $p[1], 'post_content' => $p[2] ) );
		}
	}
}

function pickora_core_pages_action() {
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'pickora_pages' ) ) {
		wp_die( 'Not allowed.' );
	}
	pickora_core_seed_terms();
	pickora_core_create_pages();
	wp_safe_redirect( admin_url( 'options-general.php?page=pickora-settings' ) );
	exit;
}
add_action( 'admin_post_pickora_pages', 'pickora_core_pages_action' );
