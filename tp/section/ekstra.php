<?php
/**
 * Extracurricular Section Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="ane-home-ekstrakurikuler gp">
	<div class="ane-container">
		<div class="ane-col-46 align-items-center">
			<div class="ane-kiri">
				<div class="ane-text">
					<?php
					$judul = get_sub_field( 'ane_title' );
					if ( ! empty( $judul ) ) :
						echo '<h2 class="judul-utama">' . wp_kses_post( $judul ) . '</h2>';
					else :
						echo '<h2 class="judul-utama">Ekstra<span>kurikuler</span></h2>';
					endif;

					$deskripsi = get_sub_field( 'ane_deskripsi' );
					if ( ! empty( $deskripsi ) ) :
						echo '<p>' . esc_html( $deskripsi ) . '</p>';
					endif;
					?>
					<a class="btn btn-primary" href="<?php echo esc_url( get_post_type_archive_link( 'ekstrakurikuler' ) ); ?>">
						<?php esc_html_e( 'View All', 'makhadane' ); ?> <i class="ane-chevron-right-alt-2"></i>
					</a>
				</div>
			</div>

			<div class="ane-kanan">
				<div class="ane-col-row">
					<?php
					$the_query = new WP_Query( array(
						'post_type'      => 'ekstrakurikuler',
						'posts_per_page' => 4,
					) );
					if ( $the_query->have_posts() ) :
						while ( $the_query->have_posts() ) : $the_query->the_post();
							get_template_part( 'tp/content', 'ekstrakurikuler' );
						endwhile;
						wp_reset_postdata();
					endif;
					?>
				</div>
			</div>
		</div>
	</div>
</section>
