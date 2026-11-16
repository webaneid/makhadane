<?php
/**
 * Teachers Section Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals, WordPress.WP.GlobalVariablesOverride.Prohibited, WordPress.WP.I18n.MissingTranslatorsComment, WordPress.Security.EscapeOutput.OutputNotEscaped
?>
<section class="ane-home-guru gp">
	<div class="ane-container">
		<div class="ane-text">
			<?php
			$judul = get_sub_field( 'ane_title' );
			if ( ! empty( $judul ) ) :
				echo '<h2 class="section-title">' . wp_kses_post( $judul ) . '</h2>';
			else :
				echo '<h2 class="section-title">' . esc_html__( 'Our', 'makhadane' ) . ' <span>' . esc_html__( 'Teachers', 'makhadane' ) . '</span></h2>';
			endif;

			$deskripsi = get_sub_field( 'ane_deskripsi' );
			if ( ! empty( $deskripsi ) ) :
				echo '<p>' . esc_html( $deskripsi ) . '</p>';
			endif;
			?>

			<div class="lainnya">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'ustadz' ) ); ?>" class="btn btn-primary">
					<?php esc_html_e( 'View All', 'makhadane' ); ?>
					<?php echo wp_kses_post( makhadane_icon( 'chevron-right-alt-2' ) ); ?>
				</a>
			</div>
		</div>

		<?php
		$the_query = new WP_Query(
			array(
				'post_type'      => 'ustadz',
				'posts_per_page' => 8,
				'post_status'    => 'publish',
			)
		);

		if ( $the_query->have_posts() ) :
			?>
			<div class="owl-carousel dot-style2" id="home-sliding">
				<?php
				while ( $the_query->have_posts() ) :
					$the_query->the_post();
					get_template_part( 'tp/content', 'ustadz' );
				endwhile;
				?>
			</div>
			<?php
			wp_reset_postdata();
		endif;
		?>
	</div>
</section>
