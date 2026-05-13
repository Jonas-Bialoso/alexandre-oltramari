<?php
/**
 * Fallback template — required by WordPress.
 *
 * On the homepage, WP uses front-page.php instead. This file only kicks
 * in for archives, single posts, and other queries that don't have a
 * more specific template. We keep it minimal: header, the loop, footer.
 *
 * @package AlexandreOltramari
 */

get_header();
?>
<main class="site-main" style="padding: 80px 24px; max-width: 720px; margin: 0 auto;">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class(); ?>>
				<header>
					<h1><?php the_title(); ?></h1>
				</header>
				<div class="content">
					<?php the_content(); ?>
				</div>
			</article>
		<?php endwhile; ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Nada encontrado.', 'alexandre-oltramari' ); ?></p>
	<?php endif; ?>
</main>
<?php
get_footer();
