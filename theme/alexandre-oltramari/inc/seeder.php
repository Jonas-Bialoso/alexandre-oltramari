<?php
/**
 * Seeder — popula os 9 cases iniciais a partir do conteúdo do site estático.
 *
 * Como rodar: visite /wp-admin/admin.php?page=olt-seed e clique no botão.
 * Roda só 1 vez (marca uma option). Para rodar de novo, apague a option
 * `olt_seed_done` em wp_options.
 *
 * @package AlexandreOltramari
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add seed page to admin menu.
 */
function olt_seed_menu() {
	add_management_page(
		'OLT — Importar cases',
		'OLT seed',
		'manage_options',
		'olt-seed',
		'olt_seed_page'
	);
}
add_action( 'admin_menu', 'olt_seed_menu' );

/**
 * Render the seeder page.
 */
function olt_seed_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Sem permissão.' );
	}
	$done = get_option( 'olt_seed_done' );
	?>
	<div class="wrap olt-seeder-page">
		<h1><?php esc_html_e( 'OLT — Importar cases iniciais', 'alexandre-oltramari' ); ?></h1>

		<?php
		if ( isset( $_POST['olt_seed_run'] ) && check_admin_referer( 'olt_seed' ) ) {
			$result = olt_seed_run();
			?>
			<div class="olt-seeder-result">
				<strong><?php esc_html_e( 'Seed concluído!', 'alexandre-oltramari' ); ?></strong>
				<ul style="margin: 8px 0 0; line-height: 1.6;">
					<li><?php echo esc_html( sprintf( '%d cases criados', (int) $result['created'] ) ); ?></li>
					<li><?php echo esc_html( sprintf( '%d cases atualizados (vídeos sincronizados)', (int) $result['updated'] ) ); ?></li>
					<li><?php echo esc_html( sprintf( '%d imagens importadas pra Biblioteca de Mídia', (int) $result['images'] ) ); ?></li>
					<li><?php echo esc_html( $result['home_page'] ? 'Página inicial: configurada ✓' : 'Página inicial: não alterada' ); ?></li>
				</ul>
				<p style="margin-top: 12px;">
					<a href="<?php echo esc_url( home_url() ); ?>" class="button button-secondary" target="_blank"><?php esc_html_e( 'Abrir site', 'alexandre-oltramari' ); ?></a>
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=olt_case' ) ); ?>" class="button button-secondary"><?php esc_html_e( 'Ver cases', 'alexandre-oltramari' ); ?></a>
				</p>
			</div>
			<?php
		}
		?>

		<div class="olt-seeder-card">
			<h2><?php esc_html_e( 'Setup completo em 1 clique', 'alexandre-oltramari' ); ?></h2>
			<p><?php esc_html_e( 'Esse script faz toda a configuração inicial do site automaticamente:', 'alexandre-oltramari' ); ?></p>
			<ul class="olt-seeder-steps">
				<li><?php esc_html_e( 'Cria os 9 cases com textos, plates, subtítulos e URLs do Vimeo', 'alexandre-oltramari' ); ?></li>
				<li><?php esc_html_e( 'Importa todas as imagens (38 arquivos .webp) pra Biblioteca de Mídia', 'alexandre-oltramari' ); ?></li>
				<li><?php esc_html_e( 'Anexa capa, logo e thumbs dos vídeos em cada case automaticamente', 'alexandre-oltramari' ); ?></li>
				<li><?php esc_html_e( 'Cria a página "Home" e configura como página inicial do site', 'alexandre-oltramari' ); ?></li>
			</ul>

			<?php if ( $done ) : ?>
				<p style="background:#fcf0e0;border-left:4px solid #dba617;padding:10px 14px;border-radius:4px;margin:16px 0;">
					<strong>ℹ️ Seed já foi executado anteriormente</strong> em <?php echo esc_html( $done ); ?>. Rodar de novo é seguro — cases existentes serão atualizados (vídeos resincronizados), imagens não serão duplicadas.
				</p>
			<?php endif; ?>

			<form method="post" style="margin-top: 20px;">
				<?php wp_nonce_field( 'olt_seed' ); ?>
				<button class="button button-primary button-hero" name="olt_seed_run" value="1"><?php echo $done ? '🔄 ' . esc_html__( 'Rodar seed novamente', 'alexandre-oltramari' ) : '🚀 ' . esc_html__( 'Rodar seed completo', 'alexandre-oltramari' ); ?></button>
			</form>
		</div>
	</div>
	<?php
}

