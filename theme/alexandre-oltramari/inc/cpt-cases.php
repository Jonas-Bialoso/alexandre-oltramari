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
	<style>
		.olt-field { margin: 16px 0; }
		.olt-field label { display:block; font-weight:600; margin-bottom:4px; }
		.olt-field input[type="text"],
		.olt-field input[type="number"],
		.olt-field textarea { width: 100%; max-width: 600px; }
		.olt-logo-preview img { max-height: 80px; background: #222; padding: 8px; border-radius: 4px; }
	</style>

	<p class="olt-field">
		<label for="olt_plate_tagline"><?php esc_html_e( 'Texto do plate (aparece no card preto, ex: "É daqui pra melhor")', 'alexandre-oltramari' ); ?></label>
		<input type="text" id="olt_plate_tagline" name="olt_plate_tagline" value="<?php echo esc_attr( $plate_tagline ); ?>" placeholder="Use &lt;br&gt; para quebra de linha">
		<small><?php esc_html_e( 'Pode incluir &lt;br&gt; para quebra de linha.', 'alexandre-oltramari' ); ?></small>
	</p>

	<p class="olt-field">
		<label for="olt_subtitle"><?php esc_html_e( 'Título da seção de texto (h2 acima do carrossel, ex: "O povo venceu de novo")', 'alexandre-oltramari' ); ?></label>
		<input type="text" id="olt_subtitle" name="olt_subtitle" value="<?php echo esc_attr( $subtitle ); ?>">
	</p>

	<div class="olt-field">
		<label><?php esc_html_e( 'Logo da campanha (PNG/SVG com fundo transparente)', 'alexandre-oltramari' ); ?></label>
		<div class="olt-logo-preview">
			<?php if ( $plate_logo_url ) : ?>
				<img src="<?php echo esc_url( $plate_logo_url ); ?>" alt="">
			<?php endif; ?>
		</div>
		<input type="hidden" id="olt_plate_logo_id" name="olt_plate_logo_id" value="<?php echo esc_attr( $plate_logo_id ); ?>">
		<button type="button" class="button" id="olt-pick-logo"><?php esc_html_e( 'Selecionar imagem', 'alexandre-oltramari' ); ?></button>
		<button type="button" class="button" id="olt-clear-logo"><?php esc_html_e( 'Remover', 'alexandre-oltramari' ); ?></button>
	</div>

	<p class="olt-field">
		<label for="olt_plate_logo_width"><?php esc_html_e( 'Largura do logo em px (ex: 170)', 'alexandre-oltramari' ); ?></label>
		<input type="number" id="olt_plate_logo_width" name="olt_plate_logo_width" value="<?php echo esc_attr( $plate_logo_w ); ?>" min="40" max="400" step="1">
	</p>

	<p class="olt-field">
		<label for="olt_mobile_pos"><?php esc_html_e( 'object-position no mobile (opcional, ex: "30% center" para deslocar o crop)', 'alexandre-oltramari' ); ?></label>
		<input type="text" id="olt_mobile_pos" name="olt_mobile_pos" value="<?php echo esc_attr( $mobile_pos ); ?>" placeholder="center">
	</p>

	<script>
	(function($){
		var frame;
		$('#olt-pick-logo').on('click', function(e){
			e.preventDefault();
			if (frame) { frame.open(); return; }
			frame = wp.media({
				title: 'Selecionar logo',
				button: { text: 'Usar este logo' },
				multiple: false
			});
			frame.on('select', function(){
				var att = frame.state().get('selection').first().toJSON();
				$('#olt_plate_logo_id').val(att.id);
				$('.olt-logo-preview').html('<img src="'+att.url+'" alt="">');
			});
			frame.open();
		});
		$('#olt-clear-logo').on('click', function(e){
			e.preventDefault();
			$('#olt_plate_logo_id').val('');
			$('.olt-logo-preview').empty();
		});
	})(jQuery);
	</script>
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
	?>
	<p><?php esc_html_e( 'Adicione um vídeo por linha. Deixe a tabela vazia para esconder o carrossel.', 'alexandre-oltramari' ); ?></p>
	<table class="widefat striped" id="olt-videos">
		<thead>
			<tr>
				<th style="width:120px"><?php esc_html_e( 'Thumb', 'alexandre-oltramari' ); ?></th>
				<th><?php esc_html_e( 'URL do vídeo (YouTube)', 'alexandre-oltramari' ); ?></th>
				<th><?php esc_html_e( 'Rótulo', 'alexandre-oltramari' ); ?></th>
				<th style="width:60px"></th>
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
	<p>
		<button type="button" class="button" id="olt-add-video"><?php esc_html_e( '+ adicionar vídeo', 'alexandre-oltramari' ); ?></button>
	</p>
	<script>
	(function($){
		var idx = <?php echo (int) max( 1, count( $videos ) ); ?>;
		$('#olt-add-video').on('click', function(e){
			e.preventDefault();
			var html = <?php echo wp_json_encode( olt_render_video_row( '__IDX__', array(), true ) ); ?>.replace(/__IDX__/g, idx);
			$('#olt-videos tbody').append(html);
			idx++;
		});
		$('#olt-videos').on('click', '.olt-pick-thumb', function(e){
			e.preventDefault();
			var $btn = $(this);
			var frame = wp.media({ title: 'Thumb do vídeo', multiple: false });
			frame.on('select', function(){
				var att = frame.state().get('selection').first().toJSON();
				$btn.siblings('input.olt-thumb-id').val(att.id);
				$btn.siblings('.olt-thumb-preview').html('<img src="'+att.url+'" style="max-width:100px;max-height:60px">');
			});
			frame.open();
		});
		$('#olt-videos').on('click', '.olt-remove-video', function(e){
			e.preventDefault();
			$(this).closest('tr').remove();
		});
	})(jQuery);
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

	ob_start();
	?>
	<tr>
		<td>
			<div class="olt-thumb-preview">
				<?php if ( $thumb_url ) : ?>
					<img src="<?php echo esc_url( $thumb_url ); ?>" style="max-width:100px;max-height:60px">
				<?php endif; ?>
			</div>
			<input type="hidden" class="olt-thumb-id" name="olt_videos[<?php echo esc_attr( $i ); ?>][thumb_id]" value="<?php echo esc_attr( $thumb_id ); ?>">
			<button type="button" class="button olt-pick-thumb"><?php esc_html_e( 'Selecionar', 'alexandre-oltramari' ); ?></button>
		</td>
		<td>
			<input type="url" name="olt_videos[<?php echo esc_attr( $i ); ?>][url]" value="<?php echo esc_attr( $url ); ?>" placeholder="https://www.youtube.com/watch?v=..." style="width:100%">
		</td>
		<td>
			<input type="text" name="olt_videos[<?php echo esc_attr( $i ); ?>][label]" value="<?php echo esc_attr( $label ); ?>" placeholder="Veja a campanha aqui" style="width:100%">
		</td>
		<td>
			<button type="button" class="button button-link-delete olt-remove-video">×</button>
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
 * Enqueue media uploader on Case edit screen.
 */
function olt_case_admin_assets( $hook ) {
	global $post;
	if ( ( 'post.php' === $hook || 'post-new.php' === $hook ) && isset( $post->post_type ) && 'olt_case' === $post->post_type ) {
		wp_enqueue_media();
	}
}
add_action( 'admin_enqueue_scripts', 'olt_case_admin_assets' );
