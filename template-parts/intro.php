<?php
/**
 * Template part: Intro.
 *
 * @package AlexandreOltramari
 */

$intro_text = get_theme_mod( 'olt_intro_text', 'Comunicação que influencia. Estratégia que decide. Combinamos dados, IA e experiência política para contar histórias que mobilizam e influenciam o debate público.' );
?>
<section class="snap intro">
	<span class="intro__tab" aria-hidden="true"></span>
	<p class="intro__text" data-anim="fade-up">
		<?php echo wp_kses_post( $intro_text ); ?>
	</p>
</section>