/**
 * Run the seed.
 *
 * @return int Number of cases created.
 */
function olt_seed_run() {
	// 1. Import all theme images into Media Library (idempotent).
	$image_map = olt_seed_import_images();

	$cases = array(
		array(
			'title'    => 'É daqui pra melhor',
			'subtitle' => 'O povo venceu de novo',
			'tagline'  => 'É daqui<br>pra melhor',
			'bg'       => 'sec1-bg.webp',
			'logo'     => 'sec1-logo.webp',
			'thumbs'   => array( 'c1-v1.webp', 'c1-v2.webp', 'c1-v3.webp' ),
			'logo_w'   => 170,
			'body'     => 'Campanha de reeleição começa no início do mandato. Não foi as o governador do Amazonas, Wilson Lima. Por causa da pandemia e de sabotagens do grupo que governou antes, Wilson só conseguiu mostrar trabalho a partir da metade de seu primeiro mandato. Mas foram dois anos que valeram por quatro. De azarão, Wilson passou a liderar todas as pesquisas, antes mesmo de a campanha começar. Daí em diante, era só não errar. Não erramos. No final de outubro, com quase 60 por cento dos votos válidos, Wilson Lima foi reeleito governador do Amazonas.',
			'order'    => 10,
			'videos'   => array(
				'https://player.vimeo.com/video/817992702',
				'https://player.vimeo.com/video/817993281',
				'https://player.vimeo.com/video/817993657',
			),
		),
		array(
			'title'    => 'Bora mudar Maceió?',
			'subtitle' => 'Bora mudar Maceió?',
			'tagline'  => 'Bora mudar Maceió?',
			'bg'       => 'sec2-bg.webp',
			'logo'     => 'sec2-logo.webp',
			'thumbs'   => array( 'c2-v1.webp', 'c2-v2.webp' ),
			'logo_w'   => 141,
			'body'     => 'Em 2020, Maceió queria mudar. Queria um prefeito moderno, preparado, independente, próximo às pessoas – tudo isso pra já. Ajudei a formular a estratégia de marketing, participei da criação da campanha e escrevi roteiros de peças publicitárias exibidas na TV e na internet. JHC deu uma pisa nos adversários – e botou a velha política pra fora da prefeitura.',
			'order'    => 20,
			'videos'   => array(
				'https://player.vimeo.com/video/568984128',
				'https://player.vimeo.com/video/568984094',
			),
		),
		array(
			'title'    => 'Dia cinza',
			'subtitle' => 'Dia cinza',
			'tagline'  => 'Dia cinza',
			'bg'       => 'sec3-bg.webp',
			'logo'     => 'sec3-logo.webp',
			'thumbs'   => array( 'c3-v1.webp', 'c3-v1.webp' ),
			'logo_w'   => 190,
			'body'     => 'Droga é um tema delicado. Dialogar com jovem sobre isso é mais delicado ainda. Por isso, essa campanha, realizada em parceria com a agência Fields360, não tem cara de campanha. Fenômeno no YouTube, com sucessos que atingem 250 milhões de visualizações, a banda Melim lançou a música Dia Cinza contando a história real de um jovem envolvido com drogas. Após o engajamento de seus seguidores, veio a revelação: era uma campanha do Ministério da Cidadania alertando para o risco de usar drogas. Por ser incomum, a campanha produziu mídia espontânea e atingiu outros públicos na internet, no rádio e na televisão.',
			'order'    => 30,
			'videos'   => array(
				'https://player.vimeo.com/video/370505146',
				'https://player.vimeo.com/video/370505159',
			),
		),
		array(
			'title'      => 'Virando a mesa',
			'subtitle'   => 'O povo escolheu o novo',
			'tagline'    => 'Virando<br>a mesa',
			'bg'         => 'sec4-bg.webp',
			'logo'       => 'sec4-logo.webp',
			'thumbs'     => array( 'c4-v1.webp', 'c4-v2.webp', 'c4-v3.webp', 'c4-v4.webp' ),
			'logo_w'     => 140,
			'mobile_pos' => '30% 30%',
			'body'       => 'Em 2018, o jornalista Wilson Lima surpreendeu ao avançar para o segundo turno em pé de igualdade com o adversário, que pretendia governar o Amazonas pela quarta vez. Mas o confronto direto deixou ainda mais evidente a diferença entre a velha e a nova política. Selfie-vídeos, conversa franca e propostas realistas, ajudaram a eleger Wilson Lima governador do Amazonas com quase 20 pontos de vantagem sobre Amazonino Mendes.',
			'order'      => 40,
			'videos'     => array(
				'https://player.vimeo.com/video/819657972',
				'https://player.vimeo.com/video/819660578',
				'https://player.vimeo.com/video/819662274',
				'https://player.vimeo.com/video/819664811',
			),
		),
		array(
			'title'    => 'Mudança de verdade',
			'subtitle' => 'Mudança de verdade',
			'tagline'  => 'Mudança<br>de verdade',
			'bg'       => 'sec5-bg.webp',
			'logo'     => 'sec5-logo.webp',
			'thumbs'   => array( 'c5-v1.webp', 'c5-v2.webp', 'c5-v3.webp' ),
			'logo_w'   => 227,
			'body'     => 'Cidade com nome de santo. Onde o tempo é sagrado. Onde tudo acontece primeiro. Esta campanha, criada por mim e pelo jornalista Chico Mendez para a agência Nova S/B, é uma homenagem à maior metrópole da América Latina. Filmes, spots de rádio e peças para a internet destacam mudanças que humanizaram e modernizaram São Paulo. Tudo contado pelos próprios moradores da terra da garoa.',
			'order'    => 50,
			'videos'   => array(
				'https://player.vimeo.com/video/204880158',
				'https://player.vimeo.com/video/205131153',
				'https://player.vimeo.com/video/204886251',
			),
		),
		array(
			'title'    => 'Quanto mais cuidado, mais futuro',
			'subtitle' => 'Quanto mais cuidado, mais futuro',
			'tagline'  => 'Quanto mais cuidado, mais futuro',
			'bg'       => 'sec6-bg.webp',
			'logo'     => 'sec6-logo.webp',
			'thumbs'   => array( 'c6-v1.webp' ),
			'logo_w'   => 141,
			'body'     => 'O Prêmio Nobel de Economia, James Heckman, provou que estímulos no início da vida são decisivos para o sucesso na vida adulta. Essa é a ideia central do Criança Feliz, um programa social que ajuda milhares de famílias a promover o desenvolvimento de seus filhos. Esta campanha (na internet, no rádio, na TV e nas milhares de cidades que receberam o projeto) foi feita em parceria com a agência Fields360 para marcar o lançamento do Criança Feliz.',
			'order'    => 60,
			'videos'   => array(
				'https://player.vimeo.com/video/268091918',
			),
		),
		array(
			'title'      => 'Agora é ela',
			'subtitle'   => 'Agora é ela',
			'tagline'    => 'Agora é ela',
			'bg'         => 'sec7-bg.webp',
			'logo'       => 'sec7-logo.webp',
			'thumbs'     => array( 'c7-v1.webp', 'c7-v2.webp', 'c7-v3.webp' ),
			'logo_w'     => 93,
			'mobile_pos' => '15% center',
			'body'       => 'Filha de político, Simone Tebet construiu uma carreira com luz própria no Mato Grosso do Sul. Ela foi professora, advogada, prefeita e vice-governadora. Em 2014, Simone decidiu dar um passo ainda mais ousado: disputar uma vaga no Senado. Nessa campanha, coordenei uma equipe de cerca de 50 pessoas. Fomos responsáveis pela estratégia e por todo o conteúdo veiculado na TV, no rádio e na internet. E o povo disse sim para Simone — eleita com 52% dos votos válidos.',
			'order'      => 70,
			'videos'     => array(
				'https://player.vimeo.com/video/110841788',
				'https://player.vimeo.com/video/167177114',
				'https://player.vimeo.com/video/110839141',
			),
		),
		array(
			'title'    => 'Vamos conversar?',
			'subtitle' => 'Vamos conversar?',
			'tagline'  => 'Vamos conversar?',
			'bg'       => 'sec8-bg.webp',
			'logo'     => 'sec8-logo.webp',
			'thumbs'   => array( 'c8-v1.webp' ),
			'logo_w'   => 123,
			'body'     => 'Em 2013, Aécio Neves estava prestes a assumir a presidência do PSDB. Em vez de discurso, era hora de conversar com os brasileiros. Prestei consultoria para a formulação da nova estratégia de marketing, que inovou ao unir a mídia tradicional às novas mídias sociais. Ao convidar os brasileiros para o diálogo, Aécio consolidou sua candidatura à Presidência da República.',
			'order'    => 80,
			'videos'   => array(
				'https://player.vimeo.com/video/167172319',
			),
		),
		array(
			'title'    => 'Orgulho de ser goiano',
			'subtitle' => 'Orgulho de ser goiano',
			'tagline'  => 'Orgulho de ser goiano',
			'bg'       => 'sec9-bg.webp',
			'logo'     => 'sec9-logo.webp',
			'thumbs'   => array( 'c9-v1.webp' ),
			'logo_w'   => 173,
			'body'     => 'Marconi Perillo tinha um enorme desafio em 2010: derrotar um adversário histórico para voltar ao governo de Goiás pela quarta vez. Eu queria muito vencer a primeira eleição que disputava como "marqueteiro político". Na coordenação de marketing da campanha, ajudei a definir a estratégia, escrevi roteiros para a televisão e treinei o candidato para os debates. Deu Marconi de novo.',
			'order'    => 90,
			'videos'   => array(
				'https://player.vimeo.com/video/69646345',
			),
		),
	);

	$created = 0;
	$updated = 0;
	foreach ( $cases as $case ) {
		$existing = get_posts(
			array(
				'post_type'   => 'olt_case',
				'title'       => $case['title'],
				'post_status' => 'any',
				'numberposts' => 1,
				'fields'      => 'ids',
			)
		);

		if ( $existing ) {
			$post_id = (int) $existing[0];
			olt_seed_apply_videos( $post_id, $case['videos'], $case['thumbs'], $image_map );
			olt_seed_apply_images( $post_id, $case, $image_map );
			$updated++;
			continue;
		}

		$post_id = wp_insert_post(
			array(
				'post_type'    => 'olt_case',
				'post_status'  => 'publish',
				'post_title'   => $case['title'],
				'post_content' => $case['body'],
				'menu_order'   => $case['order'],
			)
		);
		if ( $post_id && ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, '_olt_subtitle', $case['subtitle'] );
			update_post_meta( $post_id, '_olt_plate_tagline', $case['tagline'] );
			update_post_meta( $post_id, '_olt_plate_logo_width', $case['logo_w'] );
			if ( ! empty( $case['mobile_pos'] ) ) {
				update_post_meta( $post_id, '_olt_mobile_pos', $case['mobile_pos'] );
			}
			olt_seed_apply_videos( $post_id, $case['videos'], $case['thumbs'], $image_map );
			olt_seed_apply_images( $post_id, $case, $image_map );
			$created++;
		}
	}

	// 4. Ensure a "Home" page exists and is set as front page.
	$home_page_configured = olt_seed_setup_home_page();

	update_option( 'olt_seed_done', current_time( 'mysql' ) );
	return array(
		'created'   => $created,
		'updated'   => $updated,
		'images'    => count( $image_map ),
		'home_page' => $home_page_configured,
	);
}

