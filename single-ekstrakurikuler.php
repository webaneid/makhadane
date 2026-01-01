<?php
/**
 * Single Ekstrakurikuler Template
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main" class="site-main">
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'ane-post-single ane-page-ekstra' ); ?>>
		<?php
		if ( have_posts() ) :
			ane_set_views( get_the_ID() );
			while ( have_posts() ) :
				the_post();

				$thumbImg = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' );
				$photo    = has_post_thumbnail() ? esc_url( $thumbImg[0] ) : esc_url( ane_dummy_thumbnail() );
				?>
				<header class="entry-header">
					<div class="single-post-header" style="background: url('<?php echo esc_url( $photo ); ?>')"></div>
				</header>

				<?php ane_display_breadcrumbs(); ?>

				<div class="entry-content">
					<div class="ane-single-title">
						<h1 class="post-title"><?php the_title(); ?></h1>
					</div>

					<div class="ane-image">
						<img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
					</div>

					<div class="ane-content">
						<div class="share-this">
							<?php
							if ( function_exists( 'ane_social_share' ) ) {
								echo ane_social_share();
							}
							?>
						</div>
						<?php the_content(); ?>
					</div>
				</div>
			<?php
			endwhile;
		endif;
		?>

		<div class="konten-terkait-v2">
			<?php
			$related_query = new WP_Query(
				array(
					'post_type'      => 'ekstrakurikuler',
					'posts_per_page' => 4,
					'orderby'        => 'rand',
					'post__not_in'   => array( get_the_ID() ),
				)
			);

			if ( $related_query->have_posts() ) :
				?>
				<h2><?php esc_html_e( 'View More', 'makhadane' ); ?></h2>
				<div class="testimoni-slider owl-carousel">
					<?php
					while ( $related_query->have_posts() ) :
						$related_query->the_post();
						get_template_part( 'tp/content', 'ekstrakurikuler' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			<?php endif; ?>
		</div>
	</article>
</main>

<?php get_footer(); ?>
