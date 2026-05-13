<?php
/**
 * Template part: Site Footer.
 *
 * @package AlexandreOltramari
 */

$title    = get_theme_mod( 'olt_footer_title', 'QUEM É ALEXANDRE OLTRAMARI' );
$bio      = get_theme_mod( 'olt_footer_bio', '' );
$whatsapp = get_theme_mod( 'olt_footer_whatsapp', '+55 (61) 99966-1000' );
$wa_link  = get_theme_mod( 'olt_footer_whatsapp_link', 'https://wa.me/5561999661000' );
?>
<footer class="snap site-footer">
	<div class="site-footer__inner">
		<div class="site-footer__block">
			<h2 class="site-footer__title" data-anim="fade-up"><?php echo esc_html( $title ); ?></h2>
			<?php if ( $bio ) : ?>
				<p class="site-footer__text" data-anim="fade-up"><?php echo wp_kses_post( $bio ); ?></p>
			<?php endif; ?>
		</div>
		<div class="site-footer__block">
			<h3 class="site-footer__subtitle" data-anim="fade-up"><?php esc_html_e( 'CONTATO', 'alexandre-oltramari' ); ?></h3>
			<a class="site-footer__contact" href="<?php echo esc_url( $wa_link ); ?>" target="_blank" rel="noopener" data-anim="fade-up">
				<img src="<?php echo esc_url( OLT_THEME_URI . '/assets/images/icon-whatsapp.svg' ); ?>" alt="">
				<span><?php echo esc_html( $whatsapp ); ?></span>
			</a>
		</div>
	</div>
</footer>