/**
 * Import every theme image into the Media Library (idempotent).
 * Returns map: filename => attachment_id.
 *
 * @return array<string,int>
 */
function olt_seed_import_images() {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$dir = OLT_THEME_DIR . '/assets/images';
	if ( ! is_dir( $dir ) ) {
		return array();
	}

	$existing = get_option( 'olt_image_map' );
	$map      = is_array( $existing ) ? $existing : array();

	$files = glob( $dir . '/*.{webp,png,jpg,jpeg,svg,gif}', GLOB_BRACE );
	if ( empty( $files ) ) {
		return $map;
	}

	foreach ( $files as $file ) {
		$basename = basename( $file );

		// If already imported and the attachment still exists, skip.
		if ( isset( $map[ $basename ] ) ) {
			$att_id = (int) $map[ $basename ];
			if ( $att_id && get_post( $att_id ) ) {
				continue;
			}
		}

		// Look for an existing attachment with the same _olt_seed_source meta.
		$search = get_posts(
			array(
				'post_type'   => 'attachment',
				'meta_key'    => '_olt_seed_source',
				'meta_value'  => $basename,
				'numberposts' => 1,
				'fields'      => 'ids',
				'post_status' => 'inherit',
			)
		);
		if ( $search ) {
			$map[ $basename ] = (int) $search[0];
			continue;
		}

		// Side-load the file into the Media Library.
		$tmp = wp_tempnam( $basename );
		copy( $file, $tmp );
		$file_array = array( 'name' => $basename, 'tmp_name' => $tmp );
		$att_id     = media_handle_sideload( $file_array, 0 );
		if ( is_wp_error( $att_id ) ) {
			@unlink( $tmp );
			continue;
		}
		update_post_meta( $att_id, '_olt_seed_source', $basename );
		$map[ $basename ] = (int) $att_id;
	}

	update_option( 'olt_image_map', $map );
	return $map;
}

