<?php
/**
 * Front page template — single-page layout with stacking scroll.
 *
 * Sections rendered in this order:
 *   1. hero            (Customizer)
 *   2. intro           (Customizer)
 *   3. for each case:  case-image + case-text
 *   4. site-footer     (Customizer)
 *
 * @package AlexandreOltramari
 */

get_header();
?>

<?php get_template_part( 'template-parts/hero' ); ?>

<?php get_template_part( 'template-parts/intro' ); ?>

<?php
$cases = olt_get_cases_query();
if ( $cases->have_posts() ) :
	while ( $cases->have_posts() ) :
		$cases->the_post();
		get_template_part( 'template-parts/case-image' );
		get_template_part( 'template-parts/case-text' );
	endwhile;
	wp_reset_postdata();
endif;
?>

<?php get_template_part( 'template-parts/site-footer' ); ?>

<?php
get_footer();
