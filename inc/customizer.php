<?php
/**
 * Customizer: campos editáveis para hero / intro / rodapé.
 *
 * @package AlexandreOltramari
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Customizer fields.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function olt_customize_register( $wp_customize ) {
	// Section: Conteúdo da homepage.
	$wp_customize->add_section(
		'olt_home',
		array(
			'title'    => __( 'Homepage — Conteúdo', 'alexandre-oltramari' ),
			'priority' => 30,
		)
	);

	// Hero background image.
	$wp_customize->add_setting(
		'olt_hero_bg',
		array(
			'default'           => OLT_THEME_URI . '/assets/images/hero-bg.webp',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'olt_hero_bg',
			array(
				'label'    => __( 'Imagem de fundo do Hero', 'alexandre-oltramari' ),
				'section'  => 'olt_home',
				'priority' => 10,
			)
		)
	);

	// Intro paragraph.
	$wp_customize->add_setting(
		'olt_intro_text',
		array(
			'default'           => 'Comunicação que influencia. Estratégia que decide. Combinamos dados, IA e experiência política para contar histórias que mobilizam e influenciam o debate público.',
			'sanitize_callback' => 'wp_kses_post',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'olt_intro_text',
		array(
			'label'    => __( 'Texto de apresentação (seção Intro)', 'alexandre-oltramari' ),
			'section'  => 'olt_home',
			'type'     => 'textarea',
			'priority' => 20,
		)
	);

	// Footer: title.
	$wp_customize->add_setting(
		'olt_footer_title',
		array(
			'default'           => 'QUEM É ALEXANDRE OLTRAMARI',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'olt_footer_title',
		array(
			'label'    => __( 'Rodapé — Título', 'alexandre-oltramari' ),
			'section'  => 'olt_home',
			'priority' => 30,
		)
	);

	// Footer: bio.
	$wp_customize->add_setting(
		'olt_footer_bio',
		array(
			'default'           => 'Sou formado em Comunicação pela PUC do Rio Grande do Sul. Fui repórter especial da Folha de S.Paulo e, durante mais de uma década, trabalhei como editor da revista Veja, em Brasília. O mundo mudou – e eu mudei junto. Em 2010, migrei para as áreas de Relações Públicas e Marketing Institucional e Político. Estudei Marketing e Social Media na University of California (UCSD). E hoje, misturando o que aprendi na teoria e na prática, atuo ajudando pessoas, empresas e instituições a contar histórias verdadeiras, impactantes e poderosas.',
			'sanitize_callback' => 'wp_kses_post',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'olt_footer_bio',
		array(
			'label'    => __( 'Rodapé — Bio', 'alexandre-oltramari' ),
			'section'  => 'olt_home',
			'type'     => 'textarea',
			'priority' => 40,
		)
	);

	// Footer: WhatsApp.
	$wp_customize->add_setting(
		'olt_footer_whatsapp',
		array(
			'default'           => '+55 (61) 99966-1000',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'olt_footer_whatsapp',
		array(
			'label'    => __( 'Rodapé — WhatsApp (texto)', 'alexandre-oltramari' ),
			'section'  => 'olt_home',
			'priority' => 50,
		)
	);

	// Footer: WhatsApp link.
	$wp_customize->add_setting(
		'olt_footer_whatsapp_link',
		array(
			'default'           => 'https://wa.me/5561999661000',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'olt_footer_whatsapp_link',
		array(
			'label'    => __( 'Rodapé — WhatsApp (link)', 'alexandre-oltramari' ),
			'section'  => 'olt_home',
			'priority' => 60,
		)
	);
}
add_action( 'customize_register', 'olt_customize_register' );
