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
 * Detect video provider + extract ID from a URL.
 *
 * Supports:
 *  - YouTube: youtu.be/ID, youtube.com/watch?v=ID, /embed/ID, /shorts/ID
 *  - Vimeo:   vimeo.com/ID, player.vimeo.com/video/ID
 *
 * @param string $url Video URL.
 * @return array { provider: 'youtube'|'vimeo'|'', id: string }
 */
function olt_video_info( $url ) {
	$empty = array( 'provider' => '', 'id' => '' );
	if ( empty( $url ) ) {
		return $empty;
	}
	$parsed = wp_parse_url( $url );
	if ( ! $parsed || empty( $parsed['host'] ) ) {
		return $empty;
	}
	$host = strtolower( $parsed['host'] );

	// Vimeo.
	if ( false !== strpos( $host, 'vimeo.com' ) ) {
		$path = isset( $parsed['path'] ) ? trim( $parsed['path'], '/' ) : '';
		// player.vimeo.com/video/ID OR vimeo.com/ID.
		$parts = explode( '/', $path );
		$id    = '';
		foreach ( $parts as $p ) {
			if ( ctype_digit( $p ) ) {
				$id = $p;
				break;
			}
		}
		if ( $id ) {
			return array( 'provider' => 'vimeo', 'id' => $id );
		}
	}

	// YouTube.
	if ( false !== strpos( $host, 'youtu.be' ) || false !== strpos( $host, 'youtube.com' ) ) {
		$id = '';
		if ( false !== strpos( $host, 'youtu.be' ) ) {
			$id = isset( $parsed['path'] ) ? ltrim( $parsed['path'], '/' ) : '';
		} else {
			if ( isset( $parsed['query'] ) ) {
				parse_str( $parsed['query'], $q );
				$id = isset( $q['v'] ) ? $q['v'] : '';
			}
			if ( ! $id && isset( $parsed['path'] ) ) {
				$parts = explode( '/', trim( $parsed['path'], '/' ) );
				$id    = end( $parts );
			}
		}
		$id = preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $id );
		if ( $id ) {
			return array( 'provider' => 'youtube', 'id' => $id );
		}
	}

	return $empty;
}

/**
 * Build a player embed URL from a Vimeo/YouTube URL.
 *
 * @param string $url Original URL.
 * @return string Embed URL or empty.
 */
function olt_video_embed_url( $url ) {
	$info = olt_video_info( $url );
	if ( empty( $info['id'] ) ) {
		return '';
	}
	if ( 'vimeo' === $info['provider'] ) {
		return "https://player.vimeo.com/video/{$info['id']}?autoplay=1&color=00fff2";
	}
	return "https://www.youtube.com/embed/{$info['id']}?autoplay=1&rel=0";
}

/**
 * Back-compat alias kept for older templates.
 *
 * @param string $url URL.
 * @return string ID.
 */
function olt_youtube_id( $url ) {
	$info = olt_video_info( $url );
	return ( 'youtube' === $info['provider'] ) ? $info['id'] : '';
}
