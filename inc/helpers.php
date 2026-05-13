<?php
/**
 * Helpers — funções utilitárias usadas pelos templates.
 *
 * @package AlexandreOltramari
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Query all published cases ordered by menu_order.
 *
 * @return WP_Query
 */
function olt_get_cases_query() {
	return new WP_Query(
		array(
			'post_type'      => 'olt_case',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		)
	);
}

/**
 * Get URL of the WebP version of an attachment, falling back to original.
 *
 * @param int    $attachment_id Attachment ID.
 * @param string $size          Image size.
 * @return string
 */
function olt_image_url( $attachment_id, $size = 'full' ) {
	if ( ! $attachment_id ) {
		return '';
	}
	$url = wp_get_attachment_image_url( $attachment_id, $size );
	return $url ? $url : '';
}

/**
 * Extract YouTube video ID from a URL.
 *
 * Accepts: youtu.be/ID, youtube.com/watch?v=ID, /embed/ID, /shorts/ID.
 *
 * @param string $url YouTube URL.
 * @return string ID or empty string.
 */
function olt_youtube_id( $url ) {
	if ( empty( $url ) ) {
		return '';
	}
	$parsed = wp_parse_url( $url );
	if ( ! $parsed || empty( $parsed['host'] ) ) {
		return '';
	}
	$host = strtolower( $parsed['host'] );
	$id   = '';
	if ( false !== strpos( $host, 'youtu.be' ) ) {
		$id = isset( $parsed['path'] ) ? ltrim( $parsed['path'], '/' ) : '';
	} elseif ( false !== strpos( $host, 'youtube.com' ) ) {
		if ( isset( $parsed['query'] ) ) {
			parse_str( $parsed['query'], $q );
			$id = isset( $q['v'] ) ? $q['v'] : '';
		}
		if ( ! $id && isset( $parsed['path'] ) ) {
			$parts = explode( '/', trim( $parsed['path'], '/' ) );
			$id    = end( $parts );
		}
	}
	return preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $id );
}

/**
 * Build a YouTube embed URL from any input URL.
 *
 * @param string $url YouTube URL.
 * @return string Embed URL or empty.
 */
function olt_youtube_embed_url( $url ) {
	$id = olt_youtube_id( $url );
	return $id ? "https://www.youtube.com/embed/{$id}?autoplay=1" : '';
}
