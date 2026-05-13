<?php
/**
 * Template part: Case Text (título + carrossel de vídeos + body).
 *
 * Expected inside the Loop with $post set to an `olt_case` post.
 *
 * @package AlexandreOltramari
 */

$post_id  = get_the_ID();
$subtitle = (string) get_post_meta( $post_id, '_olt_subtitle', true );
if ( ! $subtitle ) {
	$subtitle = get_the_title();
}
$videos    = get_post_meta( $post_id, '_olt_videos', true );
$videos    = is_array( $videos ) ? $videos : array();
$count     = count( $videos );
$per_page  = $count > 2 ? 2 : ( $count >= 1 ? min( 2, $count ) : 1 );
$variant   = ( 1 === $count ) ? ' case-text--single' : ( ( 2 === $count ) ? ' case-text--two' : '' );

$body = apply_filters( 'the_content', get_the_content() );
?>
<article class="snap case-text<?php echo esc_attr( $variant ); ?>" data-carousel data-per-page="<?php echo (int) $per_page; ?>">
	<h2 class="case-text__title" data-anim="fade-up"><?php echo esc_html( $subtitle ); ?></h2>

	<?php if ( $count > 0 ) : ?>
		<div class="case-text__carousel" data-anim="fade">
			<div class="case-text__viewport">
				<div class="case-text__track<?php echo 1 === $count ? ' case-text__track--center' : ''; ?>">
					<?php foreach ( $videos as $v ) :
						$thumb_id  = isset( $v['thumb_id'] ) ? (int) $v['thumb_id'] : 0;
						$thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'olt-video-thumb' ) : '';
						$video_url = isset( $v['url'] ) ? (string) $v['url'] : '';
						$video_id  = olt_youtube_id( $video_url );
						$label     = isset( $v['label'] ) ? (string) $v['label'] : 'Veja a campanha aqui';
					?>
						<div class="video-card" data-video="<?php echo esc_attr( $video_id ); ?>">
							<?php if ( $thumb_url ) : ?>
								<img class="video-card__thumb" src="<?php echo esc_url( $thumb_url ); ?>" alt="">
							<?php endif; ?>
							<img class="video-card__play" src="<?php echo esc_url( OLT_THEME_URI . '/assets/images/icon-play.svg' ); ?>" alt="">
							<span class="video-card__label"><?php echo esc_html( $label ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
				<?php if ( $count > 1 ) : ?>
					<div class="case-text__nav">
						<button data-carousel-prev aria-label="<?php esc_attr_e( 'Anterior', 'alexandre-oltramari' ); ?>">
							<img src="<?php echo esc_url( OLT_THEME_URI . '/assets/images/icon-arrow-left.svg' ); ?>" alt="">
						</button>
						<button data-carousel-next aria-label="<?php esc_attr_e( 'Próximo', 'alexandre-oltramari' ); ?>">
							<img src="<?php echo esc_url( OLT_THEME_URI . '/assets/images/icon-arrow-right.svg' ); ?>" alt="">
						</button>
					</div>
				<?php endif; ?>
			</div>
			<?php if ( $count > 1 ) : ?>
				<div class="case-text__pagination" data-carousel-pagination></div>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if ( $body ) : ?>
		<div class="case-text__body" data-anim="fade-up"><?php echo $body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
	<?php endif; ?>
</article>
