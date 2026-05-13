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
	<div class="wrap">
		<h1>OLT — Importar cases iniciais</h1>
		<?php if ( $done ) : ?>
			<p>✔ Seed já executado em <?php echo esc_html( $done ); ?>. Para rodar novamente, apague a option <code>olt_seed_done</code>.</p>
		<?php endif; ?>

		<?php
		if ( isset( $_POST['olt_seed_run'] ) && check_admin_referer( 'olt_seed' ) ) {
			$result = olt_seed_run();
			$created = is_array( $result ) ? (int) $result['created'] : (int) $result;
			$updated = is_array( $result ) ? (int) $result['updated'] : 0;
			echo '<div class="notice notice-success"><p>' . esc_html( sprintf( '%d cases criados, %d atualizados (vídeos sincronizados).', $created, $updated ) ) . '</p></div>';
		}
		?>

		<form method="post">
			<?php wp_nonce_field( 'olt_seed' ); ?>
			<p>Cria os 9 cases do site original com plate, subtítulo e body. <strong>As imagens precisam ser anexadas manualmente</strong> (envie pela Mídia, depois edite cada case).</p>
			<p><button class="button button-primary" name="olt_seed_run" value="1">Rodar seed</button></p>
		</form>
	</div>
	<?php
}

/**
 * Run the seed.
 *
 * @return int Number of cases created.
 */
function olt_seed_run() {
	$cases = array(
		array(
			'title'    => 'É daqui pra melhor',
			'subtitle' => 'O povo venceu de novo',
			'tagline'  => 'É daqui<br>pra melhor',
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
		// Look up by title — if exists, update video list only (rest preserved).
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
			olt_seed_apply_videos( $post_id, $case['videos'] );
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
			olt_seed_apply_videos( $post_id, $case['videos'] );
			$created++;
		}
	}

	update_option( 'olt_seed_done', current_time( 'mysql' ) );
	return array( 'created' => $created, 'updated' => $updated );
}

/**
 * Apply a list of video URLs to a case as the carousel videos.
 * Preserves existing thumb_id if already set (matches by index).
 *
 * @param int   $post_id Post ID.
 * @param array $urls    List of video URLs.
 */
function olt_seed_apply_videos( $post_id, $urls ) {
	$existing = get_post_meta( $post_id, '_olt_videos', true );
	$existing = is_array( $existing ) ? $existing : array();

	$videos = array();
	foreach ( $urls as $i => $url ) {
		$thumb_id = isset( $existing[ $i ]['thumb_id'] ) ? (int) $existing[ $i ]['thumb_id'] : 0;
		$label    = isset( $existing[ $i ]['label'] ) ? (string) $existing[ $i ]['label'] : 'Veja a campanha aqui';
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
