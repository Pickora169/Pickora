<?php
/**
 * Site header.
 *
 * @package pickora
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip sr" href="#main">Skip to content</a>
<header class="site-header">
	<div class="wrap bar">
		<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="PICKORA home">
			<?php echo pickora_logo_icon(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<span>PICKORA</span>
		</a>
		<nav class="nav" id="nav" aria-label="Primary">
			<?php
			wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'fallback_cb' => 'pickora_fallback_menu' ) );
			?>
		</nav>
		<div class="hdr-tools">
			<div class="social"><?php echo pickora_social_links(); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<button class="icon-btn" id="search-toggle" aria-expanded="false" aria-controls="search-panel" aria-label="Search">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/></svg>
			</button>
			<button class="icon-btn burger" id="burger" aria-expanded="false" aria-controls="nav" aria-label="Menu">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
			</button>
		</div>
	</div>
	<div class="search-panel" id="search-panel"><div class="wrap"><?php get_search_form(); ?></div></div>
</header>
<main id="main">
