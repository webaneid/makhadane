<?php
/**
 * Testimonial Section Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals, WordPress.WP.GlobalVariablesOverride.Prohibited, WordPress.WP.I18n.MissingTranslatorsComment, WordPress.Security.EscapeOutput.OutputNotEscaped
?>
<section class="ane-home-testimoni">
	<div class="ane-container">
		<div class="ane-text">
			<?php
			$judul = get_sub_field( 'ane_title' );
			echo '<h2 class="section-title">' . ( ! empty( $judul ) ? wp_kses_post( $judul ) : esc_html__( 'Testi<span>moni</span>', 'makhadane' ) ) . '</h2>';

			$deskripsi = get_sub_field( 'ane_deskripsi' );
			if ( ! empty( $deskripsi ) ) {
				echo '<p>' . esc_html( $deskripsi ) . '</p>';
			}
			?>
			<div class="lainnya">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'testimoni' ) ); ?>" class="btn btn-primary" aria-label="<?php esc_attr_e( 'View All Testimonials', 'makhadane' ); ?>">
					<?php esc_html_e( 'View All', 'makhadane' ); ?> <?php echo wp_kses_post( makhadane_icon( 'chevron-right-alt-2' ) ); ?>
				</a>
			</div>
		</div>

		<?php
		$the_query = new WP_Query(
			array(
				'post_type'      => 'testimoni',
				'posts_per_page' => 9,
				'no_found_rows'  => true,
			)
		);

		if ( $the_query->have_posts() ) :
			?>
			<div class="testimoni-slider owl-carousel">
				<?php
				while ( $the_query->have_posts() ) :
					$the_query->the_post();
					get_template_part( 'tp/content', 'testimoni' );
				endwhile;
				?>
			</div>
			<?php
		endif;
		wp_reset_postdata();
		?>
	</div>
</section>
