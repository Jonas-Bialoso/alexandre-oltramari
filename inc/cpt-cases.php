<?php
/**
 * Custom Post Type: Cases.
 *
 * Cada "case" corresponde a um par (case-image + case-text) na homepage.
 * Ordem de exibição = menu_order (drag & drop em Posts -> Cases).
 *
 * @package AlexandreOltramari
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the post type.
 */
function olt_register_case_cpt() {
	$labels = array(
		'name'                  => __( 'Cases', 'alexandre-oltramari' ),
		'singular_name'         => __( 'Case', 'alexandre-oltramari' ),
		'menu_name'             => __( 'Cases', 'alexandre-oltramari' ),
		'add_new'               => __( 'Adicionar case', 'alexandre-oltramari' ),
		'add_new_item'          => __( 'Novo case', 'alexandre-oltramari' ),
		'edit_item'             => __( 'Editar case', 'alexandre-oltramari' ),
		'new_item'              => __( 'Novo case', 'alexandre-oltramari' ),
		'view_item'             => __( 'Ver case', 'alexandre-oltramari' ),
		'search_items'          => __( 'Buscar cases', 'alexandre-oltramari' ),
		'not_found'             => __( 'Nenhum case encontrado.', 'alexandre-oltramari' ),
		'all_items'             => __( 'Todos os cases', 'alexandre-oltramari' ),
		'featured_image'        => __( 'Foto de capa (desktop)', 'alexandre-oltramari' ),
		'set_featured_image'    => __( 'Definir foto de capa', 'alexandre-oltramari' ),
		'remove_featured_image' => __( 'Remover foto de capa', 'alexandre-oltramari' ),
	);

	register_post_type(
		'olt_case',
		array(
			'labels'              => $labels,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-portfolio',
			'hierarchical'        => false,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			'has_archive'         => false,
			'rewrite'             => false,
			'capability_type'     => 'post',
		)
	);
}
add_action( 'init', 'olt_register_case_cpt' );

/**
 * Sort Cases list in admin by menu_order asc by default.
 */
