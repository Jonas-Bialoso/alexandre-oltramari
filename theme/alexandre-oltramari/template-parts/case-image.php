<?php
/**
 * Template part: Case Image (foto grande + plate).
 *
 * Expected inside the Loop with $post set to an `olt_case` post.
 *
 * @package AlexandreOltramari
 */

$post_id      = get_the_ID();
$bg_id        = (int) get_post_thumbnail_id( $post_id );
$bg_url       = $bg_id ? wp_get_attachment_image_url( $bg_id, 'olt-case-desktop' ) : '';
$logo_id      = (int) get_post_meta( $post_id, '_olt_plate_logo_id', true );
$logo_url     = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';
$logo_width   = (int) get_post_meta( $post_id, '_olt_plate_logo_width', true );
$tagline      = (string) get_post_meta( $post_id, '_olt_plate_tagline', true );
$mobile_pos   = (string) get_post_meta( $post_id, '_olt_mobile_pos', true );
$logo_style   = $logo_width ? 'width:' . $logo_width . 'px' : '';
$mobile_attr  = $mobile_pos ? ' style="--mobile-pos: ' . esc_attr( $mobile_pos ) . '"' : '';
?>
<section class="snap case-image">
	<?php if ( $bg_url ) : ?>
		<img class="case-image__bg" src="<?php echo esc_url( $bg_url ); ?>" alt="" data-lightbox-image<?php echo $mobile_attr; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<?php endif; ?>
	<div class="case-image__plate" data-anim="card">
		<?php if ( $logo_url ) : ?>
			<img class="case-image__logo" src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>"<?php echo $logo_style ? ' style="' . esc_attr( $logo_style ) . '"' : ''; ?> data-anim="left">
		<?php endif; ?>
		<img class="case-image__slash" src="<?php echo esc_url( OLT_THEME_URI . '/assets/images/shape-slash.svg' ); ?>" alt="" data-anim="static">
		<p class="case-image__tag" data-anim="right"><?php echo wp_kses( $tagline, array( 'br' => array() ) ); ?></p>
	</div>
</section>
