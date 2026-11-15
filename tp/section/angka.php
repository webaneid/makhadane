<?php
/**
 * School Data Section Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals, WordPress.WP.GlobalVariablesOverride.Prohibited, WordPress.WP.I18n.MissingTranslatorsComment, WordPress.Security.EscapeOutput.OutputNotEscaped

if ( have_rows( 'ane_data_sekolah' ) ) : ?>
	<div class="ane-data-content">
		<?php
		while ( have_rows( 'ane_data_sekolah' ) ) :
			the_row();
			$icon  = get_sub_field( 'ane_icon' );
			$title = get_sub_field( 'ane_title' );
			$jml   = get_sub_field( 'ane_jumlah' );

			$icon  = ! empty( $icon ) ? esc_attr( $icon ) : '';
			$title = ! empty( $title ) ? esc_html( $title ) : '';
			$jml   = ! empty( $jml ) ? esc_html( $jml ) : '';

			if ( ! empty( $icon ) && ! empty( $title ) && ! empty( $jml ) ) :
				?>
			<div class="ane-item">
				<?php echo wp_kses_post( makhadane_icon( $icon ) ); ?>
				<h2 class="mb-0 fw-bold"><?php echo $jml; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
				<p class="mb-0"><?php echo $title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			</div>
				<?php
		endif;
endwhile;
		?>
	</div>
<?php endif; ?>