/**
 * Attach the case bg (featured image) + logo + mobile-pos meta.
 *
 * @param int   $post_id   Post ID.
 * @param array $case      Case data.
 * @param array $image_map filename => attachment_id.
 */
function olt_seed_apply_images( $post_id, $case, $image_map ) {
	if ( ! empty( $case['bg'] ) && isset( $image_map[ $case['bg'] ] ) ) {
		set_post_thumbnail( $post_id, (int) $image_map[ $case['bg'] ] );
	}
	if ( ! empty( $case['logo'] ) && isset( $image_map[ $case['logo'] ] ) ) {
		update_post_meta( $post_id, '_olt_plate_logo_id', (int) $image_map[ $case['logo'] ] );
	}
}

/**
 * Create the "Home" page if absent and set it as the static front page.
 *
 * @return bool True if front page was set/updated.
 */
function olt_seed_setup_home_page() {
	// Look for an existing "Home" page.
	$home = get_page_by_path( 'home' );
	if ( ! $home ) {
		$home_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => 'Home',
				'post_name'    => 'home',
				'post_content' => '',
			)
		);
		if ( is_wp_error( $home_id ) ) {
			return false;
		}
	} else {
		$home_id = $home->ID;
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', (int) $home_id );
	return true;
}

/**
 * Apply a list of video URLs + thumbs to a case as the carousel videos.
 *
 * Thumb filenames are resolved against $image_map. If a previously stored
 * thumb attachment is still valid (and we don't have a better one in the
 * map), we keep it — admins can override via the editor.
 *
 * @param int   $post_id   Post ID.
 * @param array $urls      List of video URLs.
 * @param array $thumbs    List of thumb filenames (same order as $urls).
 * @param array $image_map filename => attachment_id.
 */
function olt_seed_apply_videos( $post_id, $urls, $thumbs = array(), $image_map = array() ) {
	$existing = get_post_meta( $post_id, '_olt_videos', true );
	$existing = is_array( $existing ) ? $existing : array();

	$videos = array();
	foreach ( $urls as $i => $url ) {
		$existing_thumb = isset( $existing[ $i ]['thumb_id'] ) ? (int) $existing[ $i ]['thumb_id'] : 0;
		$seed_thumb     = isset( $thumbs[ $i ], $image_map[ $thumbs[ $i ] ] ) ? (int) $image_map[ $thumbs[ $i ] ] : 0;
		$thumb_id       = $seed_thumb ? $seed_thumb : $existing_thumb;
		$label          = isset( $existing[ $i ]['label'] ) ? (string) $existing[ $i ]['label'] : 'Veja a campanha aqui';

		$videos[] = array(
			'thumb_id' => $thumb_id,
			'url'      => $url,
			'label'    => $label,
		);
	}
	if ( $videos ) {
		update_post_meta( $post_id, '_olt_videos', $videos );
	}
}
