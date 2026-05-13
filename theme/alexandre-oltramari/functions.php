<?php
/**
 * Alexandre Oltramari theme — functions.
 *
 * @package AlexandreOltramari
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'OLT_THEME_VERSION', '1.1.0' );
define( 'OLT_THEME_DIR', get_template_directory() );
define( 'OLT_THEME_URI', get_template_directory_uri() );

/**
 * Theme support + setup.
 */
function olt_theme_setup() {
	// Title tag handled by WP.
	add_theme_support( 'title-tag' );

	// Featured image on Cases CPT (used for the big photo).
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'olt-case-desktop', 2400, 1080, true );
	add_image_size( 'olt-case-mobile', 750, 1200, true );
	add_image_size( 'olt-video-thumb', 1200, 675, true );

	// HTML5 markup for core blocks.
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	// Editor / front-end alignment.
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );

	// Allow custom logo (optional).
	add_theme_support( 'custom-logo' );

	// Load translations.
	load_theme_textdomain( 'alexandre-oltramari', OLT_THEME_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'olt_theme_setup' );

/**
 * Enqueue front-end assets.
 */
function olt_enqueue_assets() {
	// Google Fonts (Montserrat + Barlow Condensed).
	wp_enqueue_style(
		'olt-google-fonts',
		'https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Barlow+Condensed:wght@400;500&display=swap',
		array(),
		null
	);

	// Theme stylesheet (real CSS lives in assets/css/styles.css).
	wp_enqueue_style(
		'olt-styles',
		OLT_THEME_URI . '/assets/css/styles.css',
		array( 'olt-google-fonts' ),
		OLT_THEME_VERSION
	);

	// Style.css de cabeçalho (não tem regras, mas WP gosta de ver enfileirado).
	wp_enqueue_style(
		'olt-theme-header',
		get_stylesheet_uri(),
		array( 'olt-styles' ),
		OLT_THEME_VERSION
	);

	// JS modules, deferred.
	wp_enqueue_script(
		'olt-stacking-scroll',
		OLT_THEME_URI . '/assets/js/stacking-scroll.js',
		array(),
		OLT_THEME_VERSION,
		array( 'strategy' => 'defer', 'in_footer' => false )
	);
	wp_enqueue_script(
		'olt-carousel',
		OLT_THEME_URI . '/assets/js/carousel.js',
		array(),
		OLT_THEME_VERSION,
		array( 'strategy' => 'defer', 'in_footer' => false )
	);
	wp_enqueue_script(
		'olt-lightbox',
		OLT_THEME_URI . '/assets/js/lightbox.js',
		array(),
		OLT_THEME_VERSION,
		array( 'strategy' => 'defer', 'in_footer' => false )
	);
	wp_enqueue_script(
		'olt-menu',
		OLT_THEME_URI . '/assets/js/menu.js',
		array( 'olt-stacking-scroll' ),
		OLT_THEME_VERSION,
		array( 'strategy' => 'defer', 'in_footer' => false )
	);
}
add_action( 'wp_enqueue_scripts', 'olt_enqueue_assets' );

/**
 * Preload hero background — homepage only.
 */
function olt_preload_hero() {
	if ( ! is_front_page() ) {
		return;
	}
	$hero_bg = OLT_THEME_URI . '/assets/images/hero-bg.webp';
	echo '<link rel="preload" as="image" href="' . esc_url( $hero_bg ) . '">' . "\n";
}
add_action( 'wp_head', 'olt_preload_hero', 1 );

// Includes.
require OLT_THEME_DIR . '/inc/cpt-cases.php';
require OLT_THEME_DIR . '/inc/customizer.php';
require OLT_THEME_DIR . '/inc/helpers.php';

if ( is_admin() ) {
	require OLT_THEME_DIR . '/inc/seeder.php';
}
