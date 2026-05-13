<?php
/**
 * Header template.
 *
 * @package AlexandreOltramari
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

<!-- Burger button (always visible) -->
<button class="burger" id="burger" aria-label="<?php esc_attr_e( 'Abrir menu', 'alexandre-oltramari' ); ?>" aria-expanded="false">
	<img src="<?php echo esc_url( OLT_THEME_URI . '/assets/images/icon-list.svg' ); ?>" alt="">
</button>

<!-- Menu popup -->
<div class="menu" id="menu" aria-hidden="true" role="dialog" aria-label="<?php esc_attr_e( 'Navegação', 'alexandre-oltramari' ); ?>">
	<div class="menu__panel">
		<button class="menu__close" data-menu-close aria-label="<?php esc_attr_e( 'Fechar menu', 'alexandre-oltramari' ); ?>">×</button>
		<nav id="menu-nav" class="menu__nav"></nav>
	</div>
</div>

<!-- Lightbox -->
<div class="lightbox" id="lightbox" aria-hidden="true">
	<button class="lightbox__close" aria-label="<?php esc_attr_e( 'Fechar', 'alexandre-oltramari' ); ?>">×</button>
	<div class="lightbox__content"></div>
</div>

<div class="page">
