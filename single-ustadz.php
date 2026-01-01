<?php
/**
 * Single Ustadz Template
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
ane_set_views( get_the_ID() );

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();

		$thumb_large = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
		$thumb_kotak = wp_get_attachment_image_src( get_post_thumbnail_id(), 'kotak' );

		$photo       = has_post_thumbnail() ? esc_url( $thumb_large[0] ) : esc_url( ane_dummy_thumbnail() );
		$photo_kotak = has_post_thumbnail() ? esc_url( $thumb_kotak[0] ) : esc_url( ane_dummy_kotak() );

		$pelajaran = get_the_terms( get_the_ID(), 'pelajaran' );
		$kelas     = get_the_terms( get_the_ID(), 'kelas' );
		?>

		<main id="site-content">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'ane-post-single ane-single-ustadz' ); ?>>
				<header class="entry-header">
					<div class="single-post-header" style="background: url('<?php echo esc_url( $photo ); ?>')"></div>
				</header>

				<?php ane_display_breadcrumbs(); ?>

				<div class="entry-content">
					<div class="ane-image">
						<img src="<?php echo esc_url( $photo_kotak ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
					</div>

					<div class="ane-content">
						<div class="ane-single-title">
							<h1 class="post-title"><?php the_title(); ?></h1>
						</div>

						<div class="share-this">
							<?php echo ane_social_share(); ?>
						</div>

						<?php if ( $pelajaran || $kelas ) : ?>
							<figure class="wp-block-table">
								<table>
									<tbody>
										<?php if ( $pelajaran ) : ?>
											<tr>
												<td><?php esc_html_e( 'Subject', 'makhadane' ); ?></td>
												<td>
													<div class="content-category">
														<?php foreach ( $pelajaran as $pel ) : ?>
															<a class="post-cat" href="<?php echo esc_url( get_term_link( $pel ) ); ?>">
																<?php echo esc_html( $pel->name ); ?>
															</a>
														<?php endforeach; ?>
													</div>
												</td>
											</tr>
										<?php endif; ?>

										<?php if ( $kelas ) : ?>
											<tr>
												<td><?php esc_html_e( 'Class', 'makhadane' ); ?></td>
												<td>
													<div class="content-category">
														<?php foreach ( $kelas as $kel ) : ?>
															<a class="post-cat" href="<?php echo esc_url( get_term_link( $kel ) ); ?>">
																<?php echo esc_html( $kel->name ); ?>
															</a>
														<?php endforeach; ?>
													</div>
												</td>
											</tr>
										<?php endif; ?>
									</tbody>
								</table>
							</figure>
						<?php endif; ?>

						<?php the_content(); ?>
					</div>
				</div>
			<?php
			endwhile;
endif;
			?>

			<?php
			ane_tampilkan_ustadz_terkait( 'kelas' );
			ane_tampilkan_ustadz_terkait( 'pelajaran' );
			?>

		</article>
	</main>

<?php get_footer(); ?>