function olt_admin_cases_order( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( $screen && 'edit-olt_case' === $screen->id ) {
		$query->set( 'orderby', 'menu_order title' );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'olt_admin_cases_order' );

/**
 * Register meta boxes for the Case CPT.
 * Storing custom fields in post meta (no ACF dependency).
 */
function olt_case_metaboxes() {
	add_meta_box(
		'olt_case_details',
		__( 'Detalhes do case', 'alexandre-oltramari' ),
		'olt_case_details_render',
		'olt_case',
		'normal',
		'high'
	);
	add_meta_box(
		'olt_case_videos',
		__( 'Vídeos do carrossel', 'alexandre-oltramari' ),
		'olt_case_videos_render',
		'olt_case',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'olt_case_metaboxes' );

/**
 * Render the "Detalhes" meta box.
 *
 * @param WP_Post $post Current post.
 */
function olt_case_details_render( $post ) {
	wp_nonce_field( 'olt_case_save', 'olt_case_nonce' );

	$plate_logo_id  = (int) get_post_meta( $post->ID, '_olt_plate_logo_id', true );
	$plate_logo_w   = (string) get_post_meta( $post->ID, '_olt_plate_logo_width', true );
	$plate_tagline  = (string) get_post_meta( $post->ID, '_olt_plate_tagline', true );
	$subtitle       = (string) get_post_meta( $post->ID, '_olt_subtitle', true );
	$mobile_pos     = (string) get_post_meta( $post->ID, '_olt_mobile_pos', true );

	$plate_logo_url = $plate_logo_id ? wp_get_attachment_image_url( $plate_logo_id, 'full' ) : '';
	?>

	<!-- Card 1: Identidade do case -->
	<div class="olt-card">
		<div class="olt-card__header">
			<h3 class="olt-card__title"><?php esc_html_e( 'Identidade do case', 'alexandre-oltramari' ); ?></h3>
			<span class="olt-card__hint"><?php esc_html_e( 'Aparece no card preto da seção da foto e como título da seção de texto', 'alexandre-oltramari' ); ?></span>
		</div>
		<div class="olt-grid olt-grid--with-preview">
			<div class="olt-grid">
				<div class="olt-field">
					<label for="olt_plate_tagline"><?php esc_html_e( 'Texto do plate (card preto)', 'alexandre-oltramari' ); ?></label>
					<input type="text" id="olt_plate_tagline" name="olt_plate_tagline" value="<?php echo esc_attr( $plate_tagline ); ?>" placeholder='Ex: É daqui&lt;br&gt;pra melhor'>
					<span class="olt-field__hint"><?php esc_html_e( 'Use <br> para quebra de linha.', 'alexandre-oltramari' ); ?></span>
				</div>
				<div class="olt-field">
					<label for="olt_subtitle"><?php esc_html_e( 'Título da seção de texto', 'alexandre-oltramari' ); ?></label>
					<input type="text" id="olt_subtitle" name="olt_subtitle" value="<?php echo esc_attr( $subtitle ); ?>" placeholder="Ex: O povo venceu de novo">
					<span class="olt-field__hint"><?php esc_html_e( 'H2 que aparece acima do carrossel de vídeos.', 'alexandre-oltramari' ); ?></span>
				</div>
			</div>
			<!-- Live preview -->
			<aside class="olt-preview-pane">
				<h3><?php esc_html_e( 'Preview do plate', 'alexandre-oltramari' ); ?></h3>
				<div class="olt-preview-plate">
					<img class="olt-preview-plate__logo" src="<?php echo esc_url( $plate_logo_url ); ?>" alt="" style="<?php echo $plate_logo_w ? 'width:' . (int) $plate_logo_w . 'px' : ''; ?>; <?php echo $plate_logo_url ? '' : 'display:none'; ?>">
					<span class="olt-preview-plate__slash"></span>
					<p class="olt-preview-plate__tag"><?php echo wp_kses( $plate_tagline, array( 'br' => array() ) ); ?></p>
				</div>
				<p class="olt-preview-pane__note"><?php esc_html_e( 'Atualiza enquanto você digita. A versão real do site usa as fontes Montserrat/Barlow.', 'alexandre-oltramari' ); ?></p>
			</aside>
		</div>
	</div>

	<!-- Card 2: Logo da campanha -->
	<div class="olt-card">
		<div class="olt-card__header">
			<h3 class="olt-card__title"><?php esc_html_e( 'Logo da campanha', 'alexandre-oltramari' ); ?></h3>
			<span class="olt-card__hint"><?php esc_html_e( 'PNG ou SVG com fundo transparente', 'alexandre-oltramari' ); ?></span>
		</div>
		<div class="olt-field olt-field--inline-row">
			<div class="olt-picker">
				<div class="olt-picker__preview">
					<?php if ( $plate_logo_url ) : ?><img src="<?php echo esc_url( $plate_logo_url ); ?>" alt=""><?php endif; ?>
				</div>
				<input type="hidden" id="olt_plate_logo_id" name="olt_plate_logo_id" value="<?php echo esc_attr( $plate_logo_id ); ?>">
				<div class="olt-picker__buttons">
					<button type="button" class="button" id="olt-pick-logo"><?php esc_html_e( 'Selecionar imagem', 'alexandre-oltramari' ); ?></button>
					<button type="button" class="button" id="olt-clear-logo"><?php esc_html_e( 'Remover', 'alexandre-oltramari' ); ?></button>
				</div>
			</div>
			<div class="olt-field">
				<label for="olt_plate_logo_width"><?php esc_html_e( 'Largura (px)', 'alexandre-oltramari' ); ?></label>
				<input type="number" id="olt_plate_logo_width" name="olt_plate_logo_width" value="<?php echo esc_attr( $plate_logo_w ); ?>" min="40" max="400" step="1" placeholder="170">
			</div>
		</div>
	</div>

	<!-- Card 3: Imagem de capa (mobile crop) -->
	<div class="olt-card">
		<div class="olt-card__header">
			<h3 class="olt-card__title"><?php esc_html_e( 'Imagem de capa — crop mobile', 'alexandre-oltramari' ); ?></h3>
			<span class="olt-card__hint"><?php esc_html_e( 'A foto é a "imagem destacada" (lateral direita). Aqui só o ajuste do crop mobile.', 'alexandre-oltramari' ); ?></span>
		</div>
		<div class="olt-field">
			<label for="olt_mobile_pos"><?php esc_html_e( 'object-position no mobile', 'alexandre-oltramari' ); ?></label>
			<input type="text" id="olt_mobile_pos" name="olt_mobile_pos" value="<?php echo esc_attr( $mobile_pos ); ?>" placeholder="center">
			<span class="olt-field__hint"><?php esc_html_e( 'Escolha um preset abaixo ou digite valores customizados (ex: "30% 30%"). Só afeta a versão mobile — desktop continua centralizado.', 'alexandre-oltramari' ); ?></span>
		</div>
	</div>
	<?php
}

/**
 * Render the "Vídeos" meta box.
 *
 * @param WP_Post $post Current post.
 */
function olt_case_videos_render( $post ) {
	$videos = get_post_meta( $post->ID, '_olt_videos', true );
	if ( ! is_array( $videos ) ) {
		$videos = array();
	}
	$row_template = olt_render_video_row( '__IDX__', array(), true );
	?>
	<div class="olt-card">
		<div class="olt-card__header">
			<h3 class="olt-card__title"><?php esc_html_e( 'Vídeos do carrossel', 'alexandre-oltramari' ); ?></h3>
			<span class="olt-card__hint"><?php esc_html_e( 'Arraste pela alça à esquerda pra reordenar. Tabela vazia esconde o carrossel.', 'alexandre-oltramari' ); ?></span>
		</div>
	</div>
	<div class="olt-videos-wrap">
		<table class="olt-videos" id="olt-videos">
			<thead>
				<tr>
					<th></th>
					<th><?php esc_html_e( 'Thumb', 'alexandre-oltramari' ); ?></th>
					<th><?php esc_html_e( 'URL do vídeo (YouTube ou Vimeo)', 'alexandre-oltramari' ); ?></th>
					<th><?php esc_html_e( 'Rótulo do botão', 'alexandre-oltramari' ); ?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php
				if ( $videos ) {
					foreach ( $videos as $i => $v ) {
						olt_render_video_row( $i, $v );
					}
				} else {
					olt_render_video_row( 0, array() );
				}
				?>
			</tbody>
		</table>
		<button type="button" class="button olt-videos__add" id="olt-add-video"><?php esc_html_e( '+ adicionar vídeo', 'alexandre-oltramari' ); ?></button>
	</div>
	<script>
		window.OLT_VIDEO_ROW_TEMPLATE = <?php echo wp_json_encode( $row_template ); ?>;
	</script>
	<?php
}

/**
 * Render a single video row.
 *
 * @param int|string $i      Row index.
 * @param array      $v      Row data.
 * @param bool       $return Whether to return instead of echo.
 * @return string|void
 */
function olt_render_video_row( $i, $v, $return = false ) {
	$thumb_id  = isset( $v['thumb_id'] ) ? (int) $v['thumb_id'] : 0;
	$thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'olt-video-thumb' ) : '';
	$url       = isset( $v['url'] ) ? (string) $v['url'] : '';
	$label     = isset( $v['label'] ) ? (string) $v['label'] : 'Veja a campanha aqui';
	$provider  = '';
	if ( false !== strpos( $url, 'vimeo.com' ) ) {
		$provider = 'vimeo';
	} elseif ( false !== strpos( $url, 'youtu' ) ) {
		$provider = 'youtube';
	}

	ob_start();
	?>
	<tr>
		<td class="olt-videos__drag" title="Arraste pra reordenar">⋮⋮</td>
		<td class="olt-videos__thumb">
			<div class="olt-picker">
				<div class="olt-picker__preview">
					<?php if ( $thumb_url ) : ?><img src="<?php echo esc_url( $thumb_url ); ?>" alt=""><?php endif; ?>
				</div>
				<input type="hidden" class="olt-thumb-id" name="olt_videos[<?php echo esc_attr( $i ); ?>][thumb_id]" value="<?php echo esc_attr( $thumb_id ); ?>">
				<button type="button" class="button button-small olt-pick-thumb"><?php esc_html_e( 'Selecionar', 'alexandre-oltramari' ); ?></button>
			</div>
		</td>
		<td>
			<input type="url" class="olt-videos__url" name="olt_videos[<?php echo esc_attr( $i ); ?>][url]" value="<?php echo esc_attr( $url ); ?>" placeholder="https://vimeo.com/... ou https://youtube.com/...">
			<span class="olt-videos__provider <?php echo $provider ? 'olt-videos__provider--' . esc_attr( $provider ) : ''; ?>" <?php echo $provider ? '' : 'style="display:none"'; ?>><?php echo esc_html( $provider ); ?></span>
		</td>
		<td>
			<input type="text" name="olt_videos[<?php echo esc_attr( $i ); ?>][label]" value="<?php echo esc_attr( $label ); ?>" placeholder="Veja a campanha aqui">
		</td>
		<td>
			<button type="button" class="olt-videos__remove" title="Remover vídeo">×</button>
		</td>
	</tr>
	<?php
	$html = ob_get_clean();
	if ( $return ) {
		return $html;
	}
	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Save Case meta.
 *
 * @param int $post_id Post ID.
 */
function olt_case_save( $post_id ) {
	if ( ! isset( $_POST['olt_case_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['olt_case_nonce'] ) ), 'olt_case_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array(
		'_olt_plate_tagline'     => isset( $_POST['olt_plate_tagline'] ) ? wp_kses_post( wp_unslash( $_POST['olt_plate_tagline'] ) ) : '',
		'_olt_subtitle'          => isset( $_POST['olt_subtitle'] ) ? sanitize_text_field( wp_unslash( $_POST['olt_subtitle'] ) ) : '',
		'_olt_plate_logo_id'     => isset( $_POST['olt_plate_logo_id'] ) ? (int) $_POST['olt_plate_logo_id'] : 0,
		'_olt_plate_logo_width'  => isset( $_POST['olt_plate_logo_width'] ) ? (int) $_POST['olt_plate_logo_width'] : 0,
		'_olt_mobile_pos'        => isset( $_POST['olt_mobile_pos'] ) ? sanitize_text_field( wp_unslash( $_POST['olt_mobile_pos'] ) ) : '',
	);
	foreach ( $fields as $key => $value ) {
		if ( '' === $value || 0 === $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}

	// Videos.
	$videos = array();
	if ( isset( $_POST['olt_videos'] ) && is_array( $_POST['olt_videos'] ) ) {
		foreach ( wp_unslash( $_POST['olt_videos'] ) as $row ) { // phpcs:ignore WordPress.Security.ValidationSanitization.MissingUnslash
			$thumb_id = isset( $row['thumb_id'] ) ? (int) $row['thumb_id'] : 0;
			$url      = isset( $row['url'] ) ? esc_url_raw( $row['url'] ) : '';
			$label    = isset( $row['label'] ) ? sanitize_text_field( $row['label'] ) : '';
			if ( $thumb_id || $url ) {
				$videos[] = array(
					'thumb_id' => $thumb_id,
					'url'      => $url,
					'label'    => $label ? $label : 'Veja a campanha aqui',
				);
			}
		}
	}
	if ( $videos ) {
		update_post_meta( $post_id, '_olt_videos', $videos );
	} else {
		delete_post_meta( $post_id, '_olt_videos' );
	}
}
add_action( 'save_post_olt_case', 'olt_case_save' );

/**
 * Enqueue admin assets on Case edit screen and Cases list + Tools/OLT seed.
 */
function olt_case_admin_assets( $hook ) {
	global $post;
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	$is_case_edit = in_array( $hook, array( 'post.php', 'post-new.php' ), true )
		&& isset( $post->post_type ) && 'olt_case' === $post->post_type;
	$is_case_list = $screen && 'edit-olt_case' === $screen->id;
	$is_seeder    = $screen && false !== strpos( $screen->id, 'olt-seed' );

	if ( ! $is_case_edit && ! $is_case_list && ! $is_seeder ) {
		return;
	}

	wp_enqueue_style(
		'olt-admin',
		OLT_THEME_URI . '/assets/css/admin.css',
		array(),
		OLT_THEME_VERSION
	);

	if ( $is_case_edit ) {
		wp_enqueue_media();
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script(
			'olt-admin-case',
			OLT_THEME_URI . '/assets/js/admin-case.js',
			array( 'jquery', 'jquery-ui-sortable' ),
			OLT_THEME_VERSION,
			true
		);
	}
}
add_action( 'admin_enqueue_scripts', 'olt_case_admin_assets' );

/* ============================================================
   Admin list columns — Cases archive view
   ============================================================ */

/**
 * Register custom columns for the Cases list.
 *
 * @param array $columns Default columns.
 * @return array
 */
function olt_case_columns( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		if ( 'title' === $key ) {
			$new['olt_thumb'] = __( 'Capa', 'alexandre-oltramari' );
			$new[ $key ]       = $label;
			$new['olt_plate']  = __( 'Plate', 'alexandre-oltramari' );
			$new['olt_videos'] = __( 'Vídeos', 'alexandre-oltramari' );
			$new['olt_status'] = __( 'Status', 'alexandre-oltramari' );
		} else {
			$new[ $key ] = $label;
		}
	}
	return $new;
}
add_filter( 'manage_olt_case_posts_columns', 'olt_case_columns' );

/**
 * Render custom column content.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function olt_case_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'olt_thumb':
			$thumb = get_the_post_thumbnail_url( $post_id, 'olt-video-thumb' );
			if ( $thumb ) {
				echo '<img src="' . esc_url( $thumb ) . '" alt="">';
			} else {
				echo '<div class="column-olt_thumb__none">sem capa</div>';
			}
			break;

		case 'olt_plate':
			$tagline = (string) get_post_meta( $post_id, '_olt_plate_tagline', true );
			$logo_id = (int) get_post_meta( $post_id, '_olt_plate_logo_id', true );
			$clean   = strip_tags( str_replace( '<br>', ' / ', $tagline ) );
			echo '<strong>' . esc_html( $clean ?: '—' ) . '</strong>';
			if ( $logo_id ) {
				echo '<span style="color:#2da34a">✓ logo</span>';
			} else {
				echo '<span style="color:#d63638">⚠ sem logo</span>';
			}
			break;

		case 'olt_videos':
			$videos = get_post_meta( $post_id, '_olt_videos', true );
			$count  = is_array( $videos ) ? count( $videos ) : 0;
			$class  = $count > 0 ? 'badge--ok' : 'badge--warn';
			echo '<span class="badge ' . esc_attr( $class ) . '">' . (int) $count . '</span>';
			break;

		case 'olt_status':
			$has_thumb   = (bool) get_post_thumbnail_id( $post_id );
			$has_logo    = (bool) get_post_meta( $post_id, '_olt_plate_logo_id', true );
			$has_tagline = (bool) get_post_meta( $post_id, '_olt_plate_tagline', true );
			$videos      = get_post_meta( $post_id, '_olt_videos', true );
			$videos_ok   = is_array( $videos ) && count( $videos ) > 0;

			$score = (int) $has_thumb + (int) $has_logo + (int) $has_tagline + (int) $videos_ok;
			if ( 4 === $score ) {
				echo '<span class="olt-status-dot olt-status-dot--ok"></span>Completo';
			} elseif ( $score >= 2 ) {
				echo '<span class="olt-status-dot olt-status-dot--warn"></span>Faltam itens';
			} else {
				echo '<span class="olt-status-dot olt-status-dot--bad"></span>Vazio';
			}
			break;
	}
}
add_action( 'manage_olt_case_posts_custom_column', 'olt_case_column_content', 10, 2 );
