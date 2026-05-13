<?php
/**
 * Template part: Hero.
 *
 * @package AlexandreOltramari
 */

$hero_bg = get_theme_mod( 'olt_hero_bg', OLT_THEME_URI . '/assets/images/hero-bg.webp' );
?>
<header class="snap hero">
	<img class="hero__bg" src="<?php echo esc_url( $hero_bg ); ?>" alt="">
</header>
